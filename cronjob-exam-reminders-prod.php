<?php
/**
 * Cronjob Script für tägliche Prüfungs-Erinnerungen - PRODUKTION
 *
 * Sendet E-Mails an User mit:
 * 1. E-Mail-Zustimmung (email_consent = true)
 * 2. Gesetztem Prüfungsdatum in der Zukunft
 * 3. Noch offenen Fragen für heute (Tagespensum nicht erreicht)
 *
 * Empfohlene Ausführungszeit: Täglich um 18:00 Uhr
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

// Führe die Prüfungs-Erinnerungs-Logik aus
try {
    echo "[" . date('Y-m-d H:i:s') . "] Starte Prüfungs-Erinnerungs-Check (PRODUKTION)...\n";

    $today = \Carbon\Carbon::today();
    $emailsSent = 0;
    $skippedDone = 0;
    $skippedNoQuestions = 0;
    $errors = 0;

    $totalQuestions = \App\Models\Question::count();
    $threshold = \App\Models\UserQuestionProgress::MASTERY_THRESHOLD;

    echo "[" . date('Y-m-d H:i:s') . "] Gesamtfragen in DB: {$totalQuestions}\n";

    // Finde alle Benutzer die:
    // 1. E-Mail-Zustimmung haben (email_consent = true)
    // 2. Ein Prüfungsdatum in der Zukunft haben
    $users = \App\Models\User::where('email_consent', true)
        ->whereNotNull('exam_date')
        ->where('exam_date', '>', $today)
        ->get();

    echo "[" . date('Y-m-d H:i:s') . "] Gefunden: {$users->count()} Benutzer mit E-Mail-Zustimmung und Prüfungsdatum.\n";

    foreach ($users as $user) {
        try {
            $examDate = \Carbon\Carbon::parse($user->exam_date);
            $daysLeft = (int) $today->diffInDays($examDate, false);

            if ($daysLeft <= 0) {
                continue;
            }

            // Gemeisterte Fragen berechnen
            $masteredCount = \App\Models\UserQuestionProgress::countMastered($user->id);
            $unmasteredCount = $totalQuestions - $masteredCount;

            // Wenn alle Fragen gemeistert sind, nicht senden
            if ($unmasteredCount <= 0) {
                $skippedNoQuestions++;
                echo "[" . date('Y-m-d H:i:s') . "] Übersprungen (alle gemeistert): {$user->name} ({$user->email})\n";
                continue;
            }

            // Tages-Zielwert berechnen (gleiche Logik wie Dashboard)
            $examBuffer = min(7, max(0, $daysLeft - 1));
            $effectiveDays = max(1, $daysLeft - $examBuffer);

            // Fehlerquote aus tatsächlichen Antworten ermitteln
            $statCount = \App\Models\QuestionStatistic::where('user_id', $user->id)->count();
            $statCorrect = \App\Models\QuestionStatistic::where('user_id', $user->id)->where('is_correct', true)->count();
            $accuracy = ($statCount >= 20) ? ($statCorrect / $statCount) : 0.65;
            $errorFactor = 1 / max(0.4, $accuracy);

            // Verbleibende Interaktionen berechnen
            $progressData = \App\Models\UserQuestionProgress::where('user_id', $user->id)->get();
            $remainingInteractions = 0;
            $seenQuestionIds = $progressData->pluck('question_id');

            foreach ($progressData as $prog) {
                if ($prog->consecutive_correct < $threshold) {
                    $remaining = $threshold - $prog->consecutive_correct;
                    $remainingInteractions += $remaining * $errorFactor;
                }
            }

            $unseenCount = $totalQuestions - $seenQuestionIds->count();
            $remainingInteractions += $unseenCount * $threshold * $errorFactor;

            $dailyTarget = max(1, (int) ceil($remainingInteractions / $effectiveDays));

            // Heute bereits beantwortete Fragen
            $todayAnswered = \App\Models\QuestionStatistic::where('user_id', $user->id)
                ->whereDate('created_at', $today)
                ->count();

            // Wenn Tagesziel bereits erreicht, nicht senden
            if ($todayAnswered >= $dailyTarget) {
                $skippedDone++;
                echo "[" . date('Y-m-d H:i:s') . "] Übersprungen (Tagesziel erreicht): {$user->name} ({$user->email}) - {$todayAnswered}/{$dailyTarget}\n";
                continue;
            }

            // Fortschritt berechnen
            $progressPercent = $totalQuestions > 0
                ? round(($masteredCount / $totalQuestions) * 100)
                : 0;

            // E-Mail senden
            \Illuminate\Support\Facades\Mail::to($user->email)->send(
                new \App\Mail\ExamReminderMail($user, [
                    'days_left' => $daysLeft,
                    'daily_target' => $dailyTarget,
                    'today_answered' => $todayAnswered,
                    'mastered_count' => $masteredCount,
                    'total_questions' => $totalQuestions,
                    'progress_percent' => $progressPercent,
                ])
            );

            $emailsSent++;
            $todayRemaining = $dailyTarget - $todayAnswered;

            echo "[" . date('Y-m-d H:i:s') . "] E-Mail gesendet an: {$user->name} ({$user->email})\n";
            echo "  → Prüfung in {$daysLeft} Tagen\n";
            echo "  → Tagespensum: {$dailyTarget} Fragen ({$todayAnswered} erledigt, {$todayRemaining} offen)\n";
            echo "  → Fortschritt: {$masteredCount}/{$totalQuestions} ({$progressPercent}%)\n";

        } catch (Exception $e) {
            $errors++;
            echo "[" . date('Y-m-d H:i:s') . "] FEHLER bei {$user->email}: " . $e->getMessage() . "\n";
        }
    }

    echo "[" . date('Y-m-d H:i:s') . "] Prüfungs-Erinnerungs-Check abgeschlossen!\n";
    echo "[" . date('Y-m-d H:i:s') . "] E-Mails gesendet: {$emailsSent}\n";
    echo "[" . date('Y-m-d H:i:s') . "] Übersprungen (Tagesziel erreicht): {$skippedDone}\n";
    echo "[" . date('Y-m-d H:i:s') . "] Übersprungen (alle gemeistert): {$skippedNoQuestions}\n";
    echo "[" . date('Y-m-d H:i:s') . "] Fehler: {$errors}\n";

} catch (Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] FEHLER: " . $e->getMessage() . "\n";
    echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "[" . date('Y-m-d H:i:s') . "] Script beendet.\n";
?>
