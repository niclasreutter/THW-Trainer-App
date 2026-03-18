<?php

namespace App\Http\Controllers;

use App\Models\Ortsverband;
use App\Models\OrtsverbandLernpool;
use App\Models\OrtsverbandLernpoolQuestion;
use App\Models\OrtsverbandLernpoolProgress;
use App\Models\OrtsverbandLernpoolQuestionStatistic;
use App\Services\PracticeSessionService;
use App\Services\ProgressResolvers\LernpoolProgressResolver;
use App\Services\GamificationService;
use Illuminate\Http\Request;

class OrtsverbandLernpoolPracticeController extends Controller
{
    private PracticeSessionService $practiceService;

    public function __construct()
    {
        $this->practiceService = new PracticeSessionService(
            new LernpoolProgressResolver(),
            new GamificationService()
        );
    }

    /**
     * Enrollment-Check Helper
     */
    private function checkEnrollment($user, OrtsverbandLernpool $lernpool)
    {
        return $user->lernpoolEnrollments()
            ->where('lernpool_id', $lernpool->id)
            ->first();
    }

    /**
     * Practice starten: Alle Fragen
     */
    public function practice(Ortsverband $ortsverband, OrtsverbandLernpool $lernpool)
    {
        $user = auth()->user();

        if (!$this->checkEnrollment($user, $lernpool)) {
            return redirect()
                ->route('ortsverband.show', $ortsverband)
                ->with('error', 'Du bist nicht in diesem Lernpool eingeschrieben.');
        }

        $ids = OrtsverbandLernpoolQuestion::where('lernpool_id', $lernpool->id)
            ->pluck('id')
            ->toArray();

        if (empty($ids)) {
            return redirect()
                ->route('ortsverband.lernpools.show', [$ortsverband, $lernpool])
                ->with('error', 'Keine Fragen in diesem Lernpool.');
        }

        shuffle($ids);
        $this->practiceService->startSession('lernpool', $lernpool->id, $ids, 'all', 'requeue');

        return redirect()->route('ortsverband.lernpools.practice.show', [$ortsverband, $lernpool]);
    }

    /**
     * Practice starten: Nur ungelöste Fragen
     */
    public function practiceUnsolved(Ortsverband $ortsverband, OrtsverbandLernpool $lernpool)
    {
        $user = auth()->user();

        if (!$this->checkEnrollment($user, $lernpool)) {
            return redirect()
                ->route('ortsverband.show', $ortsverband)
                ->with('error', 'Du bist nicht in diesem Lernpool eingeschrieben.');
        }

        $ids = OrtsverbandLernpoolQuestion::where('lernpool_id', $lernpool->id)
            ->whereNotExists(function($query) use ($user) {
                $query->select('id')
                    ->from('ortsverband_lernpool_progress')
                    ->whereColumn('question_id', 'ortsverband_lernpool_questions.id')
                    ->where('user_id', $user->id)
                    ->where('solved', true);
            })
            ->pluck('id')
            ->toArray();

        if (empty($ids)) {
            return redirect()
                ->route('ortsverband.lernpools.show', [$ortsverband, $lernpool])
                ->with('info', 'Alle Fragen bereits gelöst!');
        }

        shuffle($ids);
        $this->practiceService->startSession('lernpool', $lernpool->id, $ids, 'unsolved', 'requeue');

        return redirect()->route('ortsverband.lernpools.practice.show', [$ortsverband, $lernpool]);
    }

    /**
     * Practice starten: Nach Lernabschnitt
     */
    public function practiceSection(Ortsverband $ortsverband, OrtsverbandLernpool $lernpool, $nr)
    {
        $user = auth()->user();

        if (!$this->checkEnrollment($user, $lernpool)) {
            return redirect()
                ->route('ortsverband.show', $ortsverband)
                ->with('error', 'Du bist nicht in diesem Lernpool eingeschrieben.');
        }

        $ids = OrtsverbandLernpoolQuestion::where('lernpool_id', $lernpool->id)
            ->where('lernabschnitt', $nr)
            ->pluck('id')
            ->toArray();

        if (empty($ids)) {
            return redirect()
                ->route('ortsverband.lernpools.show', [$ortsverband, $lernpool])
                ->with('error', 'Keine Fragen in diesem Lernabschnitt.');
        }

        shuffle($ids);
        $this->practiceService->startSession('lernpool', $lernpool->id, $ids, 'section', 'requeue');

        // Store section nr in session for context display
        session(["practice_lernpool_{$lernpool->id}_section_nr" => $nr]);

        return redirect()->route('ortsverband.lernpools.practice.show', [$ortsverband, $lernpool]);
    }

