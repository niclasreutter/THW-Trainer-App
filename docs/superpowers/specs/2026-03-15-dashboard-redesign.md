# Dashboard & Statistik-Seite Redesign

**Datum:** 2026-03-15
**Scope:** Dashboard (`/dashboard`) + neue Statistik-Seite (`/statistics`)
**Stack:** Laravel 12 + Blade + Tailwind CSS + Alpine.js

---

## Zusammenfassung

Komplettes Redesign des THW-Trainer Dashboards mit neuer Statistik-Seite. Mobile-First, Dark/Light Mode, THW-Blau als Primärfarbe, modulare Sektionen mit klarer Nutzerführung durch Journey-Stepper und kontextabhängige Smart Action Card.

## Entscheidungen

| Thema | Entscheidung |
|-------|-------------|
| Features | Alle behalten, tiefere Statistiken auf eigene Seite auslagern |
| Nutzerführung | Journey-Stepper + Smart Action Card kombiniert |
| Dark/Light Mode | System-Präferenz als Default + manueller Toggle |
| Farbakzente | THW-Blau solid als Marke, Gold nur für Streak/Belohnungen |
| Light Mode Stil | Glassmorphism (iOS-Style) |
| Responsive | Mobile-First, Desktop muss ebenfalls stark aussehen |
| Layout | Modulare Sektionen — Hero Action → Journey → Gamification → Aktivität → Lehrgänge |

---

## Dashboard-Struktur

### Mobile (1 Spalte, top-down)

1. **Header** — Zeitabhängige Begrüßung ("Guten Morgen/Tag/Abend"), Name, Level + XP inline
2. **Smart Action Card** — Blauer Hero-Gradient, kontextabhängige Empfehlung (siehe Smart Action Logik)
3. **Journey-Stepper** — Horizontal: Fragen lernen (%) → Alle meistern (%) → Prüfung (gesperrt/bereit/bestanden)
4. **Gamification-Row** — 3 Pill-Cards: Streak (Gold-Akzent), Gelöst, Ranking
5. **Wochenaktivität** — Mini-Barchart der letzten 7 Tage
6. **Lehrgänge** — Kompakte Karten der eingeschriebenen Kurse mit Fortschritt
7. **Quick Links** — Üben, Lehrgänge, Shop (entfällt auf Desktop, da Navigation vorhanden)

### Desktop (2-3 Spalten)

- **Links (breit):** Smart Action + Wochenaktivität + Lehrgänge
- **Rechts (schmal):** Journey-Stepper (vertikal), Gamification, Prüfungscountdown

---

## Smart Action Card — Kontextlogik

Die Card zeigt immer genau eine Aktion, priorisiert nach:

| Priorität | Bedingung | Label | Anzeige | Button / Route |
|-----------|-----------|-------|---------|----------------|
| 1 | Aktive Lernsession vorhanden | "Live" | "Aktive Session läuft" | "Beitreten" → `route('lernsession.live', $session)` |
| 2 | `exam_failed_questions` nicht leer | "Dringend" | "X Fehlerfragen wiederholen" | "Wiederholen" → `route('failed.index')` |
| 3 | `next_review_at <= now` (fällige Reviews) | "Empfohlen" | "X Fragen zur Wiederholung fällig" | "Wiederholen" → `route('practice.spaced-repetition')` |
| 4 | Alle `consecutive_correct >= 3` | "Bereit" | "Alle Fragen gemeistert — Prüfung ablegen!" | "Prüfung starten" → `route('exam.index')` |
| 5 | Fortschritt vorhanden, nicht fertig | "Weitermachen" | "Weiter mit Lernabschnitt X" (der mit meistem Fortschritt, noch nicht fertig) | "Starten" → `route('practice.section', $section)` |
| 6 | Noch nie geübt | "Los geht's" | "Starte mit deiner ersten Frage" | "Erste Frage" → `route('practice.all')` |

**Zusatz:** Falls `exam_date` gesetzt und in der Zukunft, zeigt die Card zusätzlich klein: "Noch X Tage bis zur Prüfung · Y Fragen/Tag empfohlen"

