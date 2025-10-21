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
    protected $signature = 'app:send-streak-reminders';

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
        // 3. Heute noch keine Fragen beantwortet haben (daily_questions_date != heute)
        $users = User::where('email_consent', true)
            ->where('streak_days', '>', 1)
            ->where(function($query) use ($today) {
                $query->whereNull('daily_questions_date')
                      ->orWhere('daily_questions_date', '!=', $today);
            })
            ->get();
        
        $this->info("Found {$users->count()} users to send reminders to.");
        
        foreach ($users as $user) {
            try {
                // Prüfe ob der Benutzer heute wirklich noch keine Fragen beantwortet hat
                $lastDailyActivity = $user->daily_questions_date ? Carbon::parse($user->daily_questions_date) : null;
                
                if (!$lastDailyActivity || $lastDailyActivity->lt($today)) {
                    $this->info("Sending streak reminder to: {$user->email} (ID: {$user->id}, Streak: {$user->streak_days} days)");
                    
                    // Sende E-Mail
                    Mail::to($user->email)->send(new StreakReminderMail($user, $user->streak_days));
                    
                    \Log::info('Streak reminder sent', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                        'streak_days' => $user->streak_days
                    ]);
                    
                    $emailsSent++;
                    
                    $this->info("Sent reminder to: {$user->name} ({$user->email}) - Streak: {$user->streak_days} days");
                }
                
            } catch (\Exception $e) {
                $errors++;
                $this->error("Failed to send email to {$user->email}: " . $e->getMessage());
                \Log::error('Failed to send streak reminder', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
        
        $this->info("Streak reminder check completed!");
        $this->info("Emails sent: {$emailsSent}");
        $this->info("Errors: {$errors}");
        
        return Command::SUCCESS;
    }
}
