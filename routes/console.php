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
