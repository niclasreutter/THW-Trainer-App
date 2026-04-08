<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\QuestionStatistic;

/**
 * Guest Practice Controller für die Landing-Domain (thw-trainer.de)
 *
 * Nutzt die gleiche View (practice.blade.php) wie der authentifizierte
 * PracticeController, aber ohne Gamification, Bookmarks und Reports.
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
        session()->forget(['guest_practice_mode', 'guest_practice_parameter', 'guest_practice_ids', 'guest_practice_skipped', 'guest_questions_answered']);

        $allIds = Question::pluck('id')->toArray();
        shuffle($allIds);

        session([
            'guest_practice_mode' => 'all',
            'guest_practice_parameter' => null,
            'guest_practice_ids' => $allIds,
            'guest_practice_total_start' => count($allIds),
        ]);

        return redirect()->route('landing.guest.practice.index');
    }

    /**
     * Zeige die aktuelle Frage
     */
    public function show(Request $request)
    {
        $answered = session('guest_questions_answered', 0);
        $total = Question::count();
        $mode = session('guest_practice_mode', 'all');

        // Registration-Interstitial alle 20 Fragen
        if ($answered > 0 && $answered % 20 === 0 && !$request->has('continue')) {
            $prompts = $this->getRegistrationPrompts();
            $promptIndex = (int) floor($answered / 20) - 1;
            $prompt = $prompts[$promptIndex % count($prompts)];

            return view('practice', $this->baseViewData($total, $mode) + [
                'question' => null,
                'registrationPrompt' => $prompt,
                'questionsAnswered' => $answered,
            ]);
        }

        $skipped = session('guest_practice_skipped', []);
        $practiceIds = session('guest_practice_ids', []);

        if (empty($practiceIds)) {
            return redirect()->route('landing.guest.practice.menu');
        }

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

        $idsToShow = array_values($idsToShow);
        $question = Question::find($idsToShow[0]);

        if (!$question) {
            session()->forget(['guest_practice_mode', 'guest_practice_parameter', 'guest_practice_ids', 'guest_practice_skipped']);
            return redirect()->route('landing.guest.practice.menu')
                ->with('error', 'Die angeforderte Frage konnte nicht gefunden werden.');
        }

        $totalInMode = count($practiceIds);
        $remaining = count($idsToShow);
        $currentInMode = $totalInMode - $remaining + 1;

        return view('practice', $this->baseViewData($total, $mode) + [
            'question' => $question,
            'totalInMode' => $totalInMode,
            'currentInMode' => $currentInMode,
        ]);
    }

    /**
     * Antwort absenden
     */
    public function submit(Request $request)
    {
        $question = Question::findOrFail($request->question_id);

        $mappingJson = $request->input('answer_mapping');
        $mapping = json_decode($mappingJson, true);

        $userAnswerPositions = $request->answer ?? [];

        $userAnswer = collect($userAnswerPositions)->map(function ($position) use ($mapping) {
            return $mapping[$position] ?? null;
        })->filter()->sort()->values();

        $solution = collect(explode(',', $question->loesung))->map(fn($s) => trim($s))->sort()->values();
        $isCorrect = $userAnswer->all() === $solution->all();

        QuestionStatistic::create([
            'question_id' => $question->id,
            'is_correct' => $isCorrect,
            'source' => 'practice',
        ]);

        $answered = session('guest_questions_answered', 0) + 1;
        session(['guest_questions_answered' => $answered]);

        $skipped = session('guest_practice_skipped', []);
        $practiceIds = session('guest_practice_ids', []);

        if ($isCorrect) {
            $skipped = array_diff($skipped, [$question->id]);
            session(['guest_practice_skipped' => $skipped]);

            if (!empty($practiceIds)) {
                $practiceIds = array_diff($practiceIds, [$question->id]);
                session(['guest_practice_ids' => array_values($practiceIds)]);
            }
        } else {
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

        return view('practice', $this->baseViewData($total, $mode) + [
            'question' => $question,
            'isCorrect' => $isCorrect,
            'userAnswer' => $userAnswer,
            'answerResult' => [
                'question_id' => $question->id,
                'is_correct' => $isCorrect,
                'user_answer' => $userAnswer->toArray(),
                'correct_answer' => $solution->toArray(),
                'question_progress' => 0,
                'answer_mapping' => $mapping,
            ],
            'gamificationResult' => null,
        ]);
    }

    /**
     * Gemeinsame View-Daten für alle Methoden
     */
    private function baseViewData(int $total, string $mode): array
    {
        $totalStart = session('guest_practice_total_start', $total);
        $remaining = count(session('guest_practice_ids', []));
        $progress = $totalStart - $remaining;
        $progressPercent = $totalStart > 0 ? round(($progress / $totalStart) * 100) : 0;

        return [
            'progress' => $progress,
            'total' => $totalStart,
            'mode' => $mode,
            'progressPercent' => $progressPercent,
            'difficultyInfo' => ['level' => 'unknown', 'label' => '', 'color' => '', 'percent' => null],
            'isSpacedRepetition' => false,
            'context' => 'guest',
            'isLanding' => true,
            'submitUrl' => route('landing.guest.practice.submit'),
            'showUrl' => route('landing.guest.practice.index'),
            'summaryUrl' => route('landing.guest.practice.menu'),
            'menuUrl' => route('landing.guest.practice.menu'),
        ];
    }

    /**
     * 5 verschiedene Registration-Prompts
     */
    private function getRegistrationPrompts(): array
    {
        return [
            [
                'title' => 'Dein Fortschritt geht verloren',
                'description' => 'Als Gast werden deine Antworten nicht gespeichert. Mit einem kostenlosen Account siehst du jederzeit, welche Fragen du bereits beherrscht und wo du noch Nachholbedarf hast.',
                'benefit' => 'Fortschritt speichern',
                'icon' => 'bi-save',
            ],
            [
                'title' => 'Lerne effizienter mit Wiederholungen',
                'description' => 'Registrierte Nutzer profitieren von intelligentem Spacing: Fragen, die du falsch beantwortest, werden gezielt wiederholt, bis sie sitzen. So lernst du schneller und nachhaltiger.',
                'benefit' => 'Spaced Repetition',
                'icon' => 'bi-arrow-repeat',
            ],
            [
                'title' => 'Sammle XP und steige im Level auf',
                'description' => 'Für jede richtige Antwort erhältst du Erfahrungspunkte. Schalte neue Level frei und verfolge deine Entwicklung auf deinem persönlichen Dashboard.',
                'benefit' => 'Level und XP',
                'icon' => 'bi-lightning-charge',
            ],
            [
                'title' => 'Behalte deine Lernserie bei',
                'description' => 'Tägliches Lernen wird mit Streaks belohnt. Je länger deine Serie, desto mehr Bonus-XP erhältst du. Ohne Account geht jede Serie verloren.',
                'benefit' => 'Streaks',
                'icon' => 'bi-fire',
            ],
            [
                'title' => 'Detaillierte Statistiken',
                'description' => 'Analysiere deine Stärken und Schwächen mit ausführlichen Statistiken pro Lernabschnitt. Erkenne Muster in deinen Fehlern und optimiere dein Lernverhalten.',
                'benefit' => 'Statistiken',
                'icon' => 'bi-graph-up',
            ],
        ];
    }
}
