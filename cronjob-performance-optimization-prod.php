<?php
/**
 * Performance-Optimierung Cronjob - PRODUKTION
 * Läuft alle 6 Stunden
 * 
 * Plesk Cronjob einrichten:
 * - Minute: 0
 * - Stunde: 0,6,12,18
 * - Tag: *
 * - Monat: *
 * - Wochentag: *
 */

// Fehlerbehandlung
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Logging
$logFile = __DIR__ . '/storage/logs/performance-optimization.log';
$logMessage = "[" . date('Y-m-d H:i:s') . "] Performance-Optimierung gestartet\n";

// Laravel Bootstrap
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

try {
    // Führe Performance-Optimierung aus
    $exitCode = $kernel->call('system:performance-optimization');
    
    if ($exitCode === 0) {
        $logMessage .= "[" . date('Y-m-d H:i:s') . "] Performance-Optimierung erfolgreich abgeschlossen\n";
    } else {
        $logMessage .= "[" . date('Y-m-d H:i:s') . "] Performance-Optimierung fehlgeschlagen (Exit Code: $exitCode)\n";
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

echo "Performance-Optimierung Cronjob ausgeführt\n";
?>
