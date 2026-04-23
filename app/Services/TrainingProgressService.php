<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserTrainingProgress;
use Illuminate\Support\Collection;

class TrainingProgressService
{
    /**
     * Structure of all trackable training items.
     * Lehrabschnitte (LA) have nested sub-items and can be bulk-toggled.
     * Zusatzausbildungen are single items only.
     */
    public const LEHRABSCHNITTE = [
        [
            'key' => 'la01',
            'nr' => 'LA 01',
            'title' => 'Das THW im Gefüge des Zivil- und Katastrophenschutzes',
            'items' => [
                ['key' => 'la01.1', 'label' => 'Die Bundesanstalt Technisches Hilfswerk'],
                ['key' => 'la01.2', 'label' => 'Das Bevölkerungsschutzsystem in Deutschland'],
                ['key' => 'la01.3', 'label' => 'THW und Arbeitgeber'],
                ['key' => 'la01.4', 'label' => 'THW-Bundesvereinigung e. V. und THW-Jugend e. V.'],
            ],
        ],
        [
            'key' => 'la02',
            'nr' => 'LA 02',
            'title' => 'Arbeitssicherheit und Gesundheitsschutz',
            'items' => [
                ['key' => 'la02.1', 'label' => 'Grundlagen, Akteure im Arbeitsschutz'],
                ['key' => 'la02.2', 'label' => 'Organisatorische Maßnahmen'],
                ['key' => 'la02.3', 'label' => 'Arbeitssicherheit und Gesundheitsschutz im Ortsverband'],
                ['key' => 'la02.4', 'label' => 'Arbeitssicherheit und Gesundheitsschutz im Einsatz'],
                ['key' => 'la02.5', 'label' => 'Schutzausstattung im THW'],
                ['key' => 'la02.6', 'label' => 'Maßnahmen bei extremen Witterungen'],
                ['key' => 'la02.7', 'label' => 'Gefährliche Stoffe und Güter'],
            ],
        ],
        [
            'key' => 'la03',
            'nr' => 'LA 03',
            'title' => 'Arbeiten mit Leinen, Drahtseilen, Ketten, Rund- und Bandschlingen',
            'items' => [
                ['key' => 'la03.1', 'label' => 'Arbeiten mit Leinen'],
                ['key' => 'la03.2', 'label' => 'Arbeiten mit Drahtseilen'],
                ['key' => 'la03.3', 'label' => 'Arbeiten mit Ketten'],
                ['key' => 'la03.4', 'label' => 'Arbeiten mit Rundschlingen'],
                ['key' => 'la03.5', 'label' => 'Arbeiten mit Bandschlingen'],
            ],
        ],
        [
            'key' => 'la04',
            'nr' => 'LA 04',
            'title' => 'Umgang mit Leitern',
            'items' => [
                ['key' => 'la04.1', 'label' => 'Allgemeines, Steckleitern'],
            ],
        ],
        [
            'key' => 'la05',
            'nr' => 'LA 05',
            'title' => 'Stromerzeugung und Beleuchtung',
            'items' => [
                ['key' => 'la05.1', 'label' => 'Grundlagen und Bezeichnungen'],
                ['key' => 'la05.2', 'label' => 'Gerätekunde tragbarer Stromerzeuger 8 kVA'],
                ['key' => 'la05.3', 'label' => 'Gerätekunde einer Beleuchtungsanlage'],
                ['key' => 'la05.4', 'label' => 'Ausleuchten von Einsatzstellen und Verkehrswegen'],
            ],
        ],
        [
            'key' => 'la06',
            'nr' => 'LA 06',
            'title' => 'Metall-, Holz- und Steinbearbeitung',
            'items' => [
                ['key' => 'la06.1', 'label' => 'Metallbearbeitung'],
                ['key' => 'la06.2', 'label' => 'Holzbearbeitung'],
                ['key' => 'la06.3', 'label' => 'Steinbearbeitung'],
            ],
        ],
        [
            'key' => 'la07',
            'nr' => 'LA 07',
            'title' => 'Bewegen von Lasten',
            'items' => [
                ['key' => 'la07.1', 'label' => 'Tragen und Bewegen von Lasten'],
                ['key' => 'la07.2', 'label' => 'Zuggerät'],
                ['key' => 'la07.3', 'label' => 'Kettenzug'],
                ['key' => 'la07.4', 'label' => 'Hebekissen'],
                ['key' => 'la07.5', 'label' => 'Hebe-/Pressgerät'],
                ['key' => 'la07.6', 'label' => 'Hydraulischer Heber'],
            ],
        ],
        [
            'key' => 'la08',
            'nr' => 'LA 08',
            'title' => 'Arbeiten am und auf dem Wasser',
            'items' => [
                ['key' => 'la08.1', 'label' => 'Sicheres Arbeiten am und auf dem Wasser'],
                ['key' => 'la08.2', 'label' => 'Pumpen'],
                ['key' => 'la08.3', 'label' => 'Hochwasserschutz'],
            ],
        ],
        [
            'key' => 'la09',
            'nr' => 'LA 09',
            'title' => 'Einsatzgrundlagen',
            'items' => [
                ['key' => 'la09.1', 'label' => 'THW im Einsatz'],
                ['key' => 'la09.2', 'label' => 'Allgemeine Verhaltensgrundlagen im Einsatz'],
                ['key' => 'la09.3', 'label' => 'Einsatzvor- und Nachbereitung'],
                ['key' => 'la09.4', 'label' => 'Sprechfunken'],
                ['key' => 'la09.5', 'label' => 'Verhalten an der Einsatzstelle'],
                ['key' => 'la09.6', 'label' => 'Gefahren an der Einsatzstelle'],
                ['key' => 'la09.7', 'label' => 'Psychosoziale Notfallversorgung'],
                ['key' => 'la09.8', 'label' => 'Umgang mit Medien'],
            ],
        ],
        [
            'key' => 'la10',
            'nr' => 'LA 10',
            'title' => 'Grundlagen der Rettung und Bergung',
            'items' => [
                ['key' => 'la10.1', 'label' => 'Rettungsmittel'],
                ['key' => 'la10.2', 'label' => 'Grundlagen der Rettungsmethoden'],
                ['key' => 'la10.3', 'label' => 'Die 5 Phasen der Rettung und Bergung'],
                ['key' => 'la10.4', 'label' => 'Rettung aus Trümmern'],
                ['key' => 'la10.5', 'label' => 'Überwinden von Hindernissen'],
                ['key' => 'la10.6', 'label' => 'Akutbetreuung: Psychische Erste Hilfe'],
                ['key' => 'la10.7', 'label' => 'Brandschutz'],
            ],
        ],
    ];

