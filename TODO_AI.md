# TODO_AI.md - KI-Erscheinungsbild Optimierung

> Ziel: Die Webseite soll weniger nach KI-generiertem Content aussehen.

---

## Status-Legende
- [ ] Offen
- [x] Erledigt
- [~] In Bearbeitung

---

## 1. Emojis durch Bootstrap Icons ersetzen

### 1.1 Setup
- [x] Bootstrap Icons via npm installieren (`npm install bootstrap-icons`)
- [x] CSS Import in `resources/css/app.css` hinzufügen
- [x] Build testen (Font-Dateien werden gebündelt)

### 1.2 Icon-Mapping (Konsistenz)

| Kontext | Emoji | Bootstrap Icon | CSS-Klasse |
|---------|-------|----------------|------------|
| **Navigation/Features** |
| Dashboard | - | `bi-house` | `bi bi-house` |
| Lernen/Übung | 📚 | `bi-book` | `bi bi-book` |
| Prüfung | 🎓 | `bi-mortarboard` | `bi bi-mortarboard` |
| Statistik/Fortschritt | 📊 | `bi-bar-chart` | `bi bi-bar-chart` |
| Lehrgänge | 📖 | `bi-journal-text` | `bi bi-journal-text` |
| **Gamification** |
| Level | ⭐ | `bi-star-fill` | `bi bi-star-fill` |
| Punkte | 💎 | `bi-gem` | `bi bi-gem` |
| Streak/Feuer | 🔥 | `bi-fire` | `bi bi-fire` |
| Trophy/Erfolg | 🏆 | `bi-trophy` | `bi bi-trophy` |
| Achievement | 🎖️ | `bi-award` | `bi bi-award` |
| **Status** |
| Erfolg/Richtig | ✅ | `bi-check-circle-fill` | `bi bi-check-circle-fill` |
| Fehler/Falsch | ❌ | `bi-x-circle-fill` | `bi bi-x-circle-fill` |
| Warnung | ⚠️ | `bi-exclamation-triangle` | `bi bi-exclamation-triangle` |
| Info | ℹ️ | `bi-info-circle` | `bi bi-info-circle` |
| **Aktionen** |
| Starten/Play | 🚀 | `bi-play-circle` | `bi bi-play-circle` |
| Ziel/Target | 🎯 | `bi-bullseye` | `bi bi-bullseye` |
| Fragen/FAQ | ❓ | `bi-question-circle` | `bi bi-question-circle` |
| Einstellungen | ⚙️ | `bi-gear` | `bi bi-gear` |
| **Sonstiges** |
| Kaffee/Spende | ☕ | `bi-cup-hot` | `bi bi-cup-hot` |
| Handy/App | 📲 | `bi-phone` | `bi bi-phone` |
| Person | 👤 | `bi-person` | `bi bi-person` |
| Ortsverband/Gruppe | 🏢 | `bi-building` | `bi bi-building` |
| Lesezeichen | 🔖 | `bi-bookmark` | `bi bi-bookmark` |
| Benachrichtigung | 🔔 | `bi-bell` | `bi bi-bell` |

### 1.3 Dateien zu bearbeiten

#### Priorität 1 (Hauptseiten)
- [x] `resources/views/home.blade.php`
  - [x] Hero Section (Raketen-Emoji + Animation entfernt)
  - [x] Features Section (📚, 🎓, 📊, 📲 -> Bootstrap Icons)
  - [x] CTA Section (📑 entfernt)
  - [x] FAQ Section (❓ entfernt)
  - [x] About Section (☕ -> bi-cup-hot)

- [x] `resources/views/dashboard.blade.php`
  - [x] Motivationsnachrichten sachlicher formuliert (keine Emojis mehr)
  - [x] Stat-Cards (🔥, ⭐, ⚡, 🏆 -> Bootstrap Icons)
  - [x] Section-Titel (📚 -> Bootstrap Icons)
  - [x] Empty-States (Emojis entfernt, sachlichere Texte)
  - [x] Leaderboard-Modal (Emojis -> Bootstrap Icons)
  - [x] Emoji-Rain Animation entfernt

