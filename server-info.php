<?php
/**
 * Server-Information Script
 * Lade diese Datei über deine Website auf um Server-Details zu sehen
 */

echo "<h1>🔍 Server-Information für THW-Trainer.de</h1>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px;background:#f5f5f5;} .info{background:white;padding:15px;margin:10px 0;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.1);} .path{background:#e3f2fd;padding:10px;border-radius:4px;font-family:monospace;}</style>";

echo "<div class='info'>";
echo "<h2>📁 Aktueller Pfad</h2>";
echo "<div class='path'>" . __DIR__ . "</div>";
echo "</div>";

echo "<div class='info'>";
echo "<h2>🌐 Server-Details</h2>";
echo "<p><strong>Server Name:</strong> " . ($_SERVER['SERVER_NAME'] ?? 'Unbekannt') . "</p>";
echo "<p><strong>HTTP Host:</strong> " . ($_SERVER['HTTP_HOST'] ?? 'Unbekannt') . "</p>";
echo "<p><strong>Document Root:</strong> " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unbekannt') . "</p>";
echo "<p><strong>Script Name:</strong> " . ($_SERVER['SCRIPT_NAME'] ?? 'Unbekannt') . "</p>";
echo "</div>";

echo "<div class='info'>";
echo "<h2>📂 Verzeichnis-Struktur</h2>";
echo "<h3>Aktuelles Verzeichnis:</h3>";
$currentDir = __DIR__;
echo "<div class='path'>$currentDir</div>";

echo "<h3>Inhalt des aktuellen Verzeichnisses:</h3>";
$files = scandir($currentDir);
echo "<ul>";
foreach ($files as $file) {
    if ($file !== '.' && $file !== '..') {
        $path = $currentDir . '/' . $file;
        $type = is_dir($path) ? '📁' : '📄';
        echo "<li>$type $file</li>";
    }
}
echo "</ul>";
echo "</div>";

echo "<div class='info'>";
echo "<h2>🔍 Laravel-spezifische Prüfungen</h2>";

// Prüfe ob artisan existiert
$artisanPath = __DIR__ . '/artisan';
if (file_exists($artisanPath)) {
    echo "<p>✅ <strong>artisan</strong> gefunden: <div class='path'>$artisanPath</div></p>";
} else {
    echo "<p>❌ <strong>artisan</strong> nicht gefunden im aktuellen Verzeichnis</p>";
}

// Prüfe ob composer.json existiert
$composerPath = __DIR__ . '/composer.json';
if (file_exists($composerPath)) {
    echo "<p>✅ <strong>composer.json</strong> gefunden: <div class='path'>$composerPath</div></p>";
} else {
    echo "<p>❌ <strong>composer.json</strong> nicht gefunden</p>";
}

// Prüfe Laravel-Ordner
$laravelFolders = ['app', 'config', 'database', 'resources', 'routes'];
foreach ($laravelFolders as $folder) {
    $folderPath = __DIR__ . '/' . $folder;
    if (is_dir($folderPath)) {
        echo "<p>✅ <strong>$folder/</strong> Ordner gefunden</p>";
    } else {
        echo "<p>❌ <strong>$folder/</strong> Ordner nicht gefunden</p>";
    }
}
echo "</div>";

echo "<div class='info'>";
echo "<h2>🔧 Mögliche Cronjob-Pfade</h2>";
echo "<p>Basierend auf dem aktuellen Pfad, versuche diese Cronjob-Befehle:</p>";

$possiblePaths = [
    __DIR__,
    dirname(__DIR__),
    dirname(dirname(__DIR__)),
];

foreach ($possiblePaths as $path) {
    echo "<div class='path'>cd $path && php artisan schedule:run</div>";
}

echo "</div>";

echo "<div class='info'>";
echo "<h2>📋 PHP-Information</h2>";
echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";
echo "<p><strong>PHP Binary:</strong> " . PHP_BINARY . "</p>";
echo "</div>";

echo "<div class='info'>";
echo "<h2>⚠️ Sicherheitshinweis</h2>";
echo "<p><strong>WICHTIG:</strong> Lösche diese Datei nach dem Testen!</p>";
echo "<p>Diese Datei enthält sensible Server-Informationen.</p>";
echo "</div>";

echo "<p style='text-align:center;margin-top:30px;color:#666;'>";
echo "Script erstellt am: " . date('d.m.Y H:i:s');
echo "</p>";
?>
