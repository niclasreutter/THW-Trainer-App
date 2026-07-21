# Changelog

Alle nennenswerten Änderungen an diesem Projekt werden hier dokumentiert.


## [Unreleased]

### Bugfixes

- **Liga-Teilnahme respektiert Zustimmung**: Nutzer ohne Leaderboard-Einwilligung nehmen nicht mehr am wöchentlichen Liga-Wettbewerb teil. Bisher konnten "unsichtbare" Teilnehmer (ohne Zustimmung) trotzdem Wochenbelohnungen gewinnen sowie auf- und absteigen und verzerrten so das Ranking. Jetzt werden bei der wöchentlichen Verarbeitung nur noch zustimmende Nutzer für Belohnungen, Auf- und Abstieg berücksichtigt.

## [1.1.0] - 2026-07-17

### Neue Features

- **Öffentliches Wiki** unter `/wiki`: Anleitung zu allen Funktionen der Plattform, ohne Login erreichbar. Inhaltsverzeichnis mit Scroll-Spy und Hinweisboxen. Dieses Changelog ist als Wiki-Seite unter `/wiki/changelog` eingebunden.
- **Versionsnummer im Footer**: Alle Footer (App, Startseite, Landing, Wiki) zeigen die aktuelle Version und verlinken auf das Changelog im Wiki.
- **Aushang & E-Mail-Vorlage für Ortsverbände**: Ausbildungsbeauftragte können zu jeder Einladung einen druckfertigen A4-Aushang mit QR-Code direkt aus der Einladungsseite generieren sowie eine fertige E-Mail-Vorlage zum Weiterleiten an Helferanwärter nutzen

### Bugfixes

- **Changelog im Wiki**: Ein leerer "Unreleased"-Abschnitt wird nicht mehr als nackte Überschrift über der aktuellen Version angezeigt
