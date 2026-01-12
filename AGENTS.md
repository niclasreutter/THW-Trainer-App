# THW Trainer App - Agent Dokumentation

## Projektübersicht
**Name:** THW-Trainer (Ausbildungs- und Übungsplattform)  
**Framework:** Laravel 12.34.0 mit PHP 8.3.12  
**Frontend:** Blade Templates mit Tailwind CSS und Vanilla JavaScript  
**Datenbank:** MySQL  
**Status:** In aktiver Entwicklung (Jan 2026)

---

## Architektur & Technologie Stack

### Backend (Laravel)
- **PHP:** 8.3.12
- **Laravel:** 12.34.0
- **Wichtige Services:**
  - `GamificationService`: Punkte, XP, Streaks, Achievements
  - Policy-basierte Authorization (`OrtsverbandLernpoolPolicy`, etc.)
  - Resource Controllers für RESTful APIs

### Frontend
- **CSS:** Tailwind CSS + Custom Inline Styles
- **JavaScript:** Vanilla JS mit Fetch API für AJAX
- **Mobile:** Vollständig responsive, mobile-first Design
- **Spezielle Features:**
  - Modales System mit Event Delegation
  - Cache-Busting durch Timestamps
  - Safe Area Handling für PWA

### Datenbank
- **Authentifizierung:** Laravel's Standard User Model + Policies
- **Ortsverband-System:** Multi-tenant Struktur für THW-Verbände
- **Lernpools:** `ortsverband_lernpools` mit Questions & Progress Tracking
- **Gamification:** Punkte, XP, Achievements, User Streaks

---

## Aktuelle Features

### 1. Lernpool-System (Januar 2026)
**Status:** ✅ ABGESCHLOSSEN

#### Funktionalität:
- Ausbildungsbeauftragte können Lernpools (Frage-Sammlungen) pro Ortsverband anlegen
- Mitglieder können sich zu Lernpools anmelden
- Practice-View für Lernpool zeigt **eine Frage nach der anderen** (nicht alle auf einmal)
- Identisch zur normalen Practice-View, nur für Lernpool-Fragen

#### Tabellen:
- `ortsverband_lernpools`: Name, Description, Ortsverband-ID
- `ortsverband_lernpool_questions`: Frage, 3 Antworten, Lösung, Lernabschnitt, Nummer
- `ortsverband_lernpool_enrollments`: User-Lernpool Zuordnungen
- `ortsverband_lernpool_progress`: Fortschritt pro User/Frage (consecutive_correct, solved flag)

#### Routes:
```
GET  /ortsverband/{id}/lernpools                    → index (CRUD für Ausbilder)
POST /ortsverband/{id}/lernpools                    → store
GET  /ortsverband/{id}/lernpools/{lernpool}         → show (Details)
GET  /ortsverband/{id}/lernpools/{lernpool}/edit    → edit
PUT  /ortsverband/{id}/lernpools/{lernpool}         → update
DELETE /ortsverband/{id}/lernpools/{lernpool}       → destroy

POST /ortsverband/{id}/lernpools/{lernpool}/enroll  → User anmelden
GET  /ortsverband/{id}/lernpools/{lernpool}/practice → Practice-View
POST /ortsverband/{id}/lernpools/{lernpool}/answer  → Antwort verarbeiten
POST /ortsverband/{id}/lernpools/{lernpool}/unenroll → Abmelden
```

#### Controllers:
- `OrtsverbandLernpoolController`: CRUD + Modal-Support (AJAX detection)
- `OrtsverbandLernpoolQuestionController`: Fragen-Management + Auto-Numbering
- `OrtsverbandLernpoolPracticeController`: Practice-Flow mit Session-basiertes Feedback

#### Views:
- `resources/views/ortsverband/lernpools/index.blade.php`: Dashboard mit modalen Popups
- `resources/views/ortsverband/lernpools/practice.blade.php`: **1:1 wie practice.blade.php**
- `resources/views/ortsverband/lernpools/questions/create-modal.blade.php`: Frage-Form mit Button-UI

### 2. Practice-View Eigenschaften
**Status:** ✅ ABGESCHLOSSEN

