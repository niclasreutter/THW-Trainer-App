<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\ContactMessage;
use App\Mail\VerifyRegistrationMail;
use App\Mail\ResetPasswordMail;
use App\Mail\AccountDeletionWarningMail;
use App\Mail\AccountDeletedMail;
use App\Mail\StreakReminderMail;
use App\Mail\InactiveReminderMail;
use App\Mail\NewsletterMail;
use App\Mail\ContactMail;
use App\Mail\ExamReminderMail;
use App\Mail\ExamGoodLuckMail;
use App\Mail\ExamFeedbackRequestMail;
use App\Models\ExamFeedback;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TestAllEmailsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:all-emails {email} {--only= : Nur eine bestimmte Mail senden (z.B. exam-goodluck)} {--render : Mail nur rendern, nicht senden}';

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
        $this->line('');

        // Erstelle Test-User mit realistischen Daten
        $testUser = new User();
        $testUser->id = 1;
        $testUser->name = 'Test User';
        $testUser->email = $email;
        $testUser->created_at = now()->subDays(8);
        $testUser->points = 1250;
        $testUser->level = 5;
        $testUser->streak_days = 7;
        $testUser->solved_questions = [];

        // Erstelle Test-Kontaktnachricht
        $testContactMessage = new ContactMessage();
        $testContactMessage->type = 'feedback';
        $testContactMessage->email = $email;
        $testContactMessage->message = "Dies ist eine Test-Kontaktnachricht.\n\nMit freundlichen Grüßen\nTest User";
        $testContactMessage->hermine_contact = false;
        $testContactMessage->vorname = null;
        $testContactMessage->nachname = null;
        $testContactMessage->ortsverband = null;
        $testContactMessage->error_location = null;
        $testContactMessage->setRelation('user', $testUser);

        $testEmails = [
            '1. Registrierungs-Bestätigung (verify-registration)' => function() use ($testUser, $email) {
                Mail::to($email)->send(new VerifyRegistrationMail('https://' . config('domains.app') . '/verify/test-token'));
            },
            '2. Passwort-Reset (reset-password)' => function() use ($email) {
                Mail::to($email)->send(new ResetPasswordMail('https://' . config('domains.app') . '/reset-password/test-token'));
            },
            '3. E-Mail-Bestätigung (verify-email)' => function() use ($testUser, $email) {
                Mail::send('emails.verify-email', [
                    'user' => $testUser,
                    'verificationUrl' => 'https://' . config('domains.app') . '/verify-email/test-token'
                ], function ($message) use ($email) {
                    $message->to($email)
                            ->subject('E-Mail-Adresse bestätigen');
                });
            },
            '4. Account-Löschung Warnung (account-deletion-warning)' => function() use ($testUser, $email) {
                Mail::to($email)->send(new AccountDeletionWarningMail($testUser));
            },
            '5. Account gelöscht (account-deleted)' => function() use ($testUser, $email) {
                Mail::to($email)->send(new AccountDeletedMail($testUser));
            },
            '6. Streak-Erinnerung (streak-reminder)' => function() use ($testUser, $email) {
                Mail::to($email)->send(new StreakReminderMail($testUser, 7));
            },
            '7. Inaktivitäts-Erinnerung (inactive-reminder)' => function() use ($testUser, $email) {
                // Für diese Mail muss der User in der DB existieren, also senden wir die View direkt
                Mail::send('emails.inactive-reminder', [
                    'user' => $testUser,
                    'daysInactive' => 14,
                    'remainingQuestions' => 45,
                    'totalQuestions' => 200,
                    'progressPercentage' => 78,
                    'masteredQuestions' => 155,
                ], function ($message) use ($email) {
                    $message->to($email)
                            ->subject('Du fehlst uns! Nur noch 45 Fragen bis zum Ziel');
                });
            },
            '8. Newsletter (newsletter)' => function() use ($testUser, $email) {
                $htmlContent = '
                    <h2 style="color:#003399;">Willkommen zum THW-Trainer Newsletter!</h2>
                    <p>Hallo ' . $testUser->name . ',</p>
                    <p>Dies ist ein Test-Newsletter mit dem neuen Design.</p>

                    <div class="info-card">
                        <strong>Info-Box:</strong> Das ist eine wichtige Information.
                    </div>

                    <div class="warning-card">
                        <strong>Warnung:</strong> Das ist eine Warnung.
                    </div>

                    <div class="success-card">
                        <strong>Erfolg:</strong> Das hat funktioniert!
                    </div>

                    <p style="text-align:center;">
                        <a href="https://' . config('domains.app') . '" class="glow-button">Jetzt lernen</a>
                    </p>

                    <p>Viele Grüße,<br>Dein THW-Trainer Team</p>
                ';
                Mail::to($email)->send(new NewsletterMail($testUser, 'Test-Newsletter THW-Trainer', $htmlContent));
            },
            '9. Kontaktformular (contact)' => function() use ($testContactMessage, $email) {
                Mail::to($email)->send(new ContactMail($testContactMessage));
            },
            '10. Prüfungs-Erinnerung (exam-reminder)' => function() use ($testUser, $email) {
                Mail::to($email)->send(new ExamReminderMail($testUser, [
                    'days_left' => 14,
                    'daily_target' => 25,
                    'today_answered' => 8,
                    'mastered_count' => 120,
                    'total_questions' => 400,
                    'progress_percent' => 30,
                ]));
            },
            '11. Viel-Erfolg-Mail (exam-goodluck)' => function() use ($testUser, $email) {
                Mail::to($email)->send(new ExamGoodLuckMail($testUser));
            },
            '12. Feedback-Anfrage (exam-feedback-request)' => function() use ($testUser, $email) {
                // Echten DB-User finden oder ersten nehmen
                $dbUser = User::where('email', $email)->first() ?? User::first();
                $token = 'test-' . Str::random(32);

                ExamFeedback::create([
                    'user_id' => $dbUser->id,
                    'token' => $token,
                ]);

                $feedbackUrl = url("/exam-feedback/{$token}");
                Mail::to($email)->send(new ExamFeedbackRequestMail($testUser, $feedbackUrl));

                $this->line("  Feedback-URL: {$feedbackUrl}");
            },
            '13. Admin Daily Report (admin-daily-report)' => function() use ($email) {
                $reportData = $this->generateTestReportData();
                Mail::send('emails.admin-daily-report', $reportData, function ($message) use ($email, $reportData) {
                    $message->to($email)
                            ->subject("THW-Trainer Tagesreport - {$reportData['date']} (Test)");
                });
            },
        ];

        $only = $this->option('only');
        $renderOnly = $this->option('render');

        if ($only) {
            $filtered = array_filter($testEmails, fn($key) => str_contains(strtolower($key), strtolower($only)), ARRAY_FILTER_USE_KEY);
            if (empty($filtered)) {
                $this->error("Keine Mail gefunden für: {$only}");
                $this->line('Verfügbare Mails:');
                foreach (array_keys($testEmails) as $name) {
                    $this->line("  - {$name}");
                }
                return Command::FAILURE;
            }
            $testEmails = $filtered;
        }

        if ($renderOnly) {
            $this->info('Render-Modus: Mails werden nur gerendert, nicht gesendet.');
            $this->line('');

            $renderMails = [
                'exam-reminder' => new ExamReminderMail($testUser, [
                    'days_left' => 14, 'daily_target' => 25, 'today_answered' => 8,
                    'mastered_count' => 120, 'total_questions' => 400, 'progress_percent' => 30,
                ]),
                'exam-goodluck' => new ExamGoodLuckMail($testUser),
                'exam-feedback-request' => new ExamFeedbackRequestMail($testUser, url('/exam-feedback/test-token')),
            ];

            foreach ($renderMails as $key => $mailable) {
                if ($only && !str_contains($key, strtolower($only))) {
                    continue;
                }
                $this->info("=== {$key} ===");
                $this->info("Subject: " . $mailable->envelope()->subject);
                $html = $mailable->render();
                $this->line("HTML-Länge: " . strlen($html) . " Bytes");
                $this->line(substr($html, 0, 500) . '...');
                $this->line('');
            }

            return Command::SUCCESS;
        }

        $successful = 0;
        $failed = 0;

        foreach ($testEmails as $name => $sendFunction) {
            try {
                $this->info("Sende: {$name}...");
                $sendFunction();
                $this->info("  Erfolgreich gesendet!");
                $this->line('');
                $successful++;

                // Kurze Pause zwischen E-Mails
                usleep(500000); // 0.5 Sekunden

            } catch (\Exception $e) {
                $this->error("  Fehler: " . $e->getMessage());
                $this->line('');
                $failed++;
            }
        }

        $this->line('');
        $this->info("========================================");
        $this->info("Alle E-Mail-Tests abgeschlossen!");
        $this->info("Erfolgreich: {$successful} | Fehlgeschlagen: {$failed}");
        $this->info("========================================");
        $this->line("Prüfe dein E-Mail-Postfach: {$email}");

        return Command::SUCCESS;
    }

    /**
     * Generate test data for admin daily report
     */
    private function generateTestReportData(): array
    {
        return [
            'date' => now()->subDay()->format('d.m.Y'),
            'report_day' => 'Gestern (Test)',

            'users' => [
                'total' => 1234,
                'verified' => 1100,
                'verification_rate' => 89.1,
                'active_yesterday' => 156,
                'active_2_days_ago' => 142,
                'active_trend' => ['direction' => 'up', 'percentage' => 9.9],
                'active_last_7_days' => 423,
                'active_last_30_days' => 892,
                'active_sparkline' => '▂▃▄▃▅▆▇',
                'new_yesterday' => 12,
                'new_2_days_ago' => 8,
                'new_trend' => ['direction' => 'up', 'percentage' => 50.0],
                'new_last_7_days' => 67,
                'new_sparkline' => '▃▂▄▃▅▄▆',
            ],

            'activity' => [
                'questions_answered_yesterday' => 2847,
                'questions_answered_2_days_ago' => 2534,
                'questions_trend' => ['direction' => 'up', 'percentage' => 12.4],
                'questions_sparkline' => '▃▄▃▅▄▆▇',
                'correct_answers_yesterday' => 2134,
                'correct_answers_2_days_ago' => 1892,
                'success_rate_yesterday' => 75.0,
                'success_rate_2_days_ago' => 74.7,
                'success_rate_trend' => ['direction' => 'neutral', 'percentage' => 0.4],
                'avg_questions_per_user' => 18.3,
                'total_questions_answered' => 456789,
            ],

            'gamification' => [
                'users_with_streak' => 234,
                'avg_streak_length' => 4.7,
                'longest_streak' => 42,
                'total_points_awarded' => 1234567,
                'avg_points_per_user' => 1001,
                'users_level_5_plus' => 156,
            ],

            'top_users' => [
                ['name' => 'Max Mustermann', 'points' => 12500, 'level' => 12, 'streak_days' => 42],
                ['name' => 'Anna Schmidt', 'points' => 11200, 'level' => 11, 'streak_days' => 28],
                ['name' => 'Peter Meyer', 'points' => 9800, 'level' => 10, 'streak_days' => 15],
                ['name' => 'Lisa Wagner', 'points' => 8900, 'level' => 9, 'streak_days' => 21],
                ['name' => 'Thomas Müller', 'points' => 8100, 'level' => 9, 'streak_days' => 7],
            ],

            'system' => [
                'total_questions' => 500,
                'lehrgang_questions' => 150,
                'lernpool_questions' => 75,
                'database_size' => '245.8 MB',
            ],

            'warnings' => [
                ['type' => 'success', 'message' => 'Aktivität steigt (+10%)'],
                ['type' => 'warning', 'message' => 'Dies ist ein Test-Report'],
            ],
        ];
    }
}
