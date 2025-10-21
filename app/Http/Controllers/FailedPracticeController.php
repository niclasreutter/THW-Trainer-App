<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Question;
use App\Models\QuestionStatistic;
use App\Models\UserQuestionProgress;
use App\Services\GamificationService;

class FailedPracticeController extends Controller
{
    /**
     * Zeige Failed Practice - basiert auf exam_failed_questions
     */
    public function show(Request $request)
    {
        $user = Auth::user();
        $failed = $this->ensureArray($user->exam_failed_questions);
        
        // Reset bei explizitem Reset oder wenn keine Session
        if ($request->has('reset') || !session()->has('failed_practice_ids')) {
            // Lösche alle Sessions
            session()->forget([
                'failed_practice_ids',
                'practice_ids', 
                'practice_mode', 
                'practice_parameter', 
                'practice_skipped'
            ]);
            
            if (empty($failed)) {
                return redirect()->route('practice.menu')->with('info', 'Keine falschen Fragen zum Wiederholen! 🎉');
            }
            
            // Initialisiere mit Failed Questions
            $failedIds = array_values($failed);
            shuffle($failedIds);
            
            session([
                'failed_practice_ids' => $failedIds,
                'practice_mode' => 'failed'
            ]);
        }
        
        $practiceIds = session('failed_practice_ids', []);
        
        // Wenn eine Frage gerade beantwortet wurde, zeige sie nochmal
        $answerResult = session('answer_result');
        $showAnsweredQuestion = $answerResult && isset($answerResult['question_id']);
        
        if (!empty($practiceIds)) {
            if ($showAnsweredQuestion) {
                // Zeige die gerade beantwortete Frage nochmal
                $questionId = $answerResult['question_id'];
            } else {
                // Zeige erste Frage aus Queue
                $questionId = $practiceIds[0];
            }
        } else {
            // Alle Fragen bearbeitet
            session()->forget(['failed_practice_ids', 'practice_mode']);
            return redirect()->route('practice.menu')->with('success', 'Alle falschen Fragen wiederholt! 🎉');
        }
        
        $question = Question::find($questionId);
        
        if (!$question) {
            return redirect()->route('practice.menu')->with('error', 'Frage nicht gefunden.');
        }
        
        // Fortschritt berechnen
        $total = count($failed);
        $progress = $total - count($practiceIds);
        $progressPercent = $total > 0 ? round(($progress / $total) * 100) : 0;
        
        return view('failed_practice', compact('question', 'progress', 'total', 'progressPercent'));
    }

    /**
     * Submit Failed Practice - 1:1 wie PracticeController
     */
    public function submit(Request $request)
    {
        $question = Question::findOrFail($request->question_id);
        
        // Hole das Mapping aus dem Hidden Field
        $mappingJson = $request->input('answer_mapping');
        $mapping = json_decode($mappingJson, true);
        
        // User-Antworten (Positionen 0, 1, 2)
        $userAnswerPositions = $request->answer ?? [];
        
        // Mappe Positionen zurück auf Original-Buchstaben
        $userAnswer = collect($userAnswerPositions)->map(function($position) use ($mapping) {
            return $mapping[$position] ?? null;
        })->filter()->sort()->values();
        
        $solution = collect(explode(',', $question->loesung))->map(fn($s) => trim($s))->sort()->values();
        $isCorrect = $userAnswer->all() === $solution->all();

        $user = Auth::user();

        // Statistik erfassen
        QuestionStatistic::create([
            'question_id' => $question->id,
            'user_id' => $user->id,
            'is_correct' => $isCorrect,
        ]);
        
        // Fortschritt tracken
        $progress = UserQuestionProgress::getOrCreate($user->id, $question->id);
        $progress->updateProgress($isCorrect);
        
        $gamificationResult = null;
        
        // Nur wenn Frage gemeistert (2x richtig in Folge)
        if ($progress->isMastered()) {
            // Entferne Frage aus exam_failed_questions
            $failed = $this->ensureArray($user->exam_failed_questions);
            if (in_array($question->id, $failed)) {
                $failed = array_diff($failed, [$question->id]);
                $user->exam_failed_questions = array_values($failed);
                $user->save();
            }
            
            // Gamification: Punkte nur wenn gemeistert
            $gamificationService = new GamificationService();
            $gamificationResult = $gamificationService->awardQuestionPoints($user, true, $question->id);
            
            // Entferne Frage aus der aktuellen Session
            $practiceIds = session('failed_practice_ids', []);
            if (!empty($practiceIds)) {
                $practiceIds = array_diff($practiceIds, [$question->id]);
                session(['failed_practice_ids' => array_values($practiceIds)]);
            }
        } else {
            // Frage noch nicht gemeistert - Gamification für jeden Versuch
            $gamificationService = new GamificationService();
            $gamificationResult = $gamificationService->awardQuestionPoints($user, $isCorrect, $question->id);
            
            // Frage ans Ende der Queue setzen
            $practiceIds = session('failed_practice_ids', []);
            if (!empty($practiceIds)) {
                $currentIndex = array_search($question->id, $practiceIds);
                if ($currentIndex !== false) {
                    unset($practiceIds[$currentIndex]);
                    $practiceIds[] = $question->id;
                    session(['failed_practice_ids' => array_values($practiceIds)]);
                }
            }
        }
        
        // Gamification Result in Session speichern
        if ($gamificationResult) {
            session(['gamification_result' => $gamificationResult]);
        }
        
        // Answer Result in Session speichern für Feedback-Anzeige
        session([
            'answer_result' => [
                'question_id' => $question->id,
                'is_correct' => $isCorrect,
                'user_answer' => $userAnswer->toArray(),
                'question_progress' => $progress->consecutive_correct,
                'answer_mapping' => $mapping
            ]
        ]);
        
        return redirect()->route('failed.index');
    }

    /**
     * Helper: Array aus String/Array machen
     */
    private function ensureArray($value)
    {
        if (is_string($value)) {
            return json_decode($value, true) ?? [];
        }
        return is_array($value) ? $value : [];
    }
}