<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ============================================================
// MONITORING & LOGGING
// ============================================================
$adminEmail = 'protokolle@thw-trainer.de';
$schedulerLog = storage_path('logs/scheduler.log');

$onFail = fn(string $cmd) => function () use ($cmd) {
    Log::error('Scheduler: Command fehlgeschlagen', [
        'command' => $cmd,
        'time' => now()->toDateTimeString(),
    ]);
};

// ============================================================
// TÄGLICHE JOBS
// ============================================================

// Täglicher Reset: Streak + Daily Questions (MUSS vor allem anderen laufen)
// Läuft täglich um 00:01 Uhr
Schedule::command('gamification:daily-reset')
    ->dailyAt('00:01')
    ->timezone('Europe/Berlin')
    ->withoutOverlapping(15)
    ->appendOutputTo($schedulerLog)
    ->onFailure($onFail('gamification:daily-reset'))
    ->description('Streak-Prüfung + Daily-Questions-Reset für alle User')
    ->emailOutputOnFailure($adminEmail);

// User-Count aufzeichnen
// Läuft täglich um 00:15 Uhr
Schedule::command('user-count:record')
    ->dailyAt('00:15')
    ->timezone('Europe/Berlin')
    ->appendOutputTo($schedulerLog)
    ->onFailure($onFail('user-count:record'))
    ->description('Speichert die User-Anzahl vom Vortag in user_count_history')
    ->emailOutputOnFailure($adminEmail);

// Spaced Repetition Erinnerungen
// Läuft täglich um 08:00 Uhr
Schedule::command('app:send-spaced-repetition-reminders')
    ->dailyAt('08:00')
    ->timezone('Europe/Berlin')
    ->appendOutputTo($schedulerLog)
    ->onFailure($onFail('app:send-spaced-repetition-reminders'))
    ->description('Sendet Erinnerungen an User mit fälligen Wiederholungen')
    ->emailOutputOnFailure($adminEmail);

// Spaced Repetition Push
// Laeuft taeglich um 08:00 Uhr
Schedule::command('push:send-spaced-repetition')
    ->dailyAt('08:00')
    ->timezone('Europe/Berlin')
    ->appendOutputTo($schedulerLog)
    ->onFailure($onFail('push:send-spaced-repetition'))
    ->description('Sendet Push an User mit faelligen Wiederholungen')
    ->emailOutputOnFailure($adminEmail);

// Guten-Morgen Push
// Laeuft taeglich um 08:30 Uhr
Schedule::command('push:send-morning')
    ->dailyAt('08:30')
    ->timezone('Europe/Berlin')
    ->appendOutputTo($schedulerLog)
    ->onFailure($onFail('push:send-morning'))
    ->description('Sendet Guten-Morgen Push an alle User mit Push-Subscription')
    ->emailOutputOnFailure($adminEmail);

// Pruefungs-Tagesplan Push
// Laeuft taeglich um 09:00 Uhr
Schedule::command('push:send-exam-reminder')
    ->dailyAt('09:00')
    ->timezone('Europe/Berlin')
    ->appendOutputTo($schedulerLog)
    ->onFailure($onFail('push:send-exam-reminder'))
    ->description('Sendet Pruefungs-Tagesplan Push an User mit Pruefungsdatum')
    ->emailOutputOnFailure($adminEmail);

// Pruefungstag Viel-Erfolg Push
// Laeuft taeglich um 08:00 Uhr
Schedule::command('push:send-exam-goodluck')
    ->dailyAt('08:00')
    ->timezone('Europe/Berlin')
    ->appendOutputTo($schedulerLog)
    ->onFailure($onFail('push:send-exam-goodluck'))
    ->description('Sendet Viel-Erfolg Push an User deren Pruefung heute ist')
    ->emailOutputOnFailure($adminEmail);

// Streak-Erinnerung Push (3x taeglich)
Schedule::command('push:send-streak morning')
    ->dailyAt('12:00')
    ->timezone('Europe/Berlin')
    ->appendOutputTo($schedulerLog)
    ->onFailure($onFail('push:send-streak morning'))
    ->description('Streak-Push Mittag: Erinnerung an offene Fragen')
    ->emailOutputOnFailure($adminEmail);

