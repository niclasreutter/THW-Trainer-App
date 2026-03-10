<?php
/**
 * Cronjob Script für Prüfungs-Feedback-Anfragen - TEST
 *
 * Sendet eine Feedback-Anfrage an User deren Prüfung vor einer Woche war.
 * Erstellt einen ExamFeedback-Eintrag mit Token für den Feedback-Link.
 * Prüft:
 * 1. E-Mail-Zustimmung (email_consent = true)
 * 2. Prüfungsdatum = vor 7 Tagen
 * 3. Noch keine Feedback-Anfrage gesendet (exam_feedback_request_sent_at IS NULL)
 *
 * Empfohlene Ausführungszeit: Täglich um 10:00 Uhr
 */

// Finde Laravel-Root (Script liegt im Root)
$laravelPath = realpath(__DIR__);

if (!file_exists($laravelPath . '/artisan')) {
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

// Führe die Feedback-Anfrage-Logik aus
try {
    echo "[" . date('Y-m-d H:i:s') . "] Starte Feedback-Anfrage-Versand (TEST)...\n";

    $oneWeekAgo = \Carbon\Carbon::now('Europe/Berlin')->subDays(7)->toDateString();
    $emailsSent = 0;
    $errors = 0;

    // Finde alle Benutzer die:
    // 1. E-Mail-Zustimmung haben (email_consent = true)
    // 2. Prüfungsdatum = vor 7 Tagen
    // 3. Noch keine Feedback-Anfrage bekommen haben
    $users = \App\Models\User::where('email_consent', true)
        ->whereNotNull('exam_date')
        ->whereDate('exam_date', $oneWeekAgo)
        ->whereNull('exam_feedback_request_sent_at')
        ->get();

    echo "[" . date('Y-m-d H:i:s') . "] Gefunden: {$users->count()} Benutzer mit Prüfung vor einer Woche.\n";

    foreach ($users as $user) {
        try {
            // Erstelle Feedback-Eintrag mit eindeutigem Token
            $token = \Illuminate\Support\Str::random(64);

            \App\Models\ExamFeedback::create([
                'user_id' => $user->id,
                'token' => $token,
            ]);

            $feedbackUrl = url("/exam-feedback/{$token}");

            \Illuminate\Support\Facades\Mail::to($user->email)->send(
                new \App\Mail\ExamFeedbackRequestMail($user, $feedbackUrl)
            );

            // Markiere als gesendet
            $user->exam_feedback_request_sent_at = \Carbon\Carbon::now();
            $user->save();

            $emailsSent++;

            echo "[" . date('Y-m-d H:i:s') . "] Feedback-Anfrage gesendet an: {$user->name} ({$user->email})\n";
            echo "  → Prüfungsdatum: {$user->exam_date->format('d.m.Y')}\n";
            echo "  → Feedback-URL: {$feedbackUrl}\n";

        } catch (Exception $e) {
            $errors++;
            echo "[" . date('Y-m-d H:i:s') . "] FEHLER bei {$user->email}: " . $e->getMessage() . "\n";
        }
    }

    echo "[" . date('Y-m-d H:i:s') . "] Feedback-Anfrage-Versand abgeschlossen!\n";
    echo "[" . date('Y-m-d H:i:s') . "] E-Mails gesendet: {$emailsSent}\n";
    echo "[" . date('Y-m-d H:i:s') . "] Fehler: {$errors}\n";

} catch (Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] FEHLER: " . $e->getMessage() . "\n";
    echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "[" . date('Y-m-d H:i:s') . "] Script beendet.\n";
?>
