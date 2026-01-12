<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Question;
use App\Models\QuestionStatistic;
use App\Models\UserQuestionProgress;
use App\Models\ExamStatistic;
use Illuminate\Support\Facades\Session;
use App\Services\GamificationService;

class ExamController extends Controller
{
    public function start()
    {
        // Lösche die Session für verarbeitete Prüfungen
        $user = Auth::user();
        session()->forget('exam_processed_' . $user->id);
        
        // Prüfe ob noch Fehler zu wiederholen sind
        $failedQuestions = $this->ensureArray($user->exam_failed_questions);
        if (!empty($failedQuestions) && count($failedQuestions) > 0) {
            // Weiterleitung zum Dashboard mit Fehlermeldung
            return redirect()->route('dashboard')->with('error', 'Du musst zuerst deine falschen Antworten wiederholen, bevor du eine neue Prüfung starten kannst.');
        }
        
        // Strategie: Pro Lernabschnitt (1-10) mindestens 1 Frage
        $selectedIds = [];
        
        // Versuche Fragen zu vermeiden, die in den letzten 3 Prüfungen verwendet wurden
        $recentQuestions = session('recent_exam_questions', []);
        
        // 1. Wähle mindestens 1 Frage pro Lernabschnitt (1-10)
        for ($lernabschnitt = 1; $lernabschnitt <= 10; $lernabschnitt++) {
            // Hole alle Fragen dieses Lernabschnitts
            $sectionQuestions = Question::where('lernabschnitt', $lernabschnitt)
                ->pluck('id')
                ->toArray();
            
            if (empty($sectionQuestions)) {
                continue; // Falls kein Lernabschnitt existiert, überspringen
            }
            
            // Bevorzuge Fragen, die nicht kürzlich verwendet wurden
            $availableSectionQuestions = array_diff($sectionQuestions, $recentQuestions);
            
            // Falls keine verfügbar, nimm alle aus diesem Abschnitt
            if (empty($availableSectionQuestions)) {
                $availableSectionQuestions = $sectionQuestions;
            }
            
            // Wähle zufällig 1 Frage aus diesem Lernabschnitt
            $randomKey = array_rand($availableSectionQuestions);
            $selectedIds[] = $availableSectionQuestions[$randomKey];
        }
        
        // 2. Fülle die restlichen Plätze auf 40 Fragen mit zufälligen Fragen auf
        $remainingCount = 40 - count($selectedIds);
        
        if ($remainingCount > 0) {
            // Hole alle verfügbaren Fragen, die noch nicht ausgewählt wurden
            $allQuestionIds = Question::pluck('id')->toArray();
            $availableIds = array_diff($allQuestionIds, $selectedIds);
            
            // Bevorzuge Fragen, die nicht kürzlich verwendet wurden
            $preferredIds = array_diff($availableIds, $recentQuestions);
            
            // Falls nicht genug bevorzugte Fragen, nimm alle verfügbaren
            if (count($preferredIds) < $remainingCount) {
                $preferredIds = $availableIds;
            }
            
            // Wähle zufällig die restlichen Fragen
            if (count($preferredIds) >= $remainingCount) {
                $additionalIds = array_rand(array_flip($preferredIds), $remainingCount);
                
                // Stelle sicher, dass es ein Array ist
                if (!is_array($additionalIds)) {
                    $additionalIds = [$additionalIds];
                }
                
                $selectedIds = array_merge($selectedIds, $additionalIds);
            } else {
                // Falls nicht genug Fragen vorhanden, nimm alle verfügbaren
                $selectedIds = array_merge($selectedIds, $preferredIds);
            }
        }
        
        // Speichere diese Fragen als "kürzlich verwendet"
        $newRecentQuestions = array_merge($recentQuestions, $selectedIds);
        // Behalte nur die letzten 120 Fragen (3 Prüfungen à 40 Fragen)
        if (count($newRecentQuestions) > 120) {
            $newRecentQuestions = array_slice($newRecentQuestions, -120);
        }
        session(['recent_exam_questions' => $newRecentQuestions]);
        
        // Hole die Fragen und mische sie zufällig
        $fragen = Question::whereIn('id', $selectedIds)->get()->shuffle();
        
        return view('exam', ['fragen' => $fragen]);
    }