Schedule::command('push:send-streak afternoon')
    ->dailyAt('17:00')
    ->timezone('Europe/Berlin')
    ->appendOutputTo($schedulerLog)
    ->onFailure($onFail('push:send-streak afternoon'))
    ->description('Streak-Push Nachmittag: Erinnerung an offene Fragen')
    ->emailOutputOnFailure($adminEmail);

Schedule::command('push:send-streak evening')
    ->dailyAt('21:00')
    ->timezone('Europe/Berlin')
    ->appendOutputTo($schedulerLog)
    ->onFailure($onFail('push:send-streak evening'))
    ->description('Streak-Push Abend: Letzte Chance fuer den Streak')
    ->emailOutputOnFailure($adminEmail);

// Tägliche Admin-Übersicht (nur Production)
// Läuft täglich um 08:00 Uhr
Schedule::command('admin:daily-report')
    ->dailyAt('08:00')
    ->timezone('Europe/Berlin')
    ->environments(['production'])
    ->appendOutputTo($schedulerLog)
    ->onFailure($onFail('admin:daily-report'))
    ->description('Sendet tägliche Admin-Übersicht per E-Mail')
    ->emailOutputOnFailure($adminEmail);

// Automatische Bereinigung unbestätigter Accounts (nur Production)
// Läuft täglich um 09:00 Uhr
Schedule::command('accounts:cleanup-unconfirmed')
    ->dailyAt('09:00')
    ->timezone('Europe/Berlin')
    ->environments(['production'])
    ->appendOutputTo($schedulerLog)
    ->onFailure($onFail('accounts:cleanup-unconfirmed'))
    ->description('Benachrichtigt und löscht unbestätigte Accounts nach 7 bzw. 9 Tagen')
    ->emailOutputOnFailure($adminEmail);

// Inaktive User Erinnerungen
// Läuft täglich um 10:00 Uhr
Schedule::command('app:send-inactive-reminders')
    ->dailyAt('10:00')
    ->timezone('Europe/Berlin')
    ->appendOutputTo($schedulerLog)
    ->onFailure($onFail('app:send-inactive-reminders'))
    ->description('Sendet Erinnerungen an User die 4+ Tage inaktiv sind')
    ->emailOutputOnFailure($adminEmail);

// Inaktive User Push-Erinnerung (7+ Tage inaktiv)
// Laeuft taeglich um 10:00 Uhr
Schedule::command('push:send-inactive')
    ->dailyAt('10:00')
    ->timezone('Europe/Berlin')
    ->appendOutputTo($schedulerLog)
    ->onFailure($onFail('push:send-inactive'))
    ->description('Sendet Push-Erinnerung an User die 7+ Tage inaktiv sind')
    ->emailOutputOnFailure($adminEmail);

// Prüfungs-Feedback-Anfrage (eine Woche nach der Prüfung)
// Läuft täglich um 10:00 Uhr
Schedule::command('exam:send-feedback-requests')
    ->dailyAt('10:00')
    ->timezone('Europe/Berlin')
    ->appendOutputTo($schedulerLog)
    ->onFailure($onFail('exam:send-feedback-requests'))
    ->description('Sendet Feedback-Anfragen an User deren Prüfung vor einer Woche war')
    ->emailOutputOnFailure($adminEmail);

// Prüfungs-Viel-Erfolg-Mail (einen Tag vor der Prüfung)
// Läuft täglich um 17:00 Uhr
Schedule::command('exam:send-goodluck')
    ->dailyAt('17:00')
    ->timezone('Europe/Berlin')
    ->appendOutputTo($schedulerLog)
    ->onFailure($onFail('exam:send-goodluck'))
    ->description('Sendet Viel-Erfolg-Mails an User deren Prüfung morgen ist')
    ->emailOutputOnFailure($adminEmail);

