# Changelog

Alle nennenswerten Änderungen an diesem Projekt werden hier dokumentiert.


## [Unreleased]

### Neue Features

- **Eine Seite pro Lernabschnitt**: Jeder der 10 Lernabschnitte hat eine eigene Seite mit allen Fragen, Lösungen und Erklärungen, dazu fünf Fragen zum direkten Ausprobieren
- **Lernabschnitt ohne Konto üben**: Von jeder Abschnittsseite aus lassen sich genau die Fragen dieses Abschnitts anonym üben

### UI & Design

- **Übersicht der Lernabschnitte**: Die Seite „THW Theorie“ zeigt alle 10 Lernabschnitte mit Fragenanzahl und führt direkt zu den Abschnittsseiten
- **Version des Fragenkatalogs**: Startseite und Infoseiten nennen den verwendeten Fragenkatalog (Version 03.5, Stand September 2024)
- **Neues Design für Dashboard, Übungsmenü, Üben und Prüfung**: Die Lernseiten erscheinen im Stil der Startseite, hell oder dunkel je nach Farbschema. Oben steht immer genau eine Empfehlung, was als Nächstes dran ist
- **Neue Seitenleiste und mobiles Menü**: Profilbild, Mitteilungen, Farbschema und Abmelden sitzen gesammelt unten in der Seitenleiste; auf dem Handy öffnet sich das Menü von rechts
- **Wiederholung verständlich erklärt**: Überall heißt es jetzt einheitlich „Wiederholung“. Das Dashboard zeigt, wie viele Fragen heute, morgen und in den nächsten Tagen wiederkommen, und nach jeder Antwort steht, wann die Frage wiederkommt und wie oft sie noch richtig beantwortet werden muss
- **Ruhiges Üben**: Beim Üben und in der Prüfung blendet sich die Navigation aus; Antworten werden unten im Daumenbereich abgeschickt und in einem Fenster von unten aufgelöst
- **Prüfung mit allen 40 Fragen im Blick**: Eine Leiste zeigt beantwortete, offene und markierte Fragen; das Ergebnis zeigt einen Balken mit der 80-%-Grenze, und jede Frage lässt sich direkt zur Nachbesprechung antippen
- **Ohne Konto üben und Prüfung simulieren im neuen Design**: Üben und Prüfungssimulation ohne Anmeldung zeigen die Navigation der Startseite und die gleiche Oberfläche wie für angemeldete Nutzer; in der Auswertung springt ein Tipp auf die Fragenleiste direkt zur passenden Lösung

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
