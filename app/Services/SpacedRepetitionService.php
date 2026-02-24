<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserQuestionProgress;
use Carbon\Carbon;

class SpacedRepetitionService
{
    /**
     * Globale Schwierigkeit einer Frage anhand der Fehlerquote aller Nutzer.
     * Gleiche Logik wie PracticeController::getQuestionDifficulty().
     */
    private function getGlobalDifficulty(int $questionId): string
    {
        $total = \App\Models\QuestionStatistic::where('question_id', $questionId)->count();

        if ($total < 5) {
            return 'unknown';
        }

        $correct = \App\Models\QuestionStatistic::where('question_id', $questionId)
            ->where('is_correct', true)->count();
        $errorRate = (($total - $correct) / $total) * 100;

        if ($errorRate >= 60) return 'hard';
        if ($errorRate >= 30) return 'medium';
        return 'easy';
    }

    /**
     * SM-2 Algorithmus: Berechnet das nächste Wiederholungsdatum.
     *
     * Richtig: 1 Tag → 3 Tage → exponentiell → 3x = gemeistert (next_review_at = null)
     * Falsch:  Intervall +1 Tag pro Fehler, Cap je nach globaler Schwierigkeit:
     *          hard=7 Tage, medium=10 Tage, easy/unknown=14 Tage
     */
    public function calculateNextReview(UserQuestionProgress $progress, bool $isCorrect): void
    {
        $quality = $isCorrect ? ($progress->isMastered() ? 5 : 4) : 1;
        $difficulty = $this->getGlobalDifficulty($progress->question_id);

        // EF: initial je nach globaler Schwierigkeit setzen
        $ef = $progress->easiness_factor ?? match($difficulty) {
            'hard'   => 2.0,
            'medium' => 2.3,
            'easy'   => 2.8,
            default  => 2.5,
        };
        $interval = $progress->review_interval ?? 0;
        $repetition = $progress->repetition_count ?? 0;

        if ($quality >= 3) {
            // Richtig beantwortet
            if ($repetition == 0) {
                $interval = 1; // 1 Tag
            } elseif ($repetition == 1) {
                $interval = 3; // 3 Tage
            } else {
                $interval = (int) round($interval * $ef);
            }
            $repetition++;
        } else {
            // Falsch beantwortet: Intervall graduell erhöhen, Cap je nach Schwierigkeit
            $maxWrongInterval = match($difficulty) {
                'hard'   => 7,
                'medium' => 10,
                default  => 14,
            };
            $repetition = 0;
            $interval = min(max(1, $interval + 1), $maxWrongInterval);
        }

        // EF aktualisieren (SM-2 Formel)
        $ef = $ef + (0.1 - (5 - $quality) * (0.08 + (5 - $quality) * 0.02));
        $ef = max(1.3, $ef); // Minimum 1.3

        // Max 90 Tage Intervall
        $interval = min($interval, 90);

        $progress->easiness_factor = round($ef, 1);
        $progress->review_interval = $interval;
        $progress->repetition_count = $repetition;

        // Gemeisterte Fragen aus SR entfernen – keine weiteren E-Mails
        if ($progress->isMastered()) {
            $progress->next_review_at = null;
        } else {
            $progress->next_review_at = Carbon::today()->addDays($interval);
        }

        $progress->save();
    }

    /**
     * Fällige Wiederholungen für einen User
     */
    public function getDueQuestions(int $userId): array
    {
        return UserQuestionProgress::where('user_id', $userId)
            ->whereNotNull('next_review_at')
            ->where('next_review_at', '<=', Carbon::now())
            ->orderBy('next_review_at', 'asc')
            ->pluck('question_id')
            ->toArray();
    }

    /**
     * Anzahl fälliger Wiederholungen
     */
    public function getDueCount(int $userId): int
    {
        return UserQuestionProgress::where('user_id', $userId)
            ->whereNotNull('next_review_at')
            ->where('next_review_at', '<=', Carbon::now())
            ->count();
    }

    /**
     * Initialisiert Spaced Repetition für eine beantwortete Frage
     * Wird nach jeder Antwort aufgerufen
     */
    public function processAnswer(UserQuestionProgress $progress, bool $isCorrect): void
    {
        $this->calculateNextReview($progress, $isCorrect);
    }

    /**
     * Statistiken für den User
     */
    public function getStats(int $userId): array
    {
        $now = Carbon::now();
        $tomorrow = Carbon::tomorrow();
        $dayAfterTomorrow = Carbon::today()->addDays(2);
        $endOfWeek = Carbon::today()->addWeek();

        $dueNow = UserQuestionProgress::where('user_id', $userId)
            ->whereNotNull('next_review_at')
            ->where('next_review_at', '<=', $now)
            ->count();

        $dueTomorrow = UserQuestionProgress::where('user_id', $userId)
            ->whereNotNull('next_review_at')
            ->where('next_review_at', '>=', $tomorrow)
            ->where('next_review_at', '<', $dayAfterTomorrow)
            ->count();

        $dueThisWeek = UserQuestionProgress::where('user_id', $userId)
            ->whereNotNull('next_review_at')
            ->where('next_review_at', '>', $now)
            ->where('next_review_at', '<=', $endOfWeek)
            ->count();

        $totalInSystem = UserQuestionProgress::where('user_id', $userId)
            ->whereNotNull('next_review_at')
            ->count();

        return [
            'due_now' => $dueNow,
            'due_tomorrow' => $dueTomorrow,
            'due_this_week' => $dueThisWeek,
            'total_in_system' => $totalInSystem,
            'upcoming_schedule' => $this->getUpcomingSchedule($userId),
        ];
    }

    /**
     * Kommende Wiederholungen gruppiert nach Tag (nächste 7 Tage)
     */
    public function getUpcomingSchedule(int $userId, int $days = 7): array
    {
        $today = Carbon::today();

        $upcoming = UserQuestionProgress::where('user_id', $userId)
            ->whereNotNull('next_review_at')
            ->where('next_review_at', '>', $today)
            ->where('next_review_at', '<=', $today->copy()->addDays($days))
            ->selectRaw('DATE(next_review_at) as due_date, COUNT(*) as count')
            ->groupBy('due_date')
            ->orderBy('due_date')
            ->get();

        $schedule = [];
        foreach ($upcoming as $row) {
            $dueDate = Carbon::parse($row->due_date);
            $diffDays = $today->diffInDays($dueDate);
            $schedule[] = [
                'date' => $row->due_date,
                'days_from_now' => $diffDays,
                'count' => $row->count,
                'label' => $diffDays === 1 ? 'Morgen' : "In {$diffDays} Tagen",
            ];
        }

        return $schedule;
    }
}