    // Wertet die abgegebene Prüfung aus und zeigt das Ergebnis
    public function submit(Request $request)
    {
        // 🔒 SECURITY: Validate input before processing
        $validated = $request->validate([
            'fragen_ids' => 'required|array|size:40',
            'fragen_ids.*' => 'required|integer|exists:questions,id',
            'answer' => 'nullable|array',
            'answer.*' => 'nullable|array',
            'answer.*.*' => 'string|in:A,B,C',
        ]);

        // Load questions securely (only validated IDs)
        $fragen = Question::whereIn('id', $validated['fragen_ids'])
            ->get()
            ->keyBy('id')
            ->sortBy(function ($question) use ($validated) {
                return array_search($question->id, $validated['fragen_ids']);
            })
            ->values();

        // Verify we got exactly 40 questions
        if ($fragen->count() !== 40) {
            return redirect()->route('exam.start')
                ->with('error', 'Ungültige Prüfung. Bitte starte eine neue Prüfung.');
        }

        $userAnswers = $validated['answer'] ?? [];
        $results = [];
        $correctCount = 0;
        $failed = [];
        foreach ($fragen as $nr => $frage) {
            $solution = collect(explode(',', $frage->loesung))->map(fn($s) => trim($s));
            $userAnswer = collect($userAnswers[$nr] ?? []);
            $isCorrect = $userAnswer->sort()->values()->all() === $solution->sort()->values()->all();
            $results[$nr] = [
                'frage' => $frage,
                'userAnswer' => $userAnswer,
                'solution' => $solution,
                'isCorrect' => $isCorrect
            ];
            if ($isCorrect) {
                $correctCount++;
            } else {
                $failed[] = $frage->id;
            }
        }
        $total = count($fragen);
        $passed = $total > 0 && $correctCount / $total >= 0.8;
        $user = Auth::user();
        
        // Fragenstatistiken erfassen (mit User ID)
        foreach ($results as $result) {
            QuestionStatistic::create([
                'question_id' => $result['frage']->id,
                'user_id' => $user->id,
                'is_correct' => $result['isCorrect'],
            ]);
            
            // NEU: Auch Fortschritt in user_question_progress tracken
            // Hinweis: In Prüfungen wird NICHT automatisch zu solved_questions hinzugefügt
            // User müssen Fragen im Practice-Modus 2x richtig beantworten
            $progress = UserQuestionProgress::getOrCreate($user->id, $result['frage']->id);
            $progress->updateProgress($result['isCorrect']);
        }
        
        // Prüfungsstatistik erfassen
        ExamStatistic::create([
            'user_id' => $user->id,
            'is_passed' => $passed,
            'correct_answers' => $correctCount,
        ]);
        
        $gamificationResult = null;
        
        // Prüfe ob diese Prüfung bereits verarbeitet wurde
        $examProcessed = session('exam_processed_' . $user->id, false);
        
        if ($passed && !$examProcessed) {
            $user->exam_passed_count = ($user->exam_passed_count ?? 0) + 1;
            
            // Nur bei 100% korrekten Antworten alle Fehler löschen
            if ($correctCount == $total) {
                $user->exam_failed_questions = [];
            } else {
                // Bei bestandener Prüfung mit Fehlern: Fehler hinzufügen
                $existingFailed = $this->ensureArray($user->exam_failed_questions);
                $user->exam_failed_questions = array_unique(array_merge($existingFailed, $failed));
            }
            
            // Gamification: Punkte für bestandene Prüfung
            $gamificationService = new GamificationService();
            $gamificationResult = $gamificationService->awardExamPoints($user, $correctCount, $total);
            
            // Markiere diese Prüfung als verarbeitet
            session(['exam_processed_' . $user->id => true]);
        } elseif (!$passed) {
            $user->exam_passed_count = 0;
            $user->exam_failed_questions = $failed;
        }
        $user->save();
        
        return view('exam', [
            'fragen' => $fragen,
            'results' => $results,
            'submitted' => true,
            'correctCount' => $correctCount,
            'total' => $total,
            'passed' => $passed,
            'gamification_result' => $gamificationResult
        ]);
    }

