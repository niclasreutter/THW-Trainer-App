# Dashboard Redesign – "THW Operations"

**Datum:** 2026-03-15
**Route:** `/dashboard`
**View:** `resources/views/dashboard.blade.php`
**Stack:** Laravel 12 + Blade + Tailwind CSS + Alpine.js

---

## Ziel

Komplettes Redesign des Nutzer-Dashboards. Weg vom generischen Glassmorphism-Look, hin zu einem einzigartigen "Command Center"-Stil mit THW-Blau als dominante Akzentfarbe. Das Layout nutzt die bestehende App-Sidebar und füllt den Content-Bereich mit einem technischen, strukturierten Grid.

---

## Design-Prinzipien

- **THW-Blau dominant** (`#00337F`, `#004db3`) – Struktur, Borders, Fortschrittsbalken, primäre CTAs
- **Gold** (`#fbbf24`) – nur für Gamification-Elemente: Streak, besondere Achievements, Prüfung 5/5 bestanden
- **Eckigere Radii** – `0.75rem 0.25rem 0.75rem 0.75rem` statt gleichmäßig rund → technischer Look
- **Keine Emojis** im UI
- **Keine Icons in Buttons**
- **Dark + Light Mode** vollständig unterstützt via bestehende CSS-Variablen
- **Kein Collapsible** für wichtige Bereiche – alles direkt sichtbar, kompakt

---

## Layout-Struktur

Die bestehende App-Sidebar (`lg:w-64 lg:fixed`) bleibt unverändert. Das Dashboard füllt den Content-Bereich (`lg:pl-64`).

```
[Bestehende App-Sidebar 256px] | [Dashboard Content Area]
                               |  1.  Status-Strip
                               |  2.  Alert-Banner (konditionell)
                               |  3.  Next-Step Hero Card
                               |  4.  Stats Grid (2-spaltig)
                               |  5.  Countdown-Strip (immer sichtbar, 2 Varianten)
                               |  6.  Aktivitäts-Chart
                               |  7.  Heatmap + Spaced Rep (2/3 + 1/3)
                               |  8.  Ausbilder-Karte (konditionell)
                               |  9.  Lehrgänge & Lernpools Grid
                               |  10. Leaderboard-Modal (overlay, konditionell)
```

---

## Sektionen im Detail

### 1. Status-Strip

Kompakte Leiste oben im Content-Bereich. Ersetzt den bisherigen "Hallo Name"-Header.

- **Linke Seite:** Nutzername + Level-Badge, darunter XP/Theorie-Fortschrittsbalken (THW-Blau Füllung, Breite = `progressPercent`%)
- **Rechte Seite:** Streak-Wert (gold, mit Freeze-Indikator falls vorhanden), Punkte, Liga-Badge
- **Styling:** Dünne linke THW-Blau-Borderlinie (4px), `glass`-Hintergrund, `border-radius: 0.5rem`
- **Kein großer Header** – kompakt, einzeilig auf Desktop, zweizeilig auf Mobile

**Daten:** `$user->name`, `$user->level`, `$progressPercent`, `$user->streak_days`, `$user->points`, `$user->league`

---

### 2. Alert-Banner (konditionell)

Direkt unter Status-Strip. Jeder Alert ist eine schmale Zeile (`alert-compact`).

Priorität (höchste zuerst):
1. `session('error')` → `glass-error`
2. `$hasFailedQuestions` → `glass-warning` + Button "Fehler wiederholen" → `route('failed.index')`
3. `$spacedRepetitionDue > 0` (`$spacedRepetitionDue` aus Route-Closure via `SpacedRepetitionService`) → `glass` mit blauer Border + Button "Wiederholen" → `route('practice.spaced-repetition')`
4. `$activeLernsession` (inline berechnet: `app(LernsessionService::class)->getActiveSessionsForUser(auth()->user())->first()`) → `glass` mit grünem Puls-Dot + Alpine.js-Timer + Button "Teilnehmen" → `route('lernsession.live', $activeLernsession)`. Die zugehörige Session: `$activeLernsession->learningSession`.
5. `$streakAtRisk` (inline berechnet: `$user->streak_days > 0 && $questionsRemaining > 0 && (!$user->last_activity_date || Carbon::parse($user->last_activity_date)->lt(Carbon::today()))`) → `glass-warning` mit Freeze-Option (falls `$streakFreezeStatus['remaining'] > 0`) + Button "Jetzt lernen" → `route('practice.all')`

