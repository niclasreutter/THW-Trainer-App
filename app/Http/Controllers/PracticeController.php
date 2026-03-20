<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Question;
use App\Models\QuestionIssue;
use App\Models\QuestionIssueReport;
use App\Models\QuestionStatistic;
use App\Models\UserQuestionProgress;
use App\Services\GamificationService;
use App\Services\PracticeSessionService;
use App\Services\ProgressResolvers\GlobalProgressResolver;
use App\Services\SpacedRepetitionService;

class PracticeController extends Controller
{
    private PracticeSessionService $practiceService;

    public function __construct()
    {
        $this->practiceService = new PracticeSessionService(
            new GlobalProgressResolver(),
            new GamificationService()
        );
    }

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
        $failed = $this->ensureArray($user->exam_failed_questions);

        // ⚡ PERFORMANCE-OPTIMIERUNG: Eine Query statt 20+
        // Lade ALLE Fragen einmal und gruppiere nach Lernabschnitt
        $questionsBySection = Question::select('id', 'lernabschnitt')
            ->get()
            ->groupBy('lernabschnitt');

        $totalQuestions = $questionsBySection->flatten()->count();

        // Fortschritt basierend auf tatsächlichem Mastery-Status (consecutive_correct)
        $threshold = UserQuestionProgress::MASTERY_THRESHOLD;
        $progressData = UserQuestionProgress::where('user_id', $user->id)->get();
        $totalProgressPoints = 0;
        foreach ($progressData as $prog) {
            $totalProgressPoints += min($prog->consecutive_correct, $threshold);
        }
        $maxProgressPoints = $totalQuestions * $threshold;
        $progressPercentage = $maxProgressPoints > 0 ? round(($totalProgressPoints / $maxProgressPoints) * 100) : 0;

        // Gemeisterte Fragen basierend auf consecutive_correct >= MASTERY_THRESHOLD
        $masteredQuestionIds = $progressData->filter(fn($p) => $p->isMastered())->pluck('question_id')->toArray();
        $solvedCount = count($masteredQuestionIds);
        $failedCount = count($failed);
        $unsolvedCount = $totalQuestions - $solvedCount;

        // Sync: solved_questions mit tatsächlichem Mastery-Status abgleichen
        $currentSolved = $this->ensureArray($user->solved_questions);
        sort($currentSolved);
        $sortedMastered = $masteredQuestionIds;
        sort($sortedMastered);
        if ($currentSolved !== $sortedMastered) {
            $user->solved_questions = array_values($masteredQuestionIds);
            $user->save();
        }

        // Statistiken für jeden Lernabschnitt berechnen
        $sectionStats = [];
        for ($i = 1; $i <= 10; $i++) {
            $sectionQuestions = $questionsBySection->get($i, collect());
            $sectionQuestionIds = $sectionQuestions->pluck('id')->toArray();
            $totalInSection = count($sectionQuestionIds);
            $solvedInSection = count(array_intersect($masteredQuestionIds, $sectionQuestionIds));

            $sectionStats[$i] = [
                'total' => $totalInSection,
                'solved' => $solvedInSection
            ];
        }

        $sectionNames = self::SECTION_NAMES;

        // Smart Action — kontextabhängige Empfehlung
        $smartAction = match(true) {
            $failedCount > 0 => [
                'label' => 'Empfohlen',
                'title' => "$failedCount Fehler wiederholen",
                'desc'  => 'Priorisiere fehlgeschlagene Fragen zuerst',
                'route' => route('failed.index'),
            ],
            $unsolvedCount > 0 => [
                'label' => 'Weiterlernen',
                'title' => "$unsolvedCount ungelöste Fragen",
                'desc'  => 'Lerne neue Fragen und erweitere dein Wissen',
                'route' => route('practice.unsolved'),
            ],
            default => [
                'label' => 'Wiederholen',
                'title' => 'Alle Fragen wiederholen',
                'desc'  => 'Festige dein Wissen durch Wiederholung',
                'route' => route('practice.all'),
            ],
        };

        // Spaced Repetition — fällige Reviews
        $spacedRepetitionDue = app(SpacedRepetitionService::class)
            ->getDueCount($user->id);

        return view('practice-menu', compact('sectionStats', 'totalQuestions', 'solvedCount', 'failedCount', 'unsolvedCount', 'sectionNames', 'progressPercentage', 'smartAction', 'spacedRepetitionDue'));
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
                // Nur tatsächlich gemeisterte Fragen (consecutive_correct >= MASTERY_THRESHOLD) ausschließen
                $currentlyMasteredIds = UserQuestionProgress::getMasteredQuestions($user->id);
                $toLearnIds = array_diff($toLearnIds, $currentlyMasteredIds);

