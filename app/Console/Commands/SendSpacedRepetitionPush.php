<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserQuestionProgress;
use App\Notifications\PushNotification;
use App\Services\SpacedRepetitionService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendSpacedRepetitionPush extends Command
{
    protected $signature = 'push:send-spaced-repetition';

    protected $description = 'Send push to users with due spaced repetition reviews';

    public function handle()
    {
        $this->info('Sending spaced repetition push notifications...');

        $today = Carbon::today();
        $srService = new SpacedRepetitionService();

        $users = User::whereHas('pushSubscriptions')
            ->whereHas('questionProgress', function ($query) use ($today) {
                $query->whereNotNull('next_review_at')
                    ->where('next_review_at', '<=', $today);
            })
            ->get();

        $sent = 0;
        foreach ($users as $user) {
            try {
                $dueCount = $srService->getDueCount($user->id);

                if ($dueCount === 0) {
                    continue;
                }

                $user->notify(
                    (new PushNotification(
                        'Wiederholungen faellig',
                        "{$dueCount} Fragen warten auf Wiederholung — jetzt auffrischen!",
                        '/dashboard'
                    ))->delay(now()->addSeconds(rand(0, 1800)))
                );
                $sent++;
            } catch (\Exception $e) {
                $this->error("Push an User {$user->id} fehlgeschlagen: {$e->getMessage()}");
            }
        }

        $this->info("Spaced repetition push sent to {$sent} users.");

        return Command::SUCCESS;
    }
}
