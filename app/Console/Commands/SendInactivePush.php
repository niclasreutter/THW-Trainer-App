<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\PushNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendInactivePush extends Command
{
    protected $signature = 'push:send-inactive';

    protected $description = 'Send reminder push to users inactive for 7+ days';

    public function handle()
    {
        $this->info('Sending inactive user push notifications...');

        $sevenDaysAgo = Carbon::today()->subDays(7);

        $users = User::whereHas('pushSubscriptions')
            ->where(function ($query) use ($sevenDaysAgo) {
                $query->where('last_activity_at', '<=', $sevenDaysAgo)
                      ->orWhereNull('last_activity_at');
            })
            ->get();

        $sent = 0;
        foreach ($users as $user) {
            try {
                $user->notify(
                    (new PushNotification(
                        'Wir vermissen dich!',
                        'Es ist eine Weile her — starte jetzt eine kurze Lernsession.',
                        '/dashboard'
                    ))->delay(now()->addSeconds(rand(0, 1800)))
                );
                $sent++;
            } catch (\Exception $e) {
                $this->error("Push an User {$user->id} fehlgeschlagen: {$e->getMessage()}");
            }
        }

        $this->info("Inactive push sent to {$sent} users.");

        return Command::SUCCESS;
    }
}