                // Fragen ausschließen, die SR bereits für die Zukunft geplant hat.
                // Wenn next_review_at > now() ist die Frage bereits beantwortet worden und
                // wird vom SR-Algorithmus erst später wieder eingeblendet – nicht heute nochmal zeigen.
                $futureSrIds = UserQuestionProgress::where('user_id', $user->id)
                    ->whereNotNull('next_review_at')
                    ->where('next_review_at', '>', now())
                    ->pluck('question_id')
                    ->toArray();
                $toLearnIds = array_diff($toLearnIds, $futureSrIds);

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
                $remainingIds = array_diff($remainingIds, $futureSrIds);
                $remainingIds = array_values($remainingIds);
                shuffle($remainingIds);
                $idsToShow = array_merge($idsToShow, $remainingIds);
                $idsToShow = array_values(array_unique($idsToShow));

                // Debug-Ausgabe
                \Log::info('Practice Mode All Debug', [
                    'user_id' => $user->id,
                    'sr_due_count' => count($srDueIds),
                    'failed_count' => count($failedIds),
                    'unmastered_count' => count($unmasteredIds),
                    'never_answered_count' => count($neverAnsweredIds),
                    'future_sr_excluded' => count($futureSrIds),
                    'total_to_learn' => count($toLearnIds),
                    'remaining_random' => count($remainingIds),
                    'total_ids_to_show' => count($idsToShow),
                ]);
                break;
                
            case 'unsolved':
                // Nur ungelöste Fragen (nicht gemeistert) zufällig sortieren
                $masteredIds = UserQuestionProgress::getMasteredQuestions($user->id);
                $unsolvedIds = Question::whereNotIn('id', $masteredIds)->pluck('id')->toArray();

                // Fragen ausschließen, die SR bereits für die Zukunft geplant hat
                $futureSrIds = UserQuestionProgress::where('user_id', $user->id)
                    ->whereNotNull('next_review_at')
                    ->where('next_review_at', '>', now())
                    ->pluck('question_id')
                    ->toArray();
                $unsolvedIds = array_values(array_diff($unsolvedIds, $futureSrIds));

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

                // Fragen ausschließen, die SR bereits für die Zukunft geplant hat
                $futureSrIds = UserQuestionProgress::where('user_id', $user->id)
                    ->whereNotNull('next_review_at')
                    ->where('next_review_at', '>', now())
                    ->pluck('question_id')
                    ->toArray();
                $failedIds = array_values(array_diff($failedIds, $futureSrIds));

                // Zufällige Sortierung der fehlgeschlagenen Fragen
                shuffle($failedIds);

                $idsToShow = $failedIds;
                break;
                
            case 'section':
                // Fragen eines Lernabschnitts zufällig sortieren
                $allSectionIds = Question::where('lernabschnitt', $parameter)->pluck('id')->toArray();

                // Fragen ausschließen, die SR bereits für die Zukunft geplant hat
                $futureSrIds = UserQuestionProgress::where('user_id', $user->id)
                    ->whereNotNull('next_review_at')
                    ->where('next_review_at', '>', now())
                    ->pluck('question_id')
                    ->toArray();
                $allSectionIds = array_values(array_diff($allSectionIds, $futureSrIds));

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

                // Fragen ausschließen, die SR bereits für die Zukunft geplant hat
                $futureSrIds = UserQuestionProgress::where('user_id', $user->id)
                    ->whereNotNull('next_review_at')
                    ->where('next_review_at', '>', now())
                    ->pluck('question_id')
                    ->toArray();
                $searchIds = array_values(array_diff($searchIds, $futureSrIds));

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
        
        $totalQuestions = Question::count();
        $total = $totalQuestions;
        $progress = UserQuestionProgress::countMastered($user->id);
        
        // Neue Fortschrittsbalken-Logik: Berücksichtigt auch 1x richtige Antworten
        $threshold = UserQuestionProgress::MASTERY_THRESHOLD;
        $progressData = UserQuestionProgress::where('user_id', $user->id)->get();
        $totalProgressPoints = 0;
        foreach ($progressData as $prog) {
            $totalProgressPoints += min($prog->consecutive_correct, $threshold);
        }
        $maxProgressPoints = $totalQuestions * $threshold;
        $progressPercent = $maxProgressPoints > 0 ? round(($totalProgressPoints / $maxProgressPoints) * 100) : 0;

        // Session für aktuellen Modus speichern (via Service)
        $totalInMode = count($idsToShow);
        $this->practiceService->startSession('global', null, $idsToShow, $mode, 'remove');

