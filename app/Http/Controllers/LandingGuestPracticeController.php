<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\QuestionStatistic;

/**
 * Guest Practice Controller für die Landing-Domain (thw-trainer.de)
 *
 * Verwendet das Landing-Layout (Light Mode) anstelle des App-Layouts.
 * Die Logik ist identisch mit dem GuestPracticeController.
 */
class LandingGuestPracticeController extends Controller
{
    /**
     * Zeige das Guest Practice-Menü
     */
    public function menu()
    {
        return view('guest.practice-menu')->with('isLanding', true);
    }

    /**
     * Alle Fragen üben (Gast-Modus)
     */
    public function all()
    {
        session()->forget(['guest_practice_mode', 'guest_practice_parameter', 'guest_practice_ids', 'guest_practice_skipped']);

        return $this->practiceMode('all');
    }

    /**
     * Zentrale Methode für verschiedene Practice-Modi (Gast)
     */
    private function practiceMode($mode, $parameter = null)
    {
        $skipped = session('guest_practice_skipped', []);

        switch ($mode) {
            case 'all':
                $allIds = Question::pluck('id')->toArray();
                shuffle($allIds);
                $idsToShow = $allIds;
                break;

            default:
                $idsToShow = [];
        }

        $idsToShow = array_diff($idsToShow, $skipped);

        if (empty($idsToShow)) {
            return redirect()->route('landing.guest.practice.menu')
                ->with('success', 'Alle Fragen wurden bereits bearbeitet!');
        }

        if (!isset($idsToShow[0])) {
            return redirect()->route('landing.guest.practice.menu')
                ->with('error', 'Fehler beim Laden der Fragen.');
        }

        $question = Question::find($idsToShow[0]);

        if (!$question) {
            return redirect()->route('landing.guest.practice.menu')
                ->with('error', 'Die angeforderte Frage konnte nicht gefunden werden.');
        }

        session([
            'guest_practice_mode' => $mode,
            'guest_practice_parameter' => $parameter,
            'guest_practice_ids' => $idsToShow
        ]);

        $totalQuestions = Question::count();
        $progress = 0;
        $total = $totalQuestions;

        return view('guest.practice', compact('question', 'progress', 'total', 'mode'))
            ->with('isLanding', true);
    }

    public function show(Request $request)
    {
        $skipped = session('guest_practice_skipped', []);
        $practiceIds = session('guest_practice_ids', []);
        $mode = session('guest_practice_mode', 'all');

        if (!empty($practiceIds)) {
            $idsToShow = $practiceIds;

            if ($request->has('skip_id')) {
                $skipId = $request->input('skip_id');
                $idsToShow = array_diff($idsToShow, [$skipId]);
                $skipped = array_merge($skipped, [$skipId]);
                session(['guest_practice_skipped' => array_unique($skipped)]);
            } else {
                $idsToShow = array_diff($idsToShow, $skipped);
            }

            if (empty($idsToShow)) {
                session()->forget(['guest_practice_mode', 'guest_practice_parameter', 'guest_practice_ids', 'guest_practice_skipped']);
                return redirect()->route('landing.guest.practice.menu')
                    ->with('success', 'Alle Fragen bearbeitet!');
            }

            if (!isset($idsToShow[0])) {
                session()->forget(['guest_practice_mode', 'guest_practice_parameter', 'guest_practice_ids', 'guest_practice_skipped']);
                return redirect()->route('landing.guest.practice.menu')
                    ->with('error', 'Fehler beim Laden der nächsten Frage.');
            }

            $question = Question::find($idsToShow[0]);

            if (!$question) {
                session()->forget(['guest_practice_mode', 'guest_practice_parameter', 'guest_practice_ids', 'guest_practice_skipped']);
                return redirect()->route('landing.guest.practice.menu')
                    ->with('error', 'Die angeforderte Frage konnte nicht gefunden werden.');
            }

            $total = Question::count();
            $progress = 0;

        } else {
            return redirect()->route('landing.guest.practice.menu');
        }

        return view('guest.practice', compact('question', 'progress', 'total', 'mode'))
            ->with('isLanding', true);
    }

    public function submit(Request $request)
    {
        $question = Question::findOrFail($request->question_id);

        $mappingJson = $request->input('answer_mapping');
        $mapping = json_decode($mappingJson, true);

        $userAnswerPositions = $request->answer ?? [];

        $userAnswer = collect($userAnswerPositions)->map(function($position) use ($mapping) {
            return $mapping[$position] ?? null;
        })->filter()->sort()->values();

        $solution = collect(explode(',', $question->loesung))->map(fn($s) => trim($s))->sort()->values();
        $isCorrect = $userAnswer->all() === $solution->all();

        QuestionStatistic::create([
            'question_id' => $question->id,
            'is_correct' => $isCorrect,
        ]);

        $skipped = session('guest_practice_skipped', []);

        if ($isCorrect) {
            $skipped = array_diff($skipped, [$question->id]);
            session(['guest_practice_skipped' => $skipped]);

            $practiceIds = session('guest_practice_ids', []);
            if (!empty($practiceIds)) {
                $practiceIds = array_diff($practiceIds, [$question->id]);
                session(['guest_practice_ids' => array_values($practiceIds)]);
            }
        } else {
            $practiceIds = session('guest_practice_ids', []);
            if (!empty($practiceIds)) {
                $currentIndex = array_search($question->id, $practiceIds);
                if ($currentIndex !== false) {
                    unset($practiceIds[$currentIndex]);
                    $practiceIds[] = $question->id;
                    session(['guest_practice_ids' => array_values($practiceIds)]);
                }
            }

            $skipped[] = $question->id;
            session(['guest_practice_skipped' => array_unique($skipped)]);
        }

        $mode = session('guest_practice_mode', 'all');
        $total = Question::count();
        $progress = 0;

        return view('guest.practice', [
            'question' => $question,
            'isCorrect' => $isCorrect,
            'userAnswer' => $userAnswer,
            'progress' => $progress,
            'total' => $total,
            'mode' => $mode,
            'isLanding' => true
        ]);
    }
}
