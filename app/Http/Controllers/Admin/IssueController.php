<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LehrgangQuestionIssue;
use App\Models\LehrgangQuestionIssueReport;
use App\Models\QuestionIssue;
use App\Models\QuestionIssueReport;
use Illuminate\Http\Request;

class IssueController extends Controller
{
    /**
     * Zeige alle gemeldeten Fehler (Lehrgänge + normale Fragen)
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $source = $request->get('source', 'all');

        // Lehrgang Issues
        $lehrgangQuery = LehrgangQuestionIssue::with(['lehrgangQuestion.lehrgang', 'reportedByUser']);

        if ($status !== 'all') {
            $lehrgangQuery->where('status', $status);
        }

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
        });

        // Normale Fragen Issues
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
        });

        // Zusammenführen und filtern nach Quelle
        $allIssues = $lehrgangIssues->merge($questionIssues);

        if ($source === 'lehrgang') {
            $allIssues = $allIssues->where('type', 'lehrgang');
        } elseif ($source === 'question') {
            $allIssues = $allIssues->where('type', 'question');
        }

        // Sortieren
        $allIssues = $allIssues->sortByDesc('report_count')->sortByDesc('updated_at')->values();

        // Stats
        $totalLehrgang = LehrgangQuestionIssue::count();
        $totalQuestion = QuestionIssue::count();
        $openLehrgang = LehrgangQuestionIssue::where('status', 'open')->count();
        $openQuestion = QuestionIssue::where('status', 'open')->count();

        return view('admin.issues.index', [
            'issues' => $allIssues,
            'status' => $status,
            'source' => $source,
            'totalIssues' => $totalLehrgang + $totalQuestion,
            'openIssues' => $openLehrgang + $openQuestion,
            'inReviewIssues' => LehrgangQuestionIssue::where('status', 'in_review')->count() + QuestionIssue::where('status', 'in_review')->count(),
            'resolvedIssues' => LehrgangQuestionIssue::where('status', 'resolved')->count() + QuestionIssue::where('status', 'resolved')->count(),
        ]);
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
                'reports' => fn($q) => $q->with('user')->orderBy('created_at', 'asc'),
            ])->findOrFail($id);

            $question = $issue->question;
            $contextLabel = $question ? 'Grundausbildung' : null;
            $contextDetails = $question ? [
                'Lernabschnitt' => $question->lernabschnitt ?? '-',
                'Frage-Nr.' => $question->nummer ?? '-',
            ] : [];
        }

        return view('admin.issues.show', [
            'issue' => $issue,
            'type' => $type,
            'question' => $question,
            'contextLabel' => $contextLabel,
            'contextDetails' => $contextDetails,
        ]);
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

        return redirect()->route('admin.issues.show', ['issue' => $id, 'type' => $type])
                       ->with('success', 'Status aktualisiert!');
    }

    /**
     * Neuer Kommentar (Notiz) im Aktivitäts-Feed
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

        $this->createActivity($issue, $type, 'note', $validated['message']);

        return redirect()->route('admin.issues.show', ['issue' => $id, 'type' => $type])
                       ->with('success', 'Kommentar hinzugefügt!');
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
     * Erstellt einen Aktivitäts-Eintrag (Report, Notiz oder Status-Wechsel)
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
}