        // Controller-managed session keys (not part of the service)
        session([
            'practice_parameter' => $parameter,
            'practice_total_in_mode' => $totalInMode,
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
        
        // Get answer/gamification results from service (peek, don't clear yet for showAnsweredQuestion check)
        $answerResult = $this->practiceService->getAndClearAnswerResult('global', null);
        $gamificationResult = $this->practiceService->getAndClearGamificationResult('global', null);

        // Check if we're in a specific practice mode
        $practiceIds = session('practice_ids', []);
        $mode = session('practice_mode', 'all');

        // WICHTIG: Wenn eine Frage gerade beantwortet wurde (answer_result),
        // zeige diese Frage nochmal (damit die Antwort angezeigt werden kann)
        $showAnsweredQuestion = $answerResult && isset($answerResult['question_id']);

        if (!empty($practiceIds)) {
            // Continue with current practice session
            $idsToShow = $practiceIds; // Alle IDs aus der Session

            // SR-Filter: Fragen ausschließen, die noch nicht fällig sind (außer bei Antwort-Anzeige)
            if (!$showAnsweredQuestion && $mode !== 'spaced_repetition') {
                $futureSrIds = UserQuestionProgress::where('user_id', $user->id)
                    ->whereNotNull('next_review_at')
                    ->where('next_review_at', '>', now())
                    ->pluck('question_id')
                    ->toArray();
                $idsToShow = array_values(array_diff($idsToShow, $futureSrIds));

                // Session-Queue aktualisieren damit gefilterte Fragen nicht wieder auftauchen
                if (!empty($futureSrIds)) {
                    session(['practice_ids' => $idsToShow]);
                }
            }

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
                $this->practiceService->cleanSession('global', null);
                session()->forget(['practice_parameter', 'practice_skipped', 'practice_total_in_mode']);
                return redirect()->route('practice.menu')->with('error', 'Die angeforderte Frage konnte nicht gefunden werden.');
            }

            $total = Question::count();
            $progress = UserQuestionProgress::countMastered($user->id);

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

        // Service-based progress
        $serviceProgress = $this->practiceService->getProgress('global', null);

        return view('practice', compact(
            'question', 'progress', 'total', 'mode', 'progressPercent',
            'totalInMode', 'currentInMode', 'difficultyInfo', 'isSpacedRepetition',
            'answerResult', 'gamificationResult'
        ) + [
            'context' => 'global',
            'submitUrl' => route('practice.submit'),
            'showUrl' => route('practice.index'),
            'summaryUrl' => route('practice.summary'),
            'menuUrl' => route('practice.menu'),
        ]);
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
        })->filter()->sort()->values()->toArray();

        $user = Auth::user();

        // Check mastery state BEFORE service call (for exam_failed_questions logic)
        $progressObj = UserQuestionProgress::getOrCreate($user->id, $question->id);
        $wasPreviouslyMastered = $progressObj->isMastered();

        // Delegate progress update, statistic creation, gamification, session stats + queue management to service
        $result = $this->practiceService->submitAnswer('global', null, $question->id, $userAnswer, $mapping);
        $isCorrect = $result['is_correct'];

        // Reload progress after service updated it
        $progressObj->refresh();

        // Global-only: Prüfungs-fehlgeschlagene Fragen: 1x richtig reicht für Mastery
        $failed = $this->ensureArray($user->exam_failed_questions);
        if ($isCorrect && in_array($question->id, $failed)) {
            $progressObj->consecutive_correct = UserQuestionProgress::MASTERY_THRESHOLD;
            $progressObj->save();
        }

        // Bereits gemeisterte Fragen bei falscher Antwort wie Prüfungs-Fehler behandeln
        if (!$isCorrect && $wasPreviouslyMastered && !in_array($question->id, $failed)) {
            $failed[] = $question->id;
            $user->exam_failed_questions = array_values(array_unique($failed));
            $user->save();
        }

        // Spaced Repetition: Nächste Wiederholung berechnen
        (new SpacedRepetitionService())->processAnswer($progressObj, $isCorrect);

        $solved = $this->ensureArray($user->solved_questions);
        $skipped = session('practice_skipped', []);

        // Global-only: solved_questions + exam_failed_questions management
        if ($progressObj->isMastered()) {
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

            // Entferne Frage aus geskippten Liste falls dort
            $skipped = array_diff($skipped, [$question->id]);
            session(['practice_skipped' => $skipped]);
        } else {
            // Wenn die Frage vorher als gelöst markiert war, aber nicht mehr gemeistert ist,
            // muss sie aus solved_questions entfernt werden
            if (in_array($question->id, $solved)) {
                $solved = array_diff($solved, [$question->id]);
                $user->solved_questions = array_values($solved);
                $user->save();
            }
        }

