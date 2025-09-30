<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Mail\AccountDeletionWarningMail;
use App\Mail\AccountDeletedMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CleanupUnconfirmedAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accounts:cleanup-unconfirmed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Benachrichtigt und löscht unbestätigte Accounts nach 7 bzw. 9 Tagen';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starte Bereinigung unbestätigter Accounts...');
        
        // Accounts die seit 7 Tagen nicht bestätigt wurden (Warnung senden)
        $this->sendWarningEmails();
        
        // Accounts die seit 9 Tagen nicht bestätigt wurden (löschen)
        $this->deleteOldAccounts();
        
        $this->info('Bereinigung abgeschlossen.');
        
        return Command::SUCCESS;
    }

    /**
     * Sendet Warn-E-Mails an Accounts die seit 7 Tagen nicht bestätigt wurden
     */
    private function sendWarningEmails()
    {
        $sevenDaysAgo = Carbon::now()->subDays(7);
        $nineDaysAgo = Carbon::now()->subDays(9);
        
        // Accounts die zwischen 7 und 9 Tagen alt sind und noch keine Warnung erhalten haben
        $usersToWarn = User::whereNull('email_verified_at')
            ->whereNull('deletion_warning_sent_at')
            ->where('created_at', '>=', $nineDaysAgo)
            ->where('created_at', '<=', $sevenDaysAgo)
            ->get();

        $this->info("Sende Warnungen an {$usersToWarn->count()} Accounts...");

        foreach ($usersToWarn as $user) {
            try {
                Mail::to($user->email)->send(new AccountDeletionWarningMail($user));
                
                // Markiere dass Warnung gesendet wurde
                $user->update(['deletion_warning_sent_at' => now()]);
                
                $this->line("Warnung gesendet an: {$user->email}");
                Log::info("Account deletion warning sent to user: {$user->email} (ID: {$user->id})");
                
            } catch (\Exception $e) {
                $this->error("Fehler beim Senden der Warnung an {$user->email}: " . $e->getMessage());
                Log::error("Failed to send deletion warning to {$user->email}: " . $e->getMessage());
            }
        }
    }

    /**
     * Löscht Accounts die seit 9 Tagen nicht bestätigt wurden
     */
    private function deleteOldAccounts()
    {
        $nineDaysAgo = Carbon::now()->subDays(9);
        
        $usersToDelete = User::whereNull('email_verified_at')
            ->where('created_at', '<=', $nineDaysAgo)
            ->get();

        $this->info("Lösche {$usersToDelete->count()} alte unbestätigte Accounts...");

        foreach ($usersToDelete as $user) {
            try {
                // Sende finale Benachrichtigung vor Löschung
                Mail::to($user->email)->send(new AccountDeletedMail($user));
                
                // Lösche den Account
                $userEmail = $user->email;
                $user->delete();
                
                $this->line("Account gelöscht: {$userEmail}");
                Log::info("Unconfirmed account deleted: {$userEmail} (was created at: {$user->created_at})");
                
            } catch (\Exception $e) {
                $this->error("Fehler beim Löschen des Accounts {$user->email}: " . $e->getMessage());
                Log::error("Failed to delete unconfirmed account {$user->email}: " . $e->getMessage());
            }
        }
    }
}