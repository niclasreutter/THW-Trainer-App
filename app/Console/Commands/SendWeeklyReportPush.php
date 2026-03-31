<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\PushNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendWeeklyReportPush extends Command
{
    protected $signature = 'push:send-weekly-report';

    protected $description = 'Send weekly summary push to users with push subscriptions';

    public function handle()
    {
        $this->info('Sending weekly report push notifications...');

        $users = User::whereHas('pushSubscriptions')->get();

        $sent = 0;
        foreach ($users as $user) {
            try {
                $weeklyXp = $user->xpHistories()
                    ->where('created_at', '>=', Carbon::now()->subWeek())
                    ->sum('points');

                $weeklyQuestions = $user->xpHistories()
                    ->where('created_at', '>=', Carbon::now()->subWeek())
                    ->where('source_type', 'question')
                    ->count();

                if ($weeklyXp == 0 && $weeklyQuestions == 0) {
                    continue;
                }

                $user->notify(
                    (new PushNotification(
                        'Dein Wochenrueckblick',
                        "Letzte Woche: {$weeklyQuestions} Fragen beantwortet, {$weeklyXp} XP gesammelt!",
                        '/dashboard'
                    ))->delay(now()->addSeconds(rand(0, 1800)))
                );
                $sent++;
            } catch (\Exception $e) {
                $this->error("Push an User {$user->id} fehlgeschlagen: {$e->getMessage()}");
            }
        }

        $this->info("Weekly report push sent to {$sent} users.");

        return Command::SUCCESS;
    }
}
