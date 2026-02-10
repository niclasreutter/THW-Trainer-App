<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Question;
use App\Models\QuestionStatistic;
use App\Models\UserQuestionProgress;
use App\Services\GamificationService;
use App\Services\SpacedRepetitionService;

class PracticeController extends Controller
{
    /**
     * THW-Lernabschnittsnamen (offiziell 2022)
     */
    private const SECTION_NAMES = [
        1 => 'Das THW im Gefüge des Zivil- und Katastrophenschutzes',
        2 => 'Arbeitssicherheit und Gesundheitsschutz', 
        3 => 'Arbeiten mit Leinen, Drahtseilen, Ketten, Rund- und Bandschlingen',
        4 => 'Arbeiten mit Leitern',
        5 => 'Stromerzeugung und Beleuchtung',
        6 => 'Metall-, Holz- und Steinbearbeitung',
        7 => 'Bewegen von Lasten',
        8 => 'Arbeiten am und auf dem Wasser',
        9 => 'Einsatzgrundlagen',
        10 => 'Grundlagen der Rettung und Bergung'
    ];


    /**
     * Zeige das Practice-Menü
     */
    public function menu()
    {
        $user = Auth::user();
        // Defensive Programmierung für Legacy-Daten
        $solved = $this->ensureArray($user->solved_questions);
        $failed = $this->ensureArray($user->exam_failed_questions);

        // ⚡ PERFORMANCE-OPTIMIERUNG: Eine Query statt 20+
        // Lade ALLE Fragen einmal und gruppiere nach Lernabschnitt
        $questionsBySection = Question::select('id', 'lernabschnitt')
            ->get()
            ->groupBy('lernabschnitt');

        // Statistiken für jeden Lernabschnitt berechnen (ohne weitere Queries)
        $sectionStats = [];
        for ($i = 1; $i <= 10; $i++) {
            $sectionQuestions = $questionsBySection->get($i, collect());
            $sectionQuestionIds = $sectionQuestions->pluck('id')->toArray();
            $totalQuestions = count($sectionQuestionIds);
            $solvedInSection = count(array_intersect($solved, $sectionQuestionIds));

            $sectionStats[$i] = [
                'total' => $totalQuestions,
                'solved' => $solvedInSection
            ];
        }

        // Allgemeine Statistiken (aus bereits geladenen Daten)
        $totalQuestions = $questionsBySection->flatten()->count();
        $solvedCount = count($solved);
        $failedCount = count($failed);
        $unsolvedCount = $totalQuestions - $solvedCount;

        // Neue Fortschrittsbalken-Logik: Berücksichtigt auch 1x richtige Antworten
        $threshold = UserQuestionProgress::MASTERY_THRESHOLD;
        $progressData = UserQuestionProgress::where('user_id', $user->id)->get();
        $totalProgressPoints = 0;
        foreach ($progressData as $prog) {
            $totalProgressPoints += min($prog->consecutive_correct, $threshold);
        }
        $maxProgressPoints = $totalQuestions * $threshold;
        $progressPercentage = $maxProgressPoints > 0 ? round(($totalProgressPoints / $maxProgressPoints) * 100) : 0;

        $sectionNames = self::SECTION_NAMES;
        return view('practice-menu', compact('sectionStats', 'totalQuestions', 'solvedCount', 'failedCount', 'unsolvedCount', 'sectionNames', 'progressPercentage'));
    }

    /**
     * Alle Fragen üben (ungelöste bevorzugt)
     */
    public function all()
    {
        // Session zurücksetzen für neuen Modus
        session()->forget(['practice_mode', 'practice_parameter', 'practice_ids', 'practice_skipped']);
        
        // Debug: Prüfe ob Route erreicht wird
        $user = Auth::user();
        $solved = $this->ensureArray($user->solved_questions);
        
        $totalQuestions = Question::count();
        $unsolvedCount = Question::whereNotIn('id', $solved)->count();
        
        // Debug-Ausgabe
        \Log::info('Practice All Debug', [
            'total_questions' => $totalQuestions,
            'solved_count' => count($solved),
            'unsolved_count' => $unsolvedCount
        ]);
        
        // Entfernt: Kein Redirect mehr, auch wenn alle gelöst sind
        // Alle Fragen sollen trainiert werden können!
        
        return $this->practiceMode('all');
    }

