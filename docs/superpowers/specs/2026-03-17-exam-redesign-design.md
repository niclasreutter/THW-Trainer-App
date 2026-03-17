# Exam Page Redesign — Design Spec

> Redesign der /exam Seite (aktive Prüfung + Ergebnis) passend zum Design-System von Dashboard, Practice, Bookmarks und Exam-History.

## Kontext

Die aktuelle `/exam` Seite verwendet ein eigenständiges Layout mit eigenen CSS-Klassen, das optisch nicht zum restlichen Design-System (dash-container, Glass Cards, Stat-Pills, THW-Farben) passt. Ziel ist ein Redesign, das die Seite visuell in die bestehende App integriert.

**Betroffene Dateien:**
- `resources/views/exam.blade.php` (Hauptview — aktive Prüfung + Ergebnis)
- `resources/views/exam_result.blade.php` (Legacy — kann entfernt werden falls nicht mehr referenziert)
- `app/Http/Controllers/ExamController.php` (Controller — minimale Änderungen)
- `resources/css/app.css` (neue CSS-Klassen)

**Referenz-Designs:**
- `practice.blade.php` — Topbar, Fullscreen Mobile, Fixed Bottom Bar
- `dashboard.blade.php` — dash-container, Header-Pattern, Stat-Pills
- `exam-history.blade.php` — gami-pill, Glass Cards, Schwachstellen-Analyse

## Design-Entscheidungen

### 1. Aktive Prüfung — Mobile (Fullscreen)

**Topbar:**
- Close-Button (✕) links
- Zentriert: "PRÜFUNG" Label (monospace, uppercase) + "Frage **12** / 40" (Frage-Nummer in THW-Blau Gradient `#00337F → #3b82f6`)
- Timer rechts in Gold (`#fbbf24`) Monospace, wechselt zu Rot (`#ef4444`) bei <10 Min, Puls-Animation bei <5 Min

**Progress-Bar:**
- 3px Höhe direkt unter Topbar
- THW-Blau Gradient (`#00337F → #3b82f6`)
- Fortschritt basiert auf aktueller Frage / 40

**Question Card:**
- Asymmetrische Ecken: `border-radius: 1rem 0.3rem 1rem 1rem` (wie Practice)
- Blaue Top-Line (2px, Gradient)
- LA-Label in THW-Blau, Monospace, Uppercase
- Fragentext darunter

**Antwort-Optionen:**
- Checkbox-Style (eckig) für Multiple-Choice, Kreis für Single-Choice
- Selektiert: THW-Blau Hintergrund + Border + Checkmark
- Nicht selektiert: subtiler Glass-Hintergrund

**Fixed Bottom Bar:**
- Sliding Bubbles (9 sichtbar) + 3 Action-Buttons

