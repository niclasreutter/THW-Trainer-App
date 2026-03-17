# Unified Practice View — Design Spec

**Datum:** 2026-03-17
**Status:** Draft

## Zusammenfassung

Die drei separaten Practice-Implementierungen (Global, Lehrgänge, OV-Lernpools) werden zu einer einzigen, einheitlichen View und Session-Logik zusammengeführt. Alle drei Kontexte nutzen dieselbe `practice.blade.php` und `practice-summary.blade.php`, gesteuert über ein einheitliches Daten-Array.

## Motivation

- **3x duplizierter Code:** `practice.blade.php` (64KB), `lehrgaenge/practice.blade.php`, `ortsverband/lernpools/practice.blade.php` — fast identische Logik
- **Inkonsistente UX:** Lehrgänge/Lernpools haben eine einfachere Übungsoberfläche als die globale Practice
- **Wartungsaufwand:** Änderungen müssen in 3 Views synchron gehalten werden

## Scope

### In Scope
- Einheitliche Practice-View für alle drei Kontexte
- Einheitliche Summary-View + Scroll-Bug-Fix (kein Scrollen ohne Mehrwert)
- Practice-Modi für Lehrgänge/Lernpools: Alle, Ungelöste, Nach Lernabschnitt
- Modi-Buttons direkt auf Lehrgang/Lernpool-Detailseiten (kein separates Menü)
- Neuer `PracticeSessionService` als zentrale Session-Logik
- `ProgressResolverInterface` mit 3 Implementierungen

### Out of Scope
- Spaced Repetition für Lehrgänge/Lernpools (bleibt global-only)
- Such-Modus für Lehrgänge/Lernpools
- Änderungen an Models, Migrations, Gamification
- Redesign der Practice-UI selbst

## Architektur

### Einheitliches Daten-Interface

Jeder Controller liefert folgendes Array an die View:

```php
$practiceData = [
    'context'      => 'global' | 'lehrgang' | 'lernpool',
    'contextLabel' => 'Grundausbildung',           // Anzeigename
    'backUrl'      => '/lehrgaenge/ga',             // Zurück-Link
    'submitUrl'    => '/lehrgaenge/ga/submit',      // Antwort absenden
    'summaryUrl'   => '/lehrgaenge/ga/summary',     // Summary-Seite
    'issueUrl'     => '/lehrgaenge/ga/issue',       // Issue melden (nullable)

    'question'     => $question,                    // Frage-Objekt (siehe Question-Contract)
    'answerResult' => $answerResult,                // Feedback nach Antwort (nullable)
    'progress'     => [
        'current'   => 5,
        'total'     => 20,
        'correct'   => 3,
        'incorrect' => 1,
        'points'    => 30,
        'mastered'  => 2,
    ],
    'mode'         => 'all' | 'unsolved' | 'section',
    'sectionName'  => 'Lernabschnitt 3',           // optional, nur bei mode=section

    // Global-only Extras (null für Lehrgang/Lernpool):
    'difficultyInfo'    => $difficultyInfo,         // Schwierigkeits-Badge
    'isSpacedRepetition' => false,                  // SR-Indikator
    'bookmarked'        => false,                   // Lesezeichen-Status
];
```

### Question-Contract

Die View greift nur auf folgende Properties zu — alle drei Question-Models liefern diese:

| Property | Typ | Beschreibung |
|----------|-----|--------------|
| `id` | int | Primärschlüssel |
| `frage` | string | Fragetext |
| `antwort_a` | string | Antwort A |
| `antwort_b` | string | Antwort B |
| `antwort_c` | string | Antwort C |
| `loesung` | string | Korrekte Lösung ("A", "A,B") |
| `lernabschnitt` | int | Lernabschnitt-Nummer |
| `nummer` | int | Frage-Nummer |

Kontextspezifische Properties (z.B. `lehrgang_id`, `lernpool_id`) werden von der View nicht verwendet.

### PracticeSessionService

Neuer Service unter `app/Services/PracticeSessionService.php`:

```php
class PracticeSessionService
{
    public function __construct(
        private ProgressResolverInterface $resolver,
        private GamificationService $gamification,
    ) {}

    // Session mit Fragen-IDs starten, Modus setzen
    public function startSession(string $context, ?string $contextId, array $questionIds, string $mode): void;

    // Aktuelle Frage + Practice-Data-Array für die View liefern
    public function getCurrentQuestion(string $context, ?string $contextId): ?array;

    // Antwort verarbeiten: Progress updaten, Gamification, Feedback generieren
    // $answer = ['A', 'B'] — bereits gemappte Buchstaben-Antworten
    // Returns: $answerResult array für Feedback-Anzeige
    public function submitAnswer(string $context, ?string $contextId, int $questionId, array $answer): array;

    // Session-Fortschritt als Array
    public function getProgress(string $context, ?string $contextId): array;

    // Session beenden, Summary-Daten liefern
    // Returns: ['correct' => int, 'incorrect' => int, 'accuracy' => float,
    //           'points' => int, 'mastered' => int, 'totalAnswered' => int,
    //           'durationMinutes' => int, 'modeName' => string]
    public function endSession(string $context, ?string $contextId): array;
}
```