    /**
     * Nur ungelöste Fragen üben
     */
    public function unsolved()
    {
        session()->forget(['practice_mode', 'practice_parameter', 'practice_ids', 'practice_skipped']);
        return $this->practiceMode('unsolved');
    }

    /**
     * Fehlgeschlagene Prüfungsfragen wiederholen
     */
    public function failed()
    {
        session()->forget(['practice_mode', 'practice_parameter', 'practice_ids', 'practice_skipped']);
        return $this->practiceMode('failed');
    }

    /**
     * Lernabschnitt üben
     */
    public function section($section)
    {
        session()->forget(['practice_mode', 'practice_parameter', 'practice_ids', 'practice_skipped']);
        return $this->practiceMode('section', $section);
    }

    /**
     * Spaced Repetition Modus - Fällige Wiederholungen
     */
    public function spacedRepetition()
    {
        session()->forget(['practice_mode', 'practice_parameter', 'practice_ids', 'practice_skipped']);

        $user = Auth::user();
        $srService = new SpacedRepetitionService();
        $dueIds = $srService->getDueQuestions($user->id);

        if (empty($dueIds)) {
            return redirect()->route('practice.menu')->with('success', 'Keine Wiederholungen fällig! Komm später wieder.');
        }

        return $this->practiceMode('spaced_repetition', null, $dueIds);
    }

    /**
     * Fragen suchen
     */
    public function search(Request $request)
    {
        $searchTerm = $request->input('search');
        if (empty($searchTerm)) {
            return redirect()->route('practice.menu')->with('error', 'Bitte gib einen Suchbegriff ein.');
        }
        
        session()->forget(['practice_mode', 'practice_parameter', 'practice_ids', 'practice_skipped']);
        return $this->practiceMode('search', $searchTerm);
    }

