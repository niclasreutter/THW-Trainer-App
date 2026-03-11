<?php

namespace App\Http\Controllers;

use App\Models\LearningSession;
use App\Models\LearningSessionInstance;
use App\Models\Ortsverband;
use App\Models\OrtsverbandLernpool;
use App\Services\LernsessionService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class LernsessionController extends Controller
{
    use AuthorizesRequests;

    protected LernsessionService $service;

    public function __construct(LernsessionService $service)
    {
        $this->service = $service;
    }

    // Übersicht aller sichtbaren Sessions
    public function index(Request $request)
    {
        $user = auth()->user();

        $activeSessions = $this->service->getActiveSessionsForUser($user);
        $upcomingSessions = $this->service->getUpcomingSessionsForUser($user);
        $sessions = $this->service->getSessionsForUser($user);
        $wonCount = $this->service->getWonSessionsCount($user);
        $recentResults = $this->service->getRecentResults($user);

        // Prüfe ob User schon einer aktiven Session beigetreten ist
        $currentParticipation = $this->service->isUserInActiveSession($user);

        return view('lernsession.index', compact(
            'activeSessions', 'upcomingSessions', 'sessions',
            'wonCount', 'recentResults', 'currentParticipation'
        ));
    }

    // Erstellformular
    public function create(Request $request)
    {
        $user = auth()->user();

        // OV-ID aus Query Parameter oder null für global
        $ortsverband = null;
        if ($request->has('ortsverband_id')) {
            $ortsverband = Ortsverband::findOrFail($request->ortsverband_id);
            $this->authorize('create', [LearningSession::class, $ortsverband]);
        } else {
            $this->authorize('create', [LearningSession::class, null]);
        }

        // Verfügbare Lernpools (nur für OV-Sessions)
        $lernpools = $ortsverband
            ? $ortsverband->activeLernpools()->get()
            : collect();

        // Lernabschnitte
        $sections = $this->getSectionNames();

        return view('lernsession.create', compact('ortsverband', 'lernpools', 'sections'));
    }

    // Session speichern
    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'scope' => 'required|in:global,ortsverband',
            'ortsverband_id' => 'required_if:scope,ortsverband|nullable|exists:ortsverbände,id',
            'question_source' => 'required|in:all,sections,lernpool',
            'question_sections' => 'nullable|array',
            'question_sections.*' => 'integer|between:1,10',
            'lernpool_id' => 'required_if:question_source,lernpool|nullable|exists:ortsverband_lernpools,id',
            'schedule_type' => 'required|in:once,recurring',
            'starts_at' => 'required_if:schedule_type,once|nullable|date|after:now',
            'ends_at' => 'required_if:schedule_type,once|nullable|date|after:starts_at',
            'recurrence_days' => 'required_if:schedule_type,recurring|nullable|array',
            'recurrence_days.*' => 'integer|between:1,7',
            'recurrence_start_time' => 'required_if:schedule_type,recurring|nullable|date_format:H:i',
            'recurrence_end_time' => 'required_if:schedule_type,recurring|nullable|date_format:H:i|after:recurrence_start_time',
            'recurrence_valid_from' => 'nullable|date',
            'recurrence_valid_until' => 'nullable|date|after_or_equal:recurrence_valid_from',
        ]);

        // Authorization
        $ortsverband = $validated['scope'] === 'ortsverband'
            ? Ortsverband::findOrFail($validated['ortsverband_id'])
            : null;
        $this->authorize('create', [LearningSession::class, $ortsverband]);

        // Lernpool nur für OV-Sessions
        if ($validated['question_source'] === 'lernpool' && $validated['scope'] === 'global') {
            return back()->withErrors(['question_source' => 'Globale Sessions können keinen Lernpool als Fragenquelle verwenden.']);
        }

        $session = $this->service->createSession($validated, $user);

        return redirect()->route('lernsession.show', $session)
            ->with('success', 'Lernsession erfolgreich erstellt.');
    }

    // Session-Details anzeigen
    public function show(LearningSession $lernsession)
    {
        $this->authorize('view', $lernsession);

        $lernsession->load(['creator', 'ortsverband', 'lernpool']);

        $activeInstance = $lernsession->getActiveInstance();
        $nextInstance = $lernsession->getNextInstance();
        $pastInstances = $lernsession->instances()
            ->where('status', 'completed')
            ->with('winner')
            ->orderByDesc('ends_at')
            ->limit(10)
            ->get();

        $sections = $this->getSectionNames();

        return view('lernsession.show', compact(
            'lernsession', 'activeInstance', 'nextInstance', 'pastInstances', 'sections'
        ));
    }

    // Bearbeitungsformular
    public function edit(LearningSession $lernsession)
    {
        $this->authorize('update', $lernsession);

        $ortsverband = $lernsession->ortsverband;
        $lernpools = $ortsverband
            ? $ortsverband->activeLernpools()->get()
            : collect();
        $sections = $this->getSectionNames();

        return view('lernsession.edit', compact('lernsession', 'ortsverband', 'lernpools', 'sections'));
    }

    // Session aktualisieren
    public function update(Request $request, LearningSession $lernsession)
    {
        $this->authorize('update', $lernsession);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'question_source' => 'required|in:all,sections,lernpool',
            'question_sections' => 'nullable|array',
            'question_sections.*' => 'integer|between:1,10',
            'lernpool_id' => 'required_if:question_source,lernpool|nullable|exists:ortsverband_lernpools,id',
            'schedule_type' => 'required|in:once,recurring',
            'starts_at' => 'required_if:schedule_type,once|nullable|date',
            'ends_at' => 'required_if:schedule_type,once|nullable|date|after:starts_at',
            'recurrence_days' => 'required_if:schedule_type,recurring|nullable|array',
            'recurrence_days.*' => 'integer|between:1,7',
            'recurrence_start_time' => 'required_if:schedule_type,recurring|nullable|date_format:H:i',
            'recurrence_end_time' => 'required_if:schedule_type,recurring|nullable|date_format:H:i|after:recurrence_start_time',
            'recurrence_valid_from' => 'nullable|date',
            'recurrence_valid_until' => 'nullable|date|after_or_equal:recurrence_valid_from',
            'is_active' => 'boolean',
        ]);

        $this->service->updateSession($lernsession, $validated);

        return redirect()->route('lernsession.show', $lernsession)
            ->with('success', 'Lernsession erfolgreich aktualisiert.');
    }

    // Session löschen
    public function destroy(LearningSession $lernsession)
    {
        $this->authorize('delete', $lernsession);

        $this->service->deleteSession($lernsession);

        return redirect()->route('lernsession.index')
            ->with('success', 'Lernsession erfolgreich gelöscht.');
    }

    // Einer aktiven Instanz beitreten
    public function join(Request $request, LearningSessionInstance $instance)
    {
        $this->authorize('join', [LearningSession::class, $instance]);

        if (!$instance->isActive()) {
            return back()->with('error', 'Diese Session ist nicht aktiv.');
        }

        $user = auth()->user();
        $rankingConsent = $request->boolean('ranking_consent');

        $this->service->joinSession($instance, $user, $rankingConsent);

        return redirect()->route('lernsession.live', $instance)
            ->with('success', 'Du bist der Lernsession beigetreten!');
    }

    // Instanz verlassen
    public function leave(LearningSessionInstance $instance)
    {
        $user = auth()->user();
        $participant = $instance->participants()->where('user_id', $user->id)->first();

        if ($participant) {
            $participant->delete();
            \Illuminate\Support\Facades\Cache::forget("lernsession_active_{$user->id}");
        }

        return redirect()->route('lernsession.index')
            ->with('success', 'Du hast die Lernsession verlassen.');
    }

    // Live-Ansicht der Session
    public function live(LearningSessionInstance $instance)
    {
        $session = $instance->learningSession;
        $this->authorize('view', $session);

        $user = auth()->user();
        $participant = $instance->participants()->where('user_id', $user->id)->first();

        // Wenn User noch nicht beigetreten ist, Join-Dialog zeigen
        if (!$participant) {
            return view('lernsession.join', compact('instance', 'session'));
        }

        $ranking = $this->service->getLiveRankingData($instance, $user);

        return view('lernsession.live', compact('instance', 'session', 'participant', 'ranking'));
    }

    // API: Live-Ranking-Daten (JSON für Polling)
    public function ranking(LearningSessionInstance $instance)
    {
        $user = auth()->user();

        return response()->json(
            $this->service->getLiveRankingData($instance, $user)
        );
    }

    // API: Persönliche Stats (JSON)
    public function stats(LearningSessionInstance $instance)
    {
        $user = auth()->user();
        $participant = $instance->participants()->where('user_id', $user->id)->first();

        if (!$participant) {
            return response()->json(['error' => 'Nicht beigetreten'], 403);
        }

        return response()->json([
            'xp_earned' => $participant->xp_earned,
            'questions_answered' => $participant->questions_answered,
            'questions_correct' => $participant->questions_correct,
            'accuracy' => (float) $participant->accuracy_percent,
            'avg_time_ms' => $participant->avg_answer_time_ms,
            'rank_score' => (float) $participant->rank_score,
        ]);
    }

    // Lernabschnitte mit Namen
    private function getSectionNames(): array
    {
        return [
            1 => 'Grundlagen',
            2 => 'Sicherheit',
            3 => 'Arbeiten mit Leinen, Draht, Seilen, Ketten, Rollen und Winden',
            4 => 'Holzbearbeitung',
            5 => 'Gesteinsbearbeitung',
            6 => 'Metallbearbeitung',
            7 => 'Bewegen von Lasten',
            8 => 'Arbeiten am und auf dem Wasser',
            9 => 'Elektrik',
            10 => 'Verhalten in besonderen Lagen',
        ];
    }
}
