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
    public function show(Request $request)
    {
        $user = Auth::user();
        $failed = $this->ensureArray($user->exam_failed_questions);
        $progress = 0;
        $total = count($failed);
        $question = $total ? Question::find($failed[0]) : null;
        return view('failed_practice', compact('question', 'progress', 'total'));
    }

    public function submit(Request $request)
    {
        $user = Auth::user();
        $failed = $this->ensureArray($user->exam_failed_questions);
        $question = Question::findOrFail($request->question_id);
        
        // Hole das Mapping aus dem Hidden Field (falls vorhanden)
        $mappingJson = $request->input('answer_mapping');
        $mapping = $mappingJson ? json_decode($mappingJson, true) : null;
        
        // User-Antworten (Positionen 0, 1, 2)
        $userAnswerPositions = $request->answer ?? [];
        
        // Konvertiere Positionen zurück zu Original-Buchstaben
        if ($mapping) {
            $userAnswer = collect($userAnswerPositions)->map(function($pos) use ($mapping) {
                return $mapping[$pos] ?? $pos;
            });
        } else {
            $userAnswer = collect($userAnswerPositions);
        }
        
        $solution = collect(explode(',', $question->loesung))->map(fn($s) => trim($s));
        $isCorrect = $userAnswer->sort()->values()->all() === $solution->sort()->values()->all();
        
        // Statistik erfassen (mit User ID)
        QuestionStatistic::create([
            'question_id' => $question->id,
            'user_id' => $user->id,
            'is_correct' => $isCorrect,
        ]);
        
        // NEU: Fortschritt in user_question_progress tracken
        $questionProgress = UserQuestionProgress::getOrCreate($user->id, $question->id);
        $questionProgress->updateProgress($isCorrect);
        
        // Nur wenn Frage gemeistert (2x richtig in Folge)
        if ($questionProgress->isMastered()) {
            // Zu solved_questions hinzufügen
            $solved = $this->ensureArray($user->solved_questions);
            if (!in_array($question->id, $solved)) {
                $solved[] = $question->id;
                $user->solved_questions = array_unique($solved);
            }
            
            // Entferne aus exam_failed_questions
            $failed = array_diff($failed, [$question->id]);
            $user->exam_failed_questions = array_values($failed);
            $user->save();
            
            // Gamification: Punkte für richtige Antwort
            $gamificationService = new GamificationService();
            $gamificationService->awardQuestionPoints($user, true, $question->id);
            
            return redirect()->route('failed.index');
        }
        
        // Gamification: Punkte auch bei nicht-gemeistert
        $gamificationService = new GamificationService();
        $gamificationResult = $gamificationService->awardQuestionPoints($user, $isCorrect, $question->id);
        
        // Speichere Ergebnisse in Session (wie in PracticeController)
        session([
            'answer_result' => [
                'question_id' => $question->id,
                'is_correct' => $isCorrect,
                'user_answer' => $userAnswer->toArray(),
                'question_progress' => $questionProgress->consecutive_correct,
                'answer_mapping' => $mapping // Mapping auch speichern für die Anzeige
            ],
            'gamification_result' => $gamificationResult
        ]);
        
        // Frage noch nicht gemeistert
        $progress = 0;
        $total = count($failed);
        return view('failed_practice', [
            'question' => $question,
            'progress' => $progress,
            'total' => $total,
        ]);
    }

    /**
     * Stellt sicher, dass ein Wert ein Array ist (für Legacy-Kompatibilität)
     */
    private function ensureArray($value)
    {
        if (is_array($value)) {
            return $value;
        }
        
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        
        return [];
    }
}
