<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\LeagueService;

class ProcessWeeklyLeagues extends Command
{
    protected $signature = 'leagues:process-weekly';
    protected $description = 'Verarbeitet wöchentliche Liga-Auf- und Abstiege';

    public function handle()
    {
        $leagueService = new LeagueService();
        $leagueService->processWeeklyPromotions();
        $this->info('Liga-Verarbeitung abgeschlossen.');
    }
}
