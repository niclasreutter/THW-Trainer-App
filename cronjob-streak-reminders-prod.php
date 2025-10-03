<?php
/**
 * Cronjob Script für Streak-Erinnerungs-E-Mails - PRODUKTION
 * Dieses Script kann direkt als PHP-Cronjob in Plesk ausgeführt werden
 * Empfohlene Ausführungszeit: Täglich um 18:00 Uhr
 */

// Produktions-Pfad
$laravelPath = '/var/www/vhosts/web22867.bero-web.de/thw-trainer.de';

if (!file_exists($laravelPath . '/artisan')) {
    echo "[" . date('Y-m-d H:i:s') . "] FEHLER: Laravel-Verzeichnis nicht gefunden: $laravelPath\n";
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

// Führe die Streak-Erinnerungs-Logik direkt aus
try {
    echo "[" . date('Y-m-d H:i:s') . "] Starte Streak-Erinnerungs-Check (PRODUKTION)...\n";
    
    // Direkte Implementierung der Streak-Erinnerungs-Logik
    $today = \Carbon\Carbon::today();
    $emailsSent = 0;
    $errors = 0;
    
    // Finde alle Benutzer die:
    // 1. E-Mail-Zustimmung haben (email_consent = true)
    // 2. Einen Streak > 1 haben
    // 3. Heute noch keine Fragen beantwortet haben (daily_questions_date != heute)
    $users = \App\Models\User::where('email_consent', true)
        ->where('streak_days', '>', 1)
        ->where(function($query) use ($today) {
            $query->whereNull('daily_questions_date')
                  ->orWhere('daily_questions_date', '!=', $today);
        })
        ->get();
    
    echo "[" . date('Y-m-d H:i:s') . "] Gefunden: {$users->count()} Benutzer für Erinnerungen.\n";
    
    foreach ($users as $user) {
        try {
            // Prüfe ob der Benutzer heute wirklich noch keine Fragen beantwortet hat
            $lastDailyActivity = $user->daily_questions_date ? \Carbon\Carbon::parse($user->daily_questions_date) : null;
            
            if (!$lastDailyActivity || $lastDailyActivity->lt($today)) {
                // Sende E-Mail
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\StreakReminderMail($user, $user->streak_days));
                $emailsSent++;
                
                echo "[" . date('Y-m-d H:i:s') . "] E-Mail gesendet an: {$user->name} ({$user->email}) - Streak: {$user->streak_days} Tage\n";
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
