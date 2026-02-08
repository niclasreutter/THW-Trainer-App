<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\Question;
use App\Models\UserQuestionProgress;

class ProgressHelper
{
    /**
     * Berechnet den echten Fortschritt eines Users
     * 
     * @param User $user
     * @return array [
     *   'mastered' => int,           // Anzahl gemeisterter Fragen
     *   'total' => int,              // Gesamtanzahl Fragen
     *   'remaining' => int,          // Verbleibende Fragen
     *   'progress_percentage' => int // Fortschritt in %
     * ]
     */
    public static function calculateProgress(User $user): array
    {
        $totalQuestions = Question::count();
        $masteredQuestions = count($user->solved_questions ?? []);
        $remainingQuestions = max(0, $totalQuestions - $masteredQuestions);

        $threshold = UserQuestionProgress::MASTERY_THRESHOLD;
        $progressData = UserQuestionProgress::where('user_id', $user->id)->get();

        $totalProgress = 0;
        foreach ($progressData as $progress) {
            $totalProgress += min($progress->consecutive_correct, $threshold);
        }

        $maxProgress = $totalQuestions * $threshold;
        
        // Berechne Prozentsatz
        $progressPercentage = $maxProgress > 0 
            ? round(($totalProgress / $maxProgress) * 100) 
            : 0;
        
        return [
            'mastered' => $masteredQuestions,
            'total' => $totalQuestions,
            'remaining' => $remainingQuestions,
            'progress_percentage' => $progressPercentage,
            'total_progress_points' => $totalProgress,
            'max_progress_points' => $maxProgress,
        ];
    }
    
    /**
     * Berechnet den Fortschritt für einen Lernabschnitt
     * 
     * @param User $user
     * @param int $section Lernabschnitt (1-10)
     * @return array
     */
    public static function calculateSectionProgress(User $user, int $section): array
    {
        $sectionQuestions = Question::where('lernabschnitt', $section)->pluck('id')->toArray();
        $totalQuestions = count($sectionQuestions);
        
        $solved = $user->solved_questions ?? [];
        $masteredQuestions = count(array_intersect($solved, $sectionQuestions));
        $remainingQuestions = max(0, $totalQuestions - $masteredQuestions);
        
        // Hole Fortschritte für diesen Lernabschnitt
        $threshold = UserQuestionProgress::MASTERY_THRESHOLD;
        $progressData = UserQuestionProgress::where('user_id', $user->id)
            ->whereIn('question_id', $sectionQuestions)
            ->get();

        $totalProgress = 0;
        foreach ($progressData as $progress) {
            $totalProgress += min($progress->consecutive_correct, $threshold);
        }

        $maxProgress = $totalQuestions * $threshold;
        $progressPercentage = $maxProgress > 0 
            ? round(($totalProgress / $maxProgress) * 100) 
            : 0;
        
        return [
            'mastered' => $masteredQuestions,
            'total' => $totalQuestions,
            'remaining' => $remainingQuestions,
            'progress_percentage' => $progressPercentage,
        ];
    }
}

