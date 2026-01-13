<?php

namespace App\Http\Controllers;

use App\Models\Ortsverband;
use App\Models\OrtsverbandLernpool;
use App\Models\OrtsverbandLernpoolQuestion;
use App\Models\OrtsverbandLernpoolProgress;
use App\Services\GamificationService;
use Illuminate\Http\Request;

class OrtsverbandLernpoolPracticeController extends Controller
{
    protected $gamificationService;

    public function __construct(GamificationService $gamificationService)
    {
        $this->gamificationService = $gamificationService;
    }

    /**
     * Zeige Lernansicht für Lernpool (wie practice.blade.php - eine Frage nach der anderen)
     */
    public function show(Ortsverband $ortsverband, OrtsverbandLernpool $lernpool)
    {
        $user = auth()->user();
        
        // Prüfe ob User eingeschrieben ist
        $enrollment = $user->lernpoolEnrollments()
            ->where('lernpool_id', $lernpool->id)
            ->first();

        if (!$enrollment) {
            return redirect()
                ->route('ortsverband.show', $ortsverband)
                ->with('error', 'Du bist nicht in diesem Lernpool eingeschrieben.');
        }

        // Hole alle Fragen für diesen Lernpool
        $allQuestions = $lernpool->questions()
            ->orderBy('lernabschnitt')
            ->orderBy('nummer')
            ->get();

        if ($allQuestions->isEmpty()) {
            return view('ortsverband.lernpools.practice', [
                'ortsverband' => $ortsverband,
                'lernpool' => $lernpool,
                'enrollment' => $enrollment,
                'question' => null,
                'total' => 0,
                'progress' => 0,
                'progressPercent' => 0,
            ]);
        }

        // Hole Fortschritt für User (alle Fragen dieses Lernpools)
        $userProgress = $user->lernpoolProgress()
            ->whereIn('question_id', $allQuestions->pluck('id'))
            ->get()
            ->keyBy('question_id');

        // Berechne Gesamt-Fortschritt (wie in practice.blade.php)
        $totalProgressPoints = 0;
        foreach ($userProgress as $prog) {
            $totalProgressPoints += min($prog->consecutive_correct ?? 0, 2);
        }
        $maxProgressPoints = $allQuestions->count() * 2;
        $progressPercent = $maxProgressPoints > 0 ? round(($totalProgressPoints / $maxProgressPoints) * 100) : 0;

        // Zähle gemeisterte Fragen (2x richtig)
        $solvedCount = $userProgress->where('solved', true)->count();
        $totalCount = $allQuestions->count();

        // Intelligente Priorisierung für nächste Frage:
        // 1. Ungelöste Fragen (noch nicht 2x richtig)
        // 2. Wenn alle gelöst: alle Fragen zufällig
        // WICHTIG: Tracke bereits gestellte Fragen in Session, um Duplikate zu vermeiden

        // Session Key für bereits gestellte Fragen (pro Lernpool)
        $sessionKey = 'lernpool_asked_' . $lernpool->id;
        $askedQuestionIds = session($sessionKey, []);

        // 1. Ungelöste Fragen (nach Lernabschnitt sortiert)
        $unsolvedQuestions = $allQuestions->filter(function($q) use ($userProgress) {
            $p = $userProgress->get($q->id);
            return !$p || !$p->solved;
        });

        // Bestimme Pool von verfügbaren Fragen
        $questionPool = $unsolvedQuestions->isNotEmpty() ? $unsolvedQuestions : $allQuestions;

        // Filtere bereits gestellte Fragen aus
        $availableQuestions = $questionPool->filter(function($q) use ($askedQuestionIds) {
            return !in_array($q->id, $askedQuestionIds);
        });

        // Wenn keine Fragen mehr verfügbar -> Session zurücksetzen (neuer Durchgang)
        if ($availableQuestions->isEmpty() && $questionPool->isNotEmpty()) {
            session()->forget($sessionKey);
            $askedQuestionIds = [];
            $availableQuestions = $questionPool;
        }

        // Shuffle verfügbare Fragen und erste nehmen
        $idsToShow = $availableQuestions->pluck('id')->toArray();
        shuffle($idsToShow);

        // Erste Frage aus der Liste holen
        $questionId = $idsToShow[0] ?? null;
        $question = $questionId ? OrtsverbandLernpoolQuestion::find($questionId) : null;

        // Speichere gezeigte Frage in Session, um Duplikate zu vermeiden
        if ($questionId) {
            $askedQuestionIds[] = $questionId;
            session([$sessionKey => $askedQuestionIds]);
        }

        if (!$question) {
            return view('ortsverband.lernpools.practice', [
                'ortsverband' => $ortsverband,
                'lernpool' => $lernpool,
                'enrollment' => $enrollment,
                'question' => null,
                'total' => $totalCount,
                'progress' => $solvedCount,
                'progressPercent' => $progressPercent,
            ]);
        }

        return view('ortsverband.lernpools.practice', [
            'ortsverband' => $ortsverband,
            'lernpool' => $lernpool,
            'enrollment' => $enrollment,
            'question' => $question,
            'total' => $totalCount,
            'progress' => $solvedCount,
            'progressPercent' => $progressPercent,
        ]);
    }

