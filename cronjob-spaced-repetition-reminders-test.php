<?php
/**
 * Cronjob Script für Spaced-Repetition-Erinnerungs-E-Mails - TEST
 *
 * Sendet E-Mails an User mit fälligen Wiederholungen
 * Empfohlene Ausführungszeit: Täglich um 08:00 Uhr
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

// Führe die Spaced-Repetition-Erinnerungs-Logik aus
try {
    echo "[" . date('Y-m-d H:i:s') . "] Starte Spaced-Repetition-Erinnerungs-Check (TEST)...\n";

    $srService = new \App\Services\SpacedRepetitionService();
    $emailsSent = 0;
    $errors = 0;

    // Debug: Zeige alle Benutzer mit E-Mail-Zustimmung
    $allUsersWithConsent = \App\Models\User::where('email_consent', true)->get();
    echo "[" . date('Y-m-d H:i:s') . "] DEBUG: Alle Benutzer mit E-Mail-Zustimmung: {$allUsersWithConsent->count()}\n";

    foreach ($allUsersWithConsent as $debugUser) {
        $dueCount = $srService->getDueCount($debugUser->id);
        $stats = $srService->getStats($debugUser->id);
        $willSend = $dueCount > 0 ? 'JA' : 'NEIN';

        echo "[" . date('Y-m-d H:i:s') . "] DEBUG: {$debugUser->name}\n";
        echo "  → Fällige Wiederholungen: {$dueCount}\n";
        echo "  → Morgen fällig: {$stats['due_tomorrow']}\n";
        echo "  → Diese Woche fällig: {$stats['due_this_week']}\n";
        echo "  → Gesamt im System: {$stats['total_in_system']}\n";
        echo "  → Mail senden: {$willSend}\n";
    }

    // Finde alle Benutzer mit E-Mail-Zustimmung
    $users = \App\Models\User::where('email_consent', true)->get();

    echo "[" . date('Y-m-d H:i:s') . "] Gefunden: {$users->count()} Benutzer mit E-Mail-Zustimmung.\n";

    foreach ($users as $user) {
        try {
            $dueCount = $srService->getDueCount($user->id);

            if ($dueCount === 0) {
                continue;
            }

            \Illuminate\Support\Facades\Mail::to($user->email)->send(
                new \App\Mail\SpacedRepetitionReminderMail($user, $dueCount)
            );

            $emailsSent++;

            echo "[" . date('Y-m-d H:i:s') . "] E-Mail gesendet an: {$user->name} ({$user->email})\n";
            echo "  → Fällige Wiederholungen: {$dueCount}\n";

        } catch (Exception $e) {
            $errors++;
            echo "[" . date('Y-m-d H:i:s') . "] FEHLER bei {$user->email}: " . $e->getMessage() . "\n";
        }
    }

    echo "[" . date('Y-m-d H:i:s') . "] Spaced-Repetition-Erinnerungs-Check abgeschlossen!\n";
    echo "[" . date('Y-m-d H:i:s') . "] E-Mails gesendet: {$emailsSent}\n";
    echo "[" . date('Y-m-d H:i:s') . "] Fehler: {$errors}\n";

} catch (Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] FEHLER: " . $e->getMessage() . "\n";
    echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "[" . date('Y-m-d H:i:s') . "] Script beendet.\n";
?>
