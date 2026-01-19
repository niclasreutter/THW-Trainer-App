<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserCountHistory;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RecordUserCount extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'user-count:record';

    /**
     * The console command description.
     */
    protected $description = 'Speichert die User-Anzahl vom Vortag in user_count_history und löscht alte Einträge';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Speichere Daten für GESTERN (vollständiger Tag)
            $yesterday = Carbon::yesterday();

            // Zähle User-Stand vom Vortag (Ende des Tages)
            $yesterdayEnd = $yesterday->copy()->endOfDay();
            $totalUsers = User::where('created_at', '<=', $yesterdayEnd)->count();
            $verifiedUsers = User::whereNotNull('email_verified_at')
                ->where('created_at', '<=', $yesterdayEnd)
                ->count();

            // Erstelle oder aktualisiere Eintrag für gestern
            UserCountHistory::updateOrCreate(
                ['date' => $yesterday],
                [
                    'total_users' => $totalUsers,
                    'verified_users' => $verifiedUsers,
                ]
            );

            $this->info("✅ User Count gespeichert für {$yesterday->format('d.m.Y')}");
            $this->info("   Total: {$totalUsers}, Verifiziert: {$verifiedUsers}");

            // Lösche Einträge älter als 30 Tage
            $cutoffDate = Carbon::today()->subDays(30);
            $deleted = UserCountHistory::where('date', '<', $cutoffDate)->delete();

            if ($deleted > 0) {
                $this->info("🗑️  {$deleted} alte Einträge gelöscht (älter als {$cutoffDate->format('d.m.Y')})");
            }

            $this->info("✅ Cronjob erfolgreich abgeschlossen");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ Fehler: " . $e->getMessage());
            $this->error("Stack Trace: " . $e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}
