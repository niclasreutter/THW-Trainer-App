<?php
/**
 * Cronjob Script für tägliche Streak-Reset - TEST
 *
 * WICHTIG: Dieses Script sollte täglich um 00:01 Uhr ausgeführt werden (nach Mitternacht)
 *
 * LOGIK:
 * - Prüft ob User GESTERN die Mindestaktivität erreicht haben
 * - Mindestaktivität: 10 Fragen beantwortet ODER 1 Prüfung absolviert
 * - Wenn NEIN (last_activity_date < gestern), wird Streak zurückgesetzt
 * - last_activity_date wird nur gesetzt wenn Mindestaktivität erreicht (GamificationService)
 *
 * BEISPIEL:
 * Heute ist Mittwoch 00:01 Uhr
 * - User A hat Dienstag 10+ Fragen beantwortet → Streak bleibt ✓
 * - User B hat Dienstag nur 7 Fragen beantwortet → Streak wird zurückgesetzt (falls kein Freeze)
 * - User C hat Dienstag eine Prüfung absolviert → Streak bleibt ✓
 * - User D hat nicht gelernt, hat aber Freeze verfügbar → Freeze wird automatisch eingesetzt ✓
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

// Datums-Simulation: Optional ein Fake-Datum übergeben
// Verwendung: php cronjob-daily-reset-test.php 2026-03-10
if (isset($argv[1])) {
    $fakeDate = $argv[1];
    try {
        $parsedDate = \Carbon\Carbon::parse($fakeDate)->startOfDay()->addHours(0)->addMinutes(1);
        \Carbon\Carbon::setTestNow($parsedDate);
        echo "\n" . str_repeat("*", 80) . "\n";
        echo "  ZEITSIMULATION AKTIV: Heute = {$parsedDate->format('Y-m-d H:i:s')}\n";
        echo str_repeat("*", 80) . "\n\n";
    } catch (Exception $e) {
        echo "[FEHLER] Ungültiges Datum: '{$fakeDate}'. Format: YYYY-MM-DD\n";
        exit(1);
    }
}

// Führe die tägliche Reset-Logik aus
try {
    echo "[" . date('Y-m-d H:i:s') . "] Starte tägliche Streak-Reset-Prüfung (TEST)...\n";

    $today = \Carbon\Carbon::today();
    $yesterday = \Carbon\Carbon::yesterday();
    $streakResetsCount = 0;
    $streakFreezeCount = 0;
    $dailyQuestionsResetsCount = 0;
    $errors = 0;
    $gamificationService = new \App\Services\GamificationService();

    echo "[" . date('Y-m-d H:i:s') . "] Heute: {$today->format('Y-m-d')}\n";
    echo "[" . date('Y-m-d H:i:s') . "] Gestern: {$yesterday->format('Y-m-d')}\n";

    // Debug: Zeige alle Benutzer mit Daily Questions oder Streak
    $allUsers = \App\Models\User::all();
    echo "[" . date('Y-m-d H:i:s') . "] DEBUG: Alle Benutzer: {$allUsers->count()}\n\n";

    foreach ($allUsers as $debugUser) {
        $lastActivity = $debugUser->last_activity_date ? \Carbon\Carbon::parse($debugUser->last_activity_date) : null;
        $lastActivityStr = $lastActivity ? $lastActivity->format('Y-m-d') : 'NIE';
        $dailyQuestionsDate = $debugUser->daily_questions_date ? \Carbon\Carbon::parse($debugUser->daily_questions_date)->format('Y-m-d') : 'NIE';

        $streakAtRisk = ($debugUser->streak_days > 0 && (!$lastActivity || $lastActivity->lt($yesterday)));
        $freezeAvailable = $streakAtRisk ? $gamificationService->getStreakFreezeStatus($debugUser)['remaining'] : 0;
        $willResetStreak = $streakAtRisk ? ($freezeAvailable > 0 ? 'FREEZE' : 'JA') : 'NEIN';
        $willResetDaily = ($debugUser->daily_questions_date && \Carbon\Carbon::parse($debugUser->daily_questions_date)->lt($today)) ? 'JA' : 'NEIN';

        echo "[" . date('Y-m-d H:i:s') . "] DEBUG: {$debugUser->name}\n";
        echo "  → Streak: {$debugUser->streak_days} Tage (Reset: {$willResetStreak})\n";
        echo "  → Daily Questions: {$debugUser->daily_questions_solved}/20 (Datum: {$dailyQuestionsDate}, Reset: {$willResetDaily})\n";
        echo "  → Letzte Aktivität: {$lastActivityStr}\n\n";
    }

    echo str_repeat("=", 80) . "\n";
    echo "STARTE RESET-PROZESS\n";
    echo str_repeat("=", 80) . "\n\n";

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

            // 2. STREAK: Prüfe ob Mindestaktivität gestern NICHT erreicht wurde
            if ($user->streak_days > 0) {
                if (!$lastActivity || $lastActivity->lt($yesterday)) {
                    // Prüfe ob Streak Freeze verfügbar ist
                    $freezeStatus = $gamificationService->getStreakFreezeStatus($user);

                    if ($freezeStatus['remaining'] > 0) {
                        // Auto-Freeze: Streak wird geschützt
                        $freezeResult = $gamificationService->applyManualFreeze($user);

                        if ($freezeResult['success']) {
                            $streakFreezeCount++;
                            $changed = false; // applyManualFreeze speichert bereits
                            echo "[" . date('Y-m-d H:i:s') . "] Streak Freeze auto-eingesetzt: {$user->name} ({$user->email})\n";
                            echo "  → Streak: {$user->streak_days} Tage (geschützt)\n";
                            echo "  → Verbleibende Freezes: {$freezeResult['remaining']}\n";

                            // E-Mail-Benachrichtigung senden
                            if ($user->email_consent) {
                                try {
                                    \Illuminate\Support\Facades\Mail::to($user->email)
                                        ->send(new \App\Mail\StreakFreezeActivatedMail(
                                            $user,
                                            $user->streak_days,
                                            $freezeResult['remaining']
                                        ));
                                    echo "  → Freeze-Benachrichtigung gesendet an: {$user->email}\n";
                                } catch (Exception $mailError) {
                                    echo "  → WARNUNG: E-Mail konnte nicht gesendet werden: " . $mailError->getMessage() . "\n";
                                }
                            }
                        } else {
                            // Freeze fehlgeschlagen - Streak resetten
                            $oldStreak = $user->streak_days;
                            $user->streak_days = 0;
                            $changed = true;
                            $streakResetsCount++;
                            echo "[" . date('Y-m-d H:i:s') . "] Streak zurückgesetzt (Freeze fehlgeschlagen): {$user->name} ({$user->email})\n";
                            echo "  → Streak: {$oldStreak} → 0 Tage\n";
                            echo "  → Grund: {$freezeResult['message']}\n";

                            // E-Mail: Streak verloren
                            if ($user->email_consent) {
                                try {
                                    \Illuminate\Support\Facades\Mail::to($user->email)
                                        ->send(new \App\Mail\StreakLostMail($user, $oldStreak));
                                    echo "  → Streak-Verlust-Benachrichtigung gesendet an: {$user->email}\n";
                                } catch (Exception $mailError) {
                                    echo "  → WARNUNG: E-Mail konnte nicht gesendet werden: " . $mailError->getMessage() . "\n";
                                }
                            }
                        }
                    } else {
                        // Kein Freeze verfügbar - Streak resetten
                        $oldStreak = $user->streak_days;
                        $user->streak_days = 0;
                        $changed = true;
                        $streakResetsCount++;

                        echo "[" . date('Y-m-d H:i:s') . "] Streak zurückgesetzt: {$user->name} ({$user->email})\n";
                        echo "  → Streak: {$oldStreak} → 0 Tage\n";
                        echo "  → Letzte Aktivität: {$lastActivityStr}\n";

                        // E-Mail: Streak verloren
                        if ($user->email_consent) {
                            try {
                                \Illuminate\Support\Facades\Mail::to($user->email)
                                    ->send(new \App\Mail\StreakLostMail($user, $oldStreak));
                                echo "  → Streak-Verlust-Benachrichtigung gesendet an: {$user->email}\n";
                            } catch (Exception $mailError) {
                                echo "  → WARNUNG: E-Mail konnte nicht gesendet werden: " . $mailError->getMessage() . "\n";
                            }
                        }
                    }
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

    echo "\n" . str_repeat("=", 80) . "\n";
    echo "[" . date('Y-m-d H:i:s') . "] Tägliche Reset-Prüfung abgeschlossen!\n";
    echo "[" . date('Y-m-d H:i:s') . "] Daily Questions zurückgesetzt: {$dailyQuestionsResetsCount}\n";
    echo "[" . date('Y-m-d H:i:s') . "] Streaks zurückgesetzt: {$streakResetsCount}\n";
    echo "[" . date('Y-m-d H:i:s') . "] Streak Freezes auto-eingesetzt: {$streakFreezeCount}\n";
    echo "[" . date('Y-m-d H:i:s') . "] Fehler: {$errors}\n";
    echo str_repeat("=", 80) . "\n";

} catch (Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] FEHLER: " . $e->getMessage() . "\n";
    echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "[" . date('Y-m-d H:i:s') . "] Script beendet.\n";
?>
