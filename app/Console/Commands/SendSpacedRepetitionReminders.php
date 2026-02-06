<?php

namespace App\Console\Commands;

use App\Mail\SpacedRepetitionReminderMail;
use App\Models\User;
use App\Services\SpacedRepetitionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendSpacedRepetitionReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-spaced-repetition-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder emails to users who have spaced repetition reviews due';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting spaced repetition reminder check...');

        $srService = new SpacedRepetitionService();
        $emailsSent = 0;
        $errors = 0;

        // Finde alle Benutzer mit E-Mail-Zustimmung
        $users = User::where('email_consent', true)->get();

        $this->info("Found {$users->count()} users with email consent.");

        foreach ($users as $user) {
            try {
                $dueCount = $srService->getDueCount($user->id);

                if ($dueCount === 0) {
                    continue;
                }

                $this->info("Sending reminder to: {$user->email} (ID: {$user->id}, Due: {$dueCount} questions)");

                Mail::to($user->email)->send(new SpacedRepetitionReminderMail($user, $dueCount));

                \Log::info('Spaced repetition reminder sent', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'due_count' => $dueCount,
                ]);

                $emailsSent++;
            } catch (\Exception $e) {
                $errors++;
                $this->error("Failed to send email to {$user->email}: " . $e->getMessage());
                \Log::error('Failed to send spaced repetition reminder', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Spaced repetition reminder check completed!");
        $this->info("Emails sent: {$emailsSent}");
        $this->info("Errors: {$errors}");

        return Command::SUCCESS;
    }
}
