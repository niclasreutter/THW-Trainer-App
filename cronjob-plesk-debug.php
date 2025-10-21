<?php
/**
 * Debug Script für Plesk Cronjobs
 * Zeigt genau, von wo aus Plesk das Script ausführt
 */

echo "=== PLESK CRONJOB DEBUG ===\n";
echo "Timestamp: " . date('Y-m-d H:i:s') . "\n\n";

echo "=== SCRIPT INFO ===\n";
echo "__FILE__: " . __FILE__ . "\n";
echo "__DIR__: " . __DIR__ . "\n";
echo "getcwd(): " . getcwd() . "\n";
echo "Script Filename (argv[0]): " . ($argv[0] ?? 'N/A') . "\n\n";

echo "=== ENVIRONMENT ===\n";
echo "USER: " . (getenv('USER') ?: 'N/A') . "\n";
echo "HOME: " . (getenv('HOME') ?: 'N/A') . "\n";
echo "PWD: " . (getenv('PWD') ?: 'N/A') . "\n";
echo "SCRIPT_FILENAME: " . (getenv('SCRIPT_FILENAME') ?: 'N/A') . "\n\n";

echo "=== CHECKING PATHS ===\n";

// Check __DIR__
echo "Checking __DIR__ ($__DIR__):\n";
echo "  artisan exists: " . (file_exists(__DIR__ . '/artisan') ? 'YES ✓' : 'NO ✗') . "\n";
echo "  vendor/autoload.php exists: " . (file_exists(__DIR__ . '/vendor/autoload.php') ? 'YES ✓' : 'NO ✗') . "\n";
echo "  bootstrap/app.php exists: " . (file_exists(__DIR__ . '/bootstrap/app.php') ? 'YES ✓' : 'NO ✗') . "\n\n";

// Check getcwd()
$cwd = getcwd();
echo "Checking getcwd() ($cwd):\n";
echo "  artisan exists: " . (file_exists($cwd . '/artisan') ? 'YES ✓' : 'NO ✗') . "\n";
echo "  vendor/autoload.php exists: " . (file_exists($cwd . '/vendor/autoload.php') ? 'YES ✓' : 'NO ✗') . "\n";
echo "  bootstrap/app.php exists: " . (file_exists($cwd . '/bootstrap/app.php') ? 'YES ✓' : 'NO ✗') . "\n\n";

// Check possible parent directories
$parentDir = dirname(__DIR__);
echo "Checking parent dir ($parentDir):\n";
echo "  artisan exists: " . (file_exists($parentDir . '/artisan') ? 'YES ✓' : 'NO ✗') . "\n";
echo "  vendor/autoload.php exists: " . (file_exists($parentDir . '/vendor/autoload.php') ? 'YES ✓' : 'NO ✗') . "\n";
echo "  bootstrap/app.php exists: " . (file_exists($parentDir . '/bootstrap/app.php') ? 'YES ✓' : 'NO ✗') . "\n\n";

// List directory contents
echo "=== DIRECTORY LISTING ===\n";
echo "Contents of __DIR__:\n";
$files = @scandir(__DIR__);
if ($files) {
    foreach (array_slice($files, 0, 30) as $file) {
        if ($file === '.' || $file === '..') continue;
        $isDir = is_dir(__DIR__ . '/' . $file);
        echo ($isDir ? '[DIR]  ' : '[FILE] ') . $file . "\n";
    }
} else {
    echo "Could not scan directory!\n";
}

echo "\n=== SUGGESTED FIX ===\n";
if (file_exists(__DIR__ . '/artisan')) {
    echo "✓ Use: \$laravelPath = __DIR__;\n";
} elseif (file_exists(getcwd() . '/artisan')) {
    echo "✓ Use: \$laravelPath = getcwd();\n";
} elseif (file_exists($parentDir . '/artisan')) {
    echo "✓ Use: \$laravelPath = dirname(__DIR__);\n";
} else {
    echo "✗ Laravel directory not found in any expected location!\n";
    echo "  Please check the script location and Plesk configuration.\n";
}

echo "\n=== END DEBUG ===\n";
?>
