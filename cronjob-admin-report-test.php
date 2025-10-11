<?php
/**
 * Admin-Report Cronjob - TEST
 * Läuft täglich um 08:00 Uhr
 * 
 * Plesk Cronjob einrichten:
 * - Minute: 0
 * - Stunde: 8
 * - Tag: *
 * - Monat: *
 * - Wochentag: *
 */

// Fehlerbehandlung
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Logging
$logFile = __DIR__ . '/storage/logs/admin-report-test.log';
$logMessage = "[" . date('Y-m-d H:i:s') . "] [TEST] Admin-Report gestartet\n";

// Laravel Bootstrap
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

try {
    // Führe Admin-Report aus (Test-E-Mail)
    $exitCode = $kernel->call('admin:daily-report', [
        'email' => 'niclasreutter@icloud.com' // Gleiche E-Mail für Test
    ]);
    
    if ($exitCode === 0) {
        $logMessage .= "[" . date('Y-m-d H:i:s') . "] [TEST] Admin-Report erfolgreich gesendet\n";
    } else {
        $logMessage .= "[" . date('Y-m-d H:i:s') . "] [TEST] Admin-Report fehlgeschlagen (Exit Code: $exitCode)\n";
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

echo "[TEST] Admin-Report Cronjob ausgeführt\n";
?>
