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

    'question'     => $question,                    // Frage-Objekt
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
];
```

Die drei Question-Models (`Question`, `LehrgangQuestion`, `OrtsverbandLernpoolQuestion`) haben identische Spalten (`frage`, `antwort_a`, `antwort_b`, `antwort_c`, `loesung`) und können direkt übergeben werden.

### PracticeSessionService

Neuer Service unter `app/Services/PracticeSessionService.php`:

```php
class PracticeSessionService
{
    public function __construct(private ProgressResolverInterface $resolver) {}

    // Session mit Fragen-IDs starten, Modus setzen
    public function startSession(string $context, ?string $contextId, array $questionIds, string $mode): void;

    // Aktuelle Frage + Practice-Data-Array für die View liefern
    public function getCurrentQuestion(string $context, ?string $contextId): ?array;

    // Antwort verarbeiten: Progress updaten, Gamification, Feedback generieren
    public function submitAnswer(string $context, ?string $contextId, int $questionId, array $answer): array;

    // Session-Fortschritt als Array
    public function getProgress(string $context, ?string $contextId): array;

    // Session beenden, Summary-Daten liefern
    public function endSession(string $context, ?string $contextId): array;
}
```

**Session-Keys** werden kontextspezifisch generiert:
- Global: `practice_ids`, `practice_mode`, etc.
- Lehrgang: `practice_lehrgang_{id}_ids`, `practice_lehrgang_{id}_mode`, etc.
- Lernpool: `practice_lernpool_{id}_ids`, `practice_lernpool_{id}_mode`, etc.

### ProgressResolverInterface

```php
interface ProgressResolverInterface
{
    public function getProgress(int $userId, int $questionId): ?object;
    public function updateProgress(int $userId, int $questionId, bool $correct): void;
    public function getQuestionById(int $id): object;
    public function getQuestionsByIds(array $ids): Collection;
}
```

Drei Implementierungen:
- `GlobalProgressResolver` — nutzt `UserQuestionProgress` + `Question`
- `LehrgangProgressResolver` — nutzt `UserLehrgangProgress` + `LehrgangQuestion`
- `LernpoolProgressResolver` — nutzt `OrtsverbandLernpoolProgress` + `OrtsverbandLernpoolQuestion`

### Controller-Änderungen

**PracticeController** (global):
- Bestehende Methoden (`all`, `unsolved`, `section`, etc.) delegieren an `PracticeSessionService`
- `show()` ruft `getCurrentQuestion()` auf und gibt `view('practice', $data)` zurück
- `submit()` ruft `submitAnswer()` auf

**LehrgangController**:
- Bestehende `practice()` und `submitAnswer()` werden refactored
- Neue Methoden: `practiceUnsolved()`, `practiceSection()`
- Alle nutzen `PracticeSessionService` mit `LehrgangProgressResolver`
- Return: `view('practice', $data)` — gleiche View

**OrtsverbandLernpoolPracticeController**:
- Bestehende `show()` und `answer()` werden refactored
- Neue Methoden: `unsolved()`, `section()`
- Alle nutzen `PracticeSessionService` mit `LernpoolProgressResolver`
- Return: `view('practice', $data)` — gleiche View

### Neue Routes

```php
// Lehrgang Practice-Modi (zusätzlich zu bestehender practice-Route)
Route::get('/lehrgaenge/{slug}/practice/unsolved', [LehrgangController::class, 'practiceUnsolved']);
Route::get('/lehrgaenge/{slug}/practice/section/{nr}', [LehrgangController::class, 'practiceSection']);
Route::get('/lehrgaenge/{slug}/practice/show', [LehrgangController::class, 'practiceShow']);
Route::post('/lehrgaenge/{slug}/practice/submit', [LehrgangController::class, 'practiceSubmit']);
Route::get('/lehrgaenge/{slug}/practice/summary', [LehrgangController::class, 'practiceSummary']);

// Lernpool Practice-Modi (zusätzlich zu bestehender practice-Route)
Route::get('/ortsverband/{ov}/lernpools/{pool}/practice/unsolved', [..., 'unsolved']);
Route::get('/ortsverband/{ov}/lernpools/{pool}/practice/section/{nr}', [..., 'section']);
Route::get('/ortsverband/{ov}/lernpools/{pool}/practice/summary', [..., 'summary']);
```

### View-Änderungen

**`practice.blade.php`:**
- Alle hardcodierten URLs/Routes durch `$submitUrl`, `$backUrl`, `$summaryUrl` ersetzen
- Header zeigt `$contextLabel` statt festen Text
- Kontextspezifische Elemente (z.B. globale Practice-Modi im Header) nur bei `$context === 'global'` anzeigen
- Formular-Action nutzt `$submitUrl`

**`practice-summary.blade.php`:**
- Gleiche Parametrisierung wie practice.blade.php
- **Scroll-Fix:** Layout auf Viewport-Höhe begrenzen (`h-screen` + `overflow-hidden`), überflüssigen Whitespace entfernen

**Gelöschte Views:**
- `resources/views/lehrgaenge/practice.blade.php`
- `resources/views/ortsverband/lernpools/practice.blade.php`

### Detailseiten-Updates

**Lehrgang-Detailseite** (`lehrgaenge/show.blade.php`):
- Drei Buttons statt einem: "Alle üben", "Ungelöste üben", "Nach Abschnitt"
- "Nach Abschnitt" zeigt Dropdown/Liste der verfügbaren Lernabschnitte

**Lernpool-Detailseite** (Lernpool-Show-View):
- Analog drei Buttons für die Modi

## Fehlerbehandlung

- Leere Session (keine Fragen) → Redirect zurück mit Flash-Message
- Ungültiger Kontext → 404
- Nicht eingeschrieben (Lehrgang/Lernpool) → 403 via Policy

## Testing-Strategie

- Bestehende Practice-Tests als Basis
- `PracticeSessionService` Unit-Tests für alle drei Kontexte
- Feature-Tests: Practice-Flow für Lehrgang und Lernpool
- Manueller Test: Alle drei Kontexte durchspielen, Summary prüfen
