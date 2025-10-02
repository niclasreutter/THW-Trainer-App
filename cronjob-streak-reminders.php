<?php
/**
 * Cronjob Script für Streak-Erinnerungs-E-Mails
 * Dieses Script kann direkt als PHP-Cronjob in Plesk ausgeführt werden
 * Empfohlene Ausführungszeit: Täglich um 18:00 Uhr
 */

// Dynamischer Pfad - erkennt automatisch Test/Prod Umgebung
$possiblePaths = [
    '/var/www/vhosts/web22867.bero-web.de/thw-trainer.de', // Production
    '/var/www/vhosts/web22867.bero-web.de/test.thw-trainer.de', // Test
];

$laravelPath = null;
foreach ($possiblePaths as $path) {
    if (file_exists($path . '/artisan')) {
        $laravelPath = $path;
        break;
    }
}

if (!$laravelPath) {
    echo "[" . date('Y-m-d H:i:s') . "] FEHLER: Laravel-Verzeichnis nicht gefunden!\n";
    echo "Geprüfte Pfade:\n";
    foreach ($possiblePaths as $path) {
        echo "- $path\n";
    }
    exit(1);
}

echo "[" . date('Y-m-d H:i:s') . "] Laravel-Verzeichnis gefunden: $laravelPath\n";

// Wechsle in das Laravel-Verzeichnis
chdir($laravelPath);

// Setze die Umgebungsvariablen
putenv('APP_ENV=production');

// Lade Laravel
require_once $laravelPath . '/vendor/autoload.php';

// Erstelle die Laravel Application
$app = require_once $laravelPath . '/bootstrap/app.php';

// Bootstrap die Application
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Führe den Command direkt aus
try {
    echo "[" . date('Y-m-d H:i:s') . "] Starte Streak-Erinnerungs-Check...\n";
    
    // Direkter Aufruf der Command-Logik
    $command = new \App\Console\Commands\SendStreakReminders();
    $exitCode = $command->handle();
    
    echo "[" . date('Y-m-d H:i:s') . "] Command beendet mit Exit-Code: $exitCode\n";
    echo "[" . date('Y-m-d H:i:s') . "] Streak-Erinnerungen abgeschlossen.\n";
    
} catch (Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] FEHLER: " . $e->getMessage() . "\n";
    echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "[" . date('Y-m-d H:i:s') . "] Script beendet.\n";
?>