    public const ZUSATZAUSBILDUNGEN = [
        ['key' => 'zusatz.zeltaufbau', 'label' => 'Zeltaufbau'],
        ['key' => 'zusatz.erste_hilfe', 'label' => 'Erste Hilfe'],
        ['key' => 'zusatz.cbrn', 'label' => 'CBRN'],
        ['key' => 'zusatz.ent_psnv', 'label' => 'ENT / PSNV'],
        ['key' => 'zusatz.g26', 'label' => 'G26.1'],
        ['key' => 'zusatz.funk', 'label' => 'Funk'],
        ['key' => 'zusatz.knotenkunde', 'label' => 'Knotenkunde'],
        ['key' => 'zusatz.sonder', 'label' => 'Sonderausbildung'],
    ];

    /**
     * Return the set of valid keys (sub-items + zusatz) that can be toggled.
     */
    public static function validItemKeys(): array
    {
        $keys = [];
        foreach (self::LEHRABSCHNITTE as $la) {
            foreach ($la['items'] as $item) {
                $keys[] = $item['key'];
            }
        }
        foreach (self::ZUSATZAUSBILDUNGEN as $item) {
            $keys[] = $item['key'];
        }
        return $keys;
    }

    /**
     * Return the set of valid section (LA) keys — used for bulk toggle.
     */
    public static function validSectionKeys(): array
    {
        return array_column(self::LEHRABSCHNITTE, 'key');
    }

    /**
     * Resolve all sub-item keys that belong to a section.
     */
    public static function itemKeysForSection(string $sectionKey): array
    {
        foreach (self::LEHRABSCHNITTE as $la) {
            if ($la['key'] === $sectionKey) {
                return array_column($la['items'], 'key');
            }
        }
        return [];
    }

    /**
     * Return the set of completed item keys for a user.
     */
    public function completedKeys(User $user): array
    {
        return UserTrainingProgress::where('user_id', $user->id)
            ->pluck('item_key')
            ->all();
    }

    /**
     * Aggregated overview: total items / completed / percent across LA + Zusatz.
     */
    public function overview(User $user): array
    {
        $completed = $this->completedKeys($user);
        $completedSet = array_flip($completed);

        $laItemTotal = 0;
        $laItemDone = 0;
        foreach (self::LEHRABSCHNITTE as $la) {
            foreach ($la['items'] as $item) {
                $laItemTotal++;
                if (isset($completedSet[$item['key']])) {
                    $laItemDone++;
                }
            }
        }

        $zusatzTotal = count(self::ZUSATZAUSBILDUNGEN);
        $zusatzDone = 0;
        foreach (self::ZUSATZAUSBILDUNGEN as $item) {
            if (isset($completedSet[$item['key']])) {
                $zusatzDone++;
            }
        }

        $total = $laItemTotal + $zusatzTotal;
        $done = $laItemDone + $zusatzDone;

        return [
            'completed_keys' => $completed,
            'la_total' => $laItemTotal,
            'la_done' => $laItemDone,
            'la_percent' => $laItemTotal > 0 ? (int) round(($laItemDone / $laItemTotal) * 100) : 0,
            'zusatz_total' => $zusatzTotal,
            'zusatz_done' => $zusatzDone,
            'zusatz_percent' => $zusatzTotal > 0 ? (int) round(($zusatzDone / $zusatzTotal) * 100) : 0,
            'total' => $total,
            'done' => $done,
            'percent' => $total > 0 ? (int) round(($done / $total) * 100) : 0,
        ];
    }
}
