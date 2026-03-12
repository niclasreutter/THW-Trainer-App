<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ============================================================
// TÄGLICHE JOBS
// ============================================================

// Täglicher Reset: Streak + Daily Questions (MUSS vor allem anderen laufen)
// Läuft täglich um 00:01 Uhr
Schedule::command('gamification:daily-reset')
    ->dailyAt('00:01')
    ->timezone('Europe/Berlin')
    ->description('Streak-Prüfung + Daily-Questions-Reset für alle User');

// User-Count aufzeichnen
// Läuft täglich um 00:15 Uhr
Schedule::command('user-count:record')
    ->dailyAt('00:15')
    ->timezone('Europe/Berlin')
    ->description('Speichert die User-Anzahl vom Vortag in user_count_history');

// Spaced Repetition Erinnerungen
// Läuft täglich um 08:00 Uhr
Schedule::command('app:send-spaced-repetition-reminders')
    ->dailyAt('08:00')
    ->timezone('Europe/Berlin')
    ->description('Sendet Erinnerungen an User mit fälligen Wiederholungen');

// Tägliche Admin-Übersicht
// Läuft täglich um 08:00 Uhr
Schedule::command('admin:daily-report')
    ->dailyAt('08:00')
    ->timezone('Europe/Berlin')
    ->description('Sendet tägliche Admin-Übersicht per E-Mail');

// Automatische Bereinigung unbestätigter Accounts
// Läuft täglich um 09:00 Uhr
Schedule::command('accounts:cleanup-unconfirmed')
    ->dailyAt('09:00')
    ->timezone('Europe/Berlin')
    ->description('Benachrichtigt und löscht unbestätigte Accounts nach 7 bzw. 9 Tagen');

// Inaktive User Erinnerungen
// Läuft täglich um 10:00 Uhr
Schedule::command('app:send-inactive-reminders')
    ->dailyAt('10:00')
    ->timezone('Europe/Berlin')
    ->description('Sendet Erinnerungen an User die 4+ Tage inaktiv sind');

// Prüfungs-Feedback-Anfrage (eine Woche nach der Prüfung)
// Läuft täglich um 10:00 Uhr
Schedule::command('exam:send-feedback-requests')
    ->dailyAt('10:00')
    ->timezone('Europe/Berlin')
    ->description('Sendet Feedback-Anfragen an User deren Prüfung vor einer Woche war');

// Prüfungs-Viel-Erfolg-Mail (einen Tag vor der Prüfung)
// Läuft täglich um 17:00 Uhr
Schedule::command('exam:send-goodluck')
    ->dailyAt('17:00')
    ->timezone('Europe/Berlin')
    ->description('Sendet Viel-Erfolg-Mails an User deren Prüfung morgen ist');

// Prüfungs-Tagespensum-Erinnerungen
// Läuft täglich um 18:00 Uhr
Schedule::command('exam:send-reminders')
    ->dailyAt('18:00')
    ->timezone('Europe/Berlin')
    ->description('Sendet Tagespensum-Erinnerungen an User mit Prüfungsdatum');

// Streak-Reminder E-Mails
// Läuft täglich um 18:00 Uhr
Schedule::command('gamification:send-streak-reminders')
    ->dailyAt('18:00')
    ->timezone('Europe/Berlin')
    ->description('Sendet Streak-Erinnerungen an User die heute noch nicht gelernt haben');

// ============================================================
// HÄUFIGE JOBS
// ============================================================

// Lernsession-Lifecycle (generieren, aktivieren, abschließen)
// Läuft alle 5 Minuten
Schedule::command('lernsession:lifecycle')
    ->everyFiveMinutes()
    ->timezone('Europe/Berlin')
    ->description('Generiert, aktiviert und schließt Lernsession-Instanzen ab');

// Performance-Optimierung
// Läuft alle 6 Stunden
Schedule::command('system:performance-optimization')
    ->everySixHours()
    ->timezone('Europe/Berlin')
    ->description('Optimiert System-Performance durch Cache-Bereinigung und Statistiken-Updates');

// ============================================================
// WÖCHENTLICHE JOBS
// ============================================================

// Liga Auf-/Abstiege (MUSS Montags VOR dem Daily Reset laufen → 00:00)
// Läuft jeden Montag um 00:00 Uhr
Schedule::command('league:process-weekly')
    ->weeklyOn(1, '00:00')
    ->timezone('Europe/Berlin')
    ->description('Verarbeitet Liga-Auf-/Abstiege und vergibt Wochenbelohnungen');

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