    /**
     * Zentrale Methode für verschiedene Practice-Modi
     */
    private function practiceMode($mode, $parameter = null, $preloadedIds = null)
    {
        $user = Auth::user();
        $solved = $this->ensureArray($user->solved_questions);
        $failed = $this->ensureArray($user->exam_failed_questions);
        $skipped = session('practice_skipped', []);
        
        // Basis-Query je nach Modus
        $query = Question::query();
        
        switch ($mode) {
            case 'all':
                // Intelligente Priorisierung für Practice All:
                // 1. Spaced Repetition Fragen (fällige Wiederholungen, höchste Priorität)
                // 2. Falsch beantwortete + ungelöste Fragen
                // 3. Restliche Fragen in zufälliger Reihenfolge

                $idsToShow = [];
                $alreadyQueued = [];

                // 1. Spaced Repetition: Fällige Wiederholungen zuerst
                $srService = new SpacedRepetitionService();
                $srDueIds = $srService->getDueQuestions($user->id);
                shuffle($srDueIds);
                $idsToShow = array_merge($idsToShow, $srDueIds);
                $alreadyQueued = array_merge($alreadyQueued, $srDueIds);

                // 2. Falsch beantwortete Fragen aus Prüfungen
                $failedIds = array_values($failed);
                $failedIds = array_diff($failedIds, $alreadyQueued);
                shuffle($failedIds);
                $idsToShow = array_merge($idsToShow, $failedIds);
                $alreadyQueued = array_merge($alreadyQueued, $failedIds);

                // 3. Nicht-gemeisterte + nie beantwortete Fragen (ungelöst)
                $unmasteredIds = UserQuestionProgress::getUnmasteredQuestions($user->id);
                $allQuestionIds = Question::pluck('id')->toArray();
                $answeredQuestionIds = UserQuestionProgress::where('user_id', $user->id)
                    ->pluck('question_id')
                    ->toArray();
                $neverAnsweredIds = array_diff($allQuestionIds, $answeredQuestionIds);

                $toLearnIds = array_unique(array_merge($unmasteredIds, $neverAnsweredIds));
                $toLearnIds = array_diff($toLearnIds, $alreadyQueued);

                // Nach Lernabschnitten sortiert, innerhalb zufällig
                $sortedToLearnIds = [];
                for ($section = 1; $section <= 10; $section++) {
                    $sectionIds = Question::where('lernabschnitt', $section)
                        ->whereIn('id', $toLearnIds)
                        ->pluck('id')->toArray();

                    shuffle($sectionIds);
                    $sortedToLearnIds = array_merge($sortedToLearnIds, $sectionIds);
                }
                $idsToShow = array_merge($idsToShow, $sortedToLearnIds);
                $alreadyQueued = array_merge($alreadyQueued, $sortedToLearnIds);

                // 4. Restliche Fragen zufällig (bereits gemeisterte, keine SR fällig)
                $remainingIds = array_diff($allQuestionIds, $alreadyQueued);
                $remainingIds = array_values($remainingIds);
                shuffle($remainingIds);
                $idsToShow = array_merge($idsToShow, $remainingIds);

                // Debug-Ausgabe
                \Log::info('Practice Mode All Debug', [
                    'user_id' => $user->id,
                    'sr_due_count' => count($srDueIds),
                    'failed_count' => count($failedIds),
                    'unmastered_count' => count($unmasteredIds),
                    'never_answered_count' => count($neverAnsweredIds),
                    'total_to_learn' => count($toLearnIds),
                    'remaining_random' => count($remainingIds),
                    'total_ids_to_show' => count($idsToShow),
                ]);
                break;
                
            case 'unsolved':
                // Nur ungelöste Fragen zufällig sortieren
                $unsolvedIds = Question::whereNotIn('id', $solved)->pluck('id')->toArray();
                
                // Zufällige Sortierung der ungelösten Fragen
                shuffle($unsolvedIds);
                
                $idsToShow = $unsolvedIds;
                break;
                
            case 'failed':
                // Nur fehlgeschlagene Prüfungsfragen (aus exam_failed_questions)
                $failedIds = array_values($failed);
                
                if (empty($failedIds)) {
                    return redirect()->route('practice.menu')->with('info', 'Keine falschen Fragen zum Wiederholen! 🎉');
                }
                
                // Zufällige Sortierung der fehlgeschlagenen Fragen
                shuffle($failedIds);
                
                $idsToShow = $failedIds;
                break;
                
            case 'section':
                // Fragen eines Lernabschnitts zufällig sortieren
                $allSectionIds = Question::where('lernabschnitt', $parameter)->pluck('id')->toArray();
                
                // Zufällige Sortierung der Fragen innerhalb des Lernabschnitts
                shuffle($allSectionIds);
                
                $idsToShow = $allSectionIds;
                break;
                
            case 'search':
                // Fragen mit Suchbegriff zufällig sortieren
                $searchIds = Question::where(function($q) use ($parameter) {
                    $q->where('frage', 'LIKE', '%' . $parameter . '%')
                      ->orWhere('antwort_a', 'LIKE', '%' . $parameter . '%')
                      ->orWhere('antwort_b', 'LIKE', '%' . $parameter . '%')
                      ->orWhere('antwort_c', 'LIKE', '%' . $parameter . '%');
                })->pluck('id')->toArray();
                
                // Zufällige Sortierung der Suchergebnisse
                shuffle($searchIds);
                
                $idsToShow = $searchIds;
                break;
                
            case 'spaced_repetition':
                // Fällige Wiederholungen (vorgeladen)
                $idsToShow = $preloadedIds ?? [];
                break;

            case 'bookmarked':
                // Gespeicherte Fragen (bereits in richtiger Reihenfolge)
                $idsToShow = $user->bookmarked_questions ?? [];
                break;
                
            default:
                $idsToShow = [];
        }
        
        // Geskippte Fragen temporär entfernen
        $idsToShow = array_diff($idsToShow, $skipped);
        
        if (empty($idsToShow)) {
            $message = $mode === 'unsolved' 
                ? 'Alle Fragen in diesem Bereich wurden bereits gelöst! 🎉'
                : 'Keine Fragen gefunden.';
            
            \Log::info('No questions found after skipped removal', [
                'mode' => $mode,
                'parameter' => $parameter,
                'skipped_count' => count($skipped)
            ]);
                
            return redirect()->route('practice.menu')->with('success', $message);
        }
        
        // Zusätzlicher Sicherheitscheck
        if (!isset($idsToShow[0])) {
            \Log::error('Practice IDs array issue after skipped', [
                'ids_to_show' => $idsToShow,
                'mode' => $mode
            ]);
            return redirect()->route('practice.menu')->with('error', 'Fehler beim Laden der Fragen.');
        }
        
        $question = Question::find($idsToShow[0]);
        
        // Nochmals prüfen ob Frage existiert
        if (!$question) {
            \Log::error('Question not found', [
                'question_id' => $idsToShow[0],
                'mode' => $mode
            ]);
            return redirect()->route('practice.menu')->with('error', 'Die angeforderte Frage konnte nicht gefunden werden.');
        }
        
        \Log::info('Practice session starting', [
            'mode' => $mode,
            'question_id' => $question->id,
            'total_ids' => count($idsToShow)
        ]);
        
        // Fortschritt sollte immer die tatsächlich gelösten Fragen vs Gesamtfragen zeigen
        $totalQuestions = Question::count();
        $solvedCount = count($solved);
        $total = $totalQuestions;
        $progress = $solvedCount;
        
        // Neue Fortschrittsbalken-Logik: Berücksichtigt auch 1x richtige Antworten
        $threshold = UserQuestionProgress::MASTERY_THRESHOLD;
        $progressData = UserQuestionProgress::where('user_id', $user->id)->get();
        $totalProgressPoints = 0;
        foreach ($progressData as $prog) {
            $totalProgressPoints += min($prog->consecutive_correct, $threshold);
        }
        $maxProgressPoints = $totalQuestions * $threshold;
        $progressPercent = $maxProgressPoints > 0 ? round(($totalProgressPoints / $maxProgressPoints) * 100) : 0;

        // Session für aktuellen Modus speichern
        $totalInMode = count($idsToShow);
        session([
            'practice_mode' => $mode,
            'practice_parameter' => $parameter,
            'practice_ids' => $idsToShow,
            'practice_total_in_mode' => $totalInMode,
        ]);

        // Session-Statistiken initialisieren
        session([
            'practice_session_stats' => [
                'correct' => 0,
                'incorrect' => 0,
                'points' => 0,
                'mastered' => 0,
                'started_at' => now()->timestamp,
            ],
        ]);

        $currentInMode = 1;

        // Schwierigkeitsindikator für aktuelle Frage
        $difficultyInfo = $this->getQuestionDifficulty($question->id);

        // Spaced Repetition: Prüfe ob diese Frage fällig ist
        $srService = new SpacedRepetitionService();
        $srDueIds = $srService->getDueQuestions($user->id);
        $isSpacedRepetition = in_array($question->id, $srDueIds);

        return view('practice', compact('question', 'progress', 'total', 'mode', 'progressPercent', 'totalInMode', 'currentInMode', 'difficultyInfo', 'isSpacedRepetition'));
    }

