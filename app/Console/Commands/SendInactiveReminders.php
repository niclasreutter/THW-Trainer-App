<?php

namespace App\Console\Commands;

use App\Mail\InactiveReminderMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendInactiveReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-inactive-reminders {--days=4 : Number of days of inactivity}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder emails to users who have been inactive for X days (default: 4)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $inactiveDays = $this->option('days');
        $this->info("Starting inactive reminder check for users inactive for {$inactiveDays}+ days...");
        
        $inactiveThreshold = Carbon::now()->subDays($inactiveDays);
        $emailsSent = 0;
        $errors = 0;
        $skipped = 0;
        
        // Finde alle Benutzer die:
        // 1. E-Mail-Zustimmung haben (email_consent = true)
        // 2. Seit mindestens X Tagen nicht aktiv waren (last_activity_date < Schwellenwert)
        // 3. Noch keine Inaktivitäts-Mail bekommen haben (inactive_reminder_sent_at IS NULL)
        //    ODER die letzte Mail ist länger als 30 Tage her (um nicht zu spammen)
        $users = User::where('email_consent', true)
            ->whereNotNull('last_activity_date')
            ->where('last_activity_date', '<', $inactiveThreshold)
            ->where(function($query) {
                $query->whereNull('inactive_reminder_sent_at')
                      ->orWhere('inactive_reminder_sent_at', '<', Carbon::now()->subDays(30));
            })
            ->get();
        
        $this->info("Found {$users->count()} users to send inactive reminders to.");
        
        foreach ($users as $user) {
            try {
                // Berechne wie viele Tage der User wirklich inaktiv ist
                $lastActivity = Carbon::parse($user->last_activity_date);
                $daysInactive = $lastActivity->diffInDays(Carbon::now());
                
                $this->info("Sending inactive reminder to: {$user->email} (ID: {$user->id}, {$daysInactive} days inactive)");
                
                // Sende E-Mail
                Mail::to($user->email)->send(new InactiveReminderMail($user, $daysInactive));
                
                $this->info("Successfully sent inactive reminder to: {$user->email}");
                \Log::info('Inactive reminder sent', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'days_inactive' => $daysInactive
                ]);
                
                // Markiere dass die Reminder-Mail gesendet wurde
                $user->inactive_reminder_sent_at = Carbon::now();
                $user->save();
                
                $emailsSent++;
                
                $this->info("Sent reminder to: {$user->name} ({$user->email}) - Inactive for: {$daysInactive} days");
                
            } catch (\Exception $e) {
                $errors++;
                $this->error("Failed to send email to {$user->email}: " . $e->getMessage());
                \Log::error('Failed to send inactive reminder', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
        
        $this->info("Inactive reminder check completed!");
        $this->info("Emails sent: {$emailsSent}");
        $this->info("Skipped: {$skipped}");
        $this->info("Errors: {$errors}");
        
        return Command::SUCCESS;
    }
}

