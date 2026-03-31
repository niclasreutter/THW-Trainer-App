<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\PushNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendStreakPush extends Command
{
    protected $signature = 'push:send-streak {time : morning, afternoon, or evening}';

    protected $description = 'Send streak reminder push to users who have not completed their daily goal';

    private const MESSAGES = [
        'morning' => [
            'title' => 'Streak-Erinnerung',
            'body' => 'Du hast heute noch %d Fragen offen fuer deinen Streak.',
        ],
        'afternoon' => [
            'title' => 'Streak-Erinnerung',
            'body' => 'Noch %d Fragen fuer deinen %d-Tage-Streak!',
        ],
        'evening' => [
            'title' => 'Letzte Chance!',
            'body' => 'Noch %d Fragen, sonst geht dein %d-Tage-Streak verloren!',
        ],
    ];

    public function handle()
    {
        $time = $this->argument('time');

        if (!isset(self::MESSAGES[$time])) {
            $this->error("Invalid time argument: {$time}. Use morning, afternoon, or evening.");
            return Command::FAILURE;
        }

        $this->info("Sending streak push ({$time})...");

        $today = Carbon::today();

        $users = User::whereHas('pushSubscriptions')
            ->where('streak_days', '>=', 1)
            ->where(function ($query) use ($today) {
                $query->whereNull('last_activity_date')
                      ->orWhere('last_activity_date', '!=', $today);
            })
            ->get();

        $sent = 0;
        foreach ($users as $user) {
            try {
                $dailyGoal = $user->daily_streak_goal ?? 20;
                $remaining = max(0, $dailyGoal - $user->daily_questions_solved);

                if ($remaining === 0) {
                    continue;
                }

                $template = self::MESSAGES[$time];

                if ($time === 'morning') {
                    $body = sprintf($template['body'], $remaining);
                } else {
                    $body = sprintf($template['body'], $remaining, $user->streak_days);
                }

                $user->notify(
                    (new PushNotification(
                        $template['title'],
                        $body,
                        '/dashboard'
                    ))->delay(now()->addSeconds(rand(0, 1800)))
                );
                $sent++;
            } catch (\Exception $e) {
                $this->error("Push an User {$user->id} fehlgeschlagen: {$e->getMessage()}");
            }
        }

        $this->info("Streak push ({$time}) sent to {$sent} users.");

        return Command::SUCCESS;
    }
}
