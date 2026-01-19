<?php

/**
 * User Count History - PRODUCTION
 * Wrapper für Laravel Artisan Command
 *
 * PLESK KONFIGURATION:
 * - Typ: PHP Skript ausführen
 * - PHP Version: PHP 8.2
 * - Pfad: cronjob-user-count-prod.php
 * - Zeitplan: Täglich um 00:15 Uhr
 *
 * WICHTIG: Dieser Wrapper ruft den Laravel Artisan Command auf
 */

// Wechsle ins Laravel Root-Verzeichnis
chdir(__DIR__);

// PHP Binary Pfad (Plesk nutzt spezifische PHP-Versionen)
// Versuche verschiedene mögliche Pfade
$phpPaths = [
    '/opt/plesk/php/8.2/bin/php',  // Plesk PHP 8.2
    '/opt/plesk/php/8.1/bin/php',  // Plesk PHP 8.1
    '/usr/bin/php8.2',              // Standard PHP 8.2
    '/usr/bin/php',                 // Fallback
];

$phpBinary = null;
foreach ($phpPaths as $path) {
    if (file_exists($path)) {
        $phpBinary = $path;
        break;
    }
}

if ($phpBinary === null) {
    echo "❌ FEHLER: PHP Binary nicht gefunden!\n";
    echo "Versuchte Pfade:\n";
    foreach ($phpPaths as $path) {
        echo "  - {$path}\n";
    }
    exit(1);
}

// Führe Artisan Command aus
$command = escapeshellarg($phpBinary) . ' artisan user-count:record 2>&1';
$output = [];
$returnCode = 0;

exec($command, $output, $returnCode);

// Ausgabe anzeigen
echo implode("\n", $output) . "\n";

// Exit mit dem Return Code des Commands
exit($returnCode);