    public function show(Request $request)
    {
        $user = Auth::user();
        $solved = $this->ensureArray($user->solved_questions);
        $skipped = session('practice_skipped', []);
        
        // Check if we're in a specific practice mode
        $practiceIds = session('practice_ids', []);
        $mode = session('practice_mode', 'all');
        
        // WICHTIG: Wenn eine Frage gerade beantwortet wurde (answer_result in Session),
        // zeige diese Frage nochmal (damit die Antwort angezeigt werden kann)
        $answerResult = session('answer_result');
        $showAnsweredQuestion = $answerResult && isset($answerResult['question_id']);
        
        if (!empty($practiceIds)) {
            // Continue with current practice session
            $idsToShow = $practiceIds; // Alle IDs aus der Session
            
            if ($showAnsweredQuestion) {
                // Zeige die gerade beantwortete Frage nochmal
                $questionId = $answerResult['question_id'];
            } elseif ($request->has('skip_id')) {
                $skipId = $request->input('skip_id');
                // Entferne die geskippte Frage nur temporär von der Anzeige
                $idsToShow = array_diff($idsToShow, [$skipId]);
                
                // Füge zur geskippten Liste für diese Runde hinzu
                $skipped = array_merge($skipped, [$skipId]);
                session(['practice_skipped' => array_unique($skipped)]);
                
                if (empty($idsToShow)) {
                    return redirect()->route('practice.summary');
                }

                $questionId = reset($idsToShow);
            } else {
                // Normale Anzeige: entferne nur bereits verarbeitete Fragen
                $idsToShow = array_diff($idsToShow, $skipped);

                if (empty($idsToShow)) {
                    return redirect()->route('practice.summary');
                }
                
                $questionId = reset($idsToShow);
            }
            
            $question = Question::find($questionId);
            
            // Prüfe ob Frage existiert
            if (!$question) {
                session()->forget(['practice_mode', 'practice_parameter', 'practice_ids', 'practice_skipped']);
                return redirect()->route('practice.menu')->with('error', 'Die angeforderte Frage konnte nicht gefunden werden.');
            }
            
            // Fortschritt sollte immer die tatsächlich gelösten Fragen vs Gesamtfragen zeigen
            $total = Question::count();
            $progress = count($solved);
            
            // Neue Fortschrittsbalken-Logik: Berücksichtigt auch 1x richtige Antworten
            $threshold = UserQuestionProgress::MASTERY_THRESHOLD;
            $progressData = UserQuestionProgress::where('user_id', $user->id)->get();
            $totalProgressPoints = 0;
            foreach ($progressData as $prog) {
                $totalProgressPoints += min($prog->consecutive_correct, $threshold);
            }
            $maxProgressPoints = $total * $threshold;
            $progressPercent = $maxProgressPoints > 0 ? round(($totalProgressPoints / $maxProgressPoints) * 100) : 0;

        } else {
            // Legacy mode - redirect to menu
            return redirect()->route('practice.menu');
        }
        
        $totalInMode = session('practice_total_in_mode', count($practiceIds));
        $answered = $totalInMode - count(array_diff($practiceIds, $skipped));
        $currentInMode = max(1, $answered + 1);

        $difficultyInfo = $this->getQuestionDifficulty($question->id);

        // Spaced Repetition: Prüfe ob diese Frage fällig ist
        $srService = new SpacedRepetitionService();
        $srDueIds = $srService->getDueQuestions($user->id);
        $isSpacedRepetition = in_array($question->id, $srDueIds);

        return view('practice', compact('question', 'progress', 'total', 'mode', 'progressPercent', 'totalInMode', 'currentInMode', 'difficultyInfo', 'isSpacedRepetition'));
    }