    /**
     * Verarbeite Antwort (wie PracticeController submit)
     */
    public function answer(Request $request, Ortsverband $ortsverband, OrtsverbandLernpool $lernpool)
    {
        $user = auth()->user();
        
        // Prüfe ob User eingeschrieben ist
        $enrollment = $user->lernpoolEnrollments()
            ->where('lernpool_id', $lernpool->id)
            ->first();

        if (!$enrollment) {
            return redirect()
                ->back()
                ->with('error', 'Du bist nicht in diesem Lernpool eingeschrieben.');
        }

        $validated = $request->validate([
            'question_id' => 'required|exists:ortsverband_lernpool_questions,id',
            'answer' => 'nullable|array',
            'answer.*' => 'integer',
            'answer_mapping' => 'required|json',
        ]);

        $question = OrtsverbandLernpoolQuestion::findOrFail($validated['question_id']);

        // Prüfe ob Frage zu diesem Lernpool gehört
        if ($question->lernpool_id !== $lernpool->id) {
            return redirect()
                ->back()
                ->with('error', 'Ungültige Frage.');
        }

        // Parse answer_mapping
        $mapping = json_decode($validated['answer_mapping'], true);
        
        // Hole ausgewählte Antworten
        $selectedPositions = $validated['answer'] ?? [];
        $userAnswerLetters = [];
        
        foreach ($selectedPositions as $position) {
            if (isset($mapping[$position])) {
                $userAnswerLetters[] = strtoupper($mapping[$position]);
            }
        }
        sort($userAnswerLetters);
        
        // Richtige Antworten aus loesung (kann mehrere sein, z.B. "A,B")
        $correctAnswers = collect(explode(',', $question->loesung))
            ->map(fn($a) => strtoupper(trim($a)))
            ->sort()
            ->values()
            ->toArray();
        
        // Prüfe ob korrekt
        $isCorrect = $userAnswerLetters === $correctAnswers;

        // Aktualisiere Fortschritt
        $progress = OrtsverbandLernpoolProgress::firstOrCreate(
            ['user_id' => $user->id, 'question_id' => $question->id],
            ['consecutive_correct' => 0, 'total_attempts' => 0, 'correct_attempts' => 0, 'solved' => false]
        );

        $progress->total_attempts++;
        
        if ($isCorrect) {
            $progress->correct_attempts++;
            $progress->consecutive_correct++;
            
            // Gemeistert wenn 2x hintereinander richtig
            if ($progress->consecutive_correct >= 2) {
                $progress->solved = true;
            }
        } else {
            $progress->consecutive_correct = 0;
        }
        
        $progress->save();

        // Gamification: Punkte & XP über awardQuestionPoints
        $gamificationResult = $this->gamificationService->awardQuestionPoints($user, $isCorrect, $question->id);
        $pointsAwarded = $gamificationResult['points'] ?? 10;
        $reason = $gamificationResult['reason'] ?? 'Frage beantwortet';
        
        // Zusatzbonus bei Meisterung (2x hintereinander richtig)
        if ($isCorrect && $progress->solved && $progress->consecutive_correct == 2) {
            $pointsAwarded += 15;
            $reason = 'Frage gemeistert!';
        }
        
        // Streak Update bei richtiger Antwort
        if ($isCorrect) {
            $this->gamificationService->updateStreak($user);
        }

        // Session-Daten für Anzeige (wie in PracticeController)
        session()->flash('answer_result', [
            'question_id' => $question->id,
            'is_correct' => $isCorrect,
            'user_answer' => $userAnswerLetters,
            'answer_mapping' => $mapping,
            'question_progress' => $progress->consecutive_correct,
        ]);
        
        if ($isCorrect && $pointsAwarded > 0) {
            session()->flash('gamification_result', [
                'points_awarded' => $pointsAwarded,
                'reason' => $reason,
            ]);
        }

        return redirect()->route('ortsverband.lernpools.practice', [$ortsverband, $lernpool]);
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
