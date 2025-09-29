<?php
/**
 * Clear Laravel caches script for production deployment
 * This script clears all cached files that might contain local development paths
 */

// Define paths relative to this script
$basePath = __DIR__;
$bootstrapPath = $basePath . '/bootstrap';
$storagePath = $basePath . '/storage';

// Clear bootstrap cache files
$cacheFiles = [
    $bootstrapPath . '/cache/config.php',
    $bootstrapPath . '/cache/packages.php',
    $bootstrapPath . '/cache/services.php',
    $bootstrapPath . '/cache/routes.php',
];

foreach ($cacheFiles as $file) {
    if (file_exists($file)) {
        unlink($file);
        echo "Cleared: $file\n";
    }
}

// Clear storage framework cache directories
$cacheDirectories = [
    $storagePath . '/framework/cache',
    $storagePath . '/framework/sessions',
    $storagePath . '/framework/views',
    $storagePath . '/logs',
];

foreach ($cacheDirectories as $dir) {
    if (is_dir($dir)) {
        $files = array_diff(scandir($dir), array('.', '..', '.gitignore'));
        foreach ($files as $file) {
            $filePath = $dir . '/' . $file;
            if (is_file($filePath)) {
                unlink($filePath);
                echo "Cleared: $filePath\n";
            }
        }
    }
}

echo "Cache clearing completed!\n";
echo "Please run the following commands after deployment:\n";
echo "php artisan config:clear\n";
echo "php artisan view:clear\n";
echo "php artisan route:clear\n";
echo "php artisan cache:clear\n";
?>
