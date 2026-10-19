# Changelog

Alle nennenswerten Änderungen an diesem Projekt werden hier dokumentiert.


## [Unreleased]

## [1.3.0] - 2026-10-19

### Neue Features

- **Eine Seite pro Lernabschnitt**: Jeder der 10 Lernabschnitte hat eine eigene Seite mit allen Fragen, Lösungen und Erklärungen, dazu fünf Fragen zum direkten Ausprobieren
- **Lernabschnitt ohne Konto üben**: Von jeder Abschnittsseite aus lassen sich genau die Fragen dieses Abschnitts anonym üben

### UI & Design

- **Übersicht der Lernabschnitte**: Die Seite „THW Theorie“ zeigt alle 10 Lernabschnitte mit Fragenanzahl und führt direkt zu den Abschnittsseiten
- **Version des Fragenkatalogs**: Startseite und Infoseiten nennen den verwendeten Fragenkatalog (Version 03.5, Stand September 2024)

## [1.2.0] - 2026-10-14

### Bugfixes

- **Liga-Teilnahme respektiert Zustimmung**: Nutzer ohne Leaderboard-Einwilligung nehmen nicht mehr am wöchentlichen Liga-Wettbewerb teil. Bisher konnten "unsichtbare" Teilnehmer (ohne Zustimmung) trotzdem Wochenbelohnungen gewinnen sowie auf- und absteigen und verzerrten so das Ranking. Jetzt werden bei der wöchentlichen Verarbeitung nur noch zustimmende Nutzer für Belohnungen, Auf- und Abstieg berücksichtigt.
- **Richtige Prüfungsangaben**: Einige Infoseiten nannten noch 30 statt 40 Fragen pro Prüfung
- **Zahlen auf der Startseite**: Nutzer- und Prüfungszahlen wurden teilweise als „0+“ angezeigt
- **Link „Alle Achievements ansehen“**: Führt im Profil wieder zur Achievement-Übersicht statt zur Statistik

### UI & Design

- **Neues Design für Startseite und Infoseiten**: Startseite, THW Theorie, Grundausbildung, Prüfungsfragen, Theorieprüfung, Wiki und „Ohne Anmeldung üben“ erscheinen hell und aufgeräumt in THW-Blau
- **Lernweg zum Mitscrollen**: Auf der Startseite wird beim Scrollen eine echte Frage aus dem Katalog Schritt für Schritt beantwortet und aufgelöst; eine Animation zeigt, wie Lernen, Wiederholen, Simulieren und Bestehen zusammenhängen
- **Prüfungssimulation zum Anschauen**: Die Seite zur Theorieprüfung spielt beim Scrollen eine komplette Simulation mit 40 Fragen, Uhr und Bestehensgrenze durch
- **Statistik in vier Bildern**: Die Plattform-Statistik zeigt Nutzer, Wachstum, Bestehensquote und Fragenkatalog als Animation, dazu den Lernrhythmus der letzten 30 Tage
- **Anmeldung und Registrierung** im neuen Design
- **Rücksicht auf Bewegungsempfindlichkeit**: Ist auf dem Gerät „Bewegung reduzieren“ aktiviert, entfallen die Animationen

### Performance

- **Schnellere Infoseiten**: Die öffentlichen Seiten laden deutlich weniger Daten

## [1.1.0] - 2026-07-17

### Neue Features

- **Öffentliches Wiki** unter `/wiki`: Anleitung zu allen Funktionen der Plattform, ohne Login erreichbar. Inhaltsverzeichnis mit Scroll-Spy und Hinweisboxen. Dieses Changelog ist als Wiki-Seite unter `/wiki/changelog` eingebunden.
- **Versionsnummer im Footer**: Alle Footer (App, Startseite, Landing, Wiki) zeigen die aktuelle Version und verlinken auf das Changelog im Wiki.
- **Aushang & E-Mail-Vorlage für Ortsverbände**: Ausbildungsbeauftragte können zu jeder Einladung einen druckfertigen A4-Aushang mit QR-Code direkt aus der Einladungsseite generieren sowie eine fertige E-Mail-Vorlage zum Weiterleiten an Helferanwärter nutzen

### Bugfixes

- **Changelog im Wiki**: Ein leerer "Unreleased"-Abschnitt wird nicht mehr als nackte Überschrift über der aktuellen Version angezeigt
