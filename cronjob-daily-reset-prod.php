<?php
/**
 * Cronjob Script für tägliche Streak-Reset - PRODUKTION
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

echo "[" . date('Y-m-d H:i:s') . "] Laravel-Verzeichnis gefunden: $laravelPath (PRODUKTION)\n";

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

// Führe die tägliche Reset-Logik aus
try {
    echo "[" . date('Y-m-d H:i:s') . "] Starte tägliche Streak-Reset-Prüfung (PRODUKTION)...\n";

    $today = \Carbon\Carbon::today();
    $yesterday = \Carbon\Carbon::yesterday();
    $streakResetsCount = 0;
    $dailyQuestionsResetsCount = 0;
    $errors = 0;

    echo "[" . date('Y-m-d H:i:s') . "] Heute: {$today->format('Y-m-d')}\n";
    echo "[" . date('Y-m-d H:i:s') . "] Gestern: {$yesterday->format('Y-m-d')}\n";

    // WICHTIG: Hole ALLE Benutzer, nicht nur die mit Streak > 0
    // Grund: Daily Questions müssen für ALLE User zurückgesetzt werden
    $allUsers = \App\Models\User::all();

    echo "[" . date('Y-m-d H:i:s') . "] Verarbeite {$allUsers->count()} Benutzer...\n";

    foreach ($allUsers as $user) {
        try {
            $lastActivity = $user->last_activity_date ? \Carbon\Carbon::parse($user->last_activity_date) : null;
            $lastActivityStr = $lastActivity ? $lastActivity->format('Y-m-d') : 'NIE';
            $changed = false;

            // 1. RESET DAILY QUESTIONS (für ALLE User)
            // Wenn daily_questions_date < heute ist, dann muss zurückgesetzt werden
            if ($user->daily_questions_date && \Carbon\Carbon::parse($user->daily_questions_date)->lt($today)) {
                $oldDailyQuestions = $user->daily_questions_solved ?? 0;
                $user->daily_questions_solved = 0;
                $user->daily_questions_date = null;
                $changed = true;
                $dailyQuestionsResetsCount++;

                echo "[" . date('Y-m-d H:i:s') . "] Daily Questions zurückgesetzt: {$user->name} ({$user->email})\n";
                echo "  → Daily Questions: {$oldDailyQuestions} → 0\n";
                echo "  → Letzte Aktivität: {$lastActivityStr}\n";
            }

            // 2. RESET STREAK (nur für User die gestern NICHT aktiv waren)
            if ($user->streak_days > 0) {
                if (!$lastActivity || $lastActivity->lt($yesterday)) {
                    $oldStreak = $user->streak_days;
                    $user->streak_days = 0;
                    $changed = true;
                    $streakResetsCount++;

                    echo "[" . date('Y-m-d H:i:s') . "] Streak zurückgesetzt: {$user->name} ({$user->email})\n";
                    echo "  → Streak: {$oldStreak} → 0 Tage\n";
                    echo "  → Letzte Aktivität: {$lastActivityStr}\n";
                }
            }

            // Speichere nur wenn etwas geändert wurde
            if ($changed) {
                $user->save();
            }

        } catch (Exception $e) {
            $errors++;
            echo "[" . date('Y-m-d H:i:s') . "] FEHLER bei {$user->email}: " . $e->getMessage() . "\n";
        }
    }

    echo "[" . date('Y-m-d H:i:s') . "] Tägliche Reset-Prüfung abgeschlossen!\n";
    echo "[" . date('Y-m-d H:i:s') . "] Daily Questions zurückgesetzt: {$dailyQuestionsResetsCount}\n";
    echo "[" . date('Y-m-d H:i:s') . "] Streaks zurückgesetzt: {$streakResetsCount}\n";
    echo "[" . date('Y-m-d H:i:s') . "] Fehler: {$errors}\n";

} catch (Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] FEHLER: " . $e->getMessage() . "\n";
    echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "[" . date('Y-m-d H:i:s') . "] Script beendet.\n";
?>
