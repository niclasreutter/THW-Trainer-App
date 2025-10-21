<?php
/**
 * Cronjob Script für Inaktivitäts-Erinnerungs-E-Mails - PRODUKTION
 * Dieses Script kann direkt als PHP-Cronjob in Plesk ausgeführt werden
 * Empfohlene Ausführungszeit: Täglich um 10:00 Uhr
 */

// Produktions-Pfad (Script liegt im Laravel-Root)
$laravelPath = __DIR__;

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

// Führe die Inaktivitäts-Erinnerungs-Logik direkt aus
try {
    echo "[" . date('Y-m-d H:i:s') . "] Starte Inaktivitäts-Erinnerungs-Check (PRODUKTION)...\n";
    
    $inactiveDays = 4; // Mindestens 4 Tage inaktiv
    $inactiveThreshold = \Carbon\Carbon::now()->subDays($inactiveDays);
    $emailsSent = 0;
    $errors = 0;
    $skipped = 0;
    
    // Debug: Zeige alle Benutzer
    $allUsers = \App\Models\User::all();
    echo "[" . date('Y-m-d H:i:s') . "] DEBUG: Gesamt Benutzer: {$allUsers->count()}\n";
    
    // Finde alle Benutzer die:
    // 1. E-Mail-Zustimmung haben (email_consent = true)
    // 2. Seit mindestens 4 Tagen nicht aktiv waren (last_activity_date < Schwellenwert)
    // 3. Noch keine Inaktivitäts-Mail bekommen haben (inactive_reminder_sent_at IS NULL)
    //    ODER die letzte Mail ist länger als 30 Tage her (um nicht zu spammen)
    $users = \App\Models\User::where('email_consent', true)
        ->whereNotNull('last_activity_date')
        ->where('last_activity_date', '<', $inactiveThreshold)
        ->where(function($query) {
            $query->whereNull('inactive_reminder_sent_at')
                  ->orWhere('inactive_reminder_sent_at', '<', \Carbon\Carbon::now()->subDays(30));
        })
        ->get();
    
    echo "[" . date('Y-m-d H:i:s') . "] Gefunden: {$users->count()} inaktive Benutzer für Erinnerungen.\n";
    
    // Debug-Info für alle gefundenen User
    foreach ($users as $debugUser) {
        $lastActivity = \Carbon\Carbon::parse($debugUser->last_activity_date);
        $daysInactive = $lastActivity->diffInDays(\Carbon\Carbon::now());
        $reminderSent = $debugUser->inactive_reminder_sent_at ? 
            \Carbon\Carbon::parse($debugUser->inactive_reminder_sent_at)->format('Y-m-d') : 'NIEMALS';
        
        echo "[" . date('Y-m-d H:i:s') . "] DEBUG: {$debugUser->name} - Letzte Aktivität: {$lastActivity->format('Y-m-d')}, " .
             "Tage inaktiv: {$daysInactive}, Letzte Reminder-Mail: {$reminderSent}\n";
    }
    
    foreach ($users as $user) {
        try {
            // Berechne wie viele Tage der User wirklich inaktiv ist
            $lastActivity = \Carbon\Carbon::parse($user->last_activity_date);
            $daysInactive = $lastActivity->diffInDays(\Carbon\Carbon::now());
            
            // Sende E-Mail
            \Illuminate\Support\Facades\Mail::to($user->email)->send(
                new \App\Mail\InactiveReminderMail($user, $daysInactive)
            );
            
            // Markiere dass die Reminder-Mail gesendet wurde
            $user->inactive_reminder_sent_at = \Carbon\Carbon::now();
            $user->save();
            
            $emailsSent++;
            
            echo "[" . date('Y-m-d H:i:s') . "] E-Mail gesendet an: {$user->name} ({$user->email}) - " .
                 "Inaktiv seit: {$daysInactive} Tagen\n";
            
        } catch (Exception $e) {
            $errors++;
            echo "[" . date('Y-m-d H:i:s') . "] FEHLER bei {$user->email}: " . $e->getMessage() . "\n";
        }
    }
    
    echo "[" . date('Y-m-d H:i:s') . "] Inaktivitäts-Erinnerungs-Check abgeschlossen!\n";
    echo "[" . date('Y-m-d H:i:s') . "] E-Mails gesendet: {$emailsSent}\n";
    echo "[" . date('Y-m-d H:i:s') . "] Übersprungen: {$skipped}\n";
    echo "[" . date('Y-m-d H:i:s') . "] Fehler: {$errors}\n";
    
} catch (Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] FEHLER: " . $e->getMessage() . "\n";
    echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "[" . date('Y-m-d H:i:s') . "] Script beendet.\n";
?>

