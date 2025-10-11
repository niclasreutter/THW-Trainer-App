<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Automatische Bereinigung unbestätigter Accounts
// Läuft täglich um 09:00 Uhr
Schedule::command('accounts:cleanup-unconfirmed')
    ->dailyAt('09:00')
    ->timezone('Europe/Berlin')
    ->description('Benachrichtigt und löscht unbestätigte Accounts nach 7 bzw. 9 Tagen');

// Streak-Reminder E-Mails
// Läuft täglich um 18:00 Uhr
Schedule::command('gamification:send-streak-reminders')
    ->dailyAt('18:00')
    ->timezone('Europe/Berlin')
    ->description('Sendet Streak-Erinnerungen an inaktive Benutzer');

// Performance-Optimierung
// Läuft alle 6 Stunden
Schedule::command('system:performance-optimization')
    ->everySixHours()
    ->timezone('Europe/Berlin')
    ->description('Optimiert System-Performance durch Cache-Bereinigung und Statistiken-Updates');

// Tägliche Admin-Übersicht
// Läuft täglich um 08:00 Uhr
Schedule::command('admin:daily-report')
    ->dailyAt('08:00')
    ->timezone('Europe/Berlin')
    ->description('Sendet tägliche Admin-Übersicht per E-Mail');

// Wöchentliches Datenbank-Backup
// Läuft jeden Sonntag um 02:00 Uhr
Schedule::command('database:backup')
    ->weeklyOn(0, '02:00')
    ->timezone('Europe/Berlin')
    ->description('Erstellt wöchentliches Backup der Datenbank');

// System-Wartung
// Läuft jeden Sonntag um 03:00 Uhr
Schedule::command('system:maintenance')
    ->weeklyOn(0, '03:00')
    ->timezone('Europe/Berlin')
    ->description('Führt System-Wartung und Speicher-Optimierung durch');