Alle Alerts bleiben funktional identisch zum aktuellen Stand.

---

### 3. Next-Step Hero Card

**Volle Breite**, prominenteste Karte. Zeigt dynamisch genau eine Hauptaktion.

**Zustandslogik (Priorität, exklusiv, erste zutreffende Bedingung gewinnt):**

| Zustand | Bedingung | Inhalt | Button |
|---------|-----------|--------|--------|
| A – Fehler ausstehend | `$hasFailedQuestions` | "X Fehler-Fragen ausstehend" + Erklärung | "Fehler wiederholen" → `route('failed.index')` |
| B – Theorie lernen | `!$hasFailedQuestions && $progressPercent < 100` | "Noch X Fragen zu meistern" + Fortschritts-Ring | "Weiter lernen" → `route('practice.all')` |
| C – Prüfung starten | `!$hasFailedQuestions && $progressPercent >= 100 && $exams < 5` | "Bereit für Prüfung! X/5 bestanden" + Ring | "Prüfung starten" → `route('exam.index')` |
| D – Abschluss | `$exams >= 5` | "5/5 Prüfungen bestanden!" | "Prüfung wiederholen" → `route('exam.index')` |

Hinweis: `$canStartExam = ($progress >= $total && !$hasFailedQuestions)`. Zustand C entspricht `$canStartExam && $exams < 5`, was äquivalent ist aber oben explizit ausgeschrieben.

**Styling:**
- Zustand A/B/C: `glass-blue` mit THW-Blau Lensflare-Glow
- Zustand D: `glass-gold` mit Gold-Glow (Ausnahme der Gold-Regel – besonderer Moment)
- Border-Radius: `0.75rem 0.25rem 0.75rem 0.75rem`
- Layout: Linke Seite Text + CTA, rechte Seite Fortschritts-Ring (SVG, 80px)
- Ring-Farbe: THW-Blau für Theorie, Gold für Prüfungs-Streak

---

### 4. Stats Grid (2-spaltig)

Zwei gleichbreite Karten nebeneinander.

**Karte links – Theorie-Fortschritt** (`glass-tl`):
- Titel: "THEORIE-FORTSCHRITT" (uppercase, letter-spacing, klein)
- Große Zahl: `$progressPercent`% (font-weight 800)
- Fortschrittsbalken: THW-Blau, `$progress / $total`
- Zeile 1: "`$progress` von `$total` gemeistert"
- Zeile 2: "Heute: `$todayAnswered` Fragen"
- Zeile 3 (falls vorhanden): "Spaced Rep: `$spacedRepetitionDue` fällig"
- Button: "Abschnitte üben" → `route('practice.menu')`
- Top-Border: 3px THW-Blau

**Karte rechts – Prüfungs-Status** (`glass-br`):
- Titel: "PRÜFUNGS-STATUS"
- Großer Wert: "`$exams`/5 bestanden"
- Fortschrittsbalken: THW-Blau (Prüfungs-Streak, `$exams / 5 * 100`%)
- Letzter Versuch: Prozent aus `$recentExams->first()` (gold falls ≥ 75%)
- Schnitt: `$recentExams->avg(fn($e) => round(($e->correct_answers / 40) * 100))` (gold falls ≥ 75%)
- Top-Border: 3px THW-Blau
- **Button-Logik:**
  - `$canStartExam` → "Prüfung starten" (`btn-secondary`) → `route('exam.index')`
  - `$hasFailedQuestions` → "Fehler wiederholen" (`btn-ghost`) → `route('failed.index')`
  - sonst (Theorie nicht fertig) → "Erst Theorie" (`btn-ghost`, disabled-Optik, kein Link)

