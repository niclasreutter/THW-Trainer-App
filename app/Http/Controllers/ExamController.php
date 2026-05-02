<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Question;
use App\Models\QuestionStatistic;
use App\Models\UserQuestionProgress;
use App\Models\ExtraQuestion;
use App\Models\UserExtraQuestionProgress;
use App\Models\ExamStatistic;
use Illuminate\Support\Facades\Session;
use App\Services\GamificationService;

class ExamController extends Controller
{
    public function start()
    {
        $user = Auth::user();

        // Aktive Prüfung in Session? → Fortsetzen statt neu starten
        if (session()->has('exam_active')) {
            $examData = session('exam_active');
            $elapsed = time() - $examData['start_time'];
            $timeLeft = max(0, 30 * 60 - $elapsed);

            if ($timeLeft <= 0) {
                session()->forget('exam_active');
                return redirect()->route('dashboard')
                    ->with('error', 'Die Prüfungszeit ist abgelaufen. Bitte starte eine neue Prüfung.');
            }

            $questionIds = $examData['question_ids'];
            $fragen = Question::whereIn('id', $questionIds)->get()
                ->sortBy(fn($q) => array_search($q->id, $questionIds))
                ->values();

            return view('exam', [
                'fragen' => $fragen,
                'timeLeft' => $timeLeft,
                'examToken' => $examData['token'],
                'answerMappings' => $examData['answer_mappings'],
            ]);
        }

        // Lösche die Session für verarbeitete Prüfungen
        session()->forget('exam_processed_' . $user->id);

        // Prüfe ob noch Fehler zu wiederholen sind
        $failedQuestions = $this->ensureArray($user->exam_failed_questions);
        if (!empty($failedQuestions) && count($failedQuestions) > 0) {
            return redirect()->route('dashboard')->with('error', 'Du musst zuerst deine falschen Antworten wiederholen, bevor du eine neue Prüfung starten kannst.');
        }

        // Prüfe ob alle Fragen gemeistert sind (inkl. Zusatzfragen falls aktiviert)
        $totalQuestions = Question::count();
        $masteredCount = UserQuestionProgress::countMastered($user->id);

        if ($user->extras_enabled) {
            $totalQuestions += ExtraQuestion::count();
            $masteredCount += UserExtraQuestionProgress::countMastered($user->id);
        }

        if ($masteredCount < $totalQuestions) {
            $remaining = $totalQuestions - $masteredCount;
            return redirect()->route('dashboard')->with('error', "Du musst zuerst alle Fragen meistern, bevor du eine Prüfung starten kannst. Noch {$remaining} Fragen offen.");
        }

        // Strategie: Pro Lernabschnitt (1-10) mindestens 1 Frage
        $selectedIds = [];

        // Versuche Fragen zu vermeiden, die in den letzten 3 Prüfungen verwendet wurden
        $recentQuestions = session('recent_exam_questions', []);

        // 1. Wähle mindestens 1 Frage pro Lernabschnitt (1-10)
        for ($lernabschnitt = 1; $lernabschnitt <= 10; $lernabschnitt++) {
            $sectionQuestions = Question::where('lernabschnitt', $lernabschnitt)
                ->pluck('id')
                ->toArray();

            if (empty($sectionQuestions)) {
                continue;
            }

            $availableSectionQuestions = array_diff($sectionQuestions, $recentQuestions);

            if (empty($availableSectionQuestions)) {
                $availableSectionQuestions = $sectionQuestions;
            }

            $randomKey = array_rand($availableSectionQuestions);
            $selectedIds[] = $availableSectionQuestions[$randomKey];
        }

        // 2. Fülle die restlichen Plätze auf 40 Fragen mit zufälligen Fragen auf
        $remainingCount = 40 - count($selectedIds);

        if ($remainingCount > 0) {
            $allQuestionIds = Question::pluck('id')->toArray();
            $availableIds = array_diff($allQuestionIds, $selectedIds);
            $preferredIds = array_diff($availableIds, $recentQuestions);

            if (count($preferredIds) < $remainingCount) {
                $preferredIds = $availableIds;
            }

            if (count($preferredIds) >= $remainingCount) {
                $additionalIds = array_rand(array_flip($preferredIds), $remainingCount);

                if (!is_array($additionalIds)) {
                    $additionalIds = [$additionalIds];
                }

                $selectedIds = array_merge($selectedIds, $additionalIds);
            } else {
                $selectedIds = array_merge($selectedIds, $preferredIds);
            }
        }

        // Speichere diese Fragen als "kürzlich verwendet"
        $newRecentQuestions = array_merge($recentQuestions, $selectedIds);
        if (count($newRecentQuestions) > 120) {
            $newRecentQuestions = array_slice($newRecentQuestions, -120);
        }
        session(['recent_exam_questions' => $newRecentQuestions]);

        // Hole die Fragen und mische sie zufällig
        $fragen = Question::whereIn('id', $selectedIds)->get()->shuffle();

        // Antwort-Mappings serverseitig generieren (stabil bei Reload)
        $answerMappings = [];
        foreach ($fragen as $index => $frage) {
            $letters = ['A', 'B', 'C'];
            shuffle($letters);
            $mapping = [];
            foreach ($letters as $pos => $letter) {
                $mapping[$pos] = $letter;
            }
            $answerMappings[$index] = $mapping;
        }

        // Session-Token und Prüfungsdaten speichern
        $token = Str::uuid()->toString();
        session(['exam_active' => [
            'question_ids' => $fragen->pluck('id')->toArray(),
            'token' => $token,
            'start_time' => time(),
            'answer_mappings' => $answerMappings,
        ]]);

        return view('exam', [
            'fragen' => $fragen,
            'timeLeft' => 30 * 60,
            'examToken' => $token,
            'answerMappings' => $answerMappings,
        ]);
    }

