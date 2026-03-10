<?php

namespace App\Console\Commands;

use App\Mail\ExamGoodLuckMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendExamGoodLuck extends Command
{
    protected $signature = 'exam:send-goodluck';

    protected $description = 'Sendet Viel-Erfolg-Mails an User deren Prüfung morgen stattfindet';

    public function handle()
    {
        $this->info('Starte Viel-Erfolg-Mail-Versand...');

        $tomorrow = Carbon::tomorrow('Europe/Berlin')->toDateString();
        $emailsSent = 0;
        $errors = 0;

        $users = User::where('email_consent', true)
            ->whereNotNull('exam_date')
            ->whereDate('exam_date', $tomorrow)
            ->whereNull('exam_goodluck_sent_at')
            ->get();

        $this->info("Gefunden: {$users->count()} User mit Prüfung morgen.");

        foreach ($users as $user) {
            try {
                Mail::to($user->email)->send(new ExamGoodLuckMail($user));

                $user->exam_goodluck_sent_at = Carbon::now();
                $user->save();

                $emailsSent++;
                $this->info("Viel-Erfolg-Mail gesendet an: {$user->name} ({$user->email})");

                \Log::info('Exam good luck mail sent', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'exam_date' => $user->exam_date->toDateString(),
                ]);
            } catch (\Exception $e) {
                $errors++;
                $this->error("Fehler bei {$user->email}: " . $e->getMessage());
                \Log::error('Failed to send exam good luck mail', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Fertig! Gesendet: {$emailsSent}, Fehler: {$errors}");

        return Command::SUCCESS;
    }
}