- [x] `resources/views/practice-menu.blade.php`
  - [x] Section-Titel (📚, 📖, 🔍 -> Bootstrap Icons)
  - [x] Statistik-Icons (❌, ❓, ✅ -> Bootstrap Icons)
  - [x] Training-Button (🎯 -> bi-bullseye)

#### Priorität 2 (Navigation & Layout)
- [x] `resources/views/layouts/navigation.blade.php`
  - [x] User-Stats im Dropdown (⭐, 💎, 🔥 -> Bootstrap Icons)
  - [x] Notification-Icon (🔔 -> bi-bell)

- [x] `resources/views/layouts/app.blade.php`
  - [x] Footer Spenden-Button (☕ -> bi-cup-hot)
  - [x] Footer Creator-Text (💙 entfernt)

#### Priorität 3 (Weitere Views) - Phase 2
- [ ] `resources/views/gamification/achievements.blade.php`
- [ ] `resources/views/gamification/leaderboard.blade.php`
- [x] `resources/views/statistics.blade.php` - Emojis ersetzt, Titel mit Gradient
- [ ] `resources/views/exam.blade.php`
- [ ] `resources/views/practice.blade.php`
- [ ] `resources/views/ortsverband/*.blade.php`
- [ ] `resources/views/components/*.blade.php`

---

## 5. Seitenüberschriften mit gelbem Gradient (ohne Icons)

- [x] `practice-menu.blade.php` - `.practice-title` mit Gradient
- [x] `statistics.blade.php` - `.statistics-title` mit Gradient
- [x] `dashboard.blade.php` - Section-Titel ohne Icons
- [x] Alle Section-Titel Icons entfernt

---

## 2. Motivationstexte überarbeiten

### Betroffene Stellen
- [x] `dashboard.blade.php` - Motivationsnachrichten sachlicher formuliert
- [x] `home.blade.php` - Untertitel vereinfacht
- [x] `practice-menu.blade.php` - Untertitel vereinfacht

### Durchgeführte Änderungen
| Vorher (KI-typisch) | Nachher (natürlich) |
|---------------------|---------------------|
| "Du machst das großartig!" | "50% abgeschlossen" |
| "Starte deine Reise zur Grundausbildung!" | "Noch keine Fragen bearbeitet" |
| "Wähle deinen Lernmodus und verbessere dein Wissen" | "Lernmodus auswählen" |
| "Alles, was du brauchst, um dich optimal..." | "Alles für deine Grundausbildung..." |
| "Entdecke Lehrgänge!" | "Keine Lehrgänge" |
| "🚀 Lehrgänge erkunden" | "Lehrgänge ansehen" |

---

## 3. Animationen reduzieren

- [x] Emoji-Rain bei bestandenen Prüfungen entfernt
- [x] Raketen-Animation entfernt (nur noch einfacher Button)
- [ ] Konfetti-Animation bei 100% - noch vorhanden (optional)

---

## 4. FAQ-Texte überarbeiten (Phase 2)

- [ ] Variablere Satzstrukturen
- [ ] Weniger formelhaft
- [ ] Natürlichere Sprache

---

## Changelog

### 2026-01-26
- [x] Bootstrap Icons via npm installiert
- [x] CSS Import hinzugefügt (vor @tailwind Direktiven)
- [x] home.blade.php - Alle Emojis ersetzt
- [x] dashboard.blade.php - Alle Emojis ersetzt, Motivationstexte sachlicher
- [x] practice-menu.blade.php - Alle Emojis ersetzt
- [x] navigation.blade.php - User-Stats Icons ersetzt
- [x] app.blade.php - Footer Icons ersetzt
- [x] Emoji-Rain und Raketen-Animation entfernt
- [x] Build erfolgreich (Font-Dateien werden gebündelt)
- [x] Seitenüberschriften: Gelber Gradient statt blau, ohne Icons
- [x] statistics.blade.php - Komplett überarbeitet (Emojis + Gradient-Titel)
- [x] Alle Section-Titel ohne Icons

---

*Letzte Aktualisierung: 2026-01-26*
