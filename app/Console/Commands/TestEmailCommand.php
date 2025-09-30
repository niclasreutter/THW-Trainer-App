<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Mail\AccountDeletionWarningMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sendet eine Test-E-Mail für Account-Löschung Warnung';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("Sende Test-E-Mail an: {$email}");
        
        // Erstelle einen Test-User
        $user = new User();
        $user->name = 'Test User';
        $user->email = $email;
        $user->created_at = now()->subDays(8); // 8 Tage alt für Test
        
        try {
            Mail::to($email)->send(new AccountDeletionWarningMail($user));
            $this->info("✅ E-Mail erfolgreich gesendet!");
        } catch (\Exception $e) {
            $this->error("❌ Fehler beim Senden: " . $e->getMessage());
        }
        
        return Command::SUCCESS;
    }
}