**Sliding Bubbles Verhalten:**
- Immer genau 9 Bubbles sichtbar
- Aktuelle Frage ist das Ziel für die Mitte (Position 5)
- **Fragen 1–4:** Leiste zeigt 1–9, aktiver Punkt wandert von Position 1 nach 4
- **Fragen 5–36:** Punkt bleibt in der Mitte (Position 5), Leiste scrollt mit
- **Fragen 37–40:** Leiste fixiert auf 32–40, Punkt wandert von Position 6 nach 9
- Bubble-Farben: Grün (#22c55e) = beantwortet, Gold (#fbbf24) = markiert, Blau mit Glow = aktiv, Grau = offen
- Jede Bubble ist klickbar zum Springen
- Sanfte CSS-Transition beim Wechsel

**Action-Buttons (3 gleichbreit im Fixed Bar):**
- "Zurück" — Ghost-Style
- "Markieren" — Gold-Outline
- "Weiter" — Custom Blau-Gradient (nicht `btn-primary`, da dieses Gold ist im Design-System). Eigene Klasse `.exam-btn-next`
- Bei **Frage 40**: "Weiter" wird zu "Abgeben" (Grün-Gradient) mit Bestätigungs-Dialog vor Submit

**Markieren-Feature:**
- Rein client-seitig (Alpine/JS State), nicht persistiert
- Wird im `<form>` nicht mitgesendet — dient nur der Navigation während der Prüfung
- Geht bei Seiten-Reload verloren (akzeptabel für 30-Min Prüfung)

**Mobile Fullscreen:**
- `100dvh`, Nav/Footer/Header hidden
- Safe-area-inset für Notch-Geräte

### 2. Aktive Prüfung — Desktop (dash-container)

**Layout:**
- `dash-container` mit sichtbarer Navbar
- Dashboard-Header: "PRÜFUNGSSIMULATION" Greeting + "Frage **12** / 40" Titel (Barlow Condensed) + Subtitle

**Stat-Pills Row:**
- Restzeit (Rot), Beantwortet (Grün), Offen (Grau), Markiert (Gold)
- Format: `gami-pill` Pattern wie Dashboard/Exam-History

**Progress-Bar:**
- Unter Stat-Pills, gleicher Blau-Gradient

**Question Card + Answers:**
- Gleich wie Mobile, nur größer (mehr Padding, größere Schrift)

**Sliding Bubbles + Buttons:**
- Unter den Antworten, inline (nicht fixed)
- Gleiche 9-Bubble Sliding-Logik
- Aktive Bubble etwas größer (28px vs 24px)

### 3. Ergebnis-Modus — Summary Screen

**Topbar:**
- "Schliessen" links → navigiert zu `/exam-history`
- "ERGEBNIS" Label zentriert

**Ergebnis-Ring:**
- SVG-Ring, 100px
- Bestanden: Grün (#22c55e) + "Bestanden!" Text
- Nicht bestanden: Rot (#ef4444) + "Nicht bestanden" Text
- Prozent groß in der Mitte (Gradient passend zum Status)
- Darunter: "X/40" klein, Monospace

**Stats-Pills:**
- Richtig (Grün) + Falsch (Rot)
- Zentriert unter dem Ring

**Schwachstellen-Analyse:**
- Glass Card mit Fortschrittsbalken pro schwachem LA
- Rot = <50%, Orange = 50-75%
- Zeigt nur LAs mit Schwächen (nicht alle 10)

**Action-Buttons:**
- "Alle Fragen durchgehen" — Blau-Gradient (`.exam-btn-next`), volle Breite
- "Nur falsche Fragen (X)" — Rot-Outline, volle Breite
- Beide führen in den Frage-für-Frage Review-Modus

**XP / Gamification:**
- XP-Anzeige aus bestehender `gamification_result` Session beibehalten
- Kompakt unter dem Ergebnis-Ring anzeigen (z.B. "+50 XP" Badge)
- Bestehende GamificationService-Integration im Controller bleibt unverändert

### 4. Ergebnis-Modus — Frage-für-Frage Review

- Gleiches Layout wie aktive Prüfung
- Topbar zeigt "ERGEBNIS — 85%" statt "PRÜFUNG"
- Kein Timer
- Question Card: Grüne Top-Line + "RICHTIG" Badge für korrekte, Rote Top-Line + "FALSCH" Badge für falsche
- Antworten: Korrekte Antwort grün markiert, falsche Auswahl rot markiert
- Sliding Bubbles: Grün = richtig, Rot = falsch (statt beantwortet/offen)
- Kein "Markieren" Button, nur "Zurück" + "Weiter"
- Bei "Nur falsche Fragen": Bubble-Anzahl = Anzahl falscher Antworten, Sliding nur wenn > 9 falsche
- Bei ≤ 9 falschen: alle Bubbles sichtbar ohne Sliding

### 5. Dark & Light Mode

**Dark Mode** (Standard):
- Backgrounds: `rgba(255,255,255,0.03-0.06)`
- Borders: `rgba(255,255,255,0.06-0.1)`
- Text: `rgba(255,255,255,0.85)` primary, `rgba(255,255,255,0.4)` muted
- Glass-Effekt mit `backdrop-filter: blur(12px)`

**Light Mode** (`html.light-mode`):
- Backgrounds: `rgba(0,51,127,0.03-0.08)`, `rgba(255,255,255,0.7-0.85)`
- Borders: `rgba(0,51,127,0.08-0.12)`, `rgba(0,0,0,0.08)`
- Text: `#1a1a2e` primary, `#666` muted
- Cards: Leichter Box-Shadow statt Glow
- Bubbles: Gleiche Farben, aber Inaktive = `rgba(0,0,0,0.05)`

### 6. Responsive Breakpoints

- **Mobile** (`≤ 640px`): Fullscreen, Topbar, Fixed Bottom Bar
- **Desktop** (`> 640px`): dash-container, Navbar sichtbar, inline Bubbles + Buttons

### 7. Animationen

- `dash-rise` Stagger-Animation für Seitenaufbau (wie Dashboard)
- Bubble-Sliding: `transition: transform 0.3s ease`
- Timer-Warning: Puls-Animation bei <5 Min (bestehend beibehalten)
- Progress-Bar: `transition: width 0.3s ease`
- Ergebnis-Ring: SVG stroke-dashoffset Animation bei Seitenload

## Nicht im Scope

- Controller-Logik (Prüfungsregeln, Session-Management, Scoring) — bleibt unverändert
- Exam-History Seite — bereits redesigned
- Swipe-Gesten — nicht vorgesehen (Bubbles + Buttons reichen)
- Keyboard-Shortcuts — nicht in V1, kann später ergänzt werden

## Technische Hinweise

### Architektur
- **Single-Form, Show/Hide-via-JS Architektur bleibt bestehen.** Alle 40 Fragen werden in einem `<form>` gerendert und per JS ein-/ausgeblendet. Kein AJAX, keine Server-Roundtrips pro Frage.
- Vanilla JavaScript für interaktive Logik (Timer, Bubble-Navigation, Fragen-Wechsel) — konsistent mit bestehendem Exam und Practice Pattern. Kein Alpine.js Migration nötig.
- Fullscreen-Modus via `exam-active-mode` Body-Class (bestehendes Pattern beibehalten). Result-Modus entfernt diese Klasse → Navbar wird wieder sichtbar.

### Timer-Ablauf
- Bei Timer = 0: Automatische Client-seitige Abgabe (Form-Submit), kein Server-Redirect
- Bestätigungs-Dialog entfällt bei Auto-Submit

### CSS
- CSS-Klassen mit `exam-` Prefix (z.B. `.exam-shell`, `.exam-topbar`, `.exam-bubble`)
- Custom `.exam-btn-next` für Blau-Gradient Button (da `btn-primary` = Gold im Design-System)

### Aufräumen
- `exam_result.blade.php` entfernen (Legacy, wird durch integrierten Ergebnis-Modus ersetzt)
- Tote Controller-Methoden prüfen und ggf. entfernen
- Nach Änderung: `npm run build && php artisan view:clear && php artisan cache:clear`
