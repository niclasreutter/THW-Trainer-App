<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendStreakReminderPush extends Command
{
    protected $signature = 'push:streak-reminder {--urgent : Letzte Erinnerung (21:00)}';

    protected $description = 'Sendet Streak-Erinnerung Push an User die heute noch nicht gelernt haben';

    public function handle(): int
    {
        $isUrgent = $this->option('urgent');
        $this->info('Starte Streak-Erinnerung Push' . ($isUrgent ? ' (DRINGEND)' : '') . '...');

        $today = Carbon::today();
        $sent = 0;
        $errors = 0;

        // User mit aktivem Streak, heute nicht gelernt, mit Push-Subscription
        $users = User::where('streak_days', '>', 0)
            ->where(function ($query) use ($today) {
                $query->whereNull('last_activity_date')
                      ->orWhere('last_activity_date', '!=', $today);
            })
            ->whereHas('pushSubscriptions', fn($q) => $q->where('is_active', true))
            ->get();

        $this->info("Gefunden: {$users->count()} User mit gefaehrdetem Streak.");

        foreach ($users as $user) {
            try {
                $days = $user->streak_days;
                $message = $isUrgent
                    ? "Letzte Chance! Dein {$days}-Tage-Streak laeuft heute aus."
                    : "Dein Streak von {$days} Tagen ist in Gefahr! Beantworte ein paar Fragen, um ihn zu halten.";

                $type = $isUrgent ? 'streak_reminder_urgent' : 'streak_reminder';

                // Pruefen ob heute schon eine Streak-Erinnerung gesendet wurde
                $alreadySent = Notification::where('user_id', $user->id)
                    ->where('type', $type)
                    ->whereDate('created_at', $today)
                    ->exists();

                if ($alreadySent) {
                    continue;
                }

                Notification::create([
                    'user_id' => $user->id,
                    'type' => $type,
                    'title' => $isUrgent ? 'Streak in Gefahr!' : 'Streak-Erinnerung',
                    'message' => $message,
                    'icon' => 'bi-fire',
                    'data' => ['streak_days' => $days, 'urgent' => $isUrgent],
                ]);

                $sent++;
            } catch (\Throwable $e) {
                $errors++;
                $this->error("Fehler bei User {$user->id}: {$e->getMessage()}");
                \Log::error('Streak-Reminder Push fehlgeschlagen', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Streak-Erinnerung Push abgeschlossen: {$sent} gesendet, {$errors} Fehler.");

        return Command::SUCCESS;
    }
}
