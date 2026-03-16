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
| Stagger-Animation | `dash-rise` Animation wie Dashboard |

## Seitenstruktur (top → bottom)

### 1. Page Label
- Subtile Uppercase-Zeile: "THEORIE LERNEN"
- Stil: `font-size: 0.625rem`, `text-transform: uppercase`, `letter-spacing: 0.14em`
- Farbe: `--blue-accent` (Dark: `#5b9aff`, Light: `#00337F`)
- Font: `IBM Plex Mono`

### 2. Stat Pills
- Horizontal scrollbar auf Mobile
- 4 Pills: Fehlgeschlagen (rot), Ungelöst (amber), Gemeistert (grün), Gesamt (primary)
- Werte: `Barlow Condensed`, 800 weight
- Identisch zu bestehenden `.stat-pill` Klassen im CSS

### 3. Smart Action Card
- THW-Blau Gradient Background (`#00337F` → `#004db3`)
- Subtiler Radial-Gradient Glow oben rechts (blau, nicht gold)
- Kontextabhängig (gleiche Logik wie bisher):
  - Fehlgeschlagene Fragen vorhanden → "X Fehler wiederholen"
  - Ungelöste vorhanden → "X ungelöste Fragen lernen"
  - Alle gelöst → "Alle Fragen wiederholen"
- Label: IBM Plex Mono, Uppercase, "EMPFOHLEN"
- Button: Blau-Gradient (`#5b9aff` → `#0055cc`), weiß, mit Arrow-Icon
- Hover: `translateY(-2px)`, Box-Shadow

### 4. Training Mode Tiles
- 3-Column Grid (auch auf Mobile)
- Tiles: Alle Fragen, Ungelöste, Fehler
- Große Zahl oben (`Barlow Condensed`, 1.5rem, 800)
- Label: IBM Plex Mono, Uppercase
- "Starten →" Link in Blau

### 5. Split Row: Spaced Repetition + Fragensuche
- 2-Column Grid (1-Column unter 400px)
- Links: SR Nudge (glass card, Badge mit Anzahl, Titel + Desc)
- Rechts: Fragensuche (glass card, Section Label + Input)
- SR Nudge nur sichtbar wenn `$spacedRepetitionDue > 0`
- Falls SR nicht sichtbar: Fragensuche volle Breite

### 6. Lernabschnitte
- Glass card mit Section Label "LERNABSCHNITTE"
- Rechts: "10 Abschnitte" als Zähler
- 10 Abschnitt-Items als kompakte Liste:
  - Farbcodierte Nummer (grün ≥80%, blau 50-79%, rot <50%)
  - Name (truncated mit ellipsis)
  - Progress-Bar (3px, farbcodiert wie Nummer)
  - Prozent-Wert rechts
  - Chevron-Arrow
- Hover: subtiler Background-Change, Arrow wird blau
- Klick führt zu `practice.section(N)`

## Datenquellen

Alle Daten kommen bereits aus `PracticeController@menu()`:
- `$sections` — Array mit Section-Stats (name, total, solved, percentage)
- `$totalQuestions`, `$solvedQuestions`, `$failedQuestions`, `$unsolvedQuestions`
- `$spacedRepetitionDue` — Anzahl fälliger Reviews

Die Smart Action Logik muss im Controller ergänzt werden (Priorität: failed → unsolved → all).

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
- **Tablet+Desktop**: Layout identisch, max-width ~680px zentriert
- Kein 2-Column Sidebar-Layout — einheitlich gestackt

## Dark/Light Mode

Nutzt bestehende CSS-Variablen aus `app.css`. Schlüssel-Mappings:
- `--glass-bg`, `--glass-border` für Card-Backgrounds
- `--text-primary`, `--text-muted` für Text
- `--blue-accent`: `#5b9aff` (dark) / `#00337F` (light)
- Smart Action Card: Farben bleiben in beiden Modi gleich (THW-Blau)

## Animations

- Stagger-Animation `dash-rise` auf allen direkten Kindern von `.dash-container`
- Delays: 0.03s Inkrement pro Kind
- Progress-Bar Fill: `transition: width 0.6s ease-out`

## Technische Umsetzung

- View: `resources/views/practice-menu.blade.php` (komplett ersetzen)
- Controller: `PracticeController@menu()` — Smart Action Logik ergänzen
- CSS: Styles inline in `@push('styles')` (wie Dashboard), nutzt globale Variablen
- Keine neuen CSS-Klassen in `app.css` nötig — alles scoped in der View
