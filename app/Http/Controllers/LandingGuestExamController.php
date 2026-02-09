<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\QuestionStatistic;
use App\Models\ExamStatistic;

/**
 * Guest Exam Controller für die Landing-Domain (thw-trainer.de)
 *
 * Verwendet das Landing-Layout (Light Mode) anstelle des App-Layouts.
 * Die Logik ist identisch mit dem GuestExamController.
 */
class LandingGuestExamController extends Controller
{
    /**
     * Starte eine Prüfung für Gäste
     */
    public function start()
    {
        session()->forget(['guest_exam_questions', 'guest_exam_answers', 'guest_exam_current']);

        $allQuestions = Question::inRandomOrder()->limit(40)->get();

        if ($allQuestions->isEmpty()) {
            return redirect()->route('landing.guest.practice.menu')
                ->with('error', 'Keine Fragen verfügbar.');
        }

        session(['guest_exam_questions' => $allQuestions->toArray()]);

        return view('guest.exam', [
            'fragen' => $allQuestions,
            'isLanding' => true
        ]);
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
            $mappingJson = $answerMappings[$nr] ?? null;
            $mapping = $mappingJson ? json_decode($mappingJson, true) : null;

            $solution = collect(explode(',', $frage->loesung))->map(fn($s) => trim($s));

            if ($mapping) {
                $userAnswerPositions = $userAnswers[$nr] ?? [];
                $userAnswer = collect($userAnswerPositions)->map(function($position) use ($mapping) {
                    return $mapping[$position] ?? null;
                })->filter()->sort()->values();
            } else {
                $userAnswer = collect($userAnswers[$nr] ?? [])->sort()->values();
            }

            $isCorrect = $userAnswer->all() === $solution->sort()->values()->all();
            $results[$nr] = [
                'frage' => $frage,
                'userAnswer' => $userAnswer,
                'solution' => $solution,
                'isCorrect' => $isCorrect,
                'mapping' => $mapping
            ];
            if ($isCorrect) {
                $correctCount++;
            }

            QuestionStatistic::create([
                'question_id' => $frage->id,
                'is_correct' => $isCorrect,
                'source' => 'exam',
            ]);
        }

        $total = count($fragen);
        $passed = $total > 0 && $correctCount / $total >= 0.8;

        ExamStatistic::create([
            'user_id' => null,
            'is_passed' => $passed,
            'correct_answers' => $correctCount,
        ]);

        session()->forget(['guest_exam_questions', 'guest_exam_answers', 'guest_exam_current']);

        return view('guest.exam', [
            'fragen' => $fragen,
            'results' => $results,
            'submitted' => true,
            'correctCount' => $correctCount,
            'total' => $total,
            'passed' => $passed,
            'isLanding' => true
        ]);
    }
}