        // Lernsession-Tracking: Antwort in aktive Session aufzeichnen
        // Gamification result is stored in session by the service (practice_gamification_result)
        $gamificationResult = session('practice_gamification_result');
        $lernsessionService = app(\App\Services\LernsessionService::class);
        $sessionParticipant = $lernsessionService->isUserInActiveSession($user);
        if ($sessionParticipant) {
            $xpAwarded = ($gamificationResult && isset($gamificationResult['points_awarded']))
                ? $gamificationResult['points_awarded'] : 0;
            $answerTimeMs = (int) $request->input('answer_time_ms', 0);
            $lernsessionService->recordAnswer($sessionParticipant, $isCorrect, $answerTimeMs, $xpAwarded);
        }

        // Debug: Prüfe Session vor Redirect
        \Log::info('Before redirect - Session state', [
            'user_id' => $user->id,
            'question_id' => $question->id,
            'has_gamification_notifications' => session()->has('gamification_notifications'),
            'gamification_notifications' => session('gamification_notifications', []),
            'session_id' => session()->getId()
        ]);

        // WICHTIG: Immer redirect machen (Post/Redirect/Get Pattern)
        return redirect()->route('practice.index');
    }

    /**
     * Session-Zusammenfassung anzeigen
     */
    public function summary()
    {
        $parameter = session('practice_parameter');
        $mode = session('practice_mode', 'all');

        // Use service to end session and get summary data (cleans service session keys)
        $summary = $this->practiceService->endSession('global', null);

        // Fallback falls keine Stats vorhanden
        if ($summary['totalAnswered'] === 0 && $summary['correct'] === 0) {
            // Check if there was actually a session
            $stats = null;
        } else {
            $stats = [
                'correct' => $summary['correct'],
                'incorrect' => $summary['incorrect'],
                'points' => $summary['points'],
                'mastered' => $summary['mastered'],
            ];
        }

        if (!$stats) {
            return redirect()->route('practice.menu');
        }

        $totalAnswered = $summary['totalAnswered'];
        $accuracy = $summary['accuracy'];
        $durationMinutes = $summary['durationMinutes'];

        // Enhance mode name with parameter for section/search
        $modeName = $summary['modeName'];
        if ($parameter) {
            $modeName = match ($mode) {
                'section' => 'Lernabschnitt ' . $parameter,
                'search' => 'Suche: ' . $parameter,
                'failed' => 'Falsche Prüfungsfragen',
                default => $modeName,
            };
        }

        $streak = auth()->user()->streak_days ?? 0;

        // Clean up controller-managed session keys (service already cleaned its own)
        session()->forget(['practice_parameter', 'practice_skipped', 'practice_total_in_mode']);

        return view('practice-summary', compact('stats', 'totalAnswered', 'accuracy', 'durationMinutes', 'modeName', 'streak') + [
            'context' => 'global',
            'backUrl' => route('practice.menu'),
            'completed' => false,
        ]);
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

    /**
     * Melde einen Fehler in einer Frage
     */
    public function reportIssue(Request $request, $questionId)
    {
        if (!$request->expectsJson()) {
            return response()->json(['error' => 'JSON Request erforderlich'], 400);
        }

        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Du musst angemeldet sein'], 401);
        }

        $message = $request->input('message');

        if ($message && strlen($message) > 500) {
            return response()->json(['error' => 'Nachricht zu lang (max 500 Zeichen)'], 422);
        }

        try {
            $question = Question::findOrFail($questionId);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Frage nicht gefunden'], 404);
        }

        $issue = QuestionIssue::where('question_id', $question->id)->first();

        if ($issue) {
            $issue->report_count++;
            $issue->latest_message = $message ?? null;
            $issue->reported_by_user_id = $user->id;

            if ($issue->status !== 'open') {
                $issue->status = 'open';
            }

            $issue->save();
            $isNew = false;
        } else {
            $issue = QuestionIssue::create([
                'question_id' => $question->id,
                'report_count' => 1,
                'latest_message' => $message ?? null,
                'reported_by_user_id' => $user->id,
                'status' => 'open',
            ]);
            $isNew = true;
        }

        QuestionIssueReport::create([
            'question_issue_id' => $issue->id,
            'user_id' => $user->id,
            'message' => $message ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => $isNew ? 'Fehler gemeldet!' : 'Fehler aktualisiert!',
            'report_count' => $issue->report_count,
        ]);
    }
}