#### Features:
- **Single Question Mode:** Zeigt eine Frage, dann nächste (nicht alle auf einmal)
- **Antwort-Shuffle:** Antworten A,B,C werden zufällig gemischt, Mapping gespeichert
- **Multi-Select:** Checkboxes für Mehrfach-Antworten (z.B. "A,B")
- **Session-basiertes Feedback:** 
  - `answer_result`: is_correct, user_answer, answer_mapping, question_progress
  - `gamification_result`: points_awarded, reason
- **Farbiges Feedback:**
  - Grün: Richtig (auch nicht ausgewählte richtige Antworten)
  - Rot: Falsch
  - Icons (✓, ✗)
- **Gamification:**
  - Popups mit Emoji-Celebrations
  - Punkte-Vergabe (10 base + 15 Bonus bei Meisterung)
  - XP-Track
  - Streak-Updates
- **"Gemeistert"-System:** 2x hintereinander richtig = Frage gemeistert
- **Mobile-optimiert:** Vollbild auf <640px, Footer/Nav ausgeblendet

### 3. Modal-System (Lernpools)
**Status:** ✅ ABGESCHLOSSEN

#### Implementierung:
- Event Delegation: `.modal-trigger` Links laden Modal-Content per AJAX
- Cache-Busting: `_t=Date.now()` + `cache: 'no-store'` Header
- AJAX Detection: Controller prüft `request()->ajax()`, `X-Requested-With` Header, `query('ajax') === '1'`
- Modal-Styling: Backdrop Blur, Centered, Responsive

#### Modal Views:
- `show-modal.blade.php`: Details anzeigen
- `edit-modal.blade.php`: Bearbeiten
- `questions/index-modal.blade.php`: Fragen-Liste
- `questions/create-modal.blade.php`: Frage erstellen (mit Button-UI für Lösungen!)

---

## Bekannte Besonderheiten & Gotchas

### 1. Lernabschnitt-Naming
- `question.lernabschnitt`: 1-10 (numerisch)
- **Display:** Kombiniert mit `question.nummer` → "1.33" (LA1, Frage 33)
- **In Modals:** Autocomplete-Datalist, optional

### 2. Loesung (Lösung)
- **Format:** Komma-separierter String (z.B. "A,B" oder "C")
- **In Controller:** Wird zu Array konvertiert und sortiert
- **Buttons im Modal:** Checkboxes für A, B, C (nicht Dropdown!)
- **Neu:** Kann mehrere richtige Antworten haben (Multi-Select Support)

### 3. Fragenummer Auto-Fill
- **Logic:** Nächste Nummer = max(aktuelle Nummern in dieser LA) + 1
- **Fallback:** 1 wenn keine Fragen in LA
- **User kann überschreiben:** Feld ist nicht readonly

### 4. Column Naming Issue (GELÖST)
- **Problem:** Code nutzte `ortsverband_lernpool_id` aber DB-Spalte ist `lernpool_id`
- **Lösung:** Policy und alle Queries nutzen jetzt `lernpool_id`
- **Locations:** `OrtsverbandLernpoolPolicy`, `OrtsverbandLernpoolPracticeController`

### 5. Route Naming Issue (GELÖST)
- **Problem:** Route-Namen waren inkonsistent (`practice.answer` vs `answer`)
- **Lösung:** Alle nutzen jetzt volle Namen: `ortsverband.lernpools.practice`, `ortsverband.lernpools.answer`, `ortsverband.lernpools.unenroll`

### 6. Caching Issue (GELÖST)
- **Problem:** Modals zeigten gecachte/alte Inhalte
- **Lösung:** 
  - `cache: 'no-store'` in Fetch
  - `_t=Date.now()` Query-Parameter
  - Blade-Header Cache-Control im Response

### 7. Session Flash Data
- **Important:** Sessions werden nach dem Auslesen NICHT automatisch gelöscht
- **Manuell:** `session()->forget(['answer_result', 'gamification_result'])` nur wenn nötig
- **Practice:** Nutzt automatisch Flash (Blade/Redirect)

---

## Controller-Flow: Frage Beantworten

### Ablauf in `OrtsverbandLernpoolPracticeController@answer`:

1. **Validierung:**
   - Question-ID existiert
   - User ist enrolled
   - Question gehört zu Lernpool

