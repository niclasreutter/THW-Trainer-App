<?php

namespace App\Console\Commands;

use App\Models\LearningSession;
use App\Models\LearningSessionInstance;
use App\Services\LernsessionService;
use Illuminate\Console\Command;

class LernsessionLifecycle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lernsession:lifecycle';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lernsession-Lifecycle: Instanzen generieren, aktivieren und abschließen';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starte Lernsession-Lifecycle...');

        try {
            $service = new LernsessionService();

            // 1. Instanzen für wiederkehrende Sessions generieren
            $recurringSessions = LearningSession::where('schedule_type', 'recurring')
                ->where('is_active', true)
                ->get();

            $generated = 0;
            foreach ($recurringSessions as $session) {
                $before = $session->instances()->count();
                $service->generateInstances($session);
                $after = $session->instances()->count();
                $generated += ($after - $before);
            }
            $this->info("  Instanzen generiert: {$generated}");

            // 2. Scheduled Instanzen aktivieren
            $toActivate = LearningSessionInstance::where('status', 'scheduled')
                ->where('starts_at', '<=', now())
                ->get();

            $activated = 0;
            foreach ($toActivate as $instance) {
                $service->activateInstance($instance);
                $activated++;
            }
            $this->info("  Instanzen aktiviert: {$activated}");

            // 3. Aktive Instanzen abschließen
            $toComplete = LearningSessionInstance::where('status', 'active')
                ->where('ends_at', '<=', now())
                ->get();

            $completed = 0;
            foreach ($toComplete as $instance) {
                $result = $service->completeInstance($instance);
                $winner = $result['winner'];
                $this->line("  Session \"{$instance->learningSession->title}\" abgeschlossen"
                    . ($winner ? " — Gewinner: {$winner->name}" : " — Kein Gewinner")
                    . " ({$result['participants_count']} Teilnehmer)");
                $completed++;
            }
            $this->info("  Instanzen abgeschlossen: {$completed}");

            $this->info('Lernsession-Lifecycle abgeschlossen.');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('FEHLER: ' . $e->getMessage());
            $this->error('Datei: ' . $e->getFile() . ':' . $e->getLine());

            return Command::FAILURE;
        }
    }
}