**Styling:**
- Blauer Gradient-Hintergrund: `linear-gradient(135deg, #00337F, #0055cc)` mit radialem Lichteffekt
- Priorität 2 (Fehlerfragen): leicht rötlicher Akzent als Warnung
- Weißer Text, Action-Button mit `rgba(255,255,255,0.2)` Background
- Pfeil-Icon rechts als visueller Indikator

---

## Journey-Stepper

Zeigt den Lernweg in 3 Schritten:

| Schritt | Label | Fortschrittsanzeige | Status-Logik |
|---------|-------|---------------------|--------------|
| 1 | Fragen lernen | Prozent der gelösten Fragen | Aktiv wenn `progress < total` |
| 2 | Alle meistern | Prozent der gemeisterten Fragen (`consecutive_correct >= 3`) | Aktiv wenn Schritt 1 begonnen, alle Fragen mindestens einmal beantwortet |
| 3 | Prüfung | bestanden/nicht bestanden/gesperrt | "Gesperrt" wenn nicht alle gemeistert, "Bereit" wenn alle gemeistert, "Bestanden" wenn `exams >= 5` |

**Mobile:** Horizontal, Kreise mit Verbindungslinien, Labels darunter
**Desktop (Sidebar):** Vertikal, mehr Platz für Details pro Schritt

---

## Dark/Light Mode & Farbsystem

### Dark Mode (Standard)

| Element | Wert |
|---------|------|
| Hintergrund Base | `#0a0a0b` |
| Hintergrund Elevated | `#121214` |
| Hintergrund Surface | `#1a1a1d` |
| Cards | Glassmorphism: `backdrop-filter: blur(12px)`, Border `rgba(255,255,255,0.08-0.10)` |
| Text Primary | `#fff` |
| Text Secondary | `rgba(255,255,255,0.5-0.7)` |
| Primärfarbe (Flächen) | `#00337F` |
| Primärfarbe (Text/Icons) | `#5b9aff` |
| Gold-Akzent | `#fbbf24` — nur Streak und Belohnungen |

### Light Mode (Glassmorphism, iOS-Style)

| Element | Wert |
|---------|------|
| Hintergrund Base | `#f0f2f5` |
| Hintergrund Elevated | `#fff` |
| Cards | `rgba(255,255,255,0.7)`, `backdrop-filter: blur(12px)`, leichte blaue Schatten |
| Text Primary | `#111` |
| Text Secondary | `#666` |
| Primärfarbe (Flächen) | `#00337F` (unverändert) |
| Primärfarbe (Text/Icons) | `#00337F` |
| Gold-Akzent | `#d97706` (dunkler für Kontrast auf Hell) |

### Toggle-Mechanik

- CSS `prefers-color-scheme` als Default
- Toggle-Button im Header (Sonne/Mond-Icon via Bootstrap Icons)
- Präferenz in `localStorage` gespeichert, überschreibt System-Setting
- Klasse `.dark` / `.light` auf `<html>` Element

```
:root          → Light Mode Defaults
.dark          → Dark Mode Overrides
@media (prefers-color-scheme: dark) → Auto-Dark wenn kein manueller Toggle gesetzt
```

---

## Statistik-Seite (`/statistics`)

Neue Route und View. Erreichbar über Link im Dashboard ("Detaillierte Statistiken →") und über die Navigation.

### Aufbau (Mobile-First, gleiche Design-Sprache)

#### 1. Übersichts-Header
- Gesamtfortschritt als großer Prozentwert
- Gesamtanzahl gelöste Fragen / Total
- Durchschnittliche Trefferquote

#### 2. Sektions-Analyse (Hauptbereich)
- Alle 10 Lernabschnitte als Cards
- Pro Abschnitt: Fortschrittsbalken, Anzahl gemeistert/total, Trefferquote
- Farbkodierung: Grün (>80%), Blau (50-80%), Rot (<50%)
- Stärken/Schwächen auf einen Blick erkennbar
- Klickbar → `route('practice.section', $section)`

