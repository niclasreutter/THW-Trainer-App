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

echo "Führe Laravel Artisan Command aus...\n";
echo "Command: php artisan user-count:record\n\n";
echo "--- OUTPUT ---\n";

// Führe Artisan Command aus
$output = [];
$returnCode = 0;

exec('php artisan user-count:record 2>&1', $output, $returnCode);

// Ausgabe anzeigen
echo implode("\n", $output) . "\n";

echo "\n--- ENDE ---\n";
echo "Return Code: " . $returnCode . " (" . ($returnCode === 0 ? "Erfolg" : "Fehler") . ")\n";

// Exit mit dem Return Code des Commands
exit($returnCode);
