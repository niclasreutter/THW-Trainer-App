<?php
/**
 * Cronjob Script für Lernsession-Lifecycle - PRODUKTION
 *
 * LOGIK:
 * - Generiert Instanzen für wiederkehrende Sessions (nächste 14 Tage)
 * - Aktiviert scheduled Instanzen wenn Startzeit erreicht
 * - Schließt aktive Instanzen ab wenn Endzeit erreicht (Winner + Lootbox)
 *
 * CRONJOB: */5 * * * * /usr/bin/php /path/to/cronjob-lernsession-prod.php
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

echo "[" . date('Y-m-d H:i:s') . "] Laravel-Verzeichnis gefunden: $laravelPath (PRODUKTION)\n";

chdir($laravelPath);
putenv('APP_ENV=production');

require_once $laravelPath . '/vendor/autoload.php';
$app = require_once $laravelPath . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "[" . date('Y-m-d H:i:s') . "] Starte Lernsession-Lifecycle (PRODUKTION)...\n";

    $service = new \App\Services\LernsessionService();

    // 1. Instanzen für wiederkehrende Sessions generieren
    $recurringSessions = \App\Models\LearningSession::where('schedule_type', 'recurring')
        ->where('is_active', true)
        ->get();

    $generated = 0;
    foreach ($recurringSessions as $session) {
        $before = $session->instances()->count();
        $service->generateInstances($session);
        $after = $session->instances()->count();
        $generated += ($after - $before);
    }
    echo "[" . date('Y-m-d H:i:s') . "]   Instanzen generiert: {$generated}\n";

    // 2. Scheduled Instanzen aktivieren
    $toActivate = \App\Models\LearningSessionInstance::where('status', 'scheduled')
        ->where('starts_at', '<=', now())
        ->get();

    $activated = 0;
    foreach ($toActivate as $instance) {
        $service->activateInstance($instance);
        $activated++;
    }
    echo "[" . date('Y-m-d H:i:s') . "]   Instanzen aktiviert: {$activated}\n";

    // 3. Aktive Instanzen abschließen
    $toComplete = \App\Models\LearningSessionInstance::where('status', 'active')
        ->where('ends_at', '<=', now())
        ->get();

    $completed = 0;
    foreach ($toComplete as $instance) {
        $result = $service->completeInstance($instance);
        $winner = $result['winner'];
        echo "[" . date('Y-m-d H:i:s') . "]   Session \"{$instance->learningSession->title}\" abgeschlossen"
            . ($winner ? " - Gewinner: {$winner->name}" : " - Kein Gewinner")
            . " ({$result['participants_count']} Teilnehmer)\n";
        $completed++;
    }
    echo "[" . date('Y-m-d H:i:s') . "]   Instanzen abgeschlossen: {$completed}\n";

    echo "[" . date('Y-m-d H:i:s') . "] FERTIG\n";

} catch (\Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] FEHLER: " . $e->getMessage() . "\n";
    echo "  Datei: " . $e->getFile() . ":" . $e->getLine() . "\n";
    exit(1);
}
