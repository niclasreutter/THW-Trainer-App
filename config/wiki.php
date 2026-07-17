<?php

/*
|--------------------------------------------------------------------------
| Wiki / Anleitung (öffentlich, ohne Login)
|--------------------------------------------------------------------------
|
| Navigation und Seiten des Wikis. Die Inhalte liegen als Markdown-Dateien
| in resources/wiki/. Der Slug ist gleichzeitig die URL (/wiki/{slug}).
|
| Pro Seite:
|   'title'       => Titel in Navigation und <title>
|   'description' => Meta-Description (SEO) + Teaser
|   'file'        => Markdown-Datei relativ zu resources/wiki/
|                    (Sonderfall: 'root:' Präfix = relativ zum Projekt-Root,
|                    z.B. das zentrale CHANGELOG.md)
|
| Neue Seite hinzufügen: Markdown-Datei anlegen + Eintrag hier ergänzen.
|
*/

return [

    'sections' => [
        [
            'title' => 'Erste Schritte',
            'icon' => 'bi-rocket-takeoff',
            'pages' => [
                'index' => [
                    'title' => 'Überblick',
                    'description' => 'Was ist der THW-Trainer? Alle Funktionen der kostenlosen Lernplattform für die THW-Grundausbildung im Überblick.',
                    'file' => 'index.md',
                ],
                'registrierung' => [
                    'title' => 'Registrierung & Anmeldung',
                    'description' => 'Konto erstellen, anmelden oder ohne Registrierung als Gast üben – so startest du mit dem THW-Trainer.',
                    'file' => 'registrierung.md',
                ],
                'dashboard' => [
                    'title' => 'Das Dashboard',
                    'description' => 'Smart-Action-Karte, Lernfortschritt, Prüfungs-Countdown und Wochenaktivität – das Dashboard erklärt.',
                    'file' => 'dashboard.md',
                ],
                'app-installieren' => [
                    'title' => 'Als App installieren',
                    'description' => 'Den THW-Trainer als App (PWA) auf iPhone, Android oder Desktop installieren – inklusive Push-Benachrichtigungen.',
                    'file' => 'app-installieren.md',
                ],
            ],
        ],
        [
            'title' => 'Lernen & Üben',
            'icon' => 'bi-journal-text',
            'pages' => [
                'lernmodus' => [
                    'title' => 'Der Lernmodus',
                    'description' => 'Alle Übungsmodi im Detail: alle Fragen, ungelöste Fragen, Lernabschnitte, Fragensuche, Fehlerfragen und Lesezeichen.',
                    'file' => 'lernmodus.md',
                ],
                'spaced-repetition' => [
                    'title' => 'Spaced Repetition',
                    'description' => 'Intelligente Wiederholung: Wie der THW-Trainer fällige Fragen berechnet und dein Wissen langfristig sichert.',
                    'file' => 'spaced-repetition.md',
                ],
                'pruefungssimulation' => [
                    'title' => 'Die Prüfungssimulation',
                    'description' => '40 Fragen, 30 Minuten, 80 % zum Bestehen – die realistische Simulation der THW-Theorieprüfung erklärt.',
                    'file' => 'pruefungssimulation.md',
                ],
                'lehrgaenge' => [
                    'title' => 'Lehrgänge',
                    'description' => 'Zusätzliche Fragenkataloge für weitere THW-Lehrgänge: einschreiben, üben und Fortschritt verfolgen.',
                    'file' => 'lehrgaenge.md',
                ],
                'zusatzfragen' => [
                    'title' => 'Zusatzfragen',
                    'description' => 'Freiwillige Zusatzfragen über den offiziellen Prüfungskatalog hinaus – und wie du eigene Fragen einreichst.',
                    'file' => 'zusatzfragen.md',
                ],
            ],
        ],
        [
            'title' => 'Motivation',
            'icon' => 'bi-trophy',
            'pages' => [
                'gamification' => [
                    'title' => 'XP, Level & Erfolge',
                    'description' => 'Erfahrungspunkte, 20 Level und Achievements: das Belohnungssystem des THW-Trainers erklärt.',
                    'file' => 'gamification.md',
                ],
                'streaks-und-ligen' => [
                    'title' => 'Streaks & Ligen',
                    'description' => 'Tagesstreak mit Streak-Freeze und wöchentliche Ligen von Bronze bis Diamant – so bleibst du dran.',
                    'file' => 'streaks-und-ligen.md',
                ],
                'shop-und-lootboxen' => [
                    'title' => 'Shop & Lootboxen',
                    'description' => 'Punkte ausgeben im Shop: Streak-Freezes, Avatar-Zubehör und Profilrahmen – plus Lootboxen als Belohnung.',
                    'file' => 'shop-und-lootboxen.md',
                ],
            ],
        ],
        [
            'title' => 'Ortsverband',
            'icon' => 'bi-people',
            'pages' => [
                'ortsverband' => [
                    'title' => 'Ortsverband & Rollen',
                    'description' => 'Deinem Ortsverband beitreten, Mitglieder verwalten und die Rolle Ausbildungsbeauftragte:r erklärt.',
                    'file' => 'ortsverband.md',
                ],
                'lernpools' => [
                    'title' => 'Lernpools',
                    'description' => 'Eigene Fragenpools für den Ortsverband erstellen, Fragen verwalten und den Lernfortschritt der Helfer verfolgen.',
                    'file' => 'lernpools.md',
                ],
                'lernsessions' => [
                    'title' => 'Live-Lernsessions',
                    'description' => 'Gemeinsame Lern-Events mit Echtzeit-Rangliste für Ausbildungsabende im Ortsverband.',
                    'file' => 'lernsessions.md',
                ],
                'ausbildungsfortschritt' => [
                    'title' => 'Ausbildungsfortschritt',
                    'description' => 'Die Checkliste der Lernabschnitte: eigenen Fortschritt pflegen oder als Ausbilder für Helfer dokumentieren.',
                    'file' => 'ausbildungsfortschritt.md',
                ],
            ],
        ],
        [
            'title' => 'Konto & Hilfe',
            'icon' => 'bi-person-gear',
            'pages' => [
                'profil-und-einstellungen' => [
                    'title' => 'Profil & Einstellungen',
                    'description' => 'Avatar, Benachrichtigungen, Prüfungsdatum, Datenexport und Account-Löschung – alle Kontoeinstellungen.',
                    'file' => 'profil-und-einstellungen.md',
                ],
                'faq' => [
                    'title' => 'Häufige Fragen (FAQ)',
                    'description' => 'Antworten auf die häufigsten Fragen rund um den THW-Trainer.',
                    'file' => 'faq.md',
                ],
            ],
        ],
        [
            'title' => 'Über das Projekt',
            'icon' => 'bi-info-circle',
            'pages' => [
                'changelog' => [
                    'title' => 'Changelog',
                    'description' => 'Alle Neuerungen und Verbesserungen des THW-Trainers, Version für Version.',
                    'file' => 'root:CHANGELOG.md',
                ],
            ],
        ],
    ],

];
