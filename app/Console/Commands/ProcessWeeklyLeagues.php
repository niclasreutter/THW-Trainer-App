<?php

namespace App\Console\Commands;

use App\Services\LeagueService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

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
    protected $description = 'Verarbeitet wöchentliche Liga-Auf-/Abstiege und Lootbox-Vergabe';

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
            $this->line("  → Aufstiege: {$results['promotions']}");
            $this->line("  → Abstiege: {$results['relegations']}");
            $this->line("  → Lootboxen vergeben: {$results['lootboxes_awarded']}");

            Log::info('Wöchentliche Liga-Verarbeitung via Scheduler abgeschlossen', $results);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Fehler bei der Liga-Verarbeitung: ' . $e->getMessage());
            Log::error('Wöchentliche Liga-Verarbeitung fehlgeschlagen', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return Command::FAILURE;
        }
    }
}
