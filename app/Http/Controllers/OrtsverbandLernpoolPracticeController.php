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
     * Zeige Lernansicht für Lernpool (wie practice.blade.php)
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

        // Hole Fragen für diesen Lernpool
        $questions = $lernpool->questions()
            ->orderBy('lernabschnitt')
            ->orderBy('nummer')
            ->get();

        // Hole Fortschritt für User
        $progress = $user->lernpoolProgress()
            ->whereIn('question_id', $questions->pluck('id'))
            ->get()
            ->keyBy('question_id');

        // Finde nächste ungemeisterte Frage
        $currentQuestion = $questions->first(function($q) use ($progress) {
            $p = $progress->get($q->id);
            return !$p || !$p->solved;
        });

        if (!$currentQuestion) {
            // Alle Fragen gemeistert
            return view('ortsverband.lernpools.practice', [
                'ortsverband' => $ortsverband,
                'lernpool' => $lernpool,
                'questions' => $questions,
                'progress' => $progress,
                'currentQuestion' => null,
                'isCompleted' => true,
                'totalQuestions' => $questions->count(),
                'solvedQuestions' => $progress->where('solved', true)->count(),
            ]);
        }

        // Berechne Fortschritt
        $solvedCount = $progress->where('solved', true)->count();
        $totalCount = $questions->count();
        $progressPercent = $totalCount > 0 ? round(($solvedCount / $totalCount) * 100) : 0;

        // Gruppiere Fragen nach Lernabschnitt
        $questionsBySection = $questions->groupBy('lernabschnitt');

        return view('ortsverband.lernpools.practice', [
            'ortsverband' => $ortsverband,
            'lernpool' => $lernpool,
            'questions' => $questions,
            'questionsBySection' => $questionsBySection,
            'progress' => $progress,
            'currentQuestion' => $currentQuestion,
            'isCompleted' => false,
            'totalQuestions' => $totalCount,
            'solvedQuestions' => $solvedCount,
            'progressPercent' => $progressPercent,
        ]);
    }

    /**
     * Verarbeite Antwort
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
            'answer' => 'required|string|in:a,b,c',
        ]);

        $question = OrtsverbandLernpoolQuestion::findOrFail($validated['question_id']);

        // Prüfe ob Frage zu diesem Lernpool gehört
        if ($question->lernpool_id !== $lernpool->id) {
            return redirect()
                ->back()
                ->with('error', 'Ungültige Frage.');
        }

        // Prüfe Antwort
        $isCorrect = $question->loesung === $validated['answer'];

        // Aktualisiere Fortschritt
        $progress = OrtsverbandLernpoolProgress::firstOrCreate(
            ['user_id' => $user->id, 'question_id' => $question->id],
            ['consecutive_correct' => 0, 'total_attempts' => 0, 'correct_attempts' => 0]
        );

        $progress->updateProgress($isCorrect);

        // Gamification: Punkte & XP
        $points = 0;
        if ($isCorrect) {
            $points = 5; // Base Punkte pro richtige Antwort
            
            // Bonus wenn gemeistert
            if ($progress->solved) {
                $points += 10;
                $this->gamificationService->addXP($user, 25, 'lernpool_question_solved');
            } else {
                $this->gamificationService->addXP($user, 10, 'lernpool_question_correct');
            }
        } else {
            $this->gamificationService->addXP($user, 2, 'lernpool_question_attempted');
        }

        // Aktualisiere Punkte
        if ($points > 0) {
            $user->increment('punkte', $points);
        }

        // Aktualisiere Streak wenn täglich
        if ($isCorrect) {
            $this->gamificationService->updateStreak($user);
        }

        return redirect()
            ->back()
            ->with('success', $isCorrect ? 'Richtig! 🎉' : 'Leider falsch. Versuche es nochmal!');
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
