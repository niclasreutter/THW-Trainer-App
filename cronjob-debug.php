<?php
/**
 * Debug Cronjob - Zum Testen der Umgebung
 * Zeigt alle relevanten Pfade und Umgebungsvariablen
 */

echo "=== CRONJOB DEBUG INFO ===\n\n";
echo "Current Date/Time: " . date('Y-m-d H:i:s') . "\n";
echo "PHP Version: " . phpversion() . "\n";
echo "Script File: " . __FILE__ . "\n";
echo "Script Directory (__DIR__): " . __DIR__ . "\n";
echo "Current Working Directory (getcwd): " . getcwd() . "\n";
echo "User: " . get_current_user() . "\n";
echo "\n=== PATH CHECKS ===\n";
echo "vendor/autoload.php exists: " . (file_exists(__DIR__ . '/vendor/autoload.php') ? 'YES' : 'NO') . "\n";
echo "bootstrap/app.php exists: " . (file_exists(__DIR__ . '/bootstrap/app.php') ? 'YES' : 'NO') . "\n";
echo "storage/logs directory exists: " . (file_exists(__DIR__ . '/storage/logs') ? 'YES' : 'NO') . "\n";
echo ".env file exists: " . (file_exists(__DIR__ . '/.env') ? 'YES' : 'NO') . "\n";

echo "\n=== DIRECTORY LISTING ===\n";
echo "Files in __DIR__:\n";
$files = scandir(__DIR__);
foreach (array_slice($files, 0, 20) as $file) {
    echo "  - $file\n";
}

echo "\n=== ENVIRONMENT ===\n";
echo "Environment: " . (getenv('APP_ENV') ?: 'not set') . "\n";

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo "\n=== TRYING LARAVEL BOOTSTRAP ===\n";
    try {
        require_once __DIR__ . '/vendor/autoload.php';
        echo "✓ Autoload successful\n";
        
        $app = require_once __DIR__ . '/bootstrap/app.php';
        echo "✓ Bootstrap successful\n";
        
        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
        echo "✓ Kernel created\n";
        
        echo "\nApp Name: " . config('app.name', 'N/A') . "\n";
        echo "App URL: " . config('app.url', 'N/A') . "\n";
        echo "Database Connection: " . config('database.default', 'N/A') . "\n";
        
    } catch (Exception $e) {
        echo "✗ ERROR: " . $e->getMessage() . "\n";
        echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
    }
}

echo "\n=== END DEBUG INFO ===\n";
?>
