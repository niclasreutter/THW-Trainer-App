# Practice-Menu Redesign — Design Spec

## Ziel

Die `/practice-menu` Seite optisch an das Dashboard-Design anpassen. Mobile-first, responsive, Dark/Light Mode.

## Design-Entscheidungen

| Entscheidung | Ergebnis |
|---|---|
| Layout | Single Column, stacked cards (wie Dashboard mobile) |
| Header | Subtile Uppercase-Zeile "THEORIE LERNEN" in THW-Blau, IBM Plex Mono |
| Button-Stil (Smart Action) | Blau-Gradient (`#5b9aff` → `#0055cc`), weißer Text |
| Keine Gold-Buttons | Gold-Buttons werden nicht als globale Action-Buttons verwendet |
| Stagger-Animation | `dash-rise` Animation (definiert in View-Styles, nicht global) |

## Seitenstruktur (top → bottom)

### 1. Page Label
- Subtile Uppercase-Zeile: "THEORIE LERNEN"
- Stil: `font-size: 0.625rem`, `text-transform: uppercase`, `letter-spacing: 0.14em`
- Farbe: Custom Property `--pm-blue` (Dark: `#5b9aff`, Light: `#00337F`) — definiert in View-Styles
- Font: `IBM Plex Mono`

### 2. Stat Pills
- Horizontal scrollbar auf Mobile
- 4 Pills: Fehlgeschlagen (rot), Ungelöst (amber), Gemeistert (grün), Gesamt (primary)
- Werte: `Barlow Condensed`, 800 weight
- Nutzt bestehende `.stat-pill` Klassen aus `app.css`

### 3. Smart Action Card
- THW-Blau Gradient Background (`#00337F` → `#004db3`)
- Subtiler Radial-Gradient Glow oben rechts (blau, nicht gold)
- Kontextabhängig (Priorität):
  1. Fehlgeschlagene Fragen vorhanden → "X Fehler wiederholen" → `route('failed.index')`
  2. Ungelöste vorhanden → "X ungelöste Fragen lernen" → `route('practice.unsolved')`
  3. Alle gelöst → "Alle Fragen wiederholen" → `route('practice.all')`
- Label: IBM Plex Mono, Uppercase, "EMPFOHLEN"
- Button: Blau-Gradient (`#5b9aff` → `#0055cc`), weiß, mit Arrow-Icon
- Hover: `translateY(-2px)`, Box-Shadow

### 4. Training Mode Tiles
- 3-Column Grid, `min-width` pro Tile sicherstellen
- Auf sehr schmalen Screens (<340px): 2+1 Umbruch erlaubt via `flex-wrap`
- Tiles mit Routes:
  - Alle Fragen → `route('practice.all')`
  - Ungelöste → `route('practice.unsolved')`
  - Fehler → `route('failed.index')`
- Große Zahl oben (`Barlow Condensed`, 1.5rem, 800)
- Label: IBM Plex Mono, Uppercase
- "Starten →" Link in Blau

### 5. Split Row: Spaced Repetition + Fragensuche
- 2-Column Grid (1-Column unter 400px)
- Links: SR Nudge (glass card, Badge mit Anzahl, Titel + Desc) → `route('practice.spaced-repetition')`
- Rechts: Fragensuche (glass card, Section Label + Input)
  - Form mit GET zu `route('practice.search')` (bestehendes Verhalten)
- SR Nudge nur sichtbar wenn `$spacedRepetitionDue > 0`
- Falls SR nicht sichtbar: Fragensuche volle Breite

### 6. Lernabschnitte
- Glass card mit Section Label "LERNABSCHNITTE"
- Rechts: "10 Abschnitte" als Zähler
- 10 Abschnitt-Items als kompakte Liste:
  - Farbcodierte Nummer (grün ≥80%, blau 50-79%, rot <50%)
  - Name (truncated mit ellipsis)
  - Progress-Bar (3px, farbcodiert wie Nummer)
  - Prozent-Wert rechts (berechnet: `round(($solved / $total) * 100)`)
  - Chevron-Arrow
