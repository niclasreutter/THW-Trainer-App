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
        
        // Gamification: Punkte vergeben
        $gamificationService = new GamificationService();
        $gamificationResult = $gamificationService->awardQuestionPoints($user, $isCorrect, $question->id);
        
        // Hole Session-Daten
        $practiceIds = session('failed_practice_ids', []);
        $completedOnce = session('failed_practice_completed_once', []);
        $round = session('failed_practice_round', 1);
        
        // Wenn Frage gemeistert (2x richtig in Folge)
        if ($questionProgress->isMastered()) {
            // Zu solved_questions hinzufügen
            $solved = $this->ensureArray($user->solved_questions);
            if (!in_array($question->id, $solved)) {
                $solved[] = $question->id;
                $user->solved_questions = array_unique($solved);
            }
            
            // Entferne aus exam_failed_questions (permanent)
            $failed = array_diff($failed, [$question->id]);
            $user->exam_failed_questions = array_values($failed);
            $user->save();
            
            // Entferne aus Practice-IDs
            $practiceIds = array_diff($practiceIds, [$question->id]);
            $practiceIds = array_values($practiceIds);
        } else {
            // Frage wurde beantwortet (richtig oder falsch)
            // Markiere als "einmal beantwortet"
            if (!in_array($question->id, $completedOnce)) {
                $completedOnce[] = $question->id;
            }
            
            // Entferne von aktueller Liste (wird später wieder hinzugefügt wenn nötig)
            $practiceIds = array_diff($practiceIds, [$question->id]);
            $practiceIds = array_values($practiceIds);
            
            // Wenn alle Fragen einmal beantwortet wurden
            if (count($completedOnce) >= count($failed) && empty($practiceIds)) {
                // Runde 2: Alle noch nicht gemeisterten Fragen nochmal
                $notMastered = [];
                foreach ($failed as $fId) {
                    $prog = UserQuestionProgress::where('user_id', $user->id)
                                                ->where('question_id', $fId)
                                                ->first();
                    if (!$prog || !$prog->isMastered()) {
                        $notMastered[] = $fId;
                    }
                }
                
                if (!empty($notMastered)) {
                    shuffle($notMastered);
                    $practiceIds = $notMastered;
                    $round = 2;
                    $completedOnce = []; // Reset für nächste Runde
                }
            }
        }
        
        // Update Session
        session([
            'failed_practice_ids' => $practiceIds,
            'failed_practice_completed_once' => $completedOnce,
            'failed_practice_round' => $round
        ]);
        
        \Log::info('Failed Practice submit processed', [
            'question_id' => $question->id,
            'is_correct' => $isCorrect,
            'mastered' => $questionProgress->isMastered(),
            'remaining_ids' => $practiceIds,
            'round' => $round,
            'completed_once_count' => count($completedOnce)
        ]);
        
        // Speichere Antwort-Ergebnisse in Session für Popup-Anzeige
        session([
            'answer_result' => [
                'question_id' => $question->id,
                'is_correct' => $isCorrect,
                'user_answer' => $userAnswer->toArray(),
                'question_progress' => $questionProgress->consecutive_correct,
                'answer_mapping' => $mapping
            ],
            'gamification_result' => $gamificationResult
        ]);
        
        // Redirect zurück zur Show-Methode (Post/Redirect/Get Pattern)
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
