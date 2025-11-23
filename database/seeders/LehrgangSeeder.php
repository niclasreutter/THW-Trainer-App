<?php

namespace Database\Seeders;

use App\Models\Lehrgang;
use App\Models\LehrgangQuestion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LehrgangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lehrgang 1: Grundlagen
        $lehrgang1 = Lehrgang::create([
            'lehrgang' => 'Grundlagen der Sicherheit',
            'slug' => 'grundlagen-sicherheit',
            'beschreibung' => 'Lerne die grundlegenden Sicherheitsprinzipien und Best Practices. Ein Einsteigerlehrgang für alle neuen Mitglieder.',
        ]);

        // Fragen für Lehrgang 1
        $questions1 = [
            [
                'lernabschnitt' => 1,
                'nummer' => 1,
                'frage' => 'Was ist die erste Priorität beim Eintreffen an einer Unfallstelle?',
                'antwort_a' => 'Die eigene Sicherheit überprüfen',
                'antwort_b' => 'Sofort Erste Hilfe leisten',
                'antwort_c' => 'Den Notarzt anrufen',
                'loesung' => 'A',
            ],
            [
                'lernabschnitt' => 1,
                'nummer' => 2,
                'frage' => 'Welche Farbe hat die Einsatzkennzeichnung der THW-Fahrzeuge?',
                'antwort_a' => 'Rot-Weiß',
                'antwort_b' => 'Blau-Weiß',
                'antwort_c' => 'Gelb-Blau',
                'loesung' => 'B',
            ],
            [
                'lernabschnitt' => 1,
                'nummer' => 3,
                'frage' => 'Wie viele Liter Wasser kann eine Standard-Tragkraftspritze pro Minute fördern?',
                'antwort_a' => 'Etwa 400-600 l/min',
                'antwort_b' => 'Etwa 800-1000 l/min',
                'antwort_c' => 'Etwa 1200-1500 l/min',
                'loesung' => 'B',
            ],
            [
                'lernabschnitt' => 2,
                'nummer' => 1,
                'frage' => 'Welche persönliche Schutzausrüstung ist beim Betreten von Schadensgebieten immer erforderlich?',
                'antwort_a' => 'Helm, Warnweste, Handschuhe',
                'antwort_b' => 'Nur ein Helm',
                'antwort_c' => 'Eine Gasmaske',
                'loesung' => 'A',
            ],
            [
                'lernabschnitt' => 2,
                'nummer' => 2,
                'frage' => 'Was bedeutet das Zeichen "Hochspannung"?',
                'antwort_a' => 'Es ist für Laien ungefährlich',
                'antwort_b' => 'Es warnt vor lebensgefährlicher elektrischer Spannung',
                'antwort_c' => 'Es zeigt nur eine allgemeine Warnung an',
                'loesung' => 'B',
            ],
        ];

        foreach ($questions1 as $q) {
            $lehrgang1->questions()->create($q);
        }

        // Lehrgang 2: Technische Rettung
        $lehrgang2 = Lehrgang::create([
            'lehrgang' => 'Technische Rettung',
            'slug' => 'technische-rettung',
            'beschreibung' => 'Spezialisierte Techniken zur Bergung von Personen und technischen Einsätzen. Für fortgeschrittene Helfer.',
        ]);

        // Fragen für Lehrgang 2
        $questions2 = [
            [
                'lernabschnitt' => 1,
                'nummer' => 1,
                'frage' => 'Bei welcher Belastung reißt ein Standard-Rettungsseil?',
                'antwort_a' => 'Bei 5 Tonnen',
                'antwort_b' => 'Bei 10-15 Tonnen',
                'antwort_c' => 'Bei 20 Tonnen und mehr',
                'loesung' => 'B',
            ],
            [
                'lernabschnitt' => 1,
                'nummer' => 2,
                'frage' => 'Welches Werkzeug ist am effektivsten beim Schneiden von Stahlkonstruktionen?',
                'antwort_a' => 'Eine Handsäge',
                'antwort_b' => 'Ein Hydraulischer Spreizer',
                'antwort_c' => 'Ein Winkelschleifer',
                'loesung' => 'B',
            ],
            [
                'lernabschnitt' => 2,
                'nummer' => 1,
                'frage' => 'Wie wird eine verletzte Person beim Abstieg mit Rettungsausrüstung am sichersten gesichert?',
                'antwort_a' => 'Mit einer einfachen Schleife',
                'antwort_b' => 'Mit einer doppelten Sicherung und Fangleine',
                'antwort_c' => 'Sicherung ist nicht notwendig',
                'loesung' => 'B',
            ],
        ];

        foreach ($questions2 as $q) {
            $lehrgang2->questions()->create($q);
        }

        $this->command->info('Lehrgang-Seeder erfolgreich ausgeführt!');
    }
}
