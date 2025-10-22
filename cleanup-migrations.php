<?php

// Sicherheitsschlüssel - ÄNDERE DIESEN WERT!
define('CLEANUP_KEY', 'dein-geheimer-schluessel-hier-' . md5('thw-cleanup'));

if (!isset($_GET['key']) || $_GET['key'] !== CLEANUP_KEY) {
    die('❌ Ungültiger Zugriff');
}

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $deleted = DB::table('migrations')
        ->where('migration', 'like', '%shop%')
        ->orWhere('migration', 'like', '%cosmetic%')
        ->delete();

    echo "✅ {$deleted} Shop-Migrations-Einträge aus der migrations-Tabelle entfernt.\n";
    echo "\n⚠️ WICHTIG: Lösche diese Datei jetzt sofort vom Server!\n";
} catch (Exception $e) {
    echo "❌ Fehler: " . $e->getMessage();
}