    // Wertet die abgegebene Prüfung aus und zeigt das Ergebnis
    public function submit(Request $request)
    {
        // Aktive Prüfung in Session prüfen (verhindert Doppel-Submit)
        $examData = session('exam_active');
        if (!$examData) {
            $lastResultId = session('exam_last_result_id');
            if ($lastResultId) {
                return redirect()->route('exam.result', $lastResultId);
            }
            return redirect()->route('exam.index');
        }

        // Token validieren
        if ($request->input('exam_token') !== $examData['token']) {
            return redirect()->route('exam.index');
        }

        // Session sofort löschen (verhindert Doppel-Submit bei Race Condition)
        session()->forget('exam_active');

        // Validation
        $validated = $request->validate([
            'fragen_ids' => 'required|array|size:40',
            'fragen_ids.*' => 'required|integer|exists:questions,id',
            'answer' => 'nullable|array',
            'answer.*' => 'nullable|array',
            'answer.*.*' => 'string|in:0,1,2',
            'answer_mappings' => 'nullable|array',
            'answer_mappings.*' => 'nullable|string',
        ]);

        // Load questions securely (only validated IDs)
        $fragen = Question::whereIn('id', $validated['fragen_ids'])
            ->get()
            ->keyBy('id')
            ->sortBy(function ($question) use ($validated) {
                return array_search($question->id, $validated['fragen_ids']);
            })
            ->values();

        if ($fragen->count() !== 40) {
            return redirect()->route('exam.index')
                ->with('error', 'Ungültige Prüfung. Bitte starte eine neue Prüfung.');
        }

        $userAnswers = $validated['answer'] ?? [];
        $answerMappings = $validated['answer_mappings'] ?? [];
        $results = [];
        $correctCount = 0;
        $failed = [];
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
            } else {
                $failed[] = $frage->id;
            }
        }
        $total = count($fragen);
        $passed = $total > 0 && $correctCount / $total >= 0.8;
        $user = Auth::user();

        $examStatistic = ExamStatistic::create([
            'user_id' => $user->id,
            'is_passed' => $passed,
            'correct_answers' => $correctCount,
        ]);

        foreach ($results as $result) {
            QuestionStatistic::create([
                'question_id' => $result['frage']->id,
                'user_id' => $user->id,
                'is_correct' => $result['isCorrect'],
                'source' => 'exam',
                'exam_statistic_id' => $examStatistic->id,
            ]);

            $progress = UserQuestionProgress::getOrCreate($user->id, $result['frage']->id);
            $progress->updateProgress($result['isCorrect']);
        }

        $gamificationResult = null;

        if ($passed) {
            $user->exam_passed_count = ($user->exam_passed_count ?? 0) + 1;

            if ($user->exam_passed_count === 1) {
                session(['milestone_celebration' => [
                    'type' => 'first_exam_passed',
                ]]);
                session()->save();
            }

            if ($correctCount == $total) {
                $user->exam_failed_questions = [];
            } else {
                $existingFailed = $this->ensureArray($user->exam_failed_questions);
                $user->exam_failed_questions = array_unique(array_merge($existingFailed, $failed));
            }

            $gamificationService = new GamificationService();
            $gamificationResult = $gamificationService->awardExamPoints($user, $correctCount, $total);
        } else {
            $user->exam_passed_count = 0;
            $user->exam_failed_questions = $failed;

            // Auch bei nicht-bestandener Prüfung zählt die Prüfung für den Streak
            $gamificationService = new GamificationService();
            $gamificationService->recordExamActivity($user);
        }
        $user->save();

        // Ergebnis in Session speichern für Detail-Anzeige nach Redirect
        session(['exam_last_result_id' => $examStatistic->id]);
        session(['exam_last_results' => [
            'results' => $results,
            'correctCount' => $correctCount,
            'total' => $total,
            'passed' => $passed,
            'gamification_result' => $gamificationResult,
        ]]);

        // POST/Redirect/GET: Redirect verhindert Doppel-Submit bei Reload
        return redirect()->route('exam.result', $examStatistic->id);
    }

    /**
     * Ergebnis-Seite anzeigen (GET - sicher bei Reload)
     */
    public function showResult(int $id)
    {
        $user = Auth::user();
        $examStatistic = ExamStatistic::where('user_id', $user->id)->findOrFail($id);

        // Vollständige Ergebnis-Daten aus Session (direkt nach Submit)
        $sessionResults = session('exam_last_results');
        if ($sessionResults && session('exam_last_result_id') == $id) {
            session()->forget(['exam_last_results', 'exam_last_result_id']);

            return view('exam', [
                'fragen' => collect(array_map(fn($r) => $r['frage'], $sessionResults['results'])),
                'results' => $sessionResults['results'],
                'submitted' => true,
                'correctCount' => $sessionResults['correctCount'],
                'total' => $sessionResults['total'],
                'passed' => $sessionResults['passed'],
                'gamification_result' => $sessionResults['gamification_result'],
            ]);
        }

        // Rekonstruktion aus Datenbank (bei Reload)
        $questionStats = QuestionStatistic::where('exam_statistic_id', $examStatistic->id)
            ->with('question')
            ->get();

        $results = [];
        $fragen = collect();
        foreach ($questionStats as $index => $qs) {
            $frage = $qs->question;
            $fragen->push($frage);
            $solution = collect(explode(',', $frage->loesung))->map(fn($s) => trim($s));
            $results[$index] = [
                'frage' => $frage,
                'userAnswer' => $qs->is_correct ? $solution : collect(),
                'solution' => $solution,
                'isCorrect' => $qs->is_correct,
                'mapping' => null,
            ];
        }

        return view('exam', [
            'fragen' => $fragen,
            'results' => $results,
            'submitted' => true,
            'correctCount' => $examStatistic->correct_answers,
            'total' => $questionStats->count(),
            'passed' => $examStatistic->is_passed,
            'gamification_result' => null,
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
     * Prüfungshistorie anzeigen
     */
    public function history()
    {
        $user = Auth::user();

        $exams = ExamStatistic::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Durchschnitt aller Nutzer berechnen
        $globalAvg = ExamStatistic::selectRaw('AVG(correct_answers) as avg_correct')->value('avg_correct') ?? 0;
        $globalAvgPercent = round(($globalAvg / 40) * 100);

        // Trend: letzte 10 Prüfungen
        $trend = ExamStatistic::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->reverse()
            ->values();

        // Analyse pro Lernabschnitt (nur Prüfungsdaten)
        $sectionAnalysis = [];
        $linkedExamCount = QuestionStatistic::where('user_id', $user->id)
            ->whereNotNull('exam_statistic_id')
            ->distinct('exam_statistic_id')
            ->count('exam_statistic_id');
        $latestExam = $exams->first();
        if ($latestExam) {
            for ($section = 1; $section <= 10; $section++) {
                $sectionQuestionIds = Question::where('lernabschnitt', $section)->pluck('id')->toArray();
                $totalInSection = QuestionStatistic::where('user_id', $user->id)
                    ->whereIn('question_id', $sectionQuestionIds)
                    ->whereNotNull('exam_statistic_id')
                    ->count();

                $correctInSection = QuestionStatistic::where('user_id', $user->id)
                    ->whereIn('question_id', $sectionQuestionIds)
                    ->where('is_correct', true)
                    ->whereNotNull('exam_statistic_id')
                    ->count();

                $sectionAnalysis[$section] = [
                    'total' => $totalInSection,
                    'correct' => $correctInSection,
                    'percent' => $totalInSection > 0 ? round(($correctInSection / $totalInSection) * 100) : 0,
                ];
            }
        }

        // Schwächste Abschnitte identifizieren
        $weakSections = collect($sectionAnalysis)
            ->filter(fn($s) => $s['total'] >= 3)
            ->sortBy('percent')
            ->take(3)
            ->keys()
            ->toArray();

        return view('exam-history', compact('exams', 'globalAvgPercent', 'trend', 'sectionAnalysis', 'weakSections', 'linkedExamCount'));
    }

    /**
     * Detail-Ansicht einer Prüfung
     */
    public function historyDetail(int $id)
    {
        $user = Auth::user();
        $exam = ExamStatistic::where('user_id', $user->id)->findOrFail($id);

        return view('exam-history-detail', compact('exam'));
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
