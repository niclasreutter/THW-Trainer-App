<?php
/**
 * Server Environment Check and Cache Clear Script
 * Run this script on your production server after deployment
 */

echo "=== THW Trainer App - Server Environment Check ===\n\n";

// Check PHP version
echo "PHP Version: " . PHP_VERSION . "\n";

// Check if this is the correct environment
$basePath = __DIR__;
echo "Base Path: $basePath\n";

// Check for .env file
$envFile = $basePath . '/.env';
if (file_exists($envFile)) {
    echo "✓ .env file found\n";
    
    // Read APP_ENV from .env
    $envContent = file_get_contents($envFile);
    if (preg_match('/APP_ENV=(.+)/', $envContent, $matches)) {
        $appEnv = trim($matches[1]);
        echo "App Environment: $appEnv\n";
        
        if ($appEnv !== 'production') {
            echo "⚠️  WARNING: APP_ENV should be 'production' on live server\n";
        } else {
            echo "✓ App Environment is correctly set to production\n";
        }
    }
} else {
    echo "❌ .env file not found! Please create one from .env.production\n";
}

// Check directories
$directories = [
    'storage/app',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
    'bootstrap/cache'
];

echo "\n=== Directory Check ===\n";
foreach ($directories as $dir) {
    $fullPath = $basePath . '/' . $dir;
    if (is_dir($fullPath)) {
        if (is_writable($fullPath)) {
            echo "✓ $dir (writable)\n";
        } else {
            echo "⚠️  $dir (not writable - fix permissions)\n";
        }
    } else {
        echo "❌ $dir (missing)\n";
        // Try to create it
        if (mkdir($fullPath, 0755, true)) {
            echo "✓ Created $dir\n";
        }
    }
}

// Clear cache files
echo "\n=== Clearing Cache Files ===\n";
$cacheFiles = [
    'bootstrap/cache/config.php',
    'bootstrap/cache/packages.php',
    'bootstrap/cache/services.php',
    'bootstrap/cache/routes.php',
];

foreach ($cacheFiles as $file) {
    $fullPath = $basePath . '/' . $file;
    if (file_exists($fullPath)) {
        if (unlink($fullPath)) {
            echo "✓ Cleared $file\n";
        } else {
            echo "❌ Failed to clear $file\n";
        }
    }
}

// Clear storage cache directories
$storageDirectories = [
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
];

foreach ($storageDirectories as $dir) {
    $fullPath = $basePath . '/' . $dir;
    if (is_dir($fullPath)) {
        $files = array_diff(scandir($fullPath), array('.', '..', '.gitignore'));
        $clearedCount = 0;
        foreach ($files as $file) {
            $filePath = $fullPath . '/' . $file;
            if (is_file($filePath) && unlink($filePath)) {
                $clearedCount++;
            }
        }
        echo "✓ Cleared $clearedCount files from $dir\n";
    }
}

echo "\n=== Recommendations ===\n";
echo "1. Run: php artisan config:clear\n";
echo "2. Run: php artisan view:clear\n";
echo "3. Run: php artisan route:clear\n";
echo "4. Run: php artisan cache:clear\n";
echo "5. Run: php artisan migrate --force (if needed)\n";
echo "6. Run: php artisan config:cache\n";
echo "7. Run: php artisan route:cache\n";
echo "8. Run: php artisan view:cache\n";

echo "\n=== Done ===\n";
echo "Cache clearing completed. If you still get errors, check your hosting provider's open_basedir settings.\n";
?>