**Session-Handling:** Der Service nutzt durchgehend persistente Session-Keys (nicht `flash()`). Keys werden nach Verarbeitung explizit via `session()->forget()` entfernt. Dies entspricht dem Verhalten des bestehenden `PracticeController`.

**Session-Keys** werden kontextspezifisch generiert:
- Global: `practice_ids`, `practice_mode`, etc. (bestehende Keys beibehalten)
- Lehrgang: `practice_lehrgang_{id}_ids`, `practice_lehrgang_{id}_mode`, etc.
- Lernpool: `practice_lernpool_{id}_ids`, `practice_lernpool_{id}_mode`, etc.

**Answer-Shuffling:** Bleibt in der View/Alpine.js-Schicht. Die View erzeugt `answer_mapping`, der Controller mappt die Position zurück auf Buchstaben und übergibt `['A', 'B']` an den Service.

### Re-Queuing-Strategie bei falschen Antworten

| Kontext | Verhalten |
|---------|-----------|
| Global | Frage wird aus Session entfernt. Spaced Repetition plant Wiederholung. |
| Lehrgang | Falsche/teilweise richtige Fragen werden ans Ende der Session-Queue verschoben. |
| Lernpool | Falsche/teilweise richtige Fragen werden ans Ende der Session-Queue verschoben. |

Der Service erhält die Re-Queuing-Strategie als Parameter bei `startSession()`:

```php
public function startSession(
    string $context,
    ?string $contextId,
    array $questionIds,
    string $mode,
    string $requeueStrategy = 'requeue' // 'requeue' | 'remove'
): void;
```

Global nutzt `'remove'` (SR übernimmt), Lehrgang/Lernpool nutzen `'requeue'`.

### ProgressResolverInterface

```php
interface ProgressResolverInterface
{
    public function getProgress(int $userId, int $questionId): ?object;
    public function updateProgress(int $userId, int $questionId, bool $correct): void;
    public function getQuestionById(int $id): object;
    public function getQuestionsByIds(array $ids): Collection;
    public function createStatistic(int $userId, int $questionId, bool $correct): void;
}
```

Drei Implementierungen:
- `GlobalProgressResolver` — nutzt `UserQuestionProgress` + `Question` + `QuestionStatistic`
- `LehrgangProgressResolver` — nutzt `UserLehrgangProgress` + `LehrgangQuestion` + `LehrgangQuestionStatistic`
- `LernpoolProgressResolver` — nutzt `OrtsverbandLernpoolProgress` + `OrtsverbandLernpoolQuestion` + `OrtsverbandLernpoolQuestionStatistic`

### Controller-Änderungen

**PracticeController** (global):
- Bestehende Methoden (`all`, `unsolved`, `section`, etc.) delegieren an `PracticeSessionService`
- Frage-Selektion (Prioritätslogik: SR-fällig > failed > unmastered > rest) bleibt im Controller, da global-spezifisch
- `show()` ruft `getCurrentQuestion()` auf, ergänzt global-only Extras (`difficultyInfo`, `isSpacedRepetition`, `bookmarked`) und gibt `view('practice', $data)` zurück
- `submit()` mappt Antwort, ruft `submitAnswer()` auf, ruft zusätzlich `LernsessionService::recordAnswer()` auf (global-only)
- `reportIssue()` bleibt unverändert im Controller
- Globale Modi (`bookmarked`, `failed`, `search`, `spaced_repetition`) bleiben global-only, fließen aber durch denselben Service

**LehrgangController**:
- Bestehende `practice()` und `submitAnswer()` werden refactored
- Neue Methoden: `practiceUnsolved()`, `practiceSection()`
- Frage-Selektion: einfache Query (alle / where not solved / where lernabschnitt)
- Alle nutzen `PracticeSessionService` mit `LehrgangProgressResolver`
- `reportIssue()` bleibt im Controller (nutzt `LehrgangQuestionIssue`)
- Lehrgang-Completion (`lehrgaenge.complete` View) wird zur Summary-Seite — wenn alle Fragen gemeistert, zeigt Summary eine Completion-Message
- Return: `view('practice', $data)` — gleiche View

