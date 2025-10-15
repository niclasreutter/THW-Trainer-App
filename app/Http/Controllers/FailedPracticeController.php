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
        $userAnswer = collect($request->answer ?? []);
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
        
        // Frage noch nicht gemeistert
        $progress = 0;
        $total = count($failed);
        return view('failed_practice', [
            'question' => $question,
            'isCorrect' => $isCorrect,
            'userAnswer' => $userAnswer,
            'progress' => $progress,
            'total' => $total,
            'questionProgress' => $questionProgress, // NEU: Fortschritt anzeigen
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
