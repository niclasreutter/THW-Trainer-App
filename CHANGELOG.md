# Changelog

Alle nennenswerten Änderungen an diesem Projekt werden hier dokumentiert.


## [Unreleased]

### Bugfixes

- **Liga-Teilnahme respektiert Zustimmung**: Nutzer ohne Leaderboard-Einwilligung nehmen nicht mehr am wöchentlichen Liga-Wettbewerb teil. Bisher konnten "unsichtbare" Teilnehmer (ohne Zustimmung) trotzdem Wochenbelohnungen gewinnen sowie auf- und absteigen und verzerrten so das Ranking. Jetzt werden bei der wöchentlichen Verarbeitung nur noch zustimmende Nutzer für Belohnungen, Auf- und Abstieg berücksichtigt.
- **Startseite im Standardmodus**: Strukturierte Daten standen vor dem Doctype, dadurch renderte der Browser die Startseite im Quirks-Modus
- **Meta-Beschreibung von /thw-theorie**: Enthielt PHP-Code statt Text
- **Einheitliche Prüfungsfakten**: Einige Landingpages nannten 30 statt 40 Fragen pro Prüfungssimulation
- **Kennzahlen auf der Startseite**: Wurden teilweise als „0+“ angezeigt
- **Sitemap**: Anmeldung und Registrierung entfernt, da nicht zur Indexierung bestimmt

### UI & Design

- **Neues Design der öffentlichen Seiten**: Startseite, die Themenseiten (THW Theorie, Grundausbildung, Prüfungsfragen, Theorieprüfung), Statistik, Wiki, „Ohne Anmeldung üben“ sowie Anmeldung und Registrierung erscheinen hell und ruhig in THW-Blau. Beim Scrollen zeigen Animationen den Lernweg (Lernen, Wiederholen, Simulieren, Bestehen), eine echte Katalogfrage wird Schritt für Schritt beantwortet und aufgelöst, und die Statistik erzählt ihre Zahlen in vier animierten Bildern. Bei aktivierter Einstellung „Bewegung reduzieren“ entfallen die Animationen.

### Performance

- **Schlankere öffentliche Seiten**: Eigenes Stylesheet mit rund 8 KB statt 47 KB (gzip), die Statistik-Seite lädt kein Chart.js mehr

### Intern

- Neues Layout `layouts/public` mit gemeinsamen SEO-Tags (Canonical, Open Graph, JSON-LD) und eigenem Asset-Bundle (`public.css`, `public.js`); die vier Themenseiten nutzen eine gemeinsame Vorlage `landing/topic`
- Feature-Test für alle öffentlichen Seiten (Doctype, genau eine H1, gültiges JSON-LD)

## [1.1.0] - 2026-07-17

### Neue Features

- **Öffentliches Wiki** unter `/wiki`: Anleitung zu allen Funktionen der Plattform, ohne Login erreichbar. Inhaltsverzeichnis mit Scroll-Spy und Hinweisboxen. Dieses Changelog ist als Wiki-Seite unter `/wiki/changelog` eingebunden.
- **Versionsnummer im Footer**: Alle Footer (App, Startseite, Landing, Wiki) zeigen die aktuelle Version und verlinken auf das Changelog im Wiki.
- **Aushang & E-Mail-Vorlage für Ortsverbände**: Ausbildungsbeauftragte können zu jeder Einladung einen druckfertigen A4-Aushang mit QR-Code direkt aus der Einladungsseite generieren sowie eine fertige E-Mail-Vorlage zum Weiterleiten an Helferanwärter nutzen

### Bugfixes

- **Changelog im Wiki**: Ein leerer "Unreleased"-Abschnitt wird nicht mehr als nackte Überschrift über der aktuellen Version angezeigt
