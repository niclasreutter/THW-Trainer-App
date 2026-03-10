<?php
/**
 * Test-Script: Sendet alle Prüfungs-Mails an eine bestimmte E-Mail-Adresse
 *
 * Verwendung:
 *   php test-send-exam-mails.php deine@email.de
 *
 * Sendet alle 3 Mail-Typen:
 * 1. ExamReminderMail (Tägliche Erinnerung)
 * 2. ExamGoodLuckMail (Viel Erfolg - Tag vor Prüfung)
 * 3. ExamFeedbackRequestMail (Feedback nach Prüfung)
 */

// E-Mail-Adresse aus Argument lesen
$testEmail = $argv[1] ?? null;

if (!$testEmail) {
    echo "Verwendung: php test-send-exam-mails.php deine@email.de\n";
    exit(1);
}

echo "[" . date('Y-m-d H:i:s') . "] Test-Mail-Versand an: {$testEmail}\n\n";

// Laravel bootstrappen
$laravelPath = realpath(__DIR__);
chdir($laravelPath);
require_once $laravelPath . '/vendor/autoload.php';
$app = require_once $laravelPath . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Benutzer finden oder Dummy erstellen
$user = \App\Models\User::where('email', $testEmail)->first();

if (!$user) {
    echo "Kein User mit E-Mail '{$testEmail}' gefunden.\n";
    echo "Verwende ersten User aus der DB als Vorlage...\n";
    $user = \App\Models\User::first();
    if (!$user) {
        echo "FEHLER: Keine User in der Datenbank!\n";
        exit(1);
    }
}

echo "User: {$user->name} (ID: {$user->id})\n\n";

// 1. ExamReminderMail
try {
    echo "1/3 Sende ExamReminderMail...\n";
    \Illuminate\Support\Facades\Mail::to($testEmail)->send(
        new \App\Mail\ExamReminderMail($user, [
            'days_left' => 14,
            'daily_target' => 25,
            'today_answered' => 8,
            'mastered_count' => 120,
            'total_questions' => 400,
            'progress_percent' => 30,
        ])
    );
    echo "  OK\n";
} catch (Exception $e) {
    echo "  FEHLER: " . $e->getMessage() . "\n";
}

// 2. ExamGoodLuckMail
try {
    echo "2/3 Sende ExamGoodLuckMail...\n";
    \Illuminate\Support\Facades\Mail::to($testEmail)->send(
        new \App\Mail\ExamGoodLuckMail($user)
    );
    echo "  OK\n";
} catch (Exception $e) {
    echo "  FEHLER: " . $e->getMessage() . "\n";
}

// 3. ExamFeedbackRequestMail
try {
    echo "3/3 Sende ExamFeedbackRequestMail...\n";
    $fakeToken = \Illuminate\Support\Str::random(64);
    $feedbackUrl = url("/exam-feedback/{$fakeToken}");
    \Illuminate\Support\Facades\Mail::to($testEmail)->send(
        new \App\Mail\ExamFeedbackRequestMail($user, $feedbackUrl)
    );
    echo "  OK\n";
} catch (Exception $e) {
    echo "  FEHLER: " . $e->getMessage() . "\n";
}

echo "\nFertig! Prüfe dein Postfach ({$testEmail}).\n";