2. **Answer-Processing:**
   - Parse `answer_mapping` (Position → Letter)
   - Konvertiere Positionen zu Buchstaben
   - Normalisiere zu Großbuchstaben und sortiere

3. **Vergleich:**
   - User-Antwort vs. `question.loesung`
   - Beide müssen Strings sein (nach Normalisierung & Sortierung)

4. **Progress-Update:**
   - `consecutive_correct++` wenn richtig
   - `consecutive_correct = 0` wenn falsch
   - `solved = true` wenn `consecutive_correct >= 2`
   - `total_attempts++`
   - `correct_attempts++` wenn richtig

5. **Gamification:**
   - **Richtig:** 10 Punkte + XP
   - **Meistert (2x):** +15 Punkte, +25 XP, Celebration-Popup
   - **Falsch:** +2 XP minimal
   - **Streak-Update** bei richtig

6. **Session Flash:**
   ```php
   session()->flash('answer_result', [...]);
   session()->flash('gamification_result', [...]);
   ```

7. **Redirect:** Zurück zu Practice-Route (zeigt nächste Frage)

---

## Views: Practice.blade.php vs. Lernpool Practice

### Beide sind 1:1 identisch:
- **Unterschied nur:** Route-Namen & Lernpool-Kontext statt Practice-Parameter

| Feature | Praktik | Lernpool |
|---------|---------|----------|
| Header | "📚 Alle Fragen" (oder Modus) | "📚 Lernpool-Name" |
| Progress | "Fortschritt: X/Y gemeistert" | "Fortschritt: X/Y gemeistert" |
| Style | 100% identisch | 100% identisch |
| Mobile | 100% identisch | 100% identisch |
| Antwort-Route | `practice.submit` | `ortsverband.lernpools.answer` |

---

## Testing Checkliste für neue Features

### Nach Änderungen prüfen:
- [ ] `php artisan view:clear` (Views gecachet)
- [ ] `php artisan route:clear` (Routes gecachet)
- [ ] `php artisan cache:clear` (Config gecachet)
- [ ] `npm run build` (Tailwind CSS)
- [ ] Server neustart: `php artisan serve`

### Feature-Tests:
- [ ] Modal öffnet/schließt korrekt
- [ ] AJAX-Request wird gesendet
- [ ] Richtige Antwort zeigt Feedback + Popup
- [ ] Falsche Antwort zeigt Feedback + Error-Popup
- [ ] Progress berechnet sich richtig
- [ ] Mobile View: Vollbild, Buttons am unten, kein Footer/Nav
- [ ] Desktop View: Karte mit Schatten, normal Layout

---

## Wichtige Dateien für Quick Reference

```
app/Http/Controllers/
├── OrtsverbandLernpoolController.php          ← CRUD + Modal-Support
├── OrtsverbandLernpoolQuestionController.php  ← Fragen + Auto-Numbering
├── OrtsverbandLernpoolPracticeController.php  ← Practice-Flow (wichtig!)
├── PracticeController.php                     ← Referenz (1:1 Kopie-Template)
└── OrtsverbandController.php                  ← Ortsverband-Management

app/Models/
├── OrtsverbandLernpool.php
├── OrtsverbandLernpoolQuestion.php
├── OrtsverbandLernpoolEnrollment.php
└── OrtsverbandLernpoolProgress.php

app/Policies/
└── OrtsverbandLernpoolPolicy.php              ← Wichtig: lernpool_id nicht ortsverband_lernpool_id!

resources/views/ortsverband/
├── show.blade.php                            ← Ortsverband Show-Ansicht (konsistent mit Dashboard!)
├── dashboard.blade.php                       ← Ortsverband Dashboard
└── lernpools/
    ├── index.blade.php                       ← Dashboard mit Modals
    ├── practice.blade.php                    ← Practice-View (1:1 wie practice.blade.php)
    ├── show-modal.blade.php
    ├── edit-modal.blade.php
    └── questions/
        ├── create-modal.blade.php            ← Button-UI für Lösungen!
        └── index-modal.blade.php

resources/views/
└── practice.blade.php                        ← Template/Referenz (DO NOT MODIFY!)

routes/web.php                                ← Alle Routes unter `ortsverband` prefix
```

---