- Hover: subtiler Background-Change, Arrow wird blau
- Klick führt zu `route('practice.section', $i)`

## Controller-Änderungen (`PracticeController@menu()`)

Die `menu()` Methode muss erweitert werden:

### Neue Variablen:
```php
// Smart Action Logik
$smartAction = match(true) {
    $failedCount > 0 => [
        'label' => 'Empfohlen',
        'title' => "$failedCount Fehler wiederholen",
        'desc'  => 'Priorisiere fehlgeschlagene Fragen zuerst',
        'route' => route('failed.index'),
    ],
    $unsolvedCount > 0 => [
        'label' => 'Weiterlernen',
        'title' => "$unsolvedCount ungelöste Fragen",
        'desc'  => 'Lerne neue Fragen und erweitere dein Wissen',
        'route' => route('practice.unsolved'),
    ],
    default => [
        'label' => 'Wiederholen',
        'title' => 'Alle Fragen wiederholen',
        'desc'  => 'Festige dein Wissen durch Wiederholung',
        'route' => route('practice.all'),
    ],
};

// Spaced Repetition (aus SpacedRepetitionService holen, nicht inline in View)
$spacedRepetitionDue = app(\App\Services\SpacedRepetitionService::class)
    ->getDueQuestions($user)->count();
```

### Bestehende Variablen (korrekte Namen):
- `$sectionStats` — Array [1..10], je `['total' => int, 'solved' => int]`
- `$sectionNames` — Array [1..10] mit Section-Namen
- `$totalQuestions`, `$solvedCount`, `$failedCount`, `$unsolvedCount`
- `$progressPercentage`

## Content-Aufteilung practice-menu vs. statistics

### Entfernt aus practice-menu (→ bleibt in statistics):
- Detaillierte Stats-Grid (failed/unsolved/solved Cards mit Progress-Bars)
- Spaced Repetition Schedule-Liste
- Hit-Rate Anzeigen

### Bleibt in practice-menu:
- Stat Pills (kompakt)
- Smart Action Card
- Training Modi (alle/ungelöst/fehler)
- Spaced Repetition Nudge (nur Anzahl + Link)
- Fragensuche
- Lernabschnitte mit Progress

## Responsive Verhalten

- **Mobile (<400px)**: Split-Row stackt vertikal, Stat-Pills horizontal scrollbar
- **Tablet+Desktop**: Layout identisch, max-width ~680px zentriert (bewusst schmaler als Standard 1100px, passend zum Single-Column-Stacked-Design)
- Kein 2-Column Sidebar-Layout — einheitlich gestackt

## Dark/Light Mode

Nutzt bestehende CSS-Variablen aus `app.css` plus view-scoped Overrides:
- `--glass-bg`, `--glass-border` für Card-Backgrounds (global)
- `--text-primary`, `--text-muted` für Text (global)
- `--pm-blue`: `#5b9aff` (dark) / `#00337F` (light) — view-scoped Custom Property
- Smart Action Card: Farben bleiben in beiden Modi gleich (THW-Blau)

## Font-Loading

View benötigt denselben Font-Link wie Dashboard:
```html
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css2?family=Barlow+Condensed:wght@600;700;800&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
```

## Animations

- `dash-rise` Keyframes werden in `@push('styles')` der View definiert (nicht global)
- Stagger auf allen direkten Kindern von `.dash-container`
- Delays: 0.03s Inkrement pro Kind
- Progress-Bar Fill: `transition: width 0.6s ease-out`
- `@media (prefers-reduced-motion: reduce)` — Animationen deaktivieren

## Technische Umsetzung

- **View:** `resources/views/practice-menu.blade.php` (komplett ersetzen)
- **Controller:** `PracticeController@menu()` — Smart Action + SR-Daten ergänzen
- **CSS:** Styles in `@push('styles')` (wie Dashboard), nutzt globale Variablen + view-scoped Properties
- **Routes:** Bestehende Routes verwenden: `practice.all`, `practice.unsolved`, `failed.index`, `practice.section`, `practice.spaced-repetition`, `practice.search`
