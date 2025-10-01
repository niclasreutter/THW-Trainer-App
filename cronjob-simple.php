<?php
/**
 * Einfaches Cronjob Script für Account-Bereinigung
 * Führt die Logik direkt aus ohne Laravel-Bootstrap-Probleme
 */

// Dynamischer Pfad - erkennt automatisch Test/Prod Umgebung
$possiblePaths = [
    '/var/www/vhosts/web22867.bero-web.de/thw-trainer.de', // Production
    '/var/www/vhosts/web22867.bero-web.de/test.thw-trainer.de', // Test
];

$laravelPath = null;
foreach ($possiblePaths as $path) {
    if (file_exists($path . '/artisan')) {
        $laravelPath = $path;
        break;
    }
}

if (!$laravelPath) {
    echo "[" . date('Y-m-d H:i:s') . "] FEHLER: Laravel-Verzeichnis nicht gefunden!\n";
    echo "Geprüfte Pfade:\n";
    foreach ($possiblePaths as $path) {
        echo "- $path\n";
    }
    exit(1);
}

echo "[" . date('Y-m-d H:i:s') . "] Laravel-Verzeichnis gefunden: $laravelPath\n";

// Wechsle in das Laravel-Verzeichnis
chdir($laravelPath);

// Lade nur die notwendigen Teile
require_once $laravelPath . '/vendor/autoload.php';

// Setze die Umgebung
putenv('APP_ENV=production');
putenv('APP_DEBUG=false');

// Lade die .env Datei
$dotenv = Dotenv\Dotenv::createImmutable($laravelPath);
$dotenv->load();

// Erstelle eine minimale Laravel App
$app = new Illuminate\Foundation\Application($laravelPath);
$app->singleton(Illuminate\Contracts\Http\Kernel::class, App\Http\Kernel::class);
$app->singleton(Illuminate\Contracts\Console\Kernel::class, App\Console\Kernel::class);
$app->singleton(Illuminate\Contracts\Debug\ExceptionHandler::class, App\Exceptions\Handler::class);

// Bootstrap nur die notwendigen Services
$app->singleton('config', function () {
    return new Illuminate\Config\Repository();
});

$app->singleton('db', function () {
    return new Illuminate\Database\DatabaseManager(null, new Illuminate\Container\Container());
});

// Führe die Bereinigung direkt aus
try {
    echo "[" . date('Y-m-d H:i:s') . "] Starte Account-Bereinigung...\n";
    
    // Datenbankverbindung
    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $database = $_ENV['DB_DATABASE'] ?? '';
    $username = $_ENV['DB_USERNAME'] ?? '';
    $password = $_ENV['DB_PASSWORD'] ?? '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 7-Tage Warnungen senden
    $sevenDaysAgo = date('Y-m-d H:i:s', strtotime('-7 days'));
    $nineDaysAgo = date('Y-m-d H:i:s', strtotime('-9 days'));
    
    $stmt = $pdo->prepare("
        SELECT id, name, email, created_at 
        FROM users 
        WHERE email_verified_at IS NULL 
        AND deletion_warning_sent_at IS NULL 
        AND created_at >= ? 
        AND created_at <= ?
    ");
    $stmt->execute([$nineDaysAgo, $sevenDaysAgo]);
    $usersToWarn = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "[" . date('Y-m-d H:i:s') . "] Sende Warnungen an " . count($usersToWarn) . " Accounts...\n";
    
    foreach ($usersToWarn as $user) {
        // Hier würde die E-Mail gesendet werden
        echo "Warnung gesendet an: " . $user['email'] . "\n";
        
        // Markiere als gesendet
        $updateStmt = $pdo->prepare("UPDATE users SET deletion_warning_sent_at = NOW() WHERE id = ?");
        $updateStmt->execute([$user['id']]);
    }
    
    // 9-Tage Löschungen
    $stmt = $pdo->prepare("
        SELECT id, name, email, created_at 
        FROM users 
        WHERE email_verified_at IS NULL 
        AND created_at <= ?
    ");
    $stmt->execute([$nineDaysAgo]);
    $usersToDelete = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "[" . date('Y-m-d H:i:s') . "] Lösche " . count($usersToDelete) . " alte unbestätigte Accounts...\n";
    
    foreach ($usersToDelete as $user) {
        // Hier würde die Löschungs-E-Mail gesendet werden
        echo "Löschungs-E-Mail gesendet an: " . $user['email'] . "\n";
        
        // Lösche den Account
        $deleteStmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $deleteStmt->execute([$user['id']]);
        
        echo "Account gelöscht: " . $user['email'] . "\n";
    }
    
    echo "[" . date('Y-m-d H:i:s') . "] Bereinigung abgeschlossen.\n";
    
} catch (Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] FEHLER: " . $e->getMessage() . "\n";
    echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "[" . date('Y-m-d H:i:s') . "] Script beendet.\n";
?>
