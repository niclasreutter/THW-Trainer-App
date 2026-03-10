<?php

namespace App\Console\Commands;

use App\Mail\ExamFeedbackRequestMail;
use App\Models\ExamFeedback;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SendExamFeedbackRequests extends Command
{
    protected $signature = 'exam:send-feedback-requests';

    protected $description = 'Sendet Feedback-Anfragen an User deren Prüfung vor einer Woche war';

    public function handle()
    {
        $this->info('Starte Feedback-Anfrage-Versand...');

        $oneWeekAgo = Carbon::now('Europe/Berlin')->subDays(7)->toDateString();
        $emailsSent = 0;
        $errors = 0;

        $users = User::where('email_consent', true)
            ->whereNotNull('exam_date')
            ->whereDate('exam_date', $oneWeekAgo)
            ->whereNull('exam_feedback_request_sent_at')
            ->get();

        $this->info("Gefunden: {$users->count()} User mit Prüfung vor einer Woche.");

        foreach ($users as $user) {
            try {
                $token = Str::random(64);

                ExamFeedback::create([
                    'user_id' => $user->id,
                    'token' => $token,
                ]);

                $feedbackUrl = url("/exam-feedback/{$token}");

                Mail::to($user->email)->send(new ExamFeedbackRequestMail($user, $feedbackUrl));

                $user->exam_feedback_request_sent_at = Carbon::now();
                $user->save();

                $emailsSent++;
                $this->info("Feedback-Anfrage gesendet an: {$user->name} ({$user->email})");

                \Log::info('Exam feedback request sent', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'exam_date' => $user->exam_date->toDateString(),
                    'token' => $token,
                ]);
            } catch (\Exception $e) {
                $errors++;
                $this->error("Fehler bei {$user->email}: " . $e->getMessage());
                \Log::error('Failed to send exam feedback request', [
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
