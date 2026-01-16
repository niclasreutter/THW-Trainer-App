<?php
/**
 * Cronjob Script für tägliche Streak-Reset - TEST
 *
 * WICHTIG: Dieses Script sollte täglich um 00:01 Uhr ausgeführt werden (nach Mitternacht)
 *
 * LOGIK:
 * - Prüft ob User GESTERN gelernt haben
 * - Wenn NEIN (last_activity_date < gestern), wird Streak zurückgesetzt
 * - Verwendet last_activity_date (konsistent mit GamificationService)
 *
 * BEISPIEL:
 * Heute ist Mittwoch 00:01 Uhr
 * - User A lernte Dienstag → Streak bleibt ✓
 * - User B lernte Montag → Streak wird zurückgesetzt (1 Tag Pause)
 */

// Finde Laravel-Root (Script liegt im Root)
$laravelPath = realpath(__DIR__);

if (!file_exists($laravelPath . '/artisan')) {
    // Fallback: Prüfe ob Script als absoluter Pfad aufgerufen wurde
    $scriptPath = realpath($_SERVER['SCRIPT_FILENAME'] ?? __FILE__);
    $laravelPath = dirname($scriptPath);
}

if (!file_exists($laravelPath . '/artisan')) {
    echo "[" . date('Y-m-d H:i:s') . "] FEHLER: Laravel-Verzeichnis nicht gefunden\n";
    echo "  Versuchter Pfad: $laravelPath\n";
    echo "  __DIR__: " . __DIR__ . "\n";
    echo "  __FILE__: " . __FILE__ . "\n";
    echo "  SCRIPT_FILENAME: " . ($_SERVER['SCRIPT_FILENAME'] ?? 'N/A') . "\n";
    exit(1);
}

echo "[" . date('Y-m-d H:i:s') . "] Laravel-Verzeichnis gefunden: $laravelPath (TEST)\n";

// Wechsle in das Laravel-Verzeichnis
chdir($laravelPath);

// Setze die Umgebungsvariablen
putenv('APP_ENV=testing');

// Lade Laravel
require_once $laravelPath . '/vendor/autoload.php';

// Erstelle die Laravel Application
$app = require_once $laravelPath . '/bootstrap/app.php';

// Bootstrap die Application
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Führe die tägliche Reset-Logik aus
try {
    echo "[" . date('Y-m-d H:i:s') . "] Starte tägliche Streak-Reset-Prüfung (TEST)...\n";

    $today = \Carbon\Carbon::today();
    $yesterday = \Carbon\Carbon::yesterday();
    $resetsCount = 0;
    $errors = 0;

    echo "[" . date('Y-m-d H:i:s') . "] Heute: {$today->format('Y-m-d')}\n";
    echo "[" . date('Y-m-d H:i:s') . "] Gestern: {$yesterday->format('Y-m-d')}\n";

    // Debug: Zeige alle Benutzer mit Streak > 0
    $allUsersWithStreak = \App\Models\User::where('streak_days', '>', 0)->get();
    echo "[" . date('Y-m-d H:i:s') . "] DEBUG: Alle Benutzer mit Streak > 0: {$allUsersWithStreak->count()}\n";

    foreach ($allUsersWithStreak as $debugUser) {
        $lastActivity = $debugUser->last_activity_date ? \Carbon\Carbon::parse($debugUser->last_activity_date) : null;
        $lastActivityStr = $lastActivity ? $lastActivity->format('Y-m-d') : 'NIE';
        $willReset = !$lastActivity || $lastActivity->lt($yesterday) ? 'JA' : 'NEIN';

        echo "[" . date('Y-m-d H:i:s') . "] DEBUG: {$debugUser->name}\n";
        echo "  → Streak: {$debugUser->streak_days} Tage\n";
        echo "  → Letzte Aktivität: {$lastActivityStr}\n";
        echo "  → Wird zurückgesetzt: {$willReset}\n";
    }

    // Finde alle Benutzer die:
    // 1. Einen Streak > 0 haben
    // 2. Gestern NICHT aktiv waren (last_activity_date < gestern)
    //
    // WICHTIG: Wenn last_activity_date = gestern, dann hat der User gestern gelernt
    // und sein Streak bleibt bestehen (er hat heute noch Zeit bis Mitternacht)
    $users = \App\Models\User::where('streak_days', '>', 0)
        ->where(function($query) use ($yesterday) {
            $query->whereNull('last_activity_date')
                  ->orWhere('last_activity_date', '<', $yesterday);
        })
        ->get();

    echo "[" . date('Y-m-d H:i:s') . "] Gefunden: {$users->count()} Benutzer für Streak-Reset.\n";

    foreach ($users as $user) {
        try {
            // Prüfe ob der Benutzer wirklich gestern nicht aktiv war
            $lastActivity = $user->last_activity_date ? \Carbon\Carbon::parse($user->last_activity_date) : null;
            $lastActivityStr = $lastActivity ? $lastActivity->format('Y-m-d') : 'NIE';

            if (!$lastActivity || $lastActivity->lt($yesterday)) {
                // User war gestern nicht aktiv → Reset Streak
                $oldStreak = $user->streak_days;
                $user->streak_days = 0;

                // Reset Daily Questions Counter
                $oldDailyQuestions = $user->daily_questions_solved;
                $user->daily_questions_solved = 0;
                $user->daily_questions_date = null;

                $user->save();
                $resetsCount++;

                echo "[" . date('Y-m-d H:i:s') . "] Streak zurückgesetzt: {$user->name} ({$user->email})\n";
                echo "  → Streak: {$oldStreak} → 0 Tage\n";
                echo "  → Daily Questions: {$oldDailyQuestions} → 0\n";
                echo "  → Letzte Aktivität: {$lastActivityStr}\n";
            }

        } catch (Exception $e) {
            $errors++;
            echo "[" . date('Y-m-d H:i:s') . "] FEHLER bei {$user->email}: " . $e->getMessage() . "\n";
        }
    }

    echo "[" . date('Y-m-d H:i:s') . "] Tägliche Streak-Reset-Prüfung abgeschlossen!\n";
    echo "[" . date('Y-m-d H:i:s') . "] Streaks zurückgesetzt: {$resetsCount}\n";
    echo "[" . date('Y-m-d H:i:s') . "] Fehler: {$errors}\n";

} catch (Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] FEHLER: " . $e->getMessage() . "\n";
    echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "[" . date('Y-m-d H:i:s') . "] Script beendet.\n";
?>
