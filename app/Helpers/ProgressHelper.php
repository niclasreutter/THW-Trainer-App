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
     *   'mastered' => int,           // Anzahl gemeisterter Fragen (2x richtig)
     *   'total' => int,              // Gesamtanzahl Fragen
     *   'remaining' => int,          // Verbleibende Fragen
     *   'progress_percentage' => int // Fortschritt in % (inkl. 1x richtige)
     * ]
     */
    public static function calculateProgress(User $user): array
    {
        $totalQuestions = Question::count();
        $masteredQuestions = count($user->solved_questions ?? []); // 2x richtig
        $remainingQuestions = max(0, $totalQuestions - $masteredQuestions);
        
        // Fortschrittsbalken berücksichtigt auch 1x richtige Antworten
        // Hole alle Fortschritte des Users
        $progressData = UserQuestionProgress::where('user_id', $user->id)->get();
        
        $totalProgress = 0;
        foreach ($progressData as $progress) {
            // Jede Frage kann max. 2 Punkte haben (2x richtig)
            $totalProgress += min($progress->consecutive_correct, 2);
        }
        
        // Max mögliche Punkte: Alle Fragen × 2
        $maxProgress = $totalQuestions * 2;
        
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
        $progressData = UserQuestionProgress::where('user_id', $user->id)
            ->whereIn('question_id', $sectionQuestions)
            ->get();
        
        $totalProgress = 0;
        foreach ($progressData as $progress) {
            $totalProgress += min($progress->consecutive_correct, 2);
        }
        
        $maxProgress = $totalQuestions * 2;
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

