<?php
/**
 * Datenbank-Backup Cronjob - TEST
 * Läuft jeden Sonntag um 02:00 Uhr
 * 
 * Plesk Cronjob einrichten:
 * - Minute: 0
 * - Stunde: 2
 * - Tag: *
 * - Monat: *
 * - Wochentag: 0 (Sonntag)
 */

// Fehlerbehandlung
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Logging
$logFile = __DIR__ . '/storage/logs/database-backup-test.log';
$logMessage = "[" . date('Y-m-d H:i:s') . "] [TEST] Datenbank-Backup gestartet\n";

// Laravel Bootstrap
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

try {
    // Führe Datenbank-Backup aus (Test ohne Komprimierung)
    $exitCode = $kernel->call('database:backup', [
        '--compress' => 'false'
    ]);
    
    if ($exitCode === 0) {
        $logMessage .= "[" . date('Y-m-d H:i:s') . "] [TEST] Datenbank-Backup erfolgreich erstellt\n";
    } else {
        $logMessage .= "[" . date('Y-m-d H:i:s') . "] [TEST] Datenbank-Backup fehlgeschlagen (Exit Code: $exitCode)\n";
    }
    
} catch (Exception $e) {
    $logMessage .= "[" . date('Y-m-d H:i:s') . "] [TEST] FEHLER: " . $e->getMessage() . "\n";
    $logMessage .= "[" . date('Y-m-d H:i:s') . "] [TEST] Stack Trace: " . $e->getTraceAsString() . "\n";
}

// Log schreiben
file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);

// Memory cleanup
unset($app, $kernel);
if (function_exists('gc_collect_cycles')) {
    gc_collect_cycles();
}

echo "[TEST] Datenbank-Backup Cronjob ausgeführt\n";
?>