    public function show(Request $request, $nr)
    {
        $fragenIds = Session::get('exam_questions', []);
        if (!$fragenIds || $nr >= count($fragenIds)) return redirect()->route('exam.result');
        $frage = Question::find($fragenIds[$nr]);
        $time_left = max(0, 30*60 - now()->diffInSeconds(Session::get('exam_start')));
        if ($time_left <= 0) return redirect()->route('exam.result');
        return view('exam', ['frage' => $frage, 'current' => $nr, 'total' => count($fragenIds), 'time_left' => $time_left]);
    }

    public function answer(Request $request, $nr)
    {
        $fragenIds = Session::get('exam_questions', []);
        $answers = Session::get('exam_answers', []);
        $frage = Question::find($fragenIds[$nr]);
        $userAnswer = collect($request->answer ?? []);
        $solution = collect(explode(',', $frage->loesung))->map(fn($s) => trim($s));
        $isCorrect = $userAnswer->sort()->values()->all() === $solution->sort()->values()->all();
        $answers[$nr] = $isCorrect;
        Session::put('exam_answers', $answers);
        $time_left = max(0, 30*60 - now()->diffInSeconds(Session::get('exam_start')));
        if ($time_left <= 0 || $nr+1 >= count($fragenIds)) return redirect()->route('exam.result');
        return redirect()->route('exam.show', ['nr' => $nr+1]);
    }

    public function result()
    {
        $answers = Session::get('exam_answers', []);
        $fragenIds = Session::get('exam_questions', []);
        $correct = collect($answers)->filter()->count();
        $total = count($fragenIds);
        $passed = $total > 0 && $correct / $total >= 0.8;
        $user = Auth::user();
        if ($passed) {
            $user->exam_passed_count = ($user->exam_passed_count ?? 0) + 1;
            $user->exam_failed_questions = [];
            $user->save();
        } else {
            // Speichere falsch beantwortete Fragen
            $failed = [];
            foreach ($answers as $nr => $isCorrect) {
                if (!$isCorrect && isset($fragenIds[$nr])) {
                    $failed[] = $fragenIds[$nr];
                }
            }
            $user->exam_failed_questions = $failed;
            $user->save();
        }
        $done = ($user->exam_passed_count ?? 0) >= 5;
        Session::forget(['exam_questions', 'exam_answers', 'exam_start']);
        return view('exam_result', compact('correct', 'total', 'passed', 'done'));
    }

    public function repeatFailed(Request $request, $nr = 0)
    {
        $user = Auth::user();
        $failed = $this->ensureArray($user->exam_failed_questions);
        if (!$failed || $nr >= count($failed)) {
            $user->exam_failed_questions = [];
            $user->save();
            return redirect()->route('exam.start');
        }
        $frage = Question::find($failed[$nr]);
        return view('exam_repeat', ['frage' => $frage, 'current' => $nr, 'total' => count($failed)]);
    }

    public function repeatAnswer(Request $request, $nr)
    {
        $user = Auth::user();
        $failed = $this->ensureArray($user->exam_failed_questions);
        $frage = Question::find($failed[$nr]);
        $userAnswer = collect($request->answer ?? []);
        $solution = collect(explode(',', $frage->loesung))->map(fn($s) => trim($s));
        $isCorrect = $userAnswer->sort()->values()->all() === $solution->sort()->values()->all();
        if ($isCorrect) {
            unset($failed[$nr]);
            $user->exam_failed_questions = array_values($failed);
            $user->save();
            return redirect()->route('exam.repeat', ['nr' => $nr]);
        }
        return view('exam_repeat', ['frage' => $frage, 'current' => $nr, 'total' => count($failed), 'isCorrect' => false, 'userAnswer' => $userAnswer]);
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