**OrtsverbandLernpoolPracticeController**:
- Bestehende `show()` und `answer()` werden refactored
- Neue Methoden: `unsolved()`, `section()`
- Alle nutzen `PracticeSessionService` mit `LernpoolProgressResolver`
- Return: `view('practice', $data)` — gleiche View

### Neue Routes

```php
// Lehrgang Practice-Modi
Route::get('/lehrgaenge/{slug}/practice', ...)->name('lehrgaenge.practice');
Route::get('/lehrgaenge/{slug}/practice/unsolved', ...)->name('lehrgaenge.practice.unsolved');
Route::get('/lehrgaenge/{slug}/practice/section/{nr}', ...)->name('lehrgaenge.practice.section');
Route::get('/lehrgaenge/{slug}/practice/show', ...)->name('lehrgaenge.practice.show');
Route::post('/lehrgaenge/{slug}/practice/submit', ...)->name('lehrgaenge.practice.submit');
Route::get('/lehrgaenge/{slug}/practice/summary', ...)->name('lehrgaenge.practice.summary');

// Lernpool Practice-Modi
Route::get('/.../practice', ...)->name('ortsverband.lernpools.practice');
Route::get('/.../practice/unsolved', ...)->name('ortsverband.lernpools.practice.unsolved');
Route::get('/.../practice/section/{nr}', ...)->name('ortsverband.lernpools.practice.section');
Route::post('/.../practice/submit', ...)->name('ortsverband.lernpools.practice.submit');
Route::get('/.../practice/summary', ...)->name('ortsverband.lernpools.practice.summary');
```

### View-Änderungen

**`practice.blade.php`:**
- Alle hardcodierten URLs/Routes durch `$submitUrl`, `$backUrl`, `$summaryUrl` ersetzen
- Header zeigt `$contextLabel` statt festen Text
- Kontextspezifische Elemente nur bei passendem `$context` anzeigen:
  - Difficulty-Badge: nur wenn `$difficultyInfo` nicht null
  - SR-Indikator: nur wenn `$isSpacedRepetition`
  - Bookmark-Button: nur wenn `$context === 'global'`
  - Issue-Button: nutzt `$issueUrl`
- Formular-Action nutzt `$submitUrl`

**`practice-summary.blade.php`:**
- Gleiche Parametrisierung wie practice.blade.php
- Lehrgang-Completion: wenn `$completed === true`, zeigt zusätzliche Completion-Message
- **Scroll-Fix:** Layout auf Viewport-Höhe begrenzen (`h-screen` + `overflow-hidden`), überflüssigen Whitespace entfernen

**Gelöschte Views:**
- `resources/views/lehrgaenge/practice.blade.php`
- `resources/views/lehrgaenge/complete.blade.php` (wird in Summary integriert)
- `resources/views/ortsverband/lernpools/practice.blade.php`

### Detailseiten-Updates

**Lehrgang-Detailseite** (`lehrgaenge/show.blade.php`):
- Drei Buttons statt einem: "Alle üben", "Ungelöste üben", "Nach Abschnitt"
- "Nach Abschnitt" zeigt Dropdown/Liste der verfügbaren Lernabschnitte

**Lernpool-Detailseite** (Lernpool-Show-View):
- Analog drei Buttons für die Modi

## Implementierungs-Reihenfolge

1. **Phase 1:** `ProgressResolverInterface` + 3 Implementierungen erstellen
2. **Phase 2:** `PracticeSessionService` erstellen
3. **Phase 3:** Globale Practice auf Service umstellen (Regressions-Test)
4. **Phase 4:** `practice.blade.php` parametrisieren
5. **Phase 5:** Lehrgang-Practice auf Service + unified View umstellen
6. **Phase 6:** Lernpool-Practice auf Service + unified View umstellen
7. **Phase 7:** Alte Views löschen, Summary-Scroll-Fix, Detailseiten-Buttons
8. **Phase 8:** Manueller Test aller drei Kontexte

## Fehlerbehandlung

- Leere Session (keine Fragen) → Redirect zurück mit Flash-Message
- Ungültiger Kontext → 404
- Nicht eingeschrieben (Lehrgang/Lernpool) → Inline-Check im Controller (bestehend), keine neue Policy nötig

## Testing-Strategie

- Bestehende Practice-Tests als Basis
- `PracticeSessionService` Unit-Tests für alle drei Kontexte
- Feature-Tests: Practice-Flow für Lehrgang und Lernpool
- Manueller Test: Alle drei Kontexte durchspielen, Summary prüfen
