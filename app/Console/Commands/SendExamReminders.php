<?php

namespace App\Console\Commands;

use App\Mail\ExamReminderMail;
use App\Models\Question;
use App\Models\QuestionStatistic;
use App\Models\User;
use App\Models\UserQuestionProgress;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendExamReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exam:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sendet Tagespensum-Erinnerungen an User mit Prüfungsdatum in der Zukunft';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starte Prüfungs-Erinnerungs-Check...');

        $today = Carbon::today();
        $emailsSent = 0;
        $skippedDone = 0;
        $skippedNoQuestions = 0;
        $errors = 0;

        $totalQuestions = Question::count();
        $threshold = UserQuestionProgress::MASTERY_THRESHOLD;

        $this->info("Gesamtfragen in DB: {$totalQuestions}");

        // Finde alle Benutzer mit E-Mail-Zustimmung und Prüfungsdatum in der Zukunft
        $users = User::where('email_consent', true)
            ->whereNotNull('exam_date')
            ->where('exam_date', '>', $today)
            ->get();

        $this->info("Gefunden: {$users->count()} Benutzer mit E-Mail-Zustimmung und Prüfungsdatum.");

        foreach ($users as $user) {
            try {
                $examDate = Carbon::parse($user->exam_date);
                $daysLeft = (int) $today->diffInDays($examDate, false);

                if ($daysLeft <= 0) {
                    continue;
                }

                // Gemeisterte Fragen berechnen
                $masteredCount = UserQuestionProgress::countMastered($user->id);
                $unmasteredCount = $totalQuestions - $masteredCount;

                // Wenn alle Fragen gemeistert sind, nicht senden
                if ($unmasteredCount <= 0) {
                    $skippedNoQuestions++;
                    $this->line("  Übersprungen (alle gemeistert): {$user->name}");
                    continue;
                }

                // Tages-Zielwert berechnen (gleiche Logik wie Dashboard)
                $examBuffer = min(7, max(0, $daysLeft - 1));
                $effectiveDays = max(1, $daysLeft - $examBuffer);

                // Fehlerquote aus tatsächlichen Antworten ermitteln
                $statCount = QuestionStatistic::where('user_id', $user->id)->count();
                $statCorrect = QuestionStatistic::where('user_id', $user->id)->where('is_correct', true)->count();
                $accuracy = ($statCount >= 20) ? ($statCorrect / $statCount) : 0.65;
                $errorFactor = 1 / max(0.4, $accuracy);

                // Verbleibende Interaktionen berechnen
                $progressData = UserQuestionProgress::where('user_id', $user->id)->get();
                $remainingInteractions = 0;
                $seenQuestionIds = $progressData->pluck('question_id');

                foreach ($progressData as $prog) {
                    if ($prog->consecutive_correct < $threshold) {
                        $remaining = $threshold - $prog->consecutive_correct;
                        $remainingInteractions += $remaining * $errorFactor;
                    }
                }

                $unseenCount = $totalQuestions - $seenQuestionIds->count();
                $remainingInteractions += $unseenCount * $threshold * $errorFactor;

                $dailyTarget = max(1, (int) ceil($remainingInteractions / $effectiveDays));

                // Heute bereits beantwortete Fragen
                $todayAnswered = QuestionStatistic::where('user_id', $user->id)
                    ->whereDate('created_at', $today)
                    ->count();

                // Wenn Tagesziel bereits erreicht, nicht senden
                if ($todayAnswered >= $dailyTarget) {
                    $skippedDone++;
                    $this->line("  Übersprungen (Tagesziel erreicht): {$user->name} - {$todayAnswered}/{$dailyTarget}");
                    continue;
                }

                // Fortschritt berechnen
                $progressPercent = $totalQuestions > 0
                    ? round(($masteredCount / $totalQuestions) * 100)
                    : 0;

                // E-Mail senden
                Mail::to($user->email)->send(
                    new ExamReminderMail($user, [
                        'days_left' => $daysLeft,
                        'daily_target' => $dailyTarget,
                        'today_answered' => $todayAnswered,
                        'mastered_count' => $masteredCount,
                        'total_questions' => $totalQuestions,
                        'progress_percent' => $progressPercent,
                    ])
                );

                $emailsSent++;
                $todayRemaining = $dailyTarget - $todayAnswered;
                $this->info("  Mail gesendet: {$user->name} — Prüfung in {$daysLeft}d, {$todayRemaining} Fragen offen");

            } catch (\Exception $e) {
                $errors++;
                $this->error("  FEHLER bei {$user->email}: " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info('=== Prüfungs-Erinnerungs-Check abgeschlossen ===');
        $this->info("E-Mails gesendet: {$emailsSent}");
        $this->info("Übersprungen (Tagesziel erreicht): {$skippedDone}");
        $this->info("Übersprungen (alle gemeistert): {$skippedNoQuestions}");
        $this->info("Fehler: {$errors}");

        return Command::SUCCESS;
    }
}
