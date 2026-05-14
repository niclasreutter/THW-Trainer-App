<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\IssueAssignedMail;
use App\Mail\IssueMentionedMail;
use App\Models\LehrgangQuestionIssue;
use App\Models\LehrgangQuestionIssueReport;
use App\Models\Notification;
use App\Models\QuestionIssue;
use App\Models\QuestionIssueReport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class IssueController extends Controller
{
    /**
     * Zeige alle gemeldeten Fehler (Lehrgänge + normale Fragen)
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $source = $request->get('source', 'all');
        $sort = $this->normalizeSort($request->get('sort'));

        $allIssues = $this->buildFilteredIssues($status, $source, $sort);

        // Stats
        $totalLehrgang = LehrgangQuestionIssue::count();
        $totalQuestion = QuestionIssue::count();
        $openLehrgang = LehrgangQuestionIssue::where('status', 'open')->count();
        $openQuestion = QuestionIssue::where('status', 'open')->count();

        return view('admin.issues.index', [
            'issues' => $allIssues,
            'status' => $status,
            'source' => $source,
            'sort' => $sort,
            'totalIssues' => $totalLehrgang + $totalQuestion,
            'openIssues' => $openLehrgang + $openQuestion,
            'inReviewIssues' => LehrgangQuestionIssue::where('status', 'in_review')->count() + QuestionIssue::where('status', 'in_review')->count(),
            'resolvedIssues' => LehrgangQuestionIssue::where('status', 'resolved')->count() + QuestionIssue::where('status', 'resolved')->count(),
        ]);
    }

    /**
     * Erlaubte Sortier-Werte. Default ist 'recent' (zuletzt aktualisiert),
     * Alternative ist 'reports' (meiste Meldungen zuerst).
     */
    private function normalizeSort(?string $sort): string
    {
        return in_array($sort, ['recent', 'reports'], true) ? $sort : 'recent';
    }

    /**
     * Liefert die zusammengeführte, gefilterte und sortierte Issue-Liste
     * (gleiche Logik wie auf der Index-Seite — wird auch für Prev/Next-Navigation
     * in der Detail-Ansicht genutzt).
     */
    private function buildFilteredIssues(string $status, string $source, string $sort = 'recent'): \Illuminate\Support\Collection
    {
        $lehrgangQuery = LehrgangQuestionIssue::with(['lehrgangQuestion.lehrgang', 'reportedByUser']);
        if ($status !== 'all') {
            $lehrgangQuery->where('status', $status);
        }

        // toBase(): erzwingt eine Support\Collection. Ohne diesen Cast bleibt eine leere
        // Eloquent\Collection nach map() eine Eloquent\Collection, und das folgende
        // ->merge() ruft dann getKey() auf stdClass-Items auf (was crasht).
        $lehrgangIssues = $lehrgangQuery->get()->map(function ($issue) {
            return (object) [
                'id' => $issue->id,
                'type' => 'lehrgang',
                'question_text' => $issue->lehrgangQuestion?->frage ?? 'Gelöscht',
                'question_id' => $issue->lehrgang_question_id,
                'context' => $issue->lehrgangQuestion?->lehrgang?->lehrgang ?? null,
                'report_count' => $issue->report_count,
                'status' => $issue->status,
                'reported_by' => $issue->reportedByUser?->name ?? 'Anonym',
                'updated_at' => $issue->updated_at,
                'created_at' => $issue->created_at,
            ];
        })->toBase();

        $questionQuery = QuestionIssue::with(['question', 'reportedByUser']);
        if ($status !== 'all') {
            $questionQuery->where('status', $status);
        }

        $questionIssues = $questionQuery->get()->map(function ($issue) {
            $q = $issue->question;
            return (object) [
                'id' => $issue->id,
                'type' => 'question',
                'question_text' => $q?->frage ?? 'Gelöscht',
                'question_id' => $issue->question_id,
                'context' => $q ? 'LA ' . ($q->lernabschnitt ?? '-') . '.' . ($q->nummer ?? '-') : null,
                'report_count' => $issue->report_count,
                'status' => $issue->status,
                'reported_by' => $issue->reportedByUser?->name ?? 'Anonym',
                'updated_at' => $issue->updated_at,
                'created_at' => $issue->created_at,
            ];
        })->toBase();

        $allIssues = $lehrgangIssues->merge($questionIssues);

        if ($source === 'lehrgang') {
            $allIssues = $allIssues->where('type', 'lehrgang');
        } elseif ($source === 'question') {
            $allIssues = $allIssues->where('type', 'question');
        }

        // sortBy ist stabil — die letzte Sortierung bestimmt den Primärschlüssel,
        // die vorherige bleibt als Tiebreaker erhalten.
        if ($sort === 'reports') {
            return $allIssues->sortByDesc('updated_at')->sortByDesc('report_count')->values();
        }

        return $allIssues->sortByDesc('report_count')->sortByDesc('updated_at')->values();
    }

    /**
     * Zeige Details einer Fehlermeldung
     */
    public function show(Request $request, $id)
    {
        $type = $request->get('type', 'lehrgang');

        if ($type === 'lehrgang') {
            $issue = LehrgangQuestionIssue::with([
                'lehrgangQuestion.lehrgang',
                'reportedByUser',
                'assignee',
                'reports' => fn($q) => $q->with('user')->orderBy('created_at', 'asc'),
            ])->findOrFail($id);

            $question = $issue->lehrgangQuestion;
            $lehrgangName = $question?->lehrgang?->lehrgang;
            $contextLabel = $lehrgangName;
            $contextDetails = $question ? [
                'Lehrgang' => $lehrgangName ?? '-',
                'Lernabschnitt' => $question->lernabschnitt,
                'Frage-Nr.' => $question->nummer,
            ] : [];
        } else {
            $issue = QuestionIssue::with([
                'question',
                'reportedByUser',
                'assignee',
                'reports' => fn($q) => $q->with('user')->orderBy('created_at', 'asc'),
            ])->findOrFail($id);

            $question = $issue->question;
            $contextLabel = $question ? 'Grundausbildung' : null;
            $contextDetails = $question ? [
                'Lernabschnitt' => $question->lernabschnitt ?? '-',
                'Frage-Nr.' => $question->nummer ?? '-',
            ] : [];
        }

        // Mentionable users (Admins + Contributors) für den Frontend-State
        $mentionables = User::query()
            ->whereIn('useroll', ['admin', 'contributor'])
            ->orderBy('name')
            ->get(['id', 'name', 'useroll', 'avatar_path'])
            ->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'role' => $u->useroll === 'admin' ? 'Admin' : 'Contributor',
                'avatar_url' => $u->avatar_url,
            ])
            ->values();

        // Prev/Next-Navigation: gleicher Filter und gleiche Sortierung wie auf der Index-Seite.
        // Filter-Werte kommen entweder aus der URL (verlinkt von der Liste) oder defaulten auf 'all'.
        $filterStatus = $request->get('status', 'all');
        $filterSource = $request->get('source', 'all');
        $filterSort = $this->normalizeSort($request->get('sort'));

        $prevIssue = null;
        $nextIssue = null;
        $listPosition = null;
        $listTotal = null;

        $siblings = $this->buildFilteredIssues($filterStatus, $filterSource, $filterSort);
        $currentIndex = $siblings->search(
            fn ($candidate) => $candidate->type === $type && (int) $candidate->id === (int) $id
        );

        if ($currentIndex !== false) {
            $listPosition = $currentIndex + 1;
            $listTotal = $siblings->count();
            if ($currentIndex > 0) {
                $prevIssue = $siblings[$currentIndex - 1];
            }
            if ($currentIndex < $siblings->count() - 1) {
                $nextIssue = $siblings[$currentIndex + 1];
            }
        }

        return view('admin.issues.show', [
            'issue' => $issue,
            'type' => $type,
            'question' => $question,
            'contextLabel' => $contextLabel,
            'contextDetails' => $contextDetails,
            'mentionables' => $mentionables,
            'prevIssue' => $prevIssue,
            'nextIssue' => $nextIssue,
            'listPosition' => $listPosition,
            'listTotal' => $listTotal,
            'filterStatus' => $filterStatus,
            'filterSource' => $filterSource,
            'filterSort' => $filterSort,
        ]);
    }

    /**
     * JSON-Endpoint für @mention-Autocomplete im Kommentar-Composer
     */
    public function mentionables(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $users = User::query()
            ->whereIn('useroll', ['admin', 'contributor'])
            ->when($q !== '', fn ($qb) => $qb->where('name', 'like', '%' . $q . '%'))
            ->orderBy('name')
            ->limit(8)
            ->get(['id', 'name', 'useroll', 'avatar_path'])
            ->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'role' => $u->useroll === 'admin' ? 'Admin' : 'Contributor',
                'avatar_url' => $u->avatar_url,
            ]);

        return response()->json(['users' => $users]);
    }

    /**
     * Update Status (loggt Statuswechsel als Aktivität)
     */
    public function update(Request $request, $id)
    {
        $type = $request->get('type', 'lehrgang');

        $validated = $request->validate([
            'status' => 'required|in:open,in_review,resolved,rejected',
        ]);

        $issue = $type === 'lehrgang'
            ? LehrgangQuestionIssue::findOrFail($id)
            : QuestionIssue::findOrFail($id);

        $oldStatus = $issue->status;
        $newStatus = $validated['status'];

        if ($oldStatus !== $newStatus) {
            $issue->update(['status' => $newStatus]);

            $this->createActivity($issue, $type, 'status_change', null, [
                'old' => $oldStatus,
                'new' => $newStatus,
            ]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['ok' => true, 'status' => $newStatus]);
        }

        return redirect()->route('admin.issues.show', $this->showRouteParams($request, $id, $type))
                       ->with('success', 'Status aktualisiert!');
    }

    /**
     * Weist die Fehlermeldung einem Bearbeiter zu (oder hebt die Zuweisung auf).
     */
    public function assign(Request $request, $id)
    {
        $type = $request->get('type', 'lehrgang');

        $validated = $request->validate([
            'assignee_id' => 'nullable|integer|exists:users,id',
        ]);

        $issue = $type === 'lehrgang'
            ? LehrgangQuestionIssue::with('assignee')->findOrFail($id)
            : QuestionIssue::with('assignee')->findOrFail($id);

        $oldId = $issue->assignee_id;
        $newId = $validated['assignee_id'] ?? null;

        if ((int) $oldId === (int) $newId) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['ok' => true, 'unchanged' => true]);
            }
            return redirect()->route('admin.issues.show', $this->showRouteParams($request, $id, $type));
        }

        $issue->update(['assignee_id' => $newId]);

        $oldUser = $oldId ? User::find($oldId) : null;
        $newUser = $newId ? User::find($newId) : null;

        $this->createActivity($issue, $type, 'assignment', null, [
            'old_id' => $oldId,
            'old_name' => $oldUser?->name,
            'new_id' => $newId,
            'new_name' => $newUser?->name,
        ]);

        // Notify the new assignee (unless they assigned themselves)
        if ($newUser && (int) $newUser->id !== (int) auth()->id()) {
            $issueUrl = route('admin.issues.show', ['issue' => $issue->id, 'type' => $type]);

            Notification::create([
                'user_id' => $newUser->id,
                'type' => 'issue_assigned',
                'title' => 'Fehlermeldung zugewiesen',
                'message' => (auth()->user()->name ?? 'Jemand') . ' hat dir Fehlermeldung #' . $issue->id . ' zugewiesen.',
                'icon' => 'bi-person-check',
                'data' => [
                    'issue_id' => $issue->id,
                    'issue_type' => $type,
                    'url' => $issueUrl,
                ],
            ]);

            if (!empty($newUser->email)) {
                try {
                    Mail::to($newUser->email)->queue(new IssueAssignedMail(
                        assignee: $newUser,
                        assignedBy: auth()->user(),
                        issueId: $issue->id,
                        issueType: $type,
                        questionText: $this->questionTextFor($issue, $type),
                        issueUrl: $issueUrl,
                    ));
                } catch (\Throwable $e) {
                    Log::warning('IssueAssignedMail konnte nicht in die Queue gelegt werden', [
                        'issue_id' => $issue->id,
                        'user_id' => $newUser->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'assignee' => $newUser ? [
                    'id' => $newUser->id,
                    'name' => $newUser->name,
                    'role' => $newUser->useroll === 'admin' ? 'Admin' : 'Contributor',
                    'avatar_url' => $newUser->avatar_url,
                ] : null,
            ]);
        }

        return redirect()->route('admin.issues.show', $this->showRouteParams($request, $id, $type))
                       ->with('success', 'Zuweisung aktualisiert.');
    }

    /**
     * Neuer Kommentar (Notiz) im Aktivitäts-Feed
     * — parst @mentions und benachrichtigt die erwähnten User
     */
    public function storeComment(Request $request, $id)
    {
        $type = $request->get('type', 'lehrgang');

        $validated = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $issue = $type === 'lehrgang'
            ? LehrgangQuestionIssue::findOrFail($id)
            : QuestionIssue::findOrFail($id);

        // Mentions extrahieren: alle "@Vorname" Patterns finden und gegen User matchen
        $mentionedUserIds = $this->extractMentionedUserIds($validated['message']);

        $this->createActivity($issue, $type, 'note', $validated['message'], [
            'mentioned_user_ids' => $mentionedUserIds,
        ]);

        // Benachrichtigungen + E-Mail an erwähnte User (nicht an sich selbst)
        if (!empty($mentionedUserIds)) {
            $currentUserId = auth()->id();
            $author = auth()->user();
            $authorName = $author->name ?? 'Jemand';
            $issueUrl = route('admin.issues.show', ['issue' => $issue->id, 'type' => $type]);
            $questionText = $this->questionTextFor($issue, $type);

            foreach (User::whereIn('id', $mentionedUserIds)->get() as $mentionedUser) {
                if ((int) $mentionedUser->id === (int) $currentUserId) {
                    continue;
                }

                Notification::create([
                    'user_id' => $mentionedUser->id,
                    'type' => 'issue_mention',
                    'title' => 'Erwähnung in Fehlermeldung',
                    'message' => $authorName . ' hat dich in Fehlermeldung #' . $issue->id . ' erwähnt.',
                    'icon' => 'bi-at',
                    'data' => [
                        'issue_id' => $issue->id,
                        'issue_type' => $type,
                        'url' => $issueUrl,
                    ],
                ]);

                if (!empty($mentionedUser->email)) {
                    try {
                        Mail::to($mentionedUser->email)->queue(new IssueMentionedMail(
                            mentioned: $mentionedUser,
                            author: $author,
                            issueId: $issue->id,
                            issueType: $type,
                            questionText: $questionText,
                            commentMessage: $validated['message'],
                            issueUrl: $issueUrl,
                        ));
                    } catch (\Throwable $e) {
                        Log::warning('IssueMentionedMail konnte nicht in die Queue gelegt werden', [
                            'issue_id' => $issue->id,
                            'user_id' => $mentionedUser->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }
        }

        return redirect()->route('admin.issues.show', $this->showRouteParams($request, $id, $type))
                       ->with('success', 'Kommentar hinzugefügt.');
    }

    /**
     * Lösche eine Fehlermeldung
     */
    public function destroy(Request $request, $id)
    {
        $type = $request->get('type', 'lehrgang');

        if ($type === 'lehrgang') {
            LehrgangQuestionIssue::findOrFail($id)->delete();
        } else {
            QuestionIssue::findOrFail($id)->delete();
        }

        return redirect()->route('admin.issues.index')
                       ->with('success', 'Fehlermeldung gelöscht.');
    }

    /**
     * Erzeugt die Route-Parameter für admin.issues.show und behält dabei
     * vorhandene Listen-Filter (status, source) bei, damit die Prev/Next-
     * Navigation nach einem POST-Redirect denselben Kontext behält.
     */
    private function showRouteParams(Request $request, $id, string $type): array
    {
        $params = ['issue' => $id, 'type' => $type];
        foreach (['status', 'source'] as $key) {
            $value = $request->input($key, $request->query($key));
            if ($value !== null && $value !== '' && $value !== 'all') {
                $params[$key] = $value;
            }
        }
        $sort = $request->input('sort', $request->query('sort'));
        if ($sort !== null && $sort !== '' && $sort !== 'recent') {
            $params['sort'] = $sort;
        }
        return $params;
    }

    /**
     * Erstellt einen Aktivitäts-Eintrag (Report, Notiz, Statuswechsel oder Zuweisung)
     */
    private function createActivity($issue, string $type, string $activityType, ?string $message, ?array $meta = null): void
    {
        $userId = auth()->id();

        if ($type === 'lehrgang') {
            LehrgangQuestionIssueReport::create([
                'lehrgang_question_issue_id' => $issue->id,
                'user_id' => $userId,
                'type' => $activityType,
                'message' => $message,
                'meta' => $meta,
            ]);
        } else {
            QuestionIssueReport::create([
                'question_issue_id' => $issue->id,
                'user_id' => $userId,
                'type' => $activityType,
                'message' => $message,
                'meta' => $meta,
            ]);
        }
    }

    /**
     * Liefert den Fragentext zum Issue (für E-Mails) — null wenn die Frage gelöscht ist.
     */
    private function questionTextFor($issue, string $type): ?string
    {
        if ($type === 'lehrgang') {
            return $issue->lehrgangQuestion?->frage
                ?? optional($issue->lehrgangQuestion()->first())?->frage;
        }
        return $issue->question?->frage
            ?? optional($issue->question()->first())?->frage;
    }

    /**
     * Extrahiert User-IDs aus @mentions in einem Kommentar-Text.
     * Akzeptiert "@Vorname" oder "@Vorname Nachname" — best effort.
     */
    private function extractMentionedUserIds(string $text): array
    {
        // Mentions als Strings finden — Buchstaben + ggf. ein Leerzeichen + Nachname
        preg_match_all('/@([A-Za-zÄÖÜäöüß]+(?:\s[A-Za-zÄÖÜäöüß]+)?)/u', $text, $matches);

        if (empty($matches[1])) {
            return [];
        }

        $candidates = array_unique($matches[1]);
        $mentionables = User::whereIn('useroll', ['admin', 'contributor'])
            ->get(['id', 'name']);

        $ids = [];
        foreach ($candidates as $candidate) {
            $candidate = trim($candidate);
            $firstPart = explode(' ', $candidate)[0];

            // Erst nach vollem Namen suchen (case-insensitive)
            $match = $mentionables->first(fn ($u) => mb_strtolower($u->name) === mb_strtolower($candidate));
            if (!$match) {
                // sonst nach Vorname-Prefix
                $match = $mentionables->first(fn ($u) =>
                    mb_strtolower(explode(' ', $u->name)[0]) === mb_strtolower($firstPart)
                );
            }
            if ($match) {
                $ids[$match->id] = true;
            }
        }

        return array_keys($ids);
    }
}