---

### 5. Countdown-Strip (konditionell)

Position 5 im Layout wird **immer belegt** – entweder Countdown oder Fallback-Karte:

**Variablen-Quellen (alle inline in `@php` berechnet, wie im aktuellen View):**
- `$daysLeft` = `($user->exam_date && $user->exam_date->isFuture()) ? (int) now()->startOfDay()->diffInDays($user->exam_date, false) : null`
- `$dailyTarget` = komplexe Berechnung aus `$progressData`, Fehlerquote, `$effectiveDays` (exakt wie aktueller View, unverändert übernehmen). Kann `null` sein wenn kein Datum.
- `$todayAnswered` = `QuestionStatistic::where('user_id', $user->id)->whereDate('created_at', today())->count()`

**Wenn `$daysLeft !== null && $daysLeft > 0`** (Datum gesetzt, Datum in der Zukunft):
Schmale volle Breite, `glass` mit linker THW-Blau-Border (4px):
- Links: "`$daysLeft` Tage bis zur Prüfung"
- Mitte: Tages-Zielwert (`$dailyTarget`) + Mini-Fortschrittsbar (`$todayAnswered / $dailyTarget * 100`%)
- Rechts: Status ("X/Y heute – geschafft!" wenn `$todayAnswered >= $dailyTarget`, sonst "noch X übrig")

**Sonst** (kein Datum gesetzt oder Datum vergangen):
Schmale Karte gleicher Höhe, `glass` mit linker THW-Blau-Border (4px, gestrichelt):
- Text: "Prüfungsdatum eintragen für personalisierte Lernempfehlung"
- Link-Button (ghost): "Datum eintragen" → `route('profile') . '#exam_date'`

---

### 6. Aktivitäts-Chart

Volle Breite, `glass`.

- Titel-Zeile: "DIESE WOCHE" links, Trefferquote + Trend-Pfeil rechts
- 7-Balken-Chart (Mo–So), Balken in **THW-Blau** (nicht gold wie bisher)
- Heutiger Balken: helleres Blau mit leichtem Glow
- Leere Tage: subtiler Platzhalter-Balken
- Anzahl über den Balken (klein, `var(--text-muted)`)
- Wochentag-Label darunter, heute in THW-Blau

---

### 7. Heatmap + Spaced Rep (2/3 + 1/3)

**Heatmap (2/3-Breite)**, `glass-br`:
- Titel: "STÄRKEN & SCHWÄCHEN"
- 10 Zellen (Abschnitt 1–10), **fest 10 Zellen** – der THW-Grundausbildungs-Katalog hat exakt 10 Lernabschnitte (kein dynamischer Count nötig)
- Datenquelle: `$sectionStats` (aus Route-Closure: `DB::table('question_statistics')...groupBy('lernabschnitt')`) → `$sectionStats->firstWhere('lernabschnitt', $s)`
- Grid: 5 Spalten × 2 Reihen
- Farben unverändert: Grün/Gelb/Rot/Grau (semantische Bedeutung, kein THW-Blau)
- Klickbar → `route('practice.section', $s)`
- Legende unten

**Spaced Rep (1/3-Breite)**, `glass`:
- Titel: "WIEDERHOLUNGEN"
- Große Zahl: `$spacedRepetitionDue` fällig
- Kurze Erklärung: "Spaced Repetition für langfristiges Behalten"
- Button: "Wiederholen" → `route('practice.spaced-repetition')`
- Falls 0: "Alles aktuell" Meldung

---

### 8. Ausbilder-Karte (konditionell)

Nur wenn `$isAusbilder && $userOV`.

**Variablen-Quellen (alle inline in `@php` berechnet, wie im aktuellen View):**
- `$userOV` = `auth()->user()->ortsverbände->first()`
- `$isAusbilder` = `$userOV && $userOV->members()->where('user_id', auth()->id())->first()?->pivot->role === 'ausbildungsbeauftragter'`
- `$ovStats` = `['members' => $regularMembers->count(), 'avg_progress' => round($memberProgress->avg('theory_progress_percent') ?? 0)]` – alles inline, kein Controller nötig

