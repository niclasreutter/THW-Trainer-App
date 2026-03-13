<?php

namespace App\Console\Commands;

use App\Services\LeagueService;
use Illuminate\Console\Command;

class ProcessWeeklyLeagues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'league:process-weekly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Wöchentliche Liga-Verarbeitung: Auf-/Abstiege + Wochenbelohnungen';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starte wöchentliche Liga-Verarbeitung...');

        try {
            $leagueService = new LeagueService();
            $results = $leagueService->processWeeklyLeagues();

            $this->info('Liga-Verarbeitung abgeschlossen:');
            $this->info("  → Aufstiege: {$results['promotions']}");
            $this->info("  → Abstiege: {$results['relegations']}");
            $this->info("  → Wochenbelohnungen vergeben: {$results['lootboxes_awarded']}");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('FEHLER: ' . $e->getMessage());
            $this->error('Datei: ' . $e->getFile() . ':' . $e->getLine());

            return Command::FAILURE;
        }
    }
}
