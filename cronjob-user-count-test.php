<?php

/**
 * User Count History - TEST VERSION
 * Wrapper für Laravel Artisan Command mit Debug-Ausgabe
 *
 * TEST KONFIGURATION:
 * - Kann manuell aufgerufen werden: php cronjob-user-count-test.php
 * - Zeigt detaillierte Debug-Info
 */

echo "=== USER COUNT HISTORY CRONJOB TEST ===\n\n";
echo "Zeit: " . date('d.m.Y H:i:s') . "\n";
echo "Verzeichnis: " . __DIR__ . "\n\n";

// Wechsle ins Laravel Root-Verzeichnis
chdir(__DIR__);

// PHP Binary Pfad (Plesk nutzt spezifische PHP-Versionen)
$phpPaths = [
    '/opt/plesk/php/8.2/bin/php',  // Plesk PHP 8.2
    '/opt/plesk/php/8.1/bin/php',  // Plesk PHP 8.1
    '/usr/bin/php8.2',              // Standard PHP 8.2
    '/usr/bin/php',                 // Fallback
];

echo "Suche PHP Binary...\n";
$phpBinary = null;
foreach ($phpPaths as $path) {
    echo "  Prüfe: {$path} ... ";
    if (file_exists($path)) {
        echo "✅ GEFUNDEN\n";
        $phpBinary = $path;
        break;
    } else {
        echo "❌ nicht vorhanden\n";
    }
}

if ($phpBinary === null) {
    echo "\n❌ FEHLER: PHP Binary nicht gefunden!\n";
    exit(1);
}

echo "\nVerwende PHP Binary: {$phpBinary}\n\n";
echo "Führe Laravel Artisan Command aus...\n";
echo "Command: {$phpBinary} artisan user-count:record\n\n";
echo "--- OUTPUT ---\n";

// Führe Artisan Command aus
$command = escapeshellarg($phpBinary) . ' artisan user-count:record 2>&1';
$output = [];
$returnCode = 0;

exec($command, $output, $returnCode);

// Ausgabe anzeigen
echo implode("\n", $output) . "\n";

echo "\n--- ENDE ---\n";
echo "Return Code: " . $returnCode . " (" . ($returnCode === 0 ? "Erfolg" : "Fehler") . ")\n";

// Exit mit dem Return Code des Commands
exit($returnCode);

