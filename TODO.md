# TODO - THW-Trainer-App Feature-Verbesserungen

**Erstellt:** 12. Januar 2026
**Letzte Aktualisierung:** 06. Februar 2026

---

## Aktuelle Implementierung (Februar 2026)

### 1. Spaced Repetition System
- [x] Migration: `next_review_at`, `review_interval`, `easiness_factor` zu `user_question_progress`
- [x] SpacedRepetitionService erstellen (SM-2 Algorithmus)
- [x] PracticeController: Neuer Modus "Spaced Repetition"
- [x] Practice-Menü: Spaced Repetition Einstiegspunkt mit fälligen Fragen
- [x] Dashboard: Hinweis auf fällige Wiederholungen

### 2. Onboarding-Flow für neue Nutzer
- [x] Migration: `onboarding_completed` Spalte in `users`
- [x] OnboardingController erstellen
- [x] Onboarding View (3-Schritt-Wizard)
- [x] Dashboard-Redirect für neue Nutzer
- [x] Route registrieren

### 3. Practice-UI Verbesserungen
- [x] Fortschrittsbalken oben ("Frage X von Y" im aktuellen Modus)
- [x] Schwierigkeitsindikator pro Frage (basierend auf Fehlerquote aller Nutzer)
- [x] Bookmark-Stern prominent sichtbar

### 4. Prüfungsergebnis-Visualisierung + Prüfungshistorie
- [x] Balkendiagramm nach Lernabschnitt (stark/schwach)
- [x] Empfehlung "Diese Abschnitte solltest du wiederholen"
- [x] Vergleich mit Durchschnitt aller Nutzer
- [x] Trend über letzte 10 Prüfungen (Balken)
- [x] Neue Prüfungshistorie-Seite (/exam-history)
- [x] Sidebar-Navigation Link zu Prüfungshistorie

### 5. Loading States & Skeleton Screens
- [x] Skeleton-CSS-Klassen erstellen (Pulse + Shimmer Animation)
- [x] Skeleton-Blade-Component (dashboard, practice-menu, cards)

### 6. Meilenstein-Animationen
- [x] Milestone-Celebration-Component (Fullscreen Overlay)
- [x] Erste bestandene Prüfung: Spezial-Konfetti
- [x] Level-Up: Fullscreen-Animation mit Gold-Konfetti
- [x] Streak-Meilensteine (7, 30, 100 Tage) mit Fire-Konfetti
- [x] In Layout eingebunden

---

## Offen / Zukünftig

### UI/UX
- [ ] Erweiterte Dashboard-Statistiken (Wochengraph, Trends)
- [ ] 100% Lehrgang: Feier-Animation (Trigger in LehrgangController)
- [ ] Skeleton Screens in Views einbauen (Dashboard, Lehrgänge)
- [ ] Navbar modernisieren (Glassmorphism-Style)
- [ ] Mobile Bottom Navigation

### Features
- [ ] Lernplan-System (persönliche Ziele)
- [ ] Content-Management für Ausbilder (Bulk-Upload)
- [ ] Erweiterte Gamification (Badges, Challenges, Teams)
- [ ] Detaillierte Analytics für Ausbilder (Heatmaps, Export)
- [ ] Offline-Modus (PWA Enhancement)

### Technisch
- [ ] Monitoring & Logging (Telescope, Sentry)
- [ ] Sicherheits-Verbesserungen (Rate Limiting, 2FA)
- [ ] Code-Qualität (PSR-12, Type Hints)
- [ ] Caching-Strategie erweitern (Redis)