    /**
     * Schwierigkeitsindikator basierend auf Fehlerquote aller Nutzer
     */
    private function getQuestionDifficulty(int $questionId): array
    {
        $total = \App\Models\QuestionStatistic::where('question_id', $questionId)->count();

        if ($total < 5) {
            return ['level' => 'unknown', 'label' => 'Neu', 'color' => 'text-dark-muted', 'percent' => null];
        }

        $correct = \App\Models\QuestionStatistic::where('question_id', $questionId)->where('is_correct', true)->count();
        $errorRate = $total > 0 ? (($total - $correct) / $total) * 100 : 0;

        if ($errorRate >= 60) {
            return ['level' => 'hard', 'label' => 'Schwer', 'color' => 'text-error', 'percent' => round($errorRate)];
        } elseif ($errorRate >= 30) {
            return ['level' => 'medium', 'label' => 'Mittel', 'color' => 'text-warning', 'percent' => round($errorRate)];
        } else {
            return ['level' => 'easy', 'label' => 'Leicht', 'color' => 'text-success', 'percent' => round($errorRate)];
        }
    }

    public function submit(Request $request)
    {
        $question = Question::findOrFail($request->question_id);
        
        // Hole das Mapping aus dem Hidden Field
        $mappingJson = $request->input('answer_mapping');
        $mapping = json_decode($mappingJson, true);
        
        // User-Antworten (Positionen 0, 1, 2)
        $userAnswerPositions = $request->answer ?? [];
        
        // Mappe Positionen zurück auf Original-Buchstaben
        $userAnswer = collect($userAnswerPositions)->map(function($position) use ($mapping) {
            return $mapping[$position] ?? null;
        })->filter()->sort()->values();
        
        $solution = collect(explode(',', $question->loesung))->map(fn($s) => trim($s))->sort()->values();
        $isCorrect = $userAnswer->all() === $solution->all();

        $user = Auth::user();

        // Statistik erfassen (mit User ID)
        QuestionStatistic::create([
            'question_id' => $question->id,
            'user_id' => $user->id,
            'is_correct' => $isCorrect,
            'source' => 'practice',
        ]);
        
        // NEU: Fortschritt in user_question_progress tracken
        $progress = UserQuestionProgress::getOrCreate($user->id, $question->id);
        $progress->updateProgress($isCorrect);

        // Spaced Repetition: Nächste Wiederholung berechnen
        $srService = new SpacedRepetitionService();
        $srService->processAnswer($progress, $isCorrect);
        
        $solved = $this->ensureArray($user->solved_questions);
        $skipped = session('practice_skipped', []);
        
        $gamificationResult = null;
        
        // Nur wenn Frage gemeistert (2x richtig in Folge)
        if ($progress->isMastered()) {
            // Zu solved_questions hinzufügen (falls noch nicht drin)
            if (!in_array($question->id, $solved)) {
                $solved[] = $question->id;
                $user->solved_questions = array_unique($solved);
                $user->save();
            }
            
            // Entferne Frage aus exam_failed_questions falls dort vorhanden
            $failed = $this->ensureArray($user->exam_failed_questions);
            if (in_array($question->id, $failed)) {
                $failed = array_diff($failed, [$question->id]);
                $user->exam_failed_questions = array_values($failed);
                $user->save();
            }
            
            // Gamification: Punkte nur wenn gemeistert
            $gamificationService = new GamificationService();
            $gamificationResult = $gamificationService->awardQuestionPoints($user, true, $question->id);
            
            // Entferne Frage aus geskippten Liste falls dort
            $skipped = array_diff($skipped, [$question->id]);
            session(['practice_skipped' => $skipped]);
            
            // WICHTIG: Entferne gemeisterte Frage auch aus der aktuellen Practice Session
            $practiceIds = session('practice_ids', []);
            if (!empty($practiceIds)) {
                $practiceIds = array_diff($practiceIds, [$question->id]);
                session(['practice_ids' => array_values($practiceIds)]);
            }
        } else {
            // Frage noch nicht gemeistert (0 oder 1x richtig)
            // KEINE Änderung an exam_failed_questions - das ist nur für Prüfungen!
            // solved_questions NICHT entfernen - einmal gelöst bleibt gelöst

            // Gamification: Auch beim ersten richtigen Beantworten Punkte vergeben
            $gamificationService = new GamificationService();
            $gamificationResult = $gamificationService->awardQuestionPoints($user, $isCorrect, $question->id);

            // Frage aus der aktuellen Session entfernen - Spaced Repetition plant die Wiederholung
            // WICHTIG: Frage NICHT ans Ende re-queuen, da sonst dieselbe Frage mehrfach in einer
            // Session beantwortet wird und der SM-2 Algorithmus falsche Intervalle berechnet
            $practiceIds = session('practice_ids', []);
            if (!empty($practiceIds)) {
                $practiceIds = array_diff($practiceIds, [$question->id]);
                session(['practice_ids' => array_values($practiceIds)]);
            }
        }
        
        // Immer Gamification Result in Session speichern
        if ($gamificationResult) {
            session(['gamification_result' => $gamificationResult]);
        }

        // Session-Statistiken aktualisieren
        $sessionStats = session('practice_session_stats', [
            'correct' => 0, 'incorrect' => 0, 'points' => 0, 'mastered' => 0, 'started_at' => now()->timestamp,
        ]);
        if ($isCorrect) {
            $sessionStats['correct']++;
        } else {
            $sessionStats['incorrect']++;
        }
        if ($gamificationResult && isset($gamificationResult['points_awarded'])) {
            $sessionStats['points'] += $gamificationResult['points_awarded'];
        }
        if ($progress->isMastered() && !in_array($question->id, $solved)) {
            $sessionStats['mastered']++;
        }
        session(['practice_session_stats' => $sessionStats]);

        // WICHTIG: Immer answer_result in Session speichern für Feedback-Anzeige
        session([
            'answer_result' => [
                'question_id' => $question->id,
                'is_correct' => $isCorrect,
                'user_answer' => $userAnswer->toArray(),
                'question_progress' => $progress->consecutive_correct,
                'answer_mapping' => $mapping // Mapping auch speichern für die Anzeige
            ]
        ]);

        // Debug: Prüfe Session vor Redirect
        \Log::info('🔄 Before redirect - Session state', [
            'user_id' => $user->id,
            'question_id' => $question->id,
            'has_gamification_notifications' => session()->has('gamification_notifications'),
            'gamification_notifications' => session('gamification_notifications', []),
            'session_id' => session()->getId()
        ]);

        // WICHTIG: Immer redirect machen (Post/Redirect/Get Pattern)
        // um zu verhindern, dass bei F5 die Frage doppelt gezählt wird
        return redirect()->route('practice.index');
    }

