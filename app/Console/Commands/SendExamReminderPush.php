<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\PushNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendExamReminderPush extends Command
{
    protected $signature = 'push:send-exam-reminder';

    protected $description = 'Send daily exam reminder push to users with upcoming exam date';

    public function handle()
    {
        $this->info('Sending exam reminder push notifications...');

        $today = Carbon::today();

        $users = User::whereHas('pushSubscriptions')
            ->whereNotNull('exam_date')
            ->where('exam_date', '>=', $today)
            ->get();

        $sent = 0;
        foreach ($users as $user) {
            try {
                $daysLeft = $today->diffInDays(Carbon::parse($user->exam_date));
                $dailyGoal = $user->daily_streak_goal ?? 20;

                $user->notify(
                    (new PushNotification(
                        "Pruefung in {$daysLeft} Tagen",
                        "Dein Tagesziel: {$dailyGoal} Fragen — bleib dran!",
                        '/dashboard'
                    ))->delay(now()->addSeconds(rand(0, 1800)))
                );
                $sent++;
            } catch (\Exception $e) {
                $this->error("Push an User {$user->id} fehlgeschlagen: {$e->getMessage()}");
            }
        }

        $this->info("Exam reminder push sent to {$sent} users.");

        return Command::SUCCESS;
    }
}
