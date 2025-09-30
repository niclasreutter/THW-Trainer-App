<?php
/**
 * Cronjob Script für Account-Bereinigung
 * Dieses Script kann direkt als PHP-Cronjob in Plesk ausgeführt werden
 */

// Setze den korrekten Pfad
$laravelPath = '/var/www/vhosts/web22867.bero-web.de/test.thw-trainer.de';

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

// Führe den Command aus
try {
    echo "[" . date('Y-m-d H:i:s') . "] Starte Account-Bereinigung...\n";
    
    // Rufe den Laravel Command auf
    $exitCode = \Illuminate\Support\Facades\Artisan::call('accounts:cleanup-unconfirmed');
    
    echo "[" . date('Y-m-d H:i:s') . "] Command beendet mit Exit-Code: $exitCode\n";
    
    // Gib die Ausgabe aus
    $output = \Illuminate\Support\Facades\Artisan::output();
    if (!empty($output)) {
        echo "Ausgabe:\n$output\n";
    }
    
    echo "[" . date('Y-m-d H:i:s') . "] Bereinigung abgeschlossen.\n";
    
} catch (Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] FEHLER: " . $e->getMessage() . "\n";
    echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "[" . date('Y-m-d H:i:s') . "] Script beendet.\n";
?>