    /**
     * Unified Practice View: Aktuelle Frage anzeigen
     */
    public function practiceShow(Ortsverband $ortsverband, OrtsverbandLernpool $lernpool)
    {
        $user = auth()->user();

        // Check if session has questions
        if (!$this->practiceService->hasQuestionsRemaining('lernpool', $lernpool->id)) {
            return redirect()->route('ortsverband.lernpools.practice.summary', [$ortsverband, $lernpool]);
        }

        // Get answer/gamification results from service
        $answerResult = $this->practiceService->getAndClearAnswerResult('lernpool', $lernpool->id);
        $gamificationResult = $this->practiceService->getAndClearGamificationResult('lernpool', $lernpool->id);

        // If showing answered question feedback, use that question ID
        $showAnsweredQuestion = $answerResult && isset($answerResult['question_id']);

        if ($showAnsweredQuestion) {
            $questionId = $answerResult['question_id'];
        } else {
            $questionId = $this->practiceService->getCurrentQuestionId('lernpool', $lernpool->id);
        }

        if (!$questionId) {
            return redirect()->route('ortsverband.lernpools.practice.summary', [$ortsverband, $lernpool]);
        }

        $resolver = new LernpoolProgressResolver();
        $question = $resolver->getQuestionById($questionId);

        // Get progress from service
        $serviceProgress = $this->practiceService->getProgress('lernpool', $lernpool->id);
        $mode = $this->practiceService->getMode('lernpool', $lernpool->id);

        // Section name if mode is 'section'
        $sectionName = null;
        if ($mode === 'section') {
            $sectionNr = session("practice_lernpool_{$lernpool->id}_section_nr");
            if ($sectionNr) {
                $sectionName = "Lernabschnitt {$sectionNr}";
            }
        }

        // Overall lernpool progress for the progress bar
        $allQuestions = $lernpool->questions()->get();
        $totalCount = $allQuestions->count();
        $threshold = \App\Models\UserQuestionProgress::MASTERY_THRESHOLD;

        $userProgress = $user->lernpoolProgress()
            ->whereIn('question_id', $allQuestions->pluck('id'))
            ->get();

        $totalProgressPoints = 0;
        foreach ($userProgress as $prog) {
            $totalProgressPoints += min($prog->consecutive_correct ?? 0, $threshold);
        }
        $maxProgressPoints = $totalCount * $threshold;
        $progressPercent = $maxProgressPoints > 0 ? round(($totalProgressPoints / $maxProgressPoints) * 100) : 0;

        $solvedCount = $userProgress->where('solved', true)->count();

        return view('practice', [
            'question' => $question,
            'progress' => $solvedCount,
            'total' => $totalCount,
            'progressPercent' => $progressPercent,
            'user' => $user,
            'mode' => $mode,
            'totalInMode' => $serviceProgress['total'],
            'currentInMode' => $serviceProgress['current'],
            'answerResult' => $answerResult,
            'gamificationResult' => $gamificationResult,
            'context' => 'lernpool',
            'contextLabel' => $lernpool->name,
            'backUrl' => route('ortsverband.lernpools.show', [$ortsverband, $lernpool]),
            'submitUrl' => route('ortsverband.lernpools.practice.submit', [$ortsverband, $lernpool]),
            'showUrl' => route('ortsverband.lernpools.practice.show', [$ortsverband, $lernpool]),
            'summaryUrl' => route('ortsverband.lernpools.practice.summary', [$ortsverband, $lernpool]),
            'issueUrl' => null,
            'difficultyInfo' => null,
            'isSpacedRepetition' => false,
            'bookmarked' => false,
            'sectionName' => $sectionName,
        ]);
    }

    /**
     * Unified Practice Submit: Antwort verarbeiten
     */
    public function practiceSubmit(Request $request, Ortsverband $ortsverband, OrtsverbandLernpool $lernpool)
    {
        $validated = $request->validate([
            'question_id' => 'required|exists:ortsverband_lernpool_questions,id',
            'answer' => 'nullable|array',
            'answer_mapping' => 'required|json',
        ]);

        $questionId = $validated['question_id'];

        // Hole das Mapping aus dem Hidden Field
        $mappingJson = $request->input('answer_mapping');
        $mapping = json_decode($mappingJson, true);

        // User-Antworten (Positionen 0, 1, 2)
        $userAnswerPositions = $request->input('answer', []);

        // Mappe Positionen zurück auf Original-Buchstaben
        $userAnswer = collect($userAnswerPositions)->map(function($position) use ($mapping) {
            return $mapping[$position] ?? null;
        })->filter()->sort()->values()->toArray();

        // Delegate to service
        $this->practiceService->submitAnswer('lernpool', $lernpool->id, $questionId, $userAnswer, $mapping);

        return redirect()->route('ortsverband.lernpools.practice.show', [$ortsverband, $lernpool]);
    }

    /**
     * Unified Practice Summary: Session-Zusammenfassung
     */
    public function practiceSummary(Ortsverband $ortsverband, OrtsverbandLernpool $lernpool)
    {
        $user = auth()->user();

        // End session and get summary data
        $summary = $this->practiceService->endSession('lernpool', $lernpool->id);

        // Clean up section nr session key
        session()->forget("practice_lernpool_{$lernpool->id}_section_nr");

        // Fallback if no stats
        if ($summary['totalAnswered'] === 0 && $summary['correct'] === 0) {
            return redirect()->route('ortsverband.lernpools.show', [$ortsverband, $lernpool]);
        }

        $stats = [
            'correct' => $summary['correct'],
            'incorrect' => $summary['incorrect'],
            'points' => $summary['points'],
            'mastered' => $summary['mastered'],
        ];

        $streak = $user->streak_days ?? 0;

        return view('practice-summary', [
            'stats' => $stats,
            'totalAnswered' => $summary['totalAnswered'],
            'accuracy' => $summary['accuracy'],
            'durationMinutes' => $summary['durationMinutes'],
            'modeName' => $summary['modeName'],
            'streak' => $streak,
            'context' => 'lernpool',
            'contextLabel' => $lernpool->name,
            'backUrl' => route('ortsverband.lernpools.show', [$ortsverband, $lernpool]),
            'completed' => false,
        ]);
    }

    /**
     * Unenroll User aus Lernpool
     */
    public function unenroll(Request $request, Ortsverband $ortsverband, OrtsverbandLernpool $lernpool)
    {
        $user = auth()->user();

        $enrollment = $user->lernpoolEnrollments()
            ->where('lernpool_id', $lernpool->id)
            ->first();

        if ($enrollment) {
            $enrollment->delete();
        }

        return redirect()
            ->route('ortsverband.show', $ortsverband)
            ->with('success', 'Du hast dich aus diesem Lernpool abgemeldet.');
    }
}
