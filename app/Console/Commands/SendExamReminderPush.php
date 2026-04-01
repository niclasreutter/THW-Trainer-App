<?php

namespace App\Console\Commands;

use App\Models\Question;
use App\Models\QuestionStatistic;
use App\Models\User;
use App\Models\UserQuestionProgress;
use App\Notifications\PushNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendExamReminderPush extends Command
{
    protected $signature = 'push:send-exam-reminder';

    protected $description = 'Send daily exam reminder push to users with upcoming exam date';

    public function handle()
    {
        $this->info('Sending exam reminder push notifications...');

        $today = Carbon::today();
        $totalQuestions = Question::count();
        $threshold = UserQuestionProgress::MASTERY_THRESHOLD;

        $users = User::whereHas('pushSubscriptions')
            ->whereNotNull('exam_date')
            ->where('exam_date', '>', $today)
            ->get();

        $sent = 0;
        foreach ($users as $user) {
            try {
                $daysLeft = (int) $today->diffInDays(Carbon::parse($user->exam_date), false);

                if ($daysLeft <= 0) {
                    continue;
                }

                // Gemeisterte Fragen
                $masteredCount = UserQuestionProgress::countMastered($user->id);
                $unmasteredCount = $totalQuestions - $masteredCount;

                if ($unmasteredCount <= 0) {
                    continue;
                }

                // Tagespensum berechnen (gleiche Logik wie SendExamReminders)
                $examBuffer = min(7, max(0, $daysLeft - 1));
                $effectiveDays = max(1, $daysLeft - $examBuffer);

                $statCount = QuestionStatistic::where('user_id', $user->id)->count();
                $statCorrect = QuestionStatistic::where('user_id', $user->id)->where('is_correct', true)->count();
                $accuracy = ($statCount >= 20) ? ($statCorrect / $statCount) : 0.65;
                $errorFactor = 1 / max(0.4, $accuracy);

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

                // Heute bereits beantwortet
                $todayAnswered = QuestionStatistic::where('user_id', $user->id)
                    ->whereDate('created_at', $today)
                    ->count();

                $todayRemaining = $dailyTarget - $todayAnswered;

                if ($todayRemaining <= 0) {
                    continue;
                }

                $user->notify(
                    (new PushNotification(
                        "Pruefung in {$daysLeft} Tagen",
                        "Noch {$todayRemaining} Fragen fuer dein Tagesziel — bleib dran!",
                        '/dashboard'
                    ))->delay(now()->addSeconds(rand(0, 1800)))
                );
                $sent++;
            } catch (\Exception $e) {
                $this->error("Push an User {$user->id} fehlgeschlagen: {$e->getMessage()}");
            }
        }

        $this->info("Exam reminder push sent to {$sent} users.");

        return Command::SUCCESS;
    }
}
