<?php
/**
 * Cronjob Script für tägliche Streak-Reset - TEST
 * Dieses Script kann direkt als PHP-Cronjob in Plesk ausgeführt werden
 * Empfohlene Ausführungszeit: Täglich um 23:00 Uhr
 */

// Test-Pfad
$laravelPath = '/var/www/vhosts/web22867.bero-web.de/test.thw-trainer.de';

if (!file_exists($laravelPath . '/artisan')) {
    echo "[" . date('Y-m-d H:i:s') . "] FEHLER: Laravel-Verzeichnis nicht gefunden: $laravelPath\n";
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
    $resetsCount = 0;
    $errors = 0;
    
    // Debug: Zeige alle Benutzer mit Streak > 0
    $allUsersWithStreak = \App\Models\User::where('streak_days', '>', 0)->get();
    echo "[" . date('Y-m-d H:i:s') . "] DEBUG: Alle Benutzer mit Streak > 0: {$allUsersWithStreak->count()}\n";
    
    foreach ($allUsersWithStreak as $debugUser) {
        $lastDailyActivity = $debugUser->daily_questions_date ? \Carbon\Carbon::parse($debugUser->daily_questions_date) : null;
        $lastDailyActivityStr = $lastDailyActivity ? $lastDailyActivity->format('Y-m-d') : 'NULL';
        echo "[" . date('Y-m-d H:i:s') . "] DEBUG: {$debugUser->name} - Streak: {$debugUser->streak_days}, Letzte tägliche Aktivität: {$lastDailyActivityStr}\n";
    }
    
    // Finde alle Benutzer die:
    // 1. Einen Streak > 0 haben
    // 2. Heute keine Fragen beantwortet haben (daily_questions_date != heute)
    $users = \App\Models\User::where('streak_days', '>', 0)
        ->where(function($query) use ($today) {
            $query->whereNull('daily_questions_date')
                  ->orWhere('daily_questions_date', '!=', $today);
        })
        ->get();
    
    echo "[" . date('Y-m-d H:i:s') . "] Gefunden: {$users->count()} Benutzer für Streak-Reset.\n";
    
    foreach ($users as $user) {
        try {
            // Prüfe ob der Benutzer heute wirklich keine Fragen beantwortet hat
            $lastDailyActivity = $user->daily_questions_date ? \Carbon\Carbon::parse($user->daily_questions_date) : null;
            
            if (!$lastDailyActivity || $lastDailyActivity->lt($today)) {
                // Reset Streak auf 0
                $oldStreak = $user->streak_days;
                $user->streak_days = 0;
                $user->save();
                $resetsCount++;
                
                echo "[" . date('Y-m-d H:i:s') . "] Streak zurückgesetzt: {$user->name} ({$user->email}) - Von {$oldStreak} auf 0 Tage\n";
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
