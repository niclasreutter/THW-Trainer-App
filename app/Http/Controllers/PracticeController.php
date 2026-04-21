<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Question;
use App\Models\QuestionIssue;
use App\Models\QuestionIssueReport;
use App\Models\QuestionStatistic;
use App\Models\UserQuestionProgress;
use App\Models\ExtraQuestion;
use App\Models\UserExtraQuestionProgress;
use App\Services\ExtraQuestionService;
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
     * Gesamtfortschritt (optional inkl. Zusatzfragen, falls aktiviert).
     *
     * @return array{total:int,mastered:int,once:int,twice:int,percent:int}
     */
    private function computeOverallProgress($user): array
    {
        $threshold = UserQuestionProgress::MASTERY_THRESHOLD;

        $total = Question::count();
        $mastered = UserQuestionProgress::countMastered($user->id);
        $once = UserQuestionProgress::countAtLevel($user->id, 1);
        $twice = UserQuestionProgress::countAtLevel($user->id, 2);

        $totalPoints = 0;
        foreach (UserQuestionProgress::where('user_id', $user->id)->get() as $prog) {
            $totalPoints += min($prog->consecutive_correct, $threshold);
        }

        if ($user->extras_enabled) {
            $total += ExtraQuestion::count();
            $mastered += UserExtraQuestionProgress::countMastered($user->id);
            $once += UserExtraQuestionProgress::countAtLevel($user->id, 1);
            $twice += UserExtraQuestionProgress::countAtLevel($user->id, 2);

            foreach (UserExtraQuestionProgress::where('user_id', $user->id)->get() as $prog) {
                $totalPoints += min($prog->consecutive_correct, $threshold);
            }
        }

        $max = $total * $threshold;
        $percent = $max > 0 ? (int) round(($totalPoints / $max) * 100) : 0;

        return [
            'total' => $total,
            'mastered' => $mastered,
            'once' => $once,
            'twice' => $twice,
            'percent' => $percent,
        ];
    }

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

        // Gesamt-Fortschritt (inkl. Zusatzfragen falls aktiviert)
        $overall = $this->computeOverallProgress($user);
        $totalQuestions = $overall['total'];
        $progressPercentage = $overall['percent'];

        // Gemeisterte offizielle Fragen (für Sync mit solved_questions)
        $progressData = UserQuestionProgress::where('user_id', $user->id)->get();
        $masteredQuestionIds = $progressData->filter(fn($p) => $p->isMastered())->pluck('question_id')->toArray();
        $solvedCount = $overall['mastered'];
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

        // Spaced Repetition — fällige Reviews + nächster Termin
        $srService = app(SpacedRepetitionService::class);
        $spacedRepetitionDue = $srService->getDueCount($user->id);
        $upcomingSchedule = $srService->getUpcomingSchedule($user->id);
        $nextSrLabel = !empty($upcomingSchedule) ? $upcomingSchedule[0]['label'] : null;
        $nextSrCount = !empty($upcomingSchedule) ? $upcomingSchedule[0]['count'] : 0;

        // Smart Action — kontextabhängige Empfehlung
        $allDone = $unsolvedCount === 0 && $failedCount === 0 && $spacedRepetitionDue === 0;
        $examPassedCount = $user->exam_passed_count ?? 0;
        $smartAction = match(true) {
            $spacedRepetitionDue > 0 => [
                'label' => 'Wiederholung fällig',
                'title' => "$spacedRepetitionDue Reviews warten auf dich",
                'desc'  => 'Spaced Repetition hält dein Wissen langfristig frisch',
                'route' => route('practice.spaced-repetition'),
                'type'  => 'action',
            ],
            $failedCount > 0 => [
                'label' => 'Empfohlen',
                'title' => "$failedCount Fehler wiederholen",
                'desc'  => 'Priorisiere fehlgeschlagene Fragen zuerst',
                'route' => route('failed.index'),
                'type'  => 'action',
            ],
            $unsolvedCount > 0 && $solvedCount >= ($totalQuestions * 0.9) => [
                'label' => 'Wiederholen',
                'title' => "Alle $totalQuestions Fragen zufällig lernen",
                'desc'  => "$solvedCount gemeistert, $unsolvedCount ungelöste werden priorisiert",
                'route' => route('practice.all'),
                'type'  => 'action',
            ],
            $unsolvedCount > 0 => [
                'label' => 'Weiterlernen',
                'title' => "$unsolvedCount ungelöste Fragen",
                'desc'  => 'Lerne neue Fragen und erweitere dein Wissen',
                'route' => route('practice.unsolved'),
                'type'  => 'action',
            ],
            $allDone && $examPassedCount >= 5 => [
                'label' => 'Prüfungsbereit',
                'title' => "$examPassedCount/5 bestanden — bereit für die echte Prüfung!",
                'desc'  => $nextSrLabel !== null
                    ? "$nextSrCount Wiederholungen werden $nextSrLabel fällig"
                    : 'Übe weiter um sicher zu bleiben',
                'route' => route('exam.index'),
                'type'  => 'action',
            ],
            $allDone && $examPassedCount >= 1 => [
                'label' => 'Dranbleiben',
                'title' => "$examPassedCount/5 Prüfungen bestanden",
                'desc'  => $nextSrLabel !== null
                    ? "Übe weiter — $nextSrCount Wiederholungen $nextSrLabel fällig"
                    : 'Übe weiter um sicherer zu werden',
                'route' => route('exam.index'),
                'type'  => 'action',
            ],
            $allDone && $nextSrLabel !== null => [
                'label' => 'Alles erledigt',
                'title' => 'Du bist auf dem neuesten Stand',
                'desc'  => "$nextSrCount Wiederholungen werden $nextSrLabel fällig",
                'route' => null,
                'type'  => 'info',
            ],
            $allDone => [
                'label' => 'Alles erledigt',
                'title' => 'Du hast alle Fragen gemeistert',
                'desc'  => 'Starte deine erste Prüfungssimulation',
                'route' => route('exam.index'),
                'type'  => 'action',
            ],
            default => [
                'label' => 'Wiederholen',
                'title' => 'Alle Fragen wiederholen',
                'desc'  => 'Festige dein Wissen durch Wiederholung',
                'route' => route('practice.all'),
                'type'  => 'action',
            ],
        };

        return view('practice-menu', compact('sectionStats', 'totalQuestions', 'solvedCount', 'failedCount', 'unsolvedCount', 'sectionNames', 'progressPercentage', 'smartAction', 'spacedRepetitionDue', 'nextSrLabel', 'nextSrCount'));
    }

    /**
     * Alle Fragen üben (ungelöste bevorzugt)
     */
    public function all()
    {
        // Session zurücksetzen für neuen Modus
        session()->forget(['practice_mode', 'practice_parameter', 'practice_ids', 'practice_queue', 'practice_skipped']);
        
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
        session()->forget(['practice_mode', 'practice_parameter', 'practice_ids', 'practice_queue', 'practice_skipped']);
        return $this->practiceMode('unsolved');
    }

    /**
     * Fehlgeschlagene Prüfungsfragen wiederholen
     */
    public function failed()
    {
        session()->forget(['practice_mode', 'practice_parameter', 'practice_ids', 'practice_queue', 'practice_skipped']);
        return $this->practiceMode('failed');
    }

    /**
     * Lernabschnitt üben
     */
    public function section($section)
    {
        session()->forget(['practice_mode', 'practice_parameter', 'practice_ids', 'practice_queue', 'practice_skipped']);
        return $this->practiceMode('section', $section);
    }

    /**
     * Nur Zusatz-Fragen üben (Opt-in via extras_enabled)
     */
    public function extrasOnly()
    {
        $user = Auth::user();
        if (!$user || !$user->extras_enabled) {
            return redirect()->route('profile')->with('error', 'Bitte aktiviere zuerst die Zusatz-Fragen im Profil.');
        }

        session()->forget(['practice_mode', 'practice_parameter', 'practice_ids', 'practice_queue', 'practice_skipped']);
        return $this->practiceMode('extras_only');
    }

    /**
     * Spaced Repetition Modus - Fällige Wiederholungen
     */
    public function spacedRepetition()
    {
        session()->forget(['practice_mode', 'practice_parameter', 'practice_ids', 'practice_queue', 'practice_skipped']);

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
        
        session()->forget(['practice_mode', 'practice_parameter', 'practice_ids', 'practice_queue', 'practice_skipped']);
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

        // SR-Filter bypassen wenn User in aktiver Lernsession ist
        $lernsessionService = app(\App\Services\LernsessionService::class);
        $inActiveSession = (bool) $lernsessionService->isUserInActiveSession($user);

        // Basis-Query je nach Modus
        $query = Question::query();
        
        switch ($mode) {
            case 'all':
                // Intelligente Priorisierung für Practice All:
                // 1. Falsch beantwortete Fragen aus Prüfungen (dringend)
                // 2. Ungelöste Fragen: nicht-gemeistert + nie beantwortet (Lernfortschritt)
                // 3. Spaced Repetition Fragen (fällige Wiederholungen, bereits gemeistert)
                // 4. Restliche Fragen in zufälliger Reihenfolge

                $idsToShow = [];
                $alreadyQueued = [];

                // Basis-Daten laden
                $srService = new SpacedRepetitionService();
                $srDueIds = $srService->getDueQuestions($user->id);
                $allQuestionIds = Question::pluck('id')->toArray();
                $currentlyMasteredIds = UserQuestionProgress::getMasteredQuestions($user->id);

                // SR-Filter: Zukünftige SR-Fragen nur ausblenden wenn nicht in aktiver Lernsession
                if (!$inActiveSession) {
                    $futureSrIds = UserQuestionProgress::where('user_id', $user->id)
                        ->whereNotNull('next_review_at')
                        ->where('next_review_at', '>', now())
                        ->pluck('question_id')
                        ->toArray();
                } else {
                    $futureSrIds = [];
                }

                // 1. Falsch beantwortete Fragen aus Prüfungen
                $failedIds = array_values($failed);
                shuffle($failedIds);
                $idsToShow = array_merge($idsToShow, $failedIds);
                $alreadyQueued = array_merge($alreadyQueued, $failedIds);

                // 2. Nicht-gemeisterte + nie beantwortete Fragen (ungelöst)
                $unmasteredIds = UserQuestionProgress::getUnmasteredQuestions($user->id);
                $answeredQuestionIds = UserQuestionProgress::where('user_id', $user->id)
                    ->pluck('question_id')
                    ->toArray();
                $neverAnsweredIds = array_diff($allQuestionIds, $answeredQuestionIds);

                $toLearnIds = array_unique(array_merge($unmasteredIds, $neverAnsweredIds));
                $toLearnIds = array_diff($toLearnIds, $alreadyQueued);
                $toLearnIds = array_diff($toLearnIds, $currentlyMasteredIds);

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

                // 3. Spaced Repetition: Fällige Wiederholungen (bereits gemeistert, aber Review nötig)
                $srDueIds = array_diff($srDueIds, $alreadyQueued);
                $srDueIds = array_values($srDueIds);
                shuffle($srDueIds);
                $idsToShow = array_merge($idsToShow, $srDueIds);
                $alreadyQueued = array_merge($alreadyQueued, $srDueIds);

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
                    'failed_count' => count($failedIds),
                    'unmastered_count' => count($unmasteredIds),
                    'never_answered_count' => count($neverAnsweredIds),
                    'sr_due_count' => count($srDueIds),
                    'future_sr_excluded' => count($futureSrIds),
                    'total_to_learn' => count($toLearnIds),
                    'remaining_random' => count($remainingIds),
                    'total_ids_to_show' => count($idsToShow),
                    'in_active_session' => $inActiveSession,
                ]);
                break;
                
            case 'unsolved':
                // Nur ungelöste Fragen (nicht gemeistert) zufällig sortieren
                $masteredIds = UserQuestionProgress::getMasteredQuestions($user->id);
                $unsolvedIds = Question::whereNotIn('id', $masteredIds)->pluck('id')->toArray();

                // Kein SR-Filter — wenn der User aktiv üben will, darf er alle ungelösten Fragen sehen.
                // Ungelöste Fragen sind per Definition noch nicht gemeistert, also sollen sie immer verfügbar sein.
                shuffle($unsolvedIds);

                $idsToShow = $unsolvedIds;
                break;
                
            case 'failed':
                // Nur fehlgeschlagene Prüfungsfragen (aus exam_failed_questions)
                // KEIN SR-Filter hier — Fehlerfragen haben immer Vorrang
                $failedIds = array_values($failed);

                if (empty($failedIds)) {
                    return redirect()->route('practice.menu')->with('info', 'Keine falschen Fragen zum Wiederholen!');
                }

                // Bereits gemeisterte Fragen ausschließen (korrekt beantwortet)
                $masteredIds = UserQuestionProgress::getMasteredQuestions($user->id);
                $failedIds = array_values(array_diff($failedIds, $masteredIds));

                if (empty($failedIds)) {
                    return redirect()->route('practice.menu')->with('info', 'Alle Fehlerfragen bereits gemeistert!');
                }

                shuffle($failedIds);

                $idsToShow = $failedIds;
                break;
                
            case 'section':
                // Gleiche Prio-Logik wie 'all', aber auf einen Lernabschnitt beschränkt
                $allSectionIds = Question::where('lernabschnitt', $parameter)->pluck('id')->toArray();

                $idsToShow = [];
                $alreadyQueued = [];

                $currentlyMasteredIds = UserQuestionProgress::getMasteredQuestions($user->id);

                // SR-Filter: Zukünftige SR-Fragen nur ausblenden wenn nicht in aktiver Lernsession
                if (!$inActiveSession) {
                    $futureSrIds = UserQuestionProgress::where('user_id', $user->id)
                        ->whereNotNull('next_review_at')
                        ->where('next_review_at', '>', now())
                        ->pluck('question_id')
                        ->toArray();
                } else {
                    $futureSrIds = [];
                }

                // 1. Fehlerfragen aus Prüfungen
                $failedInSection = array_values(array_intersect($failed, $allSectionIds));
                shuffle($failedInSection);
                $idsToShow = array_merge($idsToShow, $failedInSection);
                $alreadyQueued = array_merge($alreadyQueued, $failedInSection);

                // 2. Ungelöste Fragen (nicht gemeistert + nie beantwortet)
                $unmasteredIds = UserQuestionProgress::getUnmasteredQuestions($user->id);
                $answeredQuestionIds = UserQuestionProgress::where('user_id', $user->id)
                    ->pluck('question_id')
                    ->toArray();
                $neverAnsweredIds = array_diff($allSectionIds, $answeredQuestionIds);

                $toLearnIds = array_unique(array_merge(
                    array_intersect($unmasteredIds, $allSectionIds),
                    $neverAnsweredIds
                ));
                $toLearnIds = array_diff($toLearnIds, $alreadyQueued);
                $toLearnIds = array_diff($toLearnIds, $currentlyMasteredIds);
                $toLearnIds = array_values($toLearnIds);
                shuffle($toLearnIds);
                $idsToShow = array_merge($idsToShow, $toLearnIds);
                $alreadyQueued = array_merge($alreadyQueued, $toLearnIds);

                // 3. SR fällige Wiederholungen (bereits gemeistert, aber Review nötig)
                $srService = new SpacedRepetitionService();
                $srDueIds = $srService->getDueQuestions($user->id);
                $srDueInSection = array_values(array_intersect($allSectionIds, $srDueIds));
                $srDueInSection = array_diff($srDueInSection, $alreadyQueued);
                $srDueInSection = array_values($srDueInSection);
                shuffle($srDueInSection);
                $idsToShow = array_merge($idsToShow, $srDueInSection);
                $alreadyQueued = array_merge($alreadyQueued, $srDueInSection);

                // 4. Restliche Fragen (gemeisterte, zur Wiederholung)
                $remainingIds = array_diff($allSectionIds, $alreadyQueued);
                $remainingIds = array_diff($remainingIds, $futureSrIds);
                $remainingIds = array_values($remainingIds);
                shuffle($remainingIds);
                $idsToShow = array_merge($idsToShow, $remainingIds);
                $idsToShow = array_values(array_unique($idsToShow));
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
                // AUSNAHME: In aktiver Lernsession werden alle Fragen angezeigt.
                if (!$inActiveSession) {
                    $futureSrIds = UserQuestionProgress::where('user_id', $user->id)
                        ->whereNotNull('next_review_at')
                        ->where('next_review_at', '>', now())
                        ->pluck('question_id')
                        ->toArray();
                    $searchIds = array_values(array_diff($searchIds, $futureSrIds));
                }

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

            case 'extras_only':
                // Nur Zusatz-Fragen — wird unten zu einer reinen Extra-Queue gemerged
                $idsToShow = [];
                break;

            default:
                $idsToShow = [];
        }
        
        // Geskippte Fragen temporär entfernen
        $idsToShow = array_diff($idsToShow, $skipped);

        // --- Queue-Interleave mit Zusatz-Fragen (opt-in) ---
        // Offizielle IDs in wrapped Form umwandeln, optional mit Extras interleaven.
        // Exam-Modus wird hier NIE erreicht (eigener Controller, eigene Routes).
        $extraQuestionService = app(\App\Services\ExtraQuestionService::class);
        $officialIds = array_values(array_map('intval', $idsToShow));
        $officialQueue = array_map(fn ($id) => ['type' => 'official', 'id' => (int) $id], $officialIds);
        $lernabschnitt = ($mode === 'section') ? (string) $parameter : null;

        if ($mode === 'extras_only') {
            $extraIds = $extraQuestionService->buildQueue($user, 'extras_only', $lernabschnitt, 40);
            $mergedQueue = array_map(
                fn ($id) => ['type' => 'extra', 'id' => (int) $id],
                array_values($extraIds)
            );
        } elseif ($user && $user->extras_enabled && !empty($officialIds)) {
            $extrasCount = max(1, intdiv(count($officialIds), 4));
            $extraIds = $extraQuestionService->buildQueue($user, 'mixed', $lernabschnitt, $extrasCount);
            $mergedQueue = $extraQuestionService->mergeQueues($officialIds, $extraIds);
        } else {
            $mergedQueue = $officialQueue;
        }

        if (empty($idsToShow) && $mode !== 'extras_only') {
            \Log::info('No questions found after skipped removal', [
                'mode' => $mode,
                'parameter' => $parameter,
                'skipped_count' => count($skipped)
            ]);

            // Bei 'unsolved' Modus: Fallback auf alle Fragen statt Redirect-Loop zum Menü
            if ($mode === 'unsolved') {
                return redirect()->route('practice.all')
                    ->with('info', 'Keine ungelösten Fragen verfügbar — alle Fragen werden geladen.');
            }

            // Prüfe ob Fragen existieren, aber alle durch SR-Zeitplanung blockiert sind
            if (in_array($mode, ['section', 'all', 'search'])) {
                $sectionQuestionIds = match($mode) {
                    'section' => Question::where('lernabschnitt', $parameter)->pluck('id')->toArray(),
                    'search' => Question::where(function($q) use ($parameter) {
                        $q->where('frage', 'LIKE', '%' . $parameter . '%')
                          ->orWhere('antwort_a', 'LIKE', '%' . $parameter . '%')
                          ->orWhere('antwort_b', 'LIKE', '%' . $parameter . '%')
                          ->orWhere('antwort_c', 'LIKE', '%' . $parameter . '%');
                    })->pluck('id')->toArray(),
                    default => Question::pluck('id')->toArray(),
                };

                if (!empty($sectionQuestionIds)) {
                    $earliestReview = UserQuestionProgress::where('user_id', $user->id)
                        ->whereIn('question_id', $sectionQuestionIds)
                        ->whereNotNull('next_review_at')
                        ->where('next_review_at', '>', now())
                        ->orderBy('next_review_at', 'asc')
                        ->value('next_review_at');

                    if ($earliestReview) {
                        $reviewDate = \Carbon\Carbon::parse($earliestReview);
                        $sectionLabel = $mode === 'section'
                            ? 'Lernabschnitt ' . $parameter . ' (' . (self::SECTION_NAMES[$parameter] ?? '') . ')'
                            : ($mode === 'search' ? 'Suchergebnis' : 'Alle Fragen');

                        if ($reviewDate->isToday()) {
                            $timeLabel = 'heute um ' . $reviewDate->format('H:i') . ' Uhr';
                        } elseif ($reviewDate->isTomorrow()) {
                            $timeLabel = 'morgen um ' . $reviewDate->format('H:i') . ' Uhr';
                        } else {
                            $timeLabel = 'am ' . $reviewDate->format('d.m.Y') . ' um ' . $reviewDate->format('H:i') . ' Uhr';
                        }

                        return redirect()->route('practice.menu')
                            ->with('practice_unavailable', [
                                'title' => 'Alle Fragen bereits beantwortet',
                                'message' => $sectionLabel . ': Alle Fragen wurden bereits beantwortet und sind durch Spaced Repetition geplant.',
                                'next_review' => 'Nächste Wiederholung: ' . $timeLabel,
                            ]);
                    }
                }
            }

            return redirect()->route('practice.menu')->with('success', 'Keine Fragen gefunden.');
        }

        // Queue-Item wählen: kann 'official' oder 'extra' sein (abhängig von mergedQueue oben).
        if (empty($mergedQueue)) {
            \Log::info('Practice: no queue items', ['mode' => $mode]);
            if ($mode === 'extras_only') {
                return redirect()->route('practice.menu')
                    ->with('info', 'Keine Zusatz-Fragen verfügbar.');
            }
            return redirect()->route('practice.menu')->with('error', 'Fehler beim Laden der Fragen.');
        }

        $firstItem = $mergedQueue[0];
        if ($firstItem['type'] === 'extra') {
            $question = ExtraQuestion::with([
                'options', 'matchCategories', 'matchItems.correctCategory',
            ])->find($firstItem['id']);
        } else {
            $question = Question::find($firstItem['id']);
        }

        // Nochmals prüfen ob Frage existiert
        if (!$question) {
            \Log::error('Practice start: first queue item not found', [
                'item' => $firstItem,
                'mode' => $mode,
            ]);
            return redirect()->route('practice.menu')->with('error', 'Die angeforderte Frage konnte nicht gefunden werden.');
        }

        \Log::info('Practice session starting', [
            'mode' => $mode,
            'first_type' => $firstItem['type'],
            'first_id' => $firstItem['id'],
            'total_ids' => count($mergedQueue),
            'official_count' => count($officialIds),
        ]);

        $overall = $this->computeOverallProgress($user);
        $total = $overall['total'];
        $progress = $overall['mastered'];
        $progressOnce = $overall['once'];
        $progressTwice = $overall['twice'];
        $progressPercent = $overall['percent'];

        // Session für aktuellen Modus speichern (via Service).
        // practice_ids enthält NUR offizielle IDs (für backwards-compat).
        $totalInMode = count($mergedQueue);
        $this->practiceService->startSession('global', null, $officialIds, $mode, 'remove');

        // Controller-managed session keys (not part of the service).
        // practice_queue ist die neue Source-of-Truth für Queue-Order.
        session([
            'practice_parameter' => $parameter,
            'practice_total_in_mode' => $totalInMode,
            'practice_queue' => $mergedQueue,
        ]);

        $currentInMode = 1;

        // Schwierigkeitsindikator nur für offizielle Fragen sinnvoll.
        $difficultyInfo = ($firstItem['type'] === 'official')
            ? $this->getQuestionDifficulty($question->id)
            : null;

        // Spaced Repetition-Badge: nur offizielle Fragen fallen in den SR-Flow.
        if ($firstItem['type'] === 'official') {
            $srService = new SpacedRepetitionService();
            $srDueIds = $srService->getDueQuestions($user->id);
            $isSpacedRepetition = in_array($question->id, $srDueIds);
        } else {
            $isSpacedRepetition = false;
        }

        return view('practice', compact('question', 'progress', 'progressOnce', 'progressTwice', 'total', 'mode', 'progressPercent', 'totalInMode', 'currentInMode', 'difficultyInfo', 'isSpacedRepetition'));
    }

    public function show(Request $request)
    {
        $user = Auth::user();
        $solved = $this->ensureArray($user->solved_questions);
        $skipped = session('practice_skipped', []);

        // Get answer/gamification results from service (peek, don't clear yet for showAnsweredQuestion check)
        $answerResult = $this->practiceService->getAndClearAnswerResult('global', null);
        $gamificationResult = $this->practiceService->getAndClearGamificationResult('global', null);

        // Extras: answer_result wird direkt als Session-Key gespeichert (nicht via Service-Prefix)
        if (!$answerResult) {
            $extraAnswerResult = session('practice_answer_result');
            if ($extraAnswerResult) {
                $answerResult = $extraAnswerResult;
                session()->forget('practice_answer_result');
            }
        }

        // Check if we're in a specific practice mode
        $practiceIds = session('practice_ids', []);
        $practiceQueue = session('practice_queue', []);
        $mode = session('practice_mode', 'all');

        // Falls nur Legacy-Daten vorhanden sind: Queue aus practice_ids rekonstruieren
        if (empty($practiceQueue) && !empty($practiceIds)) {
            $practiceQueue = array_map(
                fn ($id) => ['type' => 'official', 'id' => (int) $id],
                $practiceIds
            );
        }

        // WICHTIG: Wenn eine Frage gerade beantwortet wurde (answer_result),
        // zeige diese Frage nochmal (damit die Antwort angezeigt werden kann)
        $showAnsweredQuestion = $answerResult && isset($answerResult['question_id']);
        $answeredKind = $answerResult['question_kind'] ?? 'official';

        if (empty($practiceQueue) && !$showAnsweredQuestion) {
            // Legacy mode or no active session
            return redirect()->route('practice.menu');
        }

        $currentItem = null;

        if ($showAnsweredQuestion) {
            // Zeige die gerade beantwortete Frage nochmal (mit Result)
            $currentItem = ['type' => $answeredKind === 'extra' ? 'extra' : 'official', 'id' => (int) $answerResult['question_id']];
        } elseif ($request->has('skip_id')) {
            $skipId = (int) $request->input('skip_id');
            // Skip nur für offizielle Fragen sinnvoll; trotzdem aus Queue entfernen
            $practiceQueue = array_values(array_filter(
                $practiceQueue,
                fn ($it) => !(isset($it['id']) && (int) $it['id'] === $skipId && ($it['type'] ?? 'official') === 'official')
            ));
            session(['practice_queue' => $practiceQueue]);
            // Sync practice_ids
            session(['practice_ids' => array_values(array_map(
                fn ($it) => (int) $it['id'],
                array_filter($practiceQueue, fn ($it) => ($it['type'] ?? 'official') === 'official')
            ))]);

            $skipped = array_merge($skipped, [$skipId]);
            session(['practice_skipped' => array_unique($skipped)]);

            if (empty($practiceQueue)) {
                return redirect()->route('practice.summary');
            }
            $currentItem = $practiceQueue[0];
        } else {
            // Normale Anzeige: erstes Item in der Queue
            if (empty($practiceQueue)) {
                return redirect()->route('practice.summary');
            }
            $currentItem = $practiceQueue[0];
        }

        // Lade die Frage aus der richtigen Tabelle
        if (($currentItem['type'] ?? 'official') === 'extra') {
            $question = ExtraQuestion::with([
                'options', 'matchCategories', 'matchItems.correctCategory',
            ])->find($currentItem['id']);
        } else {
            $question = Question::find($currentItem['id']);
        }

        // Prüfe ob Frage existiert; bei korruptem Eintrag: aus Queue entfernen und neu versuchen
        if (!$question) {
            \Log::warning('Practice: queue item not found, skipping', ['item' => $currentItem]);
            $practiceQueue = array_values(array_filter(
                $practiceQueue,
                fn ($it) => !(isset($it['id']) && (int) $it['id'] === (int) $currentItem['id'] && ($it['type'] ?? 'official') === ($currentItem['type'] ?? 'official'))
            ));
            session(['practice_queue' => $practiceQueue]);
            session(['practice_ids' => array_values(array_map(
                fn ($it) => (int) $it['id'],
                array_filter($practiceQueue, fn ($it) => ($it['type'] ?? 'official') === 'official')
            ))]);

            if (empty($practiceQueue)) {
                $this->practiceService->cleanSession('global', null);
                session()->forget(['practice_parameter', 'practice_skipped', 'practice_total_in_mode', 'practice_queue']);
                return redirect()->route('practice.menu')->with('error', 'Die angeforderte Frage konnte nicht gefunden werden.');
            }
            return redirect()->route('practice.index');
        }

        $overall = $this->computeOverallProgress($user);
        $total = $overall['total'];
        $progress = $overall['mastered'];
        $progressOnce = $overall['once'];
        $progressTwice = $overall['twice'];
        $progressPercent = $overall['percent'];

        $totalInMode = session('practice_total_in_mode', count($practiceQueue));
        $answered = $totalInMode - count($practiceQueue);
        $currentInMode = max(1, $answered + 1);

        // Schwierigkeits-/SR-Info nur für offizielle Fragen
        if (($currentItem['type'] ?? 'official') === 'official') {
            $difficultyInfo = $this->getQuestionDifficulty($question->id);
            $srService = new SpacedRepetitionService();
            $srDueIds = $srService->getDueQuestions($user->id);
            $isSpacedRepetition = in_array($question->id, $srDueIds);
        } else {
            $difficultyInfo = null;
            $isSpacedRepetition = false;
            $gamificationResult = null;
        }

        // Service-based progress
        $serviceProgress = $this->practiceService->getProgress('global', null);

        return view('practice', compact(
            'question', 'progress', 'progressOnce', 'progressTwice', 'total', 'mode', 'progressPercent',
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
        // Zusatz-Fragen (matching, image_name, image_select) haben einen eigenen
        // Submit-Pfad. Detektion: explizites Hidden-Field `question_kind` = "extra".
        // (Existiert kein Field oder Wert "official", greift der klassische MC-Flow.)
        if ($request->input('question_kind') === 'extra') {
            return $this->submitExtra($request);
        }

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
        $originalNextReviewAt = $progressObj->next_review_at;

        // Delegate progress update, statistic creation, gamification, session stats + queue management to service
        $result = $this->practiceService->submitAnswer('global', null, $question->id, $userAnswer, $mapping);
        $isCorrect = $result['is_correct'];

        // practice_queue mit practice_ids synchronisieren — nur verbleibende
        // offizielle IDs bleiben; Extra-Items bleiben unverändert an ihrer Position.
        $this->removeFromQueueAndSync($question->id, 'official');

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
        // In aktiver Lernsession: SR nicht aktualisieren für Fragen die noch nicht fällig waren,
        // damit der SR-Plan nicht durcheinander kommt.
        $lernsessionService = app(\App\Services\LernsessionService::class);
        $sessionParticipantForSr = $lernsessionService->isUserInActiveSession($user);
        $hadFutureSr = $originalNextReviewAt && $originalNextReviewAt->isFuture();

        if (!$sessionParticipantForSr || !$hadFutureSr) {
            (new SpacedRepetitionService())->processAnswer($progressObj, $isCorrect);
        }

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
        if ($sessionParticipantForSr) {
            $xpAwarded = ($gamificationResult && isset($gamificationResult['points_awarded']))
                ? $gamificationResult['points_awarded'] : 0;
            $answerTimeMs = (int) $request->input('answer_time_ms', 0);
            $lernsessionService->recordAnswer($sessionParticipantForSr, $isCorrect, $answerTimeMs, $xpAwarded);
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
     * Submit-Handler für Zusatz-Fragen (matching, image_name, image_select).
     *
     * Detektion erfolgt in submit() via Hidden-Field `question_kind=extra`.
     * Progress + SR werden via ExtraQuestionService::updateProgress aktualisiert.
     * Korrektheits-Regel (strict):
     *  - matching: ALLE Items müssen korrekt zugeordnet sein
     *  - image_name / image_select: Submitted-Set === Korrekt-Set
     */
    public function submitExtra(Request $request)
    {
        // Basis-Validierung
        $validated = $request->validate([
            'question_id' => ['required', 'integer', 'exists:extra_questions,id'],
        ]);

        $extra = ExtraQuestion::with(['options', 'matchItems', 'matchCategories'])
            ->findOrFail($validated['question_id']);

        // Typ-spezifische Validierung + Correctness
        $submittedAssignments = null;
        $userAnswerIds = null;

        if ($extra->typ === ExtraQuestion::TYP_MATCHING) {
            $request->validate([
                'assignments' => ['required', 'string', 'json'],
            ]);
            $submittedAssignments = json_decode($request->input('assignments'), true) ?: [];
            $isCorrect = $this->validateMatching($extra, $submittedAssignments);
        } else {
            // image_name + image_select
            $request->validate([
                'answer' => ['required', 'array', 'min:1'],
                'answer.*' => ['integer', 'exists:extra_question_options,id'],
            ]);
            $userAnswerIds = array_map('intval', (array) $request->input('answer', []));
            $isCorrect = $this->validateOptionIds($extra, $userAnswerIds);
        }

        $user = $request->user();

        // Progress + SR via Service
        $extraService = app(ExtraQuestionService::class);
        $progress = $extraService->updateProgress($user, $extra, $isCorrect);

        // AnswerResult für das Result-Rendering im Partial bereitstellen
        $answerResult = [
            'question_id' => $extra->id,
            'question_kind' => 'extra',
            'question_typ' => $extra->typ,
            'is_correct' => $isCorrect,
            'consecutive_correct' => $progress->consecutive_correct,
            'mastered' => $progress->isMastered(),
        ];

        if ($submittedAssignments !== null) {
            $answerResult['assignments'] = $submittedAssignments;
        }
        if ($userAnswerIds !== null) {
            $answerResult['user_answer'] = $userAnswerIds;
        }

        // In Session ablegen (analog zum MC-Flow, gleicher Prefix "practice_")
        session([
            'practice_answer_result' => $answerResult,
        ]);

        // Extra-Frage aus practice_queue entfernen
        $this->removeFromQueueAndSync($extra->id, 'extra');

        // Lernsession-Tracking (XP: 0, Service hat für Extras noch kein eigenes Gamification-Hook)
        $lernsessionService = app(\App\Services\LernsessionService::class);
        $sessionParticipant = $lernsessionService->isUserInActiveSession($user);
        if ($sessionParticipant) {
            $answerTimeMs = (int) $request->input('answer_time_ms', 0);
            $lernsessionService->recordAnswer($sessionParticipant, $isCorrect, $answerTimeMs, 0);
        }

        return redirect()->route('practice.index');
    }

    /**
     * Validiert eine Matching-Frage (strict: alle Items müssen korrekt sein).
     *
     * @param array<int|string, int|string|null> $submitted  [itemId => categoryId, ...]
     */
    private function validateMatching(ExtraQuestion $extra, array $submitted): bool
    {
        $items = $extra->matchItems;
        if ($items->isEmpty()) {
            return false;
        }

        foreach ($items as $item) {
            $submittedCat = $submitted[$item->id] ?? $submitted[(string) $item->id] ?? null;
            if ($submittedCat === null) {
                return false;
            }
            if ((int) $submittedCat !== (int) $item->correct_category_id) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validiert eine Option-basierte Frage (image_name, image_select).
     * Strict-Match: Submitted-Set muss exakt dem Korrekt-Set entsprechen.
     *
     * @param array<int> $submittedIds
     */
    private function validateOptionIds(ExtraQuestion $extra, array $submittedIds): bool
    {
        $correctIds = $extra->options
            ->where('is_correct', true)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->sort()
            ->values()
            ->toArray();

        if (empty($correctIds)) {
            return false;
        }

        $submitted = collect($submittedIds)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        return $submitted === $correctIds;
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
        session()->forget(['practice_parameter', 'practice_skipped', 'practice_total_in_mode', 'practice_queue']);

        return view('practice-summary', compact('stats', 'totalAnswered', 'accuracy', 'durationMinutes', 'modeName', 'streak') + [
            'context' => 'global',
            'backUrl' => route('practice.menu'),
            'completed' => false,
        ]);
    }

    /**
     * Entfernt das erste passende Item ({$type}, {$id}) aus practice_queue und
     * synchronisiert practice_ids (nur offizielle IDs) als belt-and-suspenders
     * für Legacy-Code, der noch practice_ids liest.
     */
    private function removeFromQueueAndSync(int $id, string $type): void
    {
        $queue = session('practice_queue', []);
        if (!is_array($queue) || empty($queue)) {
            return;
        }

        $removed = false;
        $queue = array_values(array_filter($queue, function ($item) use ($id, $type, &$removed) {
            if ($removed) {
                return true;
            }
            $itemType = $item['type'] ?? 'official';
            $itemId = (int) ($item['id'] ?? 0);
            if ($itemType === $type && $itemId === $id) {
                $removed = true;
                return false;
            }
            return true;
        }));

        session(['practice_queue' => $queue]);

        // practice_ids synchron halten — nur offizielle IDs
        $officialOnly = array_values(array_map(
            fn ($it) => (int) $it['id'],
            array_filter($queue, fn ($it) => ($it['type'] ?? 'official') === 'official')
        ));
        session(['practice_ids' => $officialOnly]);
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
