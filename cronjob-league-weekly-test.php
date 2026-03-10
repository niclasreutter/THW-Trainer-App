<?php
/**
 * Cronjob Script für wöchentliche Liga-Verarbeitung - TEST
 *
 * WICHTIG: Dieses Script sollte JEDEN MONTAG um 00:00 Uhr ausgeführt werden
 * VOR dem täglichen Reset-Script
 *
 * HINWEIS: Dieses Script wird automatisch über den Laravel Scheduler ausgeführt:
 *   php artisan league:process-weekly
 * Der Scheduler wird in routes/console.php konfiguriert (weeklyOn Montag 00:00).
 * Manueller Cronjob ist nicht mehr nötig, wenn `php artisan schedule:run` minütlich läuft.
 *
 * LOGIK:
 * - Verarbeitet Auf- und Abstiege in allen 8 Ligen
 * - Top 5 jeder Liga steigen auf
 * - Letzte 5 jeder Liga steigen ab
 * - Platz 1-3 erhalten Lootboxen (Gold, Silber, Bronze)
 *
 * CRONJOB (manuell, falls Scheduler nicht genutzt): 0 0 * * 1 /usr/bin/php /path/to/cronjob-league-weekly-test.php
 */

// Finde Laravel-Root (Script liegt im Root)
$laravelPath = realpath(__DIR__);

if (!file_exists($laravelPath . '/artisan')) {
    $scriptPath = realpath($_SERVER['SCRIPT_FILENAME'] ?? __FILE__);
    $laravelPath = dirname($scriptPath);
}

if (!file_exists($laravelPath . '/artisan')) {
    echo "[" . date('Y-m-d H:i:s') . "] FEHLER: Laravel-Verzeichnis nicht gefunden\n";
    exit(1);
}

echo "[" . date('Y-m-d H:i:s') . "] Laravel-Verzeichnis gefunden: $laravelPath (TEST)\n";

chdir($laravelPath);
putenv('APP_ENV=testing');

require_once $laravelPath . '/vendor/autoload.php';
$app = require_once $laravelPath . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "[" . date('Y-m-d H:i:s') . "] Starte wöchentliche Liga-Verarbeitung (TEST)...\n";

    $leagueService = new \App\Services\LeagueService();
    $results = $leagueService->processWeeklyLeagues();

    echo "[" . date('Y-m-d H:i:s') . "] Liga-Verarbeitung abgeschlossen:\n";
    echo "  → Aufstiege: {$results['promotions']}\n";
    echo "  → Abstiege: {$results['relegations']}\n";
    echo "  → Lootboxen vergeben: {$results['lootboxes_awarded']}\n";

    echo "[" . date('Y-m-d H:i:s') . "] FERTIG\n";

} catch (\Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] FEHLER: " . $e->getMessage() . "\n";
    echo "  Datei: " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
}