// Prüfungs-Tagespensum-Erinnerungen
// Läuft täglich um 18:00 Uhr
Schedule::command('exam:send-reminders')
    ->dailyAt('18:00')
    ->timezone('Europe/Berlin')
    ->appendOutputTo($schedulerLog)
    ->onFailure($onFail('exam:send-reminders'))
    ->description('Sendet Tagespensum-Erinnerungen an User mit Prüfungsdatum')
    ->emailOutputOnFailure($adminEmail);

// Streak-Reminder E-Mails
// Läuft täglich um 18:00 Uhr
Schedule::command('gamification:send-streak-reminders')
    ->dailyAt('18:00')
    ->timezone('Europe/Berlin')
    ->appendOutputTo($schedulerLog)
    ->onFailure($onFail('gamification:send-streak-reminders'))
    ->description('Sendet Streak-Erinnerungen an User die heute noch nicht gelernt haben')
    ->emailOutputOnFailure($adminEmail);

// ============================================================
// HÄUFIGE JOBS
// ============================================================

// Lernsession-Lifecycle (generieren, aktivieren, abschließen)
// Läuft alle 5 Minuten
Schedule::command('lernsession:lifecycle')
    ->everyFiveMinutes()
    ->timezone('Europe/Berlin')
    ->withoutOverlapping(10)
    ->runInBackground()
    ->appendOutputTo($schedulerLog)
    ->onFailure($onFail('lernsession:lifecycle'))
    ->description('Generiert, aktiviert und schließt Lernsession-Instanzen ab')
    ->emailOutputOnFailure($adminEmail);

// Performance-Optimierung
// Läuft alle 6 Stunden
Schedule::command('system:performance-optimization')
    ->everySixHours()
    ->timezone('Europe/Berlin')
    ->withoutOverlapping(15)
    ->runInBackground()
    ->appendOutputTo($schedulerLog)
    ->onFailure($onFail('system:performance-optimization'))
    ->description('Optimiert System-Performance durch Cache-Bereinigung und Statistiken-Updates')
    ->emailOutputOnFailure($adminEmail);

// ============================================================
// WÖCHENTLICHE JOBS
// ============================================================

// Liga Auf-/Abstiege (MUSS Montags VOR dem Daily Reset laufen → 00:00)
// Läuft jeden Montag um 00:00 Uhr
Schedule::command('league:process-weekly')
    ->weeklyOn(1, '00:00')
    ->timezone('Europe/Berlin')
    ->withoutOverlapping(15)
    ->appendOutputTo($schedulerLog)
    ->onFailure($onFail('league:process-weekly'))
    ->description('Verarbeitet Liga-Auf-/Abstiege und vergibt Wochenbelohnungen')
    ->emailOutputOnFailure($adminEmail);

// Wochenbericht Push
// Laeuft jeden Montag um 09:00 Uhr
Schedule::command('push:send-weekly-report')
    ->weeklyOn(1, '09:00')
    ->timezone('Europe/Berlin')
    ->appendOutputTo($schedulerLog)
    ->onFailure($onFail('push:send-weekly-report'))
    ->description('Sendet Wochenrueckblick Push an alle User mit Push-Subscription')
    ->emailOutputOnFailure($adminEmail);

// Wöchentliches Datenbank-Backup (nur Production)
// Läuft jeden Sonntag um 02:00 Uhr
Schedule::command('database:backup')
    ->weeklyOn(0, '02:00')
    ->timezone('Europe/Berlin')
    ->environments(['production'])
    ->withoutOverlapping(30)
    ->runInBackground()
    ->appendOutputTo($schedulerLog)
    ->onFailure($onFail('database:backup'))
    ->description('Erstellt wöchentliches Backup der Datenbank')
    ->emailOutputOnFailure($adminEmail);

// System-Wartung
// Läuft jeden Sonntag um 03:00 Uhr
Schedule::command('system:maintenance')
    ->weeklyOn(0, '03:00')
    ->timezone('Europe/Berlin')
    ->withoutOverlapping(20)
    ->runInBackground()
    ->appendOutputTo($schedulerLog)
    ->onFailure($onFail('system:maintenance'))
    ->description('Führt System-Wartung und Speicher-Optimierung durch')
    ->emailOutputOnFailure($adminEmail);