Schmale Karte volle Breite, `glass-blue` (bestehende Klasse):
- Badge "Ausbilder" + OV-Name
- Mitgliederanzahl (`$ovStats['members']`) + Ø-Fortschritt (`$ovStats['avg_progress']`%)
- Button "Verwalten" → `route('ortsverband.index')`

---

### 9. Lehrgänge & Lernpools Grid

Volle Breite, 3-Spalten-Grid.

**Datenquellen (aus bestehendem Dashboard-View):**
- Lehrgänge: `$enrolledLehrgaenge` (`Auth::user()->enrolledLehrgaenge()->get()`)
- Lernpools: `$enrolledLernpools` (`auth()->user()->enrolledLernpools()->where('is_active', true)->get()`)
- Fortschritt je Lehrgang: inline via `UserLehrgangProgress` (wie im aktuellen View)
- Fortschritt je Lernpool: inline via `lernpoolProgress()` (wie im aktuellen View)

**Darstellung:**
- Titel-Zeile: "LEHRGÄNGE & LERNPOOLS" + "Alle anzeigen" Link → `route('lehrgaenge.index')`
- Max. 3 Karten sichtbar (Lehrgänge zuerst, dann Lernpools, max. je 3 kombiniert)
- Jede Karte: `glass` + 3px THW-Blau Top-Border
- Laufend: Fortschrittsbalken THW-Blau, Button "Weiter" → `route('lehrgaenge.practice', $lehrgang->slug)`
- Abgeschlossen: Fortschrittsbalken Grün, "Fertig"-Badge statt Button
- Falls `$enrolledLehrgaenge->isEmpty() && $enrolledLernpools->isEmpty()`: Leerer Zustand mit "Lehrgänge entdecken" CTA → `route('lehrgaenge.index')`

---

### 10. Leaderboard-Modal

Bleibt funktional identisch. Erscheint nur wenn `!$user->leaderboard_banner_dismissed && !$user->leaderboard_consent`.

---

## Responsive Verhalten

| Breakpoint | Verhalten |
|-----------|-----------|
| `lg` (≥1024px) | Sidebar fixiert, 2-spaltige Stats-Grid, Heatmap 2/3 + Spaced Rep 1/3 nebeneinander, 3-spaltige Lehrgänge |
| `md` (768–1023px) | Sidebar eingeklappt (bestehende Mobile-Nav), Stats-Grid 1-spaltig, Heatmap 2/3 + Spaced Rep 1/3 nebeneinander, Lehrgänge 2-spaltig |
| `sm` (<768px) | Alles 1-spaltig, Heatmap + Spaced Rep gestapelt (Heatmap zuerst, Spaced Rep darunter), Lehrgänge 1-spaltig |

---

## Dark/Light Mode

Alle Elemente nutzen die bestehenden CSS-Variablen (`--text-primary`, `--glass-bg`, `--glass-border` etc.). Keine hardcodierten Farben außer semantische States (Grün/Gelb/Rot).

THW-Blau-Borders und Akzente verwenden `var(--thw-blue)` / `var(--thw-blue-light)`.

Im Light Mode: Fortschrittsbalken und Borders sichtbarer da `--glass-border` bereits auf `rgba(0, 51, 127, 0.18)` gesetzt.

---

## Was sich NICHT ändert

- Alle Routen und Controller-Logik
- `$canStartExam` Prüflogik (Alle Fragen + keine Fehler)
- Leaderboard-Modal Logik
- Streak-Freeze Mechanismus
- Spaced-Repetition Logik
- Live-Lernsession Banner Logik
- Bestehende CSS-Klassen in `app.css`

---

## Dateien die geändert werden

- `resources/views/dashboard.blade.php` – komplette Neuerstellung
- Keine Controller-Änderungen
- Keine neuen CSS-Dateien (nur `@push('styles')` im View)
