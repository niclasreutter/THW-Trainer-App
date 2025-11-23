<?php
// Lese die practice.blade.php
$practice = file_get_contents('/Users/niclasreutter/THW-Trainer-App/resources/views/practice.blade.php');

// Ersetze die wichtigsten Routes und Strings für Lehrgänge
$lehrgaenge_practice = $practice;

// Route zum Submit ändern
$lehrgaenge_practice = str_replace(
    "route('practice.submit')",
    "route('lehrgaenge.submit', \$question->lehrgang_slug)",
    $lehrgaenge_practice
);

// Title ändern
$lehrgaenge_practice = str_replace(
    "@section('title', 'THW Theorie üben",
    "@section('title', \$question->lehrgang . ' - Üben",
    $lehrgaenge_practice
);

// Practice-Menu Links ändern
$lehrgaenge_practice = str_replace(
    "route('practice.menu')",
    "route('lehrgaenge.index')",
    $lehrgaenge_practice
);

// Bookmark Routes (Lehrgänge haben keine bookmarks)
$lehrgaenge_practice = str_replace(
    "route('bookmarks.toggle')",
    "'javascript:void(0)'",
    $lehrgaenge_practice
);

// Speichere die neue Datei
file_put_contents(
    '/Users/niclasreutter/THW-Trainer-App/resources/views/lehrgaenge/practice.blade.php',
    $lehrgaenge_practice
);

echo "✅ lehrgaenge/practice.blade.php erstellt!\n";
echo "📊 Größe: " . strlen($lehrgaenge_practice) . " bytes\n";
echo "📝 Zeilen: " . substr_count($lehrgaenge_practice, "\n") . "\n";
