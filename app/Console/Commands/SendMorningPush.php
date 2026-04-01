<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\PushNotification;
use Illuminate\Console\Command;

class SendMorningPush extends Command
{
    protected $signature = 'push:send-morning';

    protected $description = 'Send good morning push notification to all users with push subscriptions';

    public function handle()
    {
        $this->info('Sending morning push notifications...');

        $users = User::whereHas('pushSubscriptions')->get();

        $sent = 0;
        foreach ($users as $user) {
            try {
                $user->notify(
                    (new PushNotification(
                        'Guten Morgen!',
                        'Zeit zum Lernen — starte jetzt deine taegliche Session.',
                        '/dashboard'
                    ))->delay(now()->addSeconds(rand(0, 1800)))
                );
                $sent++;
            } catch (\Exception $e) {
                $this->error("Push an User {$user->id} fehlgeschlagen: {$e->getMessage()}");
            }
        }

        $this->info("Morning push sent to {$sent} users.");

        return Command::SUCCESS;
    }
}
