<?php

namespace App\Console\Commands;

use App\Mail\AccountDeletedMail;
use App\Mail\AccountDeletionWarningMail;
use App\Mail\ExamFeedbackRequestMail;
use App\Mail\ExamGoodLuckMail;
use App\Mail\ExamReminderMail;
use App\Mail\InactiveReminderMail;
use App\Mail\NewsletterMail;
use App\Mail\ResetPasswordMail;
use App\Mail\SpacedRepetitionReminderMail;
use App\Mail\StreakFreezeActivatedMail;
use App\Mail\StreakLostMail;
use App\Mail\StreakReminderMail;
use App\Mail\VerifyRegistrationMail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMailSend extends Command
{
    protected $signature = 'mail:test {to} {--all : Send all mail templates}';
    protected $description = 'Send test mails to the given address';

    public function handle()
    {
        $to = $this->argument('to');

        if (!$this->option('all')) {
            Mail::raw('Dies ist eine Testmail vom THW-Trainer!', function ($message) use ($to) {
                $message->to($to)->subject('THW-Trainer Testmail');
            });
            $this->info('Testmail wurde an ' . $to . ' gesendet.');
            return;
        }

        $user = User::first();
        if (!$user) {
            $this->error('Kein User in der Datenbank gefunden.');
            return;
        }

        $mails = [
            'VerifyRegistration' => new VerifyRegistrationMail('ABC123'),
            'ResetPassword' => new ResetPasswordMail(url('/reset-password/test-token')),
            'AccountDeletionWarning' => new AccountDeletionWarningMail($user),
            'AccountDeleted' => new AccountDeletedMail($user),
            'ExamGoodLuck' => new ExamGoodLuckMail($user),
            'ExamFeedbackRequest' => new ExamFeedbackRequestMail($user, url('/exam-feedback/test')),
            'ExamReminder' => new ExamReminderMail($user, [
                'days_left' => 14,
                'daily_target' => 20,
                'today_answered' => 5,
                'today_remaining' => 15,
                'mastered_count' => 120,
                'total_questions' => 300,
                'progress_percent' => 40,
            ]),
            'StreakReminder' => new StreakReminderMail($user, 7),
            'StreakLost' => new StreakLostMail($user, 12),
            'StreakFreezeActivated' => new StreakFreezeActivatedMail($user, 7, 2),
            'SpacedRepetitionReminder' => new SpacedRepetitionReminderMail($user, 15),
            'InactiveReminder' => new InactiveReminderMail($user, 7),
            'Newsletter' => new NewsletterMail($user, 'Test Newsletter', '<h1>Test Newsletter</h1><p>Dies ist ein Test-Newsletter vom THW-Trainer.</p>'),
        ];

        $sent = 0;
        $failed = 0;

        foreach ($mails as $name => $mailable) {
            try {
                Mail::to($to)->send($mailable);
                $this->info("✓ {$name}");
                $sent++;
            } catch (\Throwable $e) {
                $this->error("✗ {$name}: " . $e->getMessage());
                $failed++;
            }
        }

        $this->newLine();
        $this->info("Fertig: {$sent} gesendet, {$failed} fehlgeschlagen → {$to}");
    }
}