## Häufige Fehler & Lösungen

### 1. "Route not found"
- **Ursache:** Route-Namen falsch in View (z.B. `practice.answer` statt `ortsverband.lernpools.answer`)
- **Lösung:** Full qualified names nutzen mit Prefix

### 2. "Column not found: ortsverband_lernpool_id"
- **Ursache:** Falsche DB-Spalte
- **Lösung:** Alle Queries nutzen `lernpool_id` nicht `ortsverband_lernpool_id`

### 3. Modal zeigt alten/falschen Content
- **Ursache:** Browser-Cache
- **Lösung:** 
  - Timestamp im URL: `?_t=Date.now()`
  - `cache: 'no-store'` in Fetch
  - Hard-Refresh (Cmd+Shift+R)

### 4. Button bleibt disabled nach Select
- **Ursache:** JavaScript nicht geladen
- **Lösung:** `npm run build` & View-Cache clearen

### 5. Popup zeigt nicht
- **Ursache:** Session-Daten nicht geflasht
- **Lösung:** In Controller: `session()->flash('gamification_result', [...])`

---

## Next Steps & Zukünftige Entwicklung

### Geplant:
- [ ] Bookmark-Funktion für Lernpool-Fragen
- [ ] Lernpool-Statistiken (Zeit, Erfolgsquote pro LA)
- [ ] Bulk-Upload von Fragen (CSV)
- [ ] Frage-Sharing zwischen Lernpools
- [ ] Leaderboard (Streaks, Punkte pro Lernpool)

### Möglich:
- [ ] Wiederhol-Intervalle (Spaced Repetition)
- [ ] Fragen-Tags/Kategorien
- [ ] Schwierigkeitsgrad
- [ ] Hinweis-System

---

## Wichtige Kontakte & Konventionen

### Code Style:
- **PHP:** Laravel Standard (PSR-12)
- **Blade:** Max 120 chars, inline CSS nur für dynamic styles
- **JavaScript:** Vanilla JS, Event Delegation, Fetch API
- **Tailwind:** Utility-first, Media Queries mit `@media` oder Tailwind breakpoints

### Design-Richtlinien:
- **WICHTIG:** Alle Seiten nach dem Login orientieren sich am Design von:
  - [resources/views/dashboard.blade.php](resources/views/dashboard.blade.php) (User-Dashboard)
  - [resources/views/ortsverband/dashboard.blade.php](resources/views/ortsverband/dashboard.blade.php) (Ortsverband-Dashboard)
  - [resources/views/ortsverband/show.blade.php](resources/views/ortsverband/show.blade.php) (Ortsverband Show-Ansicht)
- **Konsistenz:** Einheitliche Navbar, Colors, Spacing, Buttons, Cards
- **Mobile-First:** Alle Views müssen vollständig responsive sein (<640px Optimierung)
- **Accessibility:** Kontraste, Font-Größen, Touch-Targets (mind. 48x48px auf Mobile)

### Development Workflow (nach Entwicklung):
Nach erfolgreichem Entwickeln von Features folgende Befehle ausführen:

```bash
npm run build
php artisan view:clear
php artisan cache:clear
php artisan config:clear
```

Dann committen mit Git:
```bash
git add -A && git commit -m "EMOJI: Beschreibung (max 4 Wörter)"
```

**Commit Message Format:**
- Emoji am Anfang: ✨ (Feature), 🐛 (Bug), 📝 (Docs), 🎨 (UI/UX), ⚡ (Performance), ♻️ (Refactor)
- Beschreibung: Maximal 4 Wörter, prägnant
- **Beispiele:**
  - ✨ Add Lernpool Practice
  - 🐛 Fix Answer Mapping Bug
  - 📝 Update AGENTS Documentation
  - 🎨 Redesign Practice View
  - ⚡ Optimize Database Queries


### Deployment Checklist:
1. `git pull origin main`
2. `composer install`
3. `npm install && npm run build`
4. `php artisan migrate`
5. `php artisan cache:clear && php artisan view:clear`
6. Test auf Staging: `php artisan serve`

---

**Last Updated:** 12. Januar 2026  
**Updated By:** GitHub Copilot Claude Haiku 4.5  
**Next Review:** Bei neuen Features oder Breaking Changes
