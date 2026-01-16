<?php
/**
 * Cronjob Script für Streak-Erinnerungs-E-Mails - TEST
 *
 * Sendet E-Mails an User mit aktivem Streak die heute noch nicht gelernt haben
 * Empfohlene Ausführungszeit: Täglich um 18:00 Uhr
 *
 * WICHTIG: Verwendet last_activity_date (konsistent mit GamificationService)
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

// Führe die Streak-Erinnerungs-Logik aus
try {
    echo "[" . date('Y-m-d H:i:s') . "] Starte Streak-Erinnerungs-Check (TEST)...\n";

    $today = \Carbon\Carbon::today();
    $emailsSent = 0;
    $errors = 0;

    echo "[" . date('Y-m-d H:i:s') . "] Heute: {$today->format('Y-m-d')}\n";

    // Debug: Zeige alle Benutzer mit Streak > 1
    $allUsersWithStreak = \App\Models\User::where('streak_days', '>', 1)->get();
    echo "[" . date('Y-m-d H:i:s') . "] DEBUG: Alle Benutzer mit Streak > 1: {$allUsersWithStreak->count()}\n";

    foreach ($allUsersWithStreak as $debugUser) {
        $lastActivity = $debugUser->last_activity_date ? \Carbon\Carbon::parse($debugUser->last_activity_date) : null;
        $lastActivityStr = $lastActivity ? $lastActivity->format('Y-m-d') : 'NIE';
        $emailConsent = $debugUser->email_consent ? 'JA' : 'NEIN';
        $willSend = (!$lastActivity || $lastActivity->lt($today)) && $debugUser->email_consent ? 'JA' : 'NEIN';

        echo "[" . date('Y-m-d H:i:s') . "] DEBUG: {$debugUser->name}\n";
        echo "  → Streak: {$debugUser->streak_days} Tage\n";
        echo "  → Letzte Aktivität: {$lastActivityStr}\n";
        echo "  → E-Mail-Zustimmung: {$emailConsent}\n";
        echo "  → Mail senden: {$willSend}\n";
    }

    // Finde alle Benutzer die:
    // 1. E-Mail-Zustimmung haben (email_consent = true)
    // 2. Einen Streak > 1 haben (schon mindestens 2 Tage Streak)
    // 3. Heute noch nicht gelernt haben (last_activity_date != heute)
    //
    // WICHTIG: Verwendet last_activity_date (konsistent mit GamificationService)
    $users = \App\Models\User::where('email_consent', true)
        ->where('streak_days', '>', 1)
        ->where(function($query) use ($today) {
            $query->whereNull('last_activity_date')
                  ->orWhere('last_activity_date', '!=', $today);
        })
        ->get();

    echo "[" . date('Y-m-d H:i:s') . "] Gefunden: {$users->count()} Benutzer für Streak-Erinnerungen.\n";

    foreach ($users as $user) {
        try {
            // Prüfe ob der Benutzer heute wirklich noch nicht aktiv war
            $lastActivity = $user->last_activity_date ? \Carbon\Carbon::parse($user->last_activity_date) : null;

            if (!$lastActivity || $lastActivity->lt($today)) {
                // User war heute noch nicht aktiv → Sende Erinnerungs-Mail
                \Illuminate\Support\Facades\Mail::to($user->email)->send(
                    new \App\Mail\StreakReminderMail($user, $user->streak_days)
                );

                $emailsSent++;

                $lastActivityStr = $lastActivity ? $lastActivity->format('Y-m-d') : 'NIE';
                echo "[" . date('Y-m-d H:i:s') . "] E-Mail gesendet an: {$user->name} ({$user->email})\n";
                echo "  → Streak: {$user->streak_days} Tage\n";
                echo "  → Letzte Aktivität: {$lastActivityStr}\n";
            }

        } catch (Exception $e) {
            $errors++;
            echo "[" . date('Y-m-d H:i:s') . "] FEHLER bei {$user->email}: " . $e->getMessage() . "\n";
        }
    }

    echo "[" . date('Y-m-d H:i:s') . "] Streak-Erinnerungs-Check abgeschlossen!\n";
    echo "[" . date('Y-m-d H:i:s') . "] E-Mails gesendet: {$emailsSent}\n";
    echo "[" . date('Y-m-d H:i:s') . "] Fehler: {$errors}\n";

} catch (Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] FEHLER: " . $e->getMessage() . "\n";
    echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "[" . date('Y-m-d H:i:s') . "] Script beendet.\n";
?>
