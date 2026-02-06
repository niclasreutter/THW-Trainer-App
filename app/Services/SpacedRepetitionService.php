<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserQuestionProgress;
use Carbon\Carbon;

class SpacedRepetitionService
{
    /**
     * SM-2 Algorithmus: Berechnet das nächste Wiederholungsdatum
     *
     * quality: 0-5 (0=komplett falsch, 5=perfekt)
     * Für unsere Zwecke: falsch=1, richtig=4, gemeistert=5
     */
    public function calculateNextReview(UserQuestionProgress $progress, bool $isCorrect): void
    {
        $quality = $isCorrect ? ($progress->consecutive_correct >= 2 ? 5 : 4) : 1;

        $ef = $progress->easiness_factor ?? 2.5;
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
            // Falsch beantwortet - zurücksetzen
            $repetition = 0;
            $interval = 1; // Morgen wieder
        }

        // EF aktualisieren (SM-2 Formel)
        $ef = $ef + (0.1 - (5 - $quality) * (0.08 + (5 - $quality) * 0.02));
        $ef = max(1.3, $ef); // Minimum 1.3

        // Max 90 Tage Intervall
        $interval = min($interval, 90);

        $progress->easiness_factor = round($ef, 1);
        $progress->review_interval = $interval;
        $progress->repetition_count = $repetition;
        $progress->next_review_at = Carbon::now()->addDays($interval);
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

        $dueNow = UserQuestionProgress::where('user_id', $userId)
            ->whereNotNull('next_review_at')
            ->where('next_review_at', '<=', $now)
            ->count();

        $dueTomorrow = UserQuestionProgress::where('user_id', $userId)
            ->whereNotNull('next_review_at')
            ->where('next_review_at', '>', $now)
            ->where('next_review_at', '<=', $now->copy()->addDay())
            ->count();

        $dueThisWeek = UserQuestionProgress::where('user_id', $userId)
            ->whereNotNull('next_review_at')
            ->where('next_review_at', '>', $now)
            ->where('next_review_at', '<=', $now->copy()->addWeek())
            ->count();

        $totalInSystem = UserQuestionProgress::where('user_id', $userId)
            ->whereNotNull('next_review_at')
            ->count();

        return [
            'due_now' => $dueNow,
            'due_tomorrow' => $dueTomorrow,
            'due_this_week' => $dueThisWeek,
            'total_in_system' => $totalInSystem,
        ];
    }
}
