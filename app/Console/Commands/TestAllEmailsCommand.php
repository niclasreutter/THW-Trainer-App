<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Mail\VerifyRegistrationMail;
use App\Mail\ResetPasswordMail;
use App\Mail\AccountDeletionWarningMail;
use App\Mail\AccountDeletedMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class TestAllEmailsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:all-emails {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sendet alle E-Mail-Templates als Test';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("Sende alle E-Mail-Templates an: {$email}");
        
        // Erstelle Test-User
        $testUser = new User();
        $testUser->name = 'Test User';
        $testUser->email = $email;
        $testUser->created_at = now()->subDays(8);
        
        $testEmails = [
            '1. Registrierungs-Bestätigung' => function() use ($testUser, $email) {
                Mail::to($email)->send(new VerifyRegistrationMail('https://test.thw-trainer.de/verify/test', $testUser->name));
            },
            '2. Passwort-Reset' => function() use ($testUser, $email) {
                Mail::to($email)->send(new ResetPasswordMail('https://test.thw-trainer.de/reset-password/test'));
            },
            '3. Account-Löschung Warnung (7 Tage)' => function() use ($testUser, $email) {
                Mail::to($email)->send(new AccountDeletionWarningMail($testUser));
            },
            '4. Account gelöscht (9 Tage)' => function() use ($testUser, $email) {
                Mail::to($email)->send(new AccountDeletedMail($testUser));
            }
        ];
        
        foreach ($testEmails as $name => $sendFunction) {
            try {
                $this->info("Sende: {$name}...");
                $sendFunction();
                $this->info("✅ {$name} erfolgreich gesendet!");
                $this->line('');
            } catch (\Exception $e) {
                $this->error("❌ Fehler bei {$name}: " . $e->getMessage());
                $this->line('');
            }
        }
        
        $this->info("🎉 Alle E-Mail-Tests abgeschlossen!");
        $this->line("Prüfe dein E-Mail-Postfach: {$email}");
        
        return Command::SUCCESS;
    }
}