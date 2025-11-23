<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LehrgangQuestionIssue;
use App\Models\LehrgangQuestion;
use Illuminate\Http\Request;

class LehrgangIssueController extends Controller
{
    /**
     * Zeige alle gemeldeten Fehler
     */
    public function index(Request $request)
    {
        // Filter nach Status
        $status = $request->get('status', 'all');
        
        $query = LehrgangQuestionIssue::with(['lehrgangQuestion.lehrgang', 'reportedByUser']);
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $issues = $query->orderByDesc('report_count')
                       ->orderByDesc('updated_at')
                       ->paginate(20);
        
        return view('admin.lehrgang-issues.index', [
            'issues' => $issues,
            'status' => $status,
            'totalIssues' => LehrgangQuestionIssue::count(),
            'openIssues' => LehrgangQuestionIssue::where('status', 'open')->count(),
            'inReviewIssues' => LehrgangQuestionIssue::where('status', 'in_review')->count(),
            'resolvedIssues' => LehrgangQuestionIssue::where('status', 'resolved')->count(),
        ]);
    }

    /**
     * Zeige Details einer Fehlermeldung
     */
    public function show(LehrgangQuestionIssue $issue)
    {
        $issue->load([
            'lehrgangQuestion.lehrgang', 
            'reportedByUser',
            'reports' => function($query) {
                $query->with('user')->orderBy('created_at', 'asc');
            }
        ]);
        return view('admin.lehrgang-issues.show', compact('issue'));
    }

    /**
     * Update Status und Admin Notes
     */
    public function update(Request $request, LehrgangQuestionIssue $issue)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,in_review,resolved,rejected',
            'admin_notes' => 'nullable|string|max:1000',
        ]);
        
        $issue->update($validated);
        
        return redirect()->route('admin.lehrgang-issues.show', $issue)
                       ->with('success', 'Fehlermeldung aktualisiert! ✓');
    }

    /**
     * Lösche eine Fehlermeldung
     */
    public function destroy(LehrgangQuestionIssue $issue)
    {
        $issue->delete();
        
        return redirect()->route('admin.lehrgang-issues.index')
                       ->with('success', 'Fehlermeldung gelöscht.');
    }
}
