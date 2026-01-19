<?php

/**
 * User Count History - PRODUCTION
 * Wrapper für Laravel Artisan Command
 *
 * PLESK KONFIGURATION:
 * - Typ: PHP Skript ausführen
 * - PHP Version: PHP 8.2
 * - Pfad: cronjob-user-count-prod.php
 * - Zeitplan: Täglich um 00:15 Uhr
 *
 * WICHTIG: Dieser Wrapper ruft den Laravel Artisan Command auf
 */

// Wechsle ins Laravel Root-Verzeichnis
chdir(__DIR__);

// Führe Artisan Command aus
$output = [];
$returnCode = 0;

exec('php artisan user-count:record 2>&1', $output, $returnCode);

// Ausgabe anzeigen
echo implode("\n", $output) . "\n";

// Exit mit dem Return Code des Commands
exit($returnCode);
