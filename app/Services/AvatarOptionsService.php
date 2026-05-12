<?php

namespace App\Services;

class AvatarOptionsService
{
    const DICEBEAR_BASE = 'https://dicebear.niclas-reutter.de/9.x/avataaars/svg';

    const CATEGORIES = [
        'top' => [
            'label' => 'Frisur',
            'param' => 'top',
            'type'  => 'select',
            'options' => [
                ['value' => 'bob',                  'label' => 'Bob'],
                ['value' => 'bun',                  'label' => 'Bun'],
                ['value' => 'curly',                'label' => 'Curly'],
                ['value' => 'curvy',                'label' => 'Curvy'],
                ['value' => 'dreads',               'label' => 'Dreads'],
                ['value' => 'frida',                'label' => 'Frida'],
                ['value' => 'fro',                  'label' => 'Fro'],
                ['value' => 'froBand',              'label' => 'Fro Band'],
                ['value' => 'longButNotTooLong',    'label' => 'Lang'],
                ['value' => 'miaWallace',           'label' => 'Mia Wallace'],
                ['value' => 'shavedSides',          'label' => 'Sides Rasiert'],
                ['value' => 'straight01',           'label' => 'Glatt 1'],
                ['value' => 'straight02',           'label' => 'Glatt 2'],
                ['value' => 'straightAndStrand',    'label' => 'Glatt & Strähne'],
                ['value' => 'dreads01',             'label' => 'Dreads 1'],
                ['value' => 'dreads02',             'label' => 'Dreads 2'],
                ['value' => 'frizzle',              'label' => 'Frizzle'],
                ['value' => 'shaggy',               'label' => 'Shaggy'],
                ['value' => 'shaggyMullet',         'label' => 'Mullet'],
                ['value' => 'shortCurly',           'label' => 'Kurz Curly'],
                ['value' => 'shortFlat',            'label' => 'Kurz Flat'],
                ['value' => 'shortRound',           'label' => 'Kurz Round'],
                ['value' => 'shortWaved',           'label' => 'Kurz Waved'],
                ['value' => 'sides',                'label' => 'Sides'],
                ['value' => 'theCaesar',            'label' => 'Caesar'],
                ['value' => 'theCaesarAndSidePart', 'label' => 'Caesar Side Part'],
                ['value' => 'bigHair',              'label' => 'Big Hair'],
            ],
        ],
        'eyes' => [
            'label' => 'Augen',
            'param' => 'eyes',
            'type'  => 'select',
            'options' => [
                ['value' => 'default',   'label' => 'Normal'],
                ['value' => 'happy',     'label' => 'Glücklich'],
                ['value' => 'hearts',    'label' => 'Herzen'],
                ['value' => 'wink',      'label' => 'Zwinkern'],
                ['value' => 'surprised', 'label' => 'Überrascht'],
                ['value' => 'squint',    'label' => 'Schmunzeln'],
                ['value' => 'side',      'label' => 'Seitlich'],
                ['value' => 'winkWacky', 'label' => 'Wacky Wink'],
            ],
        ],
        'eyebrows' => [
            'label' => 'Augenbrauen',
            'param' => 'eyebrows',
            'type'  => 'select',
            'options' => [
                ['value' => 'default',              'label' => 'Standard'],
                ['value' => 'defaultNatural',       'label' => 'Natürlich'],
                ['value' => 'flatNatural',          'label' => 'Flach'],
                ['value' => 'raisedExcited',        'label' => 'Hoch'],
                ['value' => 'raisedExcitedNatural', 'label' => 'Hoch Natürlich'],
                ['value' => 'upDown',               'label' => 'Up & Down'],
                ['value' => 'upDownNatural',        'label' => 'Up & Down Natürlich'],
            ],
        ],
        'mouth' => [
            'label' => 'Mund',
            'param' => 'mouth',
            'type'  => 'select',
            'options' => [
                ['value' => 'default', 'label' => 'Normal'],
                ['value' => 'smile',   'label' => 'Lächeln'],
                ['value' => 'tongue',  'label' => 'Zunge'],
                ['value' => 'twinkle', 'label' => 'Twinkle'],
            ],
        ],
        'skinColor' => [
            'label' => 'Hautfarbe',
            'param' => 'skinColor',
            'type'  => 'color',
            'options' => [
                ['value' => 'ffdbb4', 'label' => 'Pale',  'swatch' => '#ffdbb4'],
                ['value' => 'edb98a', 'label' => 'Hell',  'swatch' => '#edb98a'],
                ['value' => 'fd9841', 'label' => 'Yellow','swatch' => '#fd9841'],
                ['value' => 'd08b5b', 'label' => 'Tan',   'swatch' => '#d08b5b'],
                ['value' => 'ae5d29', 'label' => 'Braun', 'swatch' => '#ae5d29'],
                ['value' => '614335', 'label' => 'Dunkel','swatch' => '#614335'],
                ['value' => '2c1b18', 'label' => 'Schwarz','swatch' => '#2c1b18'],
            ],
        ],
        'hairColor' => [
            'label' => 'Haarfarbe',
            'param' => 'hairColor',
            'type'  => 'color',
            'options' => [
                ['value' => '2c1b18', 'label' => 'Schwarz',     'swatch' => '#2c1b18'],
                ['value' => '4a312c', 'label' => 'Dunkelbraun', 'swatch' => '#4a312c'],
                ['value' => '724133', 'label' => 'Braun',       'swatch' => '#724133'],
                ['value' => 'a55728', 'label' => 'Auburn',      'swatch' => '#a55728'],
                ['value' => 'b58143', 'label' => 'Blond',       'swatch' => '#b58143'],
                ['value' => 'd6b370', 'label' => 'Hellblond',   'swatch' => '#d6b370'],
                ['value' => 'c93305', 'label' => 'Rot',         'swatch' => '#c93305'],
                ['value' => 'e8e1e1', 'label' => 'Silber',      'swatch' => '#e8e1e1'],
                ['value' => 'ecdcbf', 'label' => 'Platin',      'swatch' => '#ecdcbf'],
                ['value' => 'f59797', 'label' => 'Pastell',     'swatch' => '#f59797'],
            ],
        ],
    ];