#### 3. Aktivitätsverlauf
- Wochenansicht: Barchart (wie im Dashboard, aber größer und detaillierter)
- Monatsansicht: Heatmap-Kalender (Tage mit Aktivität eingefärbt nach Intensität)
- Toggle zwischen Wochen/Monat (Alpine.js)

#### 4. Prüfungshistorie
- Liste aller Prüfungen: Datum, Ergebnis (%), bestanden/nicht bestanden Badge
- Trend-Linie: Verbesserung über Zeit sichtbar

#### 5. Spaced Repetition Stats
- Fällige Fragen (heute/diese Woche)
- Verteilung der Review-Intervalle
- Meisterungsgrad insgesamt (% der Fragen mit `consecutive_correct >= 3`)

### Desktop-Layout
2-Spalten — Sektions-Analyse links (groß), Aktivität + Prüfungen + SR rechts

---

## Responsive Verhalten

| Breakpoint | Dashboard | Statistik-Seite |
|-----------|-----------|-----------------|
| `sm` (<640px) | 1 Spalte, alles gestapelt, Journey horizontal | 1 Spalte, alles gestapelt |
| `md` (640-1023px) | 1 Spalte, Gamification-Row nebeneinander | 1 Spalte, Sektions-Cards 2-spaltig |
| `lg` (≥1024px) | 2 Spalten (Main + Sidebar), Journey vertikal in Sidebar | 2 Spalten (Sektionen + Sidebar) |

---

## Konditionelle Elemente (bestehend, werden integriert)

Diese Elemente aus dem aktuellen Dashboard bleiben erhalten und werden ins neue Layout integriert:

- **Active Session Banner** — wird in Smart Action Card Priorität 1
- **Streak-at-Risk Warnung** — wird Teil der Smart Action Card oder als kleine Badge in der Gamification-Row
- **Prüfungscountdown** — wird als Zusatzinfo in der Smart Action Card angezeigt (wenn `exam_date` gesetzt)
- **Ausbilder-Karte** — bleibt als eigene Sektion nach Lehrgängen (nur für Ausbildungsbeauftragte)
- **Leaderboard-Modal** — bleibt als Overlay, unverändert

---

## Technische Anmerkungen

- **Bestehender Stack:** Laravel 12 + Blade + Tailwind CSS + Alpine.js
- **CSS:** Erweitern der bestehenden `app.css` um Light-Mode-Variablen und neue Dashboard-spezifische Klassen
- **Alpine.js:** Für Dark/Light Toggle, Journey-Stepper-Interaktion, Wochenansicht-Toggle auf Statistik-Seite
- **Dashboard-Route:** Inline Route-Closure in `routes/web.php` erweitern um Smart Action Logik
- **Neue Route:** `GET /statistics` mit eigenem Controller oder Route-Closure
- **Bestehende Modelle:** Kein Schema-Änderung nötig, alle Daten sind bereits vorhanden
- **Bestehende Komponenten wiederverwenden:** `active-session-banner`, `skeleton-loader`, `milestone-celebration`, `achievement-popup`

## Dateien die geändert werden

- `resources/views/dashboard.blade.php` — komplette Neuerstellung
- `resources/css/app.css` — Light Mode Variablen, neue Dashboard-Klassen
- `routes/web.php` — Smart Action Logik in Dashboard-Route, neue `/statistics` Route
- **Neu:** `resources/views/statistics.blade.php`
- **Neu:** `app/Http/Controllers/StatisticsController.php` (optional, kann auch Route-Closure sein)

## Was sich NICHT ändert

- Alle bestehenden Routen und deren Logik
- `$canStartExam` Prüflogik (Alle Fragen gemeistert + keine Fehler)
- Leaderboard-Modal Logik
- Streak-Freeze Mechanismus
- Spaced-Repetition Logik (SM-2 Algorithmus)
- Gamification-Service
- Bestehende Policies und Authorization
- Datenbank-Schema
