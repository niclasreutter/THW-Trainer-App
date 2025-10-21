<?php
/**
 * Web-basiertes Debug Tool für Cronjobs
 * Aufruf: https://deine-domain.de/cronjob-debug.php
 * 
 * WICHTIG: Nach dem Debuggen diese Datei LÖSCHEN!
 */

// Sicherheits-Token (ändere dies!)
$SECRET_TOKEN = 'debug2025';
$token = $_GET['token'] ?? '';

if ($token !== $SECRET_TOKEN) {
    die('Zugriff verweigert. Bitte Token angeben: ?token=debug2025');
}

header('Content-Type: text/plain; charset=utf-8');

echo "=== CRONJOB DEBUG INFO ===\n";
echo "Current Date/Time: " . date('Y-m-d H:i:s') . "\n";
echo "PHP Version: " . phpversion() . "\n\n";

echo "=== PATHS (from public/) ===\n";
echo "Script File: " . __FILE__ . "\n";
echo "Script Directory (__DIR__): " . __DIR__ . "\n";
echo "Parent Directory: " . dirname(__DIR__) . "\n";
echo "Current Working Directory: " . getcwd() . "\n\n";

$basePath = dirname(__DIR__);

echo "=== CRITICAL FILES CHECK ===\n";
echo "vendor/autoload.php: " . (file_exists($basePath . '/vendor/autoload.php') ? '✓ EXISTS' : '✗ MISSING') . "\n";
echo "bootstrap/app.php: " . (file_exists($basePath . '/bootstrap/app.php') ? '✓ EXISTS' : '✗ MISSING') . "\n";
echo "storage/logs/: " . (is_dir($basePath . '/storage/logs') ? '✓ EXISTS' : '✗ MISSING') . "\n";
echo ".env: " . (file_exists($basePath . '/.env') ? '✓ EXISTS' : '✗ MISSING') . "\n\n";

echo "=== CRONJOB SCRIPTS CHECK ===\n";
$cronjobs = [
    'cronjob-admin-report-prod.php',
    'cronjob-admin-report-test.php',
    'cronjob-performance-optimization-prod.php',
    'cronjob-performance-optimization-test.php',
    'cronjob-database-backup-prod.php',
    'cronjob-database-backup-test.php',
    'cronjob-system-maintenance-prod.php',
    'cronjob-system-maintenance-test.php',
];

foreach ($cronjobs as $script) {
    $exists = file_exists($basePath . '/' . $script);
    echo "$script: " . ($exists ? '✓ EXISTS' : '✗ MISSING') . "\n";
}

echo "\n=== DIRECTORY STRUCTURE ===\n";
echo "Root directory listing (first 25 items):\n";
$files = @scandir($basePath);
if ($files) {
    foreach (array_slice($files, 0, 25) as $file) {
        if ($file === '.' || $file === '..') continue;
        $isDir = is_dir($basePath . '/' . $file);
        echo ($isDir ? '[DIR]  ' : '[FILE] ') . $file . "\n";
    }
}

echo "\n=== PERMISSIONS CHECK ===\n";
$checkPaths = [
    '/storage/logs',
    '/bootstrap/cache',
    '/storage/framework/cache',
    '/storage/framework/sessions',
    '/storage/framework/views',
];

foreach ($checkPaths as $path) {
    $fullPath = $basePath . $path;
    if (file_exists($fullPath)) {
        $perms = substr(sprintf('%o', fileperms($fullPath)), -4);
        $writable = is_writable($fullPath);
        echo "$path: $perms " . ($writable ? '✓ WRITABLE' : '✗ NOT WRITABLE') . "\n";
    } else {
        echo "$path: ✗ DOES NOT EXIST\n";
    }
}

echo "\n=== TRYING LARAVEL BOOTSTRAP ===\n";
if (file_exists($basePath . '/vendor/autoload.php')) {
    try {
        require_once $basePath . '/vendor/autoload.php';
        echo "✓ Autoload successful\n";
        
        $app = require_once $basePath . '/bootstrap/app.php';
        echo "✓ Bootstrap successful\n";
        
        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
        echo "✓ Kernel created successfully\n";
        
        echo "\n=== LARAVEL CONFIG ===\n";
        echo "App Name: " . config('app.name', 'N/A') . "\n";
        echo "App Environment: " . config('app.env', 'N/A') . "\n";
        echo "App URL: " . config('app.url', 'N/A') . "\n";
        echo "Database Connection: " . config('database.default', 'N/A') . "\n";
        echo "Database Host: " . config('database.connections.' . config('database.default') . '.host', 'N/A') . "\n";
        
        echo "\n=== TEST ARTISAN COMMAND ===\n";
        try {
            $exitCode = $kernel->call('list');
            echo "✓ Artisan command 'list' executed successfully (exit code: $exitCode)\n";
        } catch (Exception $e) {
            echo "✗ Artisan command failed: " . $e->getMessage() . "\n";
        }
        
    } catch (Exception $e) {
        echo "✗ BOOTSTRAP ERROR: " . $e->getMessage() . "\n";
        echo "\nStack Trace:\n" . $e->getTraceAsString() . "\n";
    }
} else {
    echo "✗ Cannot find vendor/autoload.php\n";
}

echo "\n=== SERVER INFO ===\n";
echo "Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "\n";
echo "Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "\n";
echo "Script Filename: " . ($_SERVER['SCRIPT_FILENAME'] ?? 'Unknown') . "\n";
echo "PHP User: " . get_current_user() . "\n";
echo "PHP Process UID: " . getmyuid() . "\n";

echo "\n=== LOADED PHP EXTENSIONS ===\n";
$extensions = get_loaded_extensions();
sort($extensions);
$important = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json'];
foreach ($important as $ext) {
    $loaded = in_array($ext, $extensions);
    echo "$ext: " . ($loaded ? '✓' : '✗') . "\n";
}

echo "\n=== MEMORY INFO ===\n";
echo "Memory Limit: " . ini_get('memory_limit') . "\n";
echo "Memory Usage: " . round(memory_get_usage() / 1024 / 1024, 2) . " MB\n";
echo "Peak Memory: " . round(memory_get_peak_usage() / 1024 / 1024, 2) . " MB\n";

echo "\n=== END DEBUG INFO ===\n";
echo "\nWICHTIG: Bitte diese Datei nach dem Debuggen löschen!\n";
?>