    /**
     * Whitelist-Check: Ist der Wert für diesen Parameter erlaubt?
     */
    public static function validate(string $param, string $value): bool
    {
        $category = self::CATEGORIES[$param] ?? null;
        if (!$category) {
            return false;
        }

        foreach ($category['options'] as $opt) {
            if ($opt['value'] === $value) {
                return true;
            }
        }

        return false;
    }

    /**
     * Baut eine DiceBear-Thumbnail-URL: gleicher User-Seed,
     * aber nur die übergebene Property dominiert.
     */
    public static function buildThumbnailUrl(string $seed, string $param, string $value, array $extraParams = []): string
    {
        $params = array_merge([
            'radius' => 50,
            'seed' => $seed,
            'backgroundType' => 'gradientLinear',
            'backgroundColor' => '00337f,0055cc',
            'backgroundRotation' => 135,
            'accessoriesProbability' => 0,
            'facialHairProbability' => 0,
            'eyes' => 'happy',
            'mouth' => 'smile',
        ], $extraParams, [$param => $value]);

        return self::DICEBEAR_BASE . '?' . http_build_query($params);
    }

    /**
     * Liefert alle Kategorien angereichert mit fertigen Thumbnail-URLs
     * für den jeweiligen User-Seed.
     */
    public static function buildCategoriesForUser(string $seed, array $currentParams = []): array
    {
        $result = [];

        foreach (self::CATEGORIES as $key => $cat) {
            $optionsWithThumbs = array_map(function ($opt) use ($seed, $cat, $currentParams) {
                $extra = self::buildContextParams($cat['param'], $currentParams);
                $opt['previewUrl'] = self::buildThumbnailUrl($seed, $cat['param'], $opt['value'], $extra);
                return $opt;
            }, $cat['options']);

            $result[] = [
                'key'     => $key,
                'label'   => $cat['label'],
                'param'   => $cat['param'],
                'type'    => $cat['type'],
                'shop'    => false,
                'options' => $optionsWithThumbs,
            ];
        }

        return $result;
    }

    /**
     * Wählt sinnvolle Kontext-Parameter für die Thumbnail-Generierung,
     * damit die zu wählende Property gut sichtbar bleibt.
     * Beispiel: Bei Haarfarbe-Thumbnails brauchen wir eine Frisur, die Haar zeigt.
     */
    private static function buildContextParams(string $targetParam, array $currentParams): array
    {
        $extra = [];

        if ($targetParam === 'hairColor') {
            $extra['top'] = $currentParams['top'] ?? 'shortFlat';
        }
        if ($targetParam === 'skinColor') {
            $extra['top'] = $currentParams['top'] ?? 'shortFlat';
            $extra['hairColor'] = $currentParams['hairColor'] ?? '4a312c';
        }
        if (in_array($targetParam, ['eyes', 'eyebrows', 'mouth'], true)) {
            if (!empty($currentParams['skinColor'])) {
                $extra['skinColor'] = $currentParams['skinColor'];
            }
            if (!empty($currentParams['hairColor'])) {
                $extra['hairColor'] = $currentParams['hairColor'];
            }
            $extra['top'] = $currentParams['top'] ?? 'shortFlat';
        }
        if ($targetParam === 'top') {
            if (!empty($currentParams['hairColor'])) {
                $extra['hairColor'] = $currentParams['hairColor'];
            }
            if (!empty($currentParams['skinColor'])) {
                $extra['skinColor'] = $currentParams['skinColor'];
            }
        }

        return $extra;
    }
}