    /**
     * Session-Zusammenfassung anzeigen
     */
    public function summary()
    {
        $stats = session('practice_session_stats');
        $mode = session('practice_mode', 'all');
        $parameter = session('practice_parameter');

        // Fallback falls keine Stats vorhanden
        if (!$stats) {
            return redirect()->route('practice.menu');
        }

        $totalAnswered = $stats['correct'] + $stats['incorrect'];
        $accuracy = $totalAnswered > 0 ? round(($stats['correct'] / $totalAnswered) * 100) : 0;
        $duration = now()->timestamp - ($stats['started_at'] ?? now()->timestamp);
        $durationMinutes = max(1, round($duration / 60));

        $modeName = match ($mode) {
            'all' => 'Alle Fragen',
            'unsolved' => 'Ungelöste Fragen',
            'failed' => 'Falsche Prüfungsfragen',
            'section' => 'Lernabschnitt ' . $parameter,
            'search' => 'Suche: ' . $parameter,
            'spaced_repetition' => 'Spaced Repetition',
            'bookmarked' => 'Lesezeichen',
            default => 'Übung',
        };

        // Session aufräumen
        session()->forget(['practice_mode', 'practice_parameter', 'practice_ids', 'practice_skipped', 'practice_total_in_mode', 'practice_session_stats']);

        return view('practice-summary', compact('stats', 'totalAnswered', 'accuracy', 'durationMinutes', 'modeName'));
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
