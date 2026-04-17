# Changelog

Alle nennenswerten Änderungen an diesem Projekt werden hier dokumentiert.

Format: [Keep a Changelog](https://keepachangelog.com/de/1.1.0/)
Versionierung: [Semantic Versioning](https://semver.org/lang/de/)

## [Unreleased]

## [1.0.2] - 2026-04-17

### Intern

- Dev (#459, @niclasreutter)

## [1.0.1] - 2026-04-17

### Intern

- Exam-Modus Layout und Footer-Sichtbarkeit über Breakpoints verbessert (#456, @niclasreutter)
- Icons für verschiedene Zwecke angepasst, Dark/Lightmode-Favicon hinzugefügt (#455, @thmsnhl)

## [1.0.0] - 2026-04-16

Erste offizielle Version. Bündelt die bisherige Entwicklungshistorie
(~305 PRs) in zusammengefasster Form. Detaillierte Historie siehe Git-Log.

### Neue Features

- **Spaced-Repetition-System**: Intelligente Wiederholungszeitplanung mit Mastery-Schwelle, schwierigkeitsbasiertem Algorithmus und Priorisierung ungelöster Fragen vor Wiederholungen
- **Gamification-Komplettsystem**: Level-System (20 Level), Achievements mit dynamischem Trigger-System, XP-Vergabe für Fragen und Prüfungen
- **Streak-System**: Tagesstreaks mit konfigurierbarer Mindestaktivität (20 Fragen oder 1 Prüfung), Freeze-Funktion im Dashboard einlösbar, Benachrichtigungen bei Streak-Gefahr
- **Ligen-System**: Wöchentliche Ranglisten mit Auf-/Abstieg, Wochenbelohnungen (XP/Punkte), Admin-Ligen-Übersicht und Liga-Simulator
- **Shop-System**: Erwerb von Streak Freezes, Profilrahmen und Zubehör gegen Punkte; Admin-XP-Verwaltung und Shop-Analyse-Dashboard
- **Live-Lernsessions**: Ortsverband-Sessions mit Echtzeit-Rangliste, E-Mail-Benachrichtigungen bei Start/Ende, Live-Banner im Dashboard
- **Admin-Dashboard**: Charts (Chart.js, Sparklines), Aktivitäts-Feed, Zeitsimulator, XP/Fortschritt-Reset pro Nutzer, Nutzersuche
- **Fehler-Meldungen**: Nutzer können Fragen-Fehler melden; zentrales Admin-Interface unter `/admin/issues`
- **Prüfungs-Feedback**: Sterne-Rating nach Prüfungsabschluss, Glückwunsch-Seite, Admin-Feedback-Ansicht
- **Push-Benachrichtigungen**: Web-Push via VAPID, PWA-Service-Worker, iOS-Safari-Kompatibilität, Admin-Debug-Interface
- **Lernpools**: Tags (Erstellung, OV-Ansicht, Vorschläge), Fragen bearbeiten/löschen, Lernpool-Statistiken in Gesamtansicht
- **Ranglisten**: Aktivierbar pro Ortsverband, sortiert nach Punkten; Nutzeridentifikation per ID
- **E-Mail-Prüfungserinnerungen**: Automatischer Versand anstelle von manuellen täglichen Zielen
- **Gastnutzer-Konvertierung**: Registrierungsaufforderung nach je 20 Fragen
- **Prüfungszugang mit Mastery-Gate**: Prüfung erst nach erfolgreichem Mastery aller Fragen im Lernpool zugänglich

(~100 Feature-PRs zusammengefasst)

### Bugfixes

- Lernpool-Duplikat-Bugs, Policy-Checks und Permission-Fehler bei Enrollment korrigiert
- Modal- und Event-Listener-Bugs (Buttons, Form-Actions, onclick-Handler, Nested-Forms) behoben
- Streak-Reset Cron-Job, Reminder-Timing und Tagesfortschritt-Berechnung korrigiert
- Spaced-Repetition-Zählung, Mastery-Schwelle und Fragen-Entfernung aus Session korrigiert
- NaN-Fehler und Counter-Animation-Script vollständig neu geschrieben
- Admin-Report zeigte falschen Tag (gestern statt heute) – korrigiert
- Glassmorphism- und Dark-Mode-Darstellungsfehler in Exam, Practice und Mobile behoben
- Mobile-Layout-Fixes: Safe-Area, Stats-Row-Stacking, horizontales Scrollen, Overflow
- Streak-Freeze Doppel-Tag-Bug und Anzeige-Fehler korrigiert
- Cache-Key-Kollisionen und veralteter Cache in Lernsessions behoben
- Migration für doppelt-encodiertes JSON und robuste Backfill-Logik implementiert
- Landing-Page-Statistiken zeigten null aus Cache – behoben
- Lernpool-Fragen-Loop-Bug behoben (unendliche Wiederholung nach Session-Ende)
- GoodLuck-Mail-Zustellbarkeit verbessert, Prüfungsnavigation abgesichert
- Gemeisterte Fragen bei Antwortfehler korrekt wie Prüfungsfragen behandelt

(~80 Bugfix-PRs zusammengefasst)

### UI & Design

- **Dark-Mode-Glassmorphism-Design-System**: Glass-Cards, Bento-Grid, Lensflare-Glow-Varianten, asymmetrische Karten-Typen (TL, BR, Slash, Organic)
- **Light-Mode-Unterstützung**: Alle Glass-Karten, Navbar, Popups, Formulare und Glassmorphism-Elemente im Light Mode vollständig sichtbar
- **Mobile-First-Optimierung**: Exam, Practice, Lehrgänge und Admin-Seiten vollständig responsiv überarbeitet
- Bootstrap Icons ersetzen alle Emojis in Navigation, Admin, Gamification-UI und Fragen-Seiten
- **Mobile Bottom-Navigation**: Pill-Design mit Safe-Area-Padding, Glassmorphism und korrekt gesetzter Position
- **Exam-Seite komplett neu gebaut**: Mobile-Navigation, Glassmorphism-Sidebar, Randentfernung auf Mobile
- **Landingpage modernisiert**: Hero-Bereich, Bento-Grid-Layout, Social-Proof-Statistiken, Prüfungs-Countdown
- **Admin-Dashboard-Redesign**: Bento-Grid-Layout mit Aktivitäts-Feed, neuen Statistik-Karten und Chart-Integration
- Statistik-Seite im neuen Landing-Design; Admin-Statistiken auf separate Seiten aufgeteilt
- Glassmorphism-Onboarding-Tour: Spotlight-Overlay, transparente Tooltips, pulsierende Intro-Karten
- Fullscreen-Confetti/Celebration-Animation bei Streak-Verlängerung und Level-Up
- Practice-Menu Empty-State und Lehrgang-Detailseite mit Dashboard-Layout überarbeitet
- PWA-Icons in korrekten Größen, Cache-Busting für Logo-, Service-Worker- und Manifest-Dateien
- Fehlerseiten (404, 419, Multi-Domain) optisch an das Design-System angepasst
- Shop-Seite mit konsistentem Glass-Grid-Layout und standardisiertem Profilrahmen-Styling

(~100 UI-PRs zusammengefasst)

### Performance

- Spaced-Repetition: schwierigkeitsbasierter Algorithmus für optimale Wiederholungsintervalle
- SEO-Optimierung: HTTP-Caching, gzip-Kompression, Security Headers, Title-Tags für Google Search Console
- User-Count-Cronjob optimiert (Laufzeit 00:15, Vortags-Abgleich)
- Cache-Busting für alle statischen Assets (Logo, Service-Worker, Manifest)

(~5 Performance-PRs zusammengefasst)

### Sicherheit

- `.gitignore` erweitert: Credentials, Debug-Dateien und Deployment-Skripte ausgeschlossen
- Datenleck geschlossen: Debug-Code und Dead Files aus dem Produktivcode entfernt
- Fehlversuch-Limit bei E-Mail-Verifizierungscode implementiert (Brute-Force-Schutz)
- Datenschutz: Nutzer-Wachstumsdaten im Admin durch anonymisierte Prüfungs-Statistiken ersetzt

(4 Security-PRs)

### Sonstiges

- GitHub Actions CI/CD-Deployment-Pipeline für Produktivserver eingerichtet
- Multi-Domain-Session-Handling konfiguriert (CloudPanel-Setup)
- E-Mail-Verifizierung auf numerische Codes umgestellt (statt Links)
- QR-Code-Logo-Platzierung verbessert (korrektes Seitenverhältnis)
- Spendenlink auf neuen Host (bero-host) aktualisiert
- Alle Cronjobs in Laravel Scheduler migriert (1 CloudPanel-Eintrag genügt)

(~15 sonstige PRs zusammengefasst)

[1.0.2]: https://github.com/niclasreutter/THW-Trainer-App/compare/v1.0.1...v1.0.2
[1.0.1]: https://github.com/niclasreutter/THW-Trainer-App/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/niclasreutter/THW-Trainer-App/commits/main
