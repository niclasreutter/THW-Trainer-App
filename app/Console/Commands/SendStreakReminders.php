<?php

namespace App\Console\Commands;

use App\Mail\StreakReminderMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendStreakReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'streak:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send streak reminder emails to users who have not learned today but have an active streak';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting streak reminder check...');
        
        $today = Carbon::today();
        $emailsSent = 0;
        $errors = 0;
        
        // Finde alle Benutzer die:
        // 1. E-Mail-Zustimmung haben (email_consent = true)
        // 2. Einen Streak > 1 haben
        // 3. Heute noch nicht aktiv waren (last_activity_date != heute)
        $users = User::where('email_consent', true)
            ->where('streak_days', '>', 1)
            ->where(function($query) use ($today) {
                $query->whereNull('last_activity_date')
                      ->orWhere('last_activity_date', '!=', $today);
            })
            ->get();
        
        $this->info("Found {$users->count()} users to send reminders to.");
        
        foreach ($users as $user) {
            try {
                // Prüfe ob der Benutzer heute wirklich noch nicht aktiv war
                $lastActivity = $user->last_activity_date ? Carbon::parse($user->last_activity_date) : null;
                
                if (!$lastActivity || $lastActivity->lt($today)) {
                    // Sende E-Mail
                    Mail::to($user->email)->send(new StreakReminderMail($user, $user->streak_days));
                    $emailsSent++;
                    
                    $this->info("Sent reminder to: {$user->name} ({$user->email}) - Streak: {$user->streak_days} days");
                }
                
            } catch (\Exception $e) {
                $errors++;
                $this->error("Failed to send email to {$user->email}: " . $e->getMessage());
            }
        }
        
        $this->info("Streak reminder check completed!");
        $this->info("Emails sent: {$emailsSent}");
        $this->info("Errors: {$errors}");
        
        return Command::SUCCESS;
    }
}
