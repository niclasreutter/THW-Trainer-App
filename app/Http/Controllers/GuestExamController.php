<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\QuestionStatistic;
use App\Models\ExamStatistic;

class GuestExamController extends Controller
{
    /**
     * Starte eine Prüfung für Gäste
     */
    public function start()
    {
        // Session zurücksetzen
        session()->forget(['guest_exam_questions', 'guest_exam_answers', 'guest_exam_current']);
        
        // 40 zufällige Fragen auswählen (wie bei normaler Prüfung)
        $allQuestions = Question::inRandomOrder()->limit(40)->get();
        
        if ($allQuestions->isEmpty()) {
            return redirect()->route('guest.practice.menu')->with('error', 'Keine Fragen verfügbar.');
        }
        
        // Fragen in Session speichern
        session(['guest_exam_questions' => $allQuestions->toArray()]);
        
        return view('guest.exam', ['fragen' => $allQuestions]);
    }

    /**
     * Prüfungsantwort einreichen
     */
    public function submit(Request $request)
    {
        $fragen = collect($request->fragen_ids ?? [])->map(fn($id) => Question::find($id));
        $userAnswers = $request->input('answer', []);
        $answerMappings = $request->input('answer_mappings', []);
        $results = [];
        $correctCount = 0;
        
        foreach ($fragen as $nr => $frage) {
            // Hole Mapping für diese Frage
            $mappingJson = $answerMappings[$nr] ?? null;
            $mapping = $mappingJson ? json_decode($mappingJson, true) : null;
            
            $solution = collect(explode(',', $frage->loesung))->map(fn($s) => trim($s));
            
            // Wenn Mapping vorhanden, mappe Positionen zurück auf Buchstaben
            if ($mapping) {
                $userAnswerPositions = $userAnswers[$nr] ?? [];
                $userAnswer = collect($userAnswerPositions)->map(function($position) use ($mapping) {
                    return $mapping[$position] ?? null;
                })->filter()->sort()->values();
            } else {
                // Fallback ohne Mapping (alter Code)
                $userAnswer = collect($userAnswers[$nr] ?? [])->sort()->values();
            }
            
            $isCorrect = $userAnswer->all() === $solution->sort()->values()->all();
            $results[$nr] = [
                'frage' => $frage,
                'userAnswer' => $userAnswer,
                'solution' => $solution,
                'isCorrect' => $isCorrect,
                'mapping' => $mapping // Speichere Mapping für die Anzeige
            ];
            if ($isCorrect) {
                $correctCount++;
            }
            
            // Anonyme Statistik erfassen
            QuestionStatistic::create([
                'question_id' => $frage->id,
                'is_correct' => $isCorrect,
                'source' => 'exam',
            ]);
        }
        
        $total = count($fragen);
        $passed = $total > 0 && $correctCount / $total >= 0.8; // 80% wie bei normaler Prüfung
        
        // Prüfungsstatistik erfassen (anonym, ohne user_id)
        ExamStatistic::create([
            'user_id' => null, // Gast hat keine User-ID
            'is_passed' => $passed,
            'correct_answers' => $correctCount,
        ]);
        
        // Session löschen
        session()->forget(['guest_exam_questions', 'guest_exam_answers', 'guest_exam_current']);
        
        return view('guest.exam', [
            'fragen' => $fragen,
            'results' => $results,
            'submitted' => true,
            'correctCount' => $correctCount,
            'total' => $total,
            'passed' => $passed
        ]);
    }

}
