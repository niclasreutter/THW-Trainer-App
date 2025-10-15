<?php
/**
 * Test-Script für Inaktivitäts-Erinnerungs-Mail
 * Sendet eine Test-Mail an niclasreutter@icloud.com
 */

// Bestimme automatisch den Pfad basierend auf dem Ausführungsort
$currentPath = __DIR__;
$laravelPath = $currentPath;

// Prüfe ob wir im richtigen Verzeichnis sind
if (!file_exists($laravelPath . '/artisan')) {
    echo "[" . date('Y-m-d H:i:s') . "] FEHLER: Laravel-Verzeichnis nicht gefunden: $laravelPath\n";
    echo "Bitte führe dieses Script aus dem Laravel-Root-Verzeichnis aus.\n";
    exit(1);
}

echo "[" . date('Y-m-d H:i:s') . "] Laravel-Verzeichnis gefunden: $laravelPath\n";

// Wechsle in das Laravel-Verzeichnis
chdir($laravelPath);

// Lade Laravel
require_once $laravelPath . '/vendor/autoload.php';

// Erstelle die Laravel Application
$app = require_once $laravelPath . '/bootstrap/app.php';

// Bootstrap die Application
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test-Mail senden
try {
    echo "[" . date('Y-m-d H:i:s') . "] Starte Test-Mail-Versand...\n";
    
    // Erstelle Test-User Objekt
    $testUser = new \App\Models\User();
    $testUser->id = 999;
    $testUser->name = 'Niclas (Test)';
    $testUser->email = 'niclasreutter@icloud.com';
    $testUser->email_consent = true;
    $testUser->points = 1250;
    $testUser->level = 5;
    $testUser->last_activity_date = \Carbon\Carbon::now()->subDays(7);
    
    // Simuliere gelöste Fragen (z.B. 150 von 200)
    $allQuestions = \App\Models\Question::pluck('id')->toArray();
    $totalQuestions = count($allQuestions);
    $solvedCount = (int)($totalQuestions * 0.75); // 75% gelöst
    $testUser->solved_questions = array_slice($allQuestions, 0, $solvedCount);
    
    echo "[" . date('Y-m-d H:i:s') . "] Test-User erstellt:\n";
    echo "  - Name: {$testUser->name}\n";
    echo "  - E-Mail: {$testUser->email}\n";
    echo "  - Letzte Aktivität: " . $testUser->last_activity_date->format('Y-m-d') . "\n";
    echo "  - Tage inaktiv: " . $testUser->last_activity_date->diffInDays(\Carbon\Carbon::now()) . "\n";
    echo "  - Gelöste Fragen: {$solvedCount} von {$totalQuestions}\n";
    echo "  - Fortschritt: " . round(($solvedCount / $totalQuestions) * 100) . "%\n";
    echo "  - Verbleibende Fragen: " . ($totalQuestions - $solvedCount) . "\n";
    echo "\n";
    
    // Berechne Tage inaktiv
    $daysInactive = $testUser->last_activity_date->diffInDays(\Carbon\Carbon::now());
    
    // Sende die Mail
    echo "[" . date('Y-m-d H:i:s') . "] Sende Mail an niclasreutter@icloud.com...\n";
    \Illuminate\Support\Facades\Mail::to('niclasreutter@icloud.com')
        ->send(new \App\Mail\InactiveReminderMail($testUser, $daysInactive));
    
    echo "[" . date('Y-m-d H:i:s') . "] ✅ Test-Mail erfolgreich gesendet!\n";
    echo "\n";
    echo "Prüfe dein Postfach: niclasreutter@icloud.com\n";
    echo "Falls die Mail nicht ankommt:\n";
    echo "  1. Prüfe Spam-Ordner\n";
    echo "  2. Prüfe Mail-Konfiguration in .env\n";
    echo "  3. Prüfe storage/logs/laravel.log für Fehler\n";
    
} catch (Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] ❌ FEHLER beim Mail-Versand:\n";
    echo $e->getMessage() . "\n";
    echo "\nStack Trace:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}

echo "[" . date('Y-m-d H:i:s') . "] Script beendet.\n";
?>

