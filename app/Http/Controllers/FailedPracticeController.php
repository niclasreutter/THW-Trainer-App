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
        
        // WICHTIG: Lösche andere Practice-Sessions um Interferenz zu vermeiden
        if ($request->has('reset') || !session()->has('failed_practice_ids')) {
            // Clear andere Practice Sessions
            session()->forget([
                'practice_ids', 
                'practice_mode', 
                'practice_parameter', 
                'practice_skipped'
            ]);
            
            // Initialisiere Failed Practice Session
            $failedIds = array_values($failed);
            
            if (empty($failedIds)) {
                return redirect()->route('practice.menu')->with('info', 'Keine falschen Fragen zum Wiederholen! 🎉');
            }
            
            shuffle($failedIds);
            
            \Log::info('Failed Practice initialized', [
                'user_id' => $user->id,
                'failed_count' => count($failedIds),
                'failed_ids' => $failedIds
            ]);
            
            session([
                'failed_practice_ids' => $failedIds,
                'failed_practice_round' => 1,
                'failed_practice_completed_once' => [],
            ]);
        }
        
        $practiceIds = session('failed_practice_ids', []);
        
        \Log::info('Failed Practice show', [
            'practice_ids' => $practiceIds,
            'user_failed_questions' => $failed
        ]);
        
        if (empty($practiceIds)) {
            // Alle Fragen wurden bearbeitet
            session()->forget(['failed_practice_ids', 'failed_practice_round', 'failed_practice_completed_once']);
            return redirect()->route('practice.menu')->with('success', 'Alle falschen Fragen wiederholt! 🎉');
        }
        
        $questionId = $practiceIds[0];
        
        // SICHERHEITSCHECK: Ist die Frage wirklich in Failed-Liste?
        if (!in_array($questionId, $failed)) {
            \Log::warning('Question not in failed list!', [
                'question_id' => $questionId,
                'failed_questions' => $failed,
                'practice_ids' => $practiceIds
            ]);
            
            // Session neu initialisieren
            session()->forget(['failed_practice_ids', 'failed_practice_round', 'failed_practice_completed_once']);
            return redirect()->route('failed.index', ['reset' => 1]);
        }
        
        $question = Question::find($questionId);
        
        if (!$question) {
            return redirect()->route('practice.menu')->with('error', 'Frage nicht gefunden.');
        }
        
        // Fortschritt: Anzahl gemeisterter Failed-Fragen
        $masteredCount = 0;
        foreach ($failed as $fId) {
            $prog = UserQuestionProgress::where('user_id', $user->id)
                                        ->where('question_id', $fId)
                                        ->first();
            if ($prog && $prog->isMastered()) {
                $masteredCount++;
            }
        }
        
        $progress = $masteredCount;
        $total = count($failed);
        
        // Fortschrittsbalken-Logik (gesamt)
        $totalQuestions = Question::count();
        $progressData = UserQuestionProgress::where('user_id', $user->id)->get();
        $totalProgressPoints = 0;
        foreach ($progressData as $prog) {
            $totalProgressPoints += min($prog->consecutive_correct, 2);
        }
        $maxProgressPoints = $totalQuestions * 2;
        $progressPercent = $maxProgressPoints > 0 ? round(($totalProgressPoints / $maxProgressPoints) * 100) : 0;
        
        return view('failed_practice', compact('question', 'progress', 'total', 'progressPercent'));
    }

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

        // Statistik erfassen (mit User ID)
        QuestionStatistic::create([
            'question_id' => $question->id,
            'user_id' => $user->id,
            'is_correct' => $isCorrect,
        ]);
        
        // NEU: Fortschritt in user_question_progress tracken
        $progress = UserQuestionProgress::getOrCreate($user->id, $question->id);
        $progress->updateProgress($isCorrect);
        
        $solved = $this->ensureArray($user->solved_questions);
        $failed = $this->ensureArray($user->exam_failed_questions);
        
        $gamificationResult = null;
        
        // Nur wenn Frage gemeistert (2x richtig in Folge)
        if ($progress->isMastered()) {
            // Zu solved_questions hinzufügen (falls noch nicht drin)
            if (!in_array($question->id, $solved)) {
                $solved[] = $question->id;
                $user->solved_questions = array_unique($solved);
                $user->save();
            }
            
            // Entferne aus exam_failed_questions
            if (in_array($question->id, $failed)) {
                $failed = array_diff($failed, [$question->id]);
                $user->exam_failed_questions = array_values($failed);
                $user->save();
            }
            
            // Gamification: Punkte nur wenn gemeistert
            $gamificationService = new GamificationService();
            $gamificationResult = $gamificationService->awardQuestionPoints($user, true, $question->id);
            
            // WICHTIG: Entferne gemeisterte Frage aus der aktuellen Practice Session
            $practiceIds = session('failed_practice_ids', []);
            if (!empty($practiceIds)) {
                $practiceIds = array_diff($practiceIds, [$question->id]);
                session(['failed_practice_ids' => array_values($practiceIds)]);
            }
        } else {
            // Frage noch nicht gemeistert (0 oder 1x richtig)
            
            // Gamification: Auch beim ersten richtigen Beantworten Punkte vergeben
            $gamificationService = new GamificationService();
            $gamificationResult = $gamificationService->awardQuestionPoints($user, $isCorrect, $question->id);
            
            // Bei nicht-gemeisterter Antwort: NICHT aus Failed-Liste entfernen
            // Session-IDs: Entferne aktuelle und füge am Ende hinzu
            $practiceIds = session('failed_practice_ids', []);
            if (!empty($practiceIds)) {
                $currentIndex = array_search($question->id, $practiceIds);
                if ($currentIndex !== false) {
                    unset($practiceIds[$currentIndex]);
                    $practiceIds[] = $question->id; // Am Ende wieder hinzufügen
                    session(['failed_practice_ids' => array_values($practiceIds)]);
                }
            }
        }
        
        // Immer Gamification Result in Session speichern
        if ($gamificationResult) {
            session(['gamification_result' => $gamificationResult]);
        }
        
        // WICHTIG: Immer answer_result in Session speichern für Feedback-Anzeige
        $answerResultData = [
            'question_id' => $question->id,
            'is_correct' => $isCorrect,
            'user_answer' => $userAnswer->toArray(),
            'question_progress' => $progress->consecutive_correct,
            'answer_mapping' => $mapping // Mapping auch speichern für die Anzeige
        ];
        
        session(['answer_result' => $answerResultData]);
        
        \Log::info('Failed Practice submit - SESSION SET', [
            'question_id' => $question->id,
            'is_correct' => $isCorrect,
            'consecutive_correct' => $progress->consecutive_correct,
            'mastered' => $progress->isMastered(),
            'has_gamification' => $gamificationResult !== null,
            'answer_result_set' => $answerResultData,
            'gamification_result_set' => $gamificationResult,
            'session_has_answer_result' => session()->has('answer_result'),
            'session_has_gamification' => session()->has('gamification_result')
        ]);
        
        // WICHTIG: Immer redirect machen (Post/Redirect/Get Pattern)
        return redirect()->route('failed.index');
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
