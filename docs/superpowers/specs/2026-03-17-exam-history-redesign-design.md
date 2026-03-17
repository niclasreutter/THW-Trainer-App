# Exam-History Redesign — Design Spec

**Datum:** 2026-03-17
**Status:** Approved
**Mockup:** `.superpowers/brainstorm/4224-1773731135/exam-history-redesign.html`

## Ziel

Die `/exam-history`-Seite visuell an das Design von `/dashboard`, `/bookmarks` und `/practice-menu` anpassen. Gleiche Patterns, gleiche Komponenten, gleiche Optik. Dark & Light Mode. Mobile-First.

## Änderungen im Überblick

### Was sich ändert (rein visuell)

| Alt (aktuell) | Neu |
|---|---|
| `dashboard-container` (max-width 1200px) | `dash-container` (max-width 600px, wie andere Seiten) |
| `page-title` mit Gold-Gradient `<span>` | Blauer Gradient-Titel mit `Barlow Condensed`, Uppercase-Label darüber |
| `stat-pill` mit Icons | `gami-pill` (farbcodiert, ohne Icons, wie Practice-Menu) |
| Bento Grid (3-Spalten, asymmetrisch) | Gestapeltes Layout (`space-y-4`) — alle Karten volle Breite |
| `glass-gold`, `glass-tl`, `glass-br` Karten | Einheitliche `glass p-4` Karten mit `border-radius:0.75rem` |
| Alte Section-Analyse mit `section-grid` | Section-Items wie Practice-Menu (`section-num`, `section-bar`, `section-pct`) |
| Keine Empfehlung als CTA | Smart-Action Card mit schwächstem Sachgebiet als CTA |
| Trend-Chart nur bestanden/durchgefallen | Trend-Chart mit %-Werten (Verbesserungs-Trend) |
| Statische Prüfungsliste | Prüfungsliste mit Filter-Pills (Alle/Bestanden/Durchgefallen) |
| Keine Stagger-Animation | `dash-rise` Animation wie andere Seiten |
| Fonts: System default | Fonts: `Barlow Condensed` + `IBM Plex Mono` |

### Was gleich bleibt (Inhalt/Logik)

- Alle Daten aus `ExamController@history` bleiben unverändert
- Statistik-Berechnung (Schnitt, Bestehensquote, etc.)
- Sachgebiet-Analyse Daten
- Empfehlungen (weakSections)
- Prüfungsliste
- Vergleich mit globalem Durchschnitt
- Empty State bei 0 Prüfungen

### Neues Feature: Verbesserungs-Trend

Der Trend-Chart zeigt jetzt die **prozentuale Punktzahl** pro Prüfung statt nur bestanden/durchgefallen. Dadurch sieht man Verbesserung auch bei aufeinanderfolgenden Fehlversuchen (z.B. 40% → 55% → 65%).

- Balken-Höhe proportional zur Prozentzahl
- Farbe weiterhin grün (bestanden) / rot (durchgefallen)
- %-Wert über jedem Balken
- Datum unter jedem Balken
- 80%-Schwelle als gestrichelte Linie

### Neues Feature: Filter-Pills

Die Prüfungsliste bekommt Filter-Pills:
- **Alle** — Standardansicht
- **Bestanden** — Nur bestandene Prüfungen
- **Durchgefallen** — Nur durchgefallene Prüfungen

Filterung client-seitig via Alpine.js (keine Server-Requests).

### Neues Feature: Smart-Action Card

Wenn `weakSections` vorhanden: Smart-Action Card (blauer THW-Gradient) mit dem schwächsten Sachgebiet als CTA, Link zu `practice.section`.

## Seitenstruktur (von oben nach unten)

```
1. Header
   - Label: "Auswertung" (uppercase, muted)
   - Titel: "Prüfungshistorie" (Barlow Condensed, blauer Gradient)
   - Subtitle: "Analysiere deine Ergebnisse und finde Schwachstellen"

2. Gami-Pills (flex, wrap)
   - Prüfungen (blau) | Bestanden (grün) | Schnitt (gold) | Bestes (lila)

3. Smart-Action Card (nur wenn weakSections vorhanden)
   - Label: "Empfehlung"
   - Titel: "Lernabschnitt X wiederholen"
   - Desc: "Dein schwächster Bereich..."
   - Button: "Jetzt üben →"

4. Trend-Chart (glass card)
   - Label: "Verbesserungs-Trend"
   - Balkendiagramm mit %-Werten
   - 80%-Schwellenlinie
   - Vergleich: Dein Schnitt vs. Alle Nutzer

5. Sachgebiet-Analyse (glass card)
   - Label: "Stärken & Schwächen"
   - Section-Items (wie Practice-Menu)
   - Farbcodiert: grün ≥80%, blau 50-79%, rot <50%

6. Empfehlungen (glass card, nur wenn weakSections)
   - Label: "Empfehlung"
   - Klickbare rec-items mit Chevron

7. Prüfungsliste (glass card)
   - Label: "Alle Prüfungen"
   - Filter-Pills: Alle | Bestanden | Durchgefallen
   - Exam-Items mit Badge, Score, Datum, Status-Pill

8. Empty State (bei 0 Prüfungen)
   - Glass card, zentriert
   - Icon, Titel, Beschreibung, CTA-Button
```

## CSS-Strategie

- **Keine neuen globalen CSS-Klassen** — Wiederverwendung von `gami-pill`, `smart-action`, `section-item`, `section-num`, `section-bar`, `section-pct`, `dash-container`, `dash-rise`
- **Seiten-spezifische Styles** in `@push('styles')` innerhalb der Blade-View (wie bei allen anderen Seiten)
- **Neue Klassen nur für diese Seite:** Filter-Pills (`eh-filter-pill`), Exam-Items (`eh-exam-item`), Trend-Chart (`eh-trend-*`), Recommendations (`eh-rec-*`)
- Prefix `eh-` für exam-history-spezifische Klassen

## Light-Mode

Alle Komponenten nutzen die bestehenden Light-Mode-Variablen. Seiten-spezifische Styles brauchen `html.light-mode` Overrides für:
- `eh-filter-pill.active` Hintergrund/Farbe
- `eh-exam-item` Hover/Border
- `eh-trend-bar` (Farben bleiben gleich, nur Border/Hover anpassen)
- `eh-rec-item` Background/Border

## Responsive (Mobile-First)

- Container: `dash-container` (max-width 600px, padding 1rem)
- Alle Karten volle Breite — kein Grid nötig
- `gami-pills` wrappen automatisch
- Trend-Chart: Kleinere Balken-Mindestbreite auf <480px
- Filter-Pills: Horizontal scrollbar bei Bedarf

## Betroffene Dateien

| Datei | Änderung |
|---|---|
| `resources/views/exam-history.blade.php` | Komplett neu (View-Template) |
| `app/Http/Controllers/ExamController.php` | Keine Änderung — Daten bleiben gleich |
| `routes/web.php` | Keine Änderung |

## Nicht im Scope

- Controller-Logik Änderungen
- Datenbank-Änderungen
- Neue Routes
- JavaScript-Frameworks (nur Alpine.js für Filter)
