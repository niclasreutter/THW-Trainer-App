<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\PushNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendExamGoodLuckPush extends Command
{
    protected $signature = 'push:send-exam-goodluck';

    protected $description = 'Send good luck push to users whose exam is today';

    public function handle()
    {
        $this->info('Sending exam good luck push notifications...');

        $today = Carbon::today();

        $users = User::whereHas('pushSubscriptions')
            ->where('exam_date', $today)
            ->get();

        $sent = 0;
        foreach ($users as $user) {
            try {
                $user->notify(
                    (new PushNotification(
                        'Viel Erfolg bei deiner Pruefung!',
                        'Heute ist der Tag — du hast dich gut vorbereitet. Du schaffst das!',
                        '/dashboard'
                    ))->delay(now()->addSeconds(rand(0, 1800)))
                );
                $sent++;
            } catch (\Exception $e) {
                $this->error("Push an User {$user->id} fehlgeschlagen: {$e->getMessage()}");
            }
        }

        $this->info("Exam good luck push sent to {$sent} users.");

        return Command::SUCCESS;
    }
}
