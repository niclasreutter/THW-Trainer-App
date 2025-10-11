<?php
/**
 * System-Wartung Cronjob - PRODUKTION
 * Läuft jeden Sonntag um 03:00 Uhr
 * 
 * Plesk Cronjob einrichten:
 * - Minute: 0
 * - Stunde: 3
 * - Tag: *
 * - Monat: *
 * - Wochentag: 0 (Sonntag)
 */

// Fehlerbehandlung
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Logging
$logFile = __DIR__ . '/storage/logs/system-maintenance.log';
$logMessage = "[" . date('Y-m-d H:i:s') . "] System-Wartung gestartet\n";

// Laravel Bootstrap
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

try {
    // Führe System-Wartung aus
    $exitCode = $kernel->call('system:maintenance');
    
    if ($exitCode === 0) {
        $logMessage .= "[" . date('Y-m-d H:i:s') . "] System-Wartung erfolgreich abgeschlossen\n";
    } else {
        $logMessage .= "[" . date('Y-m-d H:i:s') . "] System-Wartung fehlgeschlagen (Exit Code: $exitCode)\n";
    }
    
} catch (Exception $e) {
    $logMessage .= "[" . date('Y-m-d H:i:s') . "] FEHLER: " . $e->getMessage() . "\n";
    $logMessage .= "[" . date('Y-m-d H:i:s') . "] Stack Trace: " . $e->getTraceAsString() . "\n";
}

// Log schreiben
file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);

// Memory cleanup
unset($app, $kernel);
if (function_exists('gc_collect_cycles')) {
    gc_collect_cycles();
}

echo "System-Wartung Cronjob ausgeführt\n";
?>
