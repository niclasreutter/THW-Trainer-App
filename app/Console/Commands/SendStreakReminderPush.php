<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendStreakReminderPush extends Command
{
    protected $signature = 'push:streak-reminder {--urgent : Letzte Erinnerung (21:00)} {--morning : Motivations-Push am Morgen (08:00)}';

    protected $description = 'Sendet Streak-Erinnerung Push an User die heute noch nicht gelernt haben';

    public function handle(): int
    {
        $isUrgent = $this->option('urgent');
        $isMorning = $this->option('morning');
        $label = $isMorning ? ' (MORGEN)' : ($isUrgent ? ' (DRINGEND)' : '');
        $this->info('Starte Streak-Erinnerung Push' . $label . '...');

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

                if ($isMorning) {
                    $messages = [
                        "Guten Morgen! Halte deinen {$days}-Tage-Streak am Leben — lerne heute ein paar Fragen.",
                        "Dein {$days}-Tage-Streak wartet auf dich! Ein paar Fragen genuegen.",
                        "Tag {$days} deines Streaks! Lerne heute weiter und bleib dran.",
                    ];
                    $message = $messages[array_rand($messages)];
                } elseif ($isUrgent) {
                    $message = "Letzte Chance! Dein {$days}-Tage-Streak laeuft heute aus.";
                } else {
                    $message = "Dein Streak von {$days} Tagen ist in Gefahr! Beantworte ein paar Fragen, um ihn zu halten.";
                }

                $type = $isMorning ? 'streak_reminder_morning' : ($isUrgent ? 'streak_reminder_urgent' : 'streak_reminder');

                // Pruefen ob heute schon eine Streak-Erinnerung gesendet wurde
                $alreadySent = Notification::where('user_id', $user->id)
                    ->where('type', $type)
                    ->whereDate('created_at', $today)
                    ->exists();

                if ($alreadySent) {
                    continue;
                }

                $title = $isMorning ? 'Streak halten' : ($isUrgent ? 'Streak in Gefahr!' : 'Streak-Erinnerung');

                Notification::create([
                    'user_id' => $user->id,
                    'type' => $type,
                    'title' => $title,
                    'message' => $message,
                    'icon' => 'bi-fire',
                    'data' => ['streak_days' => $days, 'urgent' => $isUrgent, 'morning' => $isMorning],
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
