<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamFeedback;
use Illuminate\Http\Request;

class ExamFeedbackController extends Controller
{
    public function index(Request $request)
    {
        $query = ExamFeedback::with('user')
            ->whereNotNull('passed');

        if ($request->filled('result')) {
            $query->where('passed', $request->result === 'passed');
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        $feedbacks = $query->latest()->paginate(20)->withQueryString();

        $allFeedbacks = ExamFeedback::whereNotNull('passed');
        $stats = [
            'total' => (clone $allFeedbacks)->count(),
            'passed' => (clone $allFeedbacks)->where('passed', true)->count(),
            'failed' => (clone $allFeedbacks)->where('passed', false)->count(),
            'with_text' => (clone $allFeedbacks)->whereNotNull('feedback')->where('feedback', '!=', '')->count(),
            'avg_rating' => (clone $allFeedbacks)->whereNotNull('rating')->avg('rating'),
            'rating_count' => (clone $allFeedbacks)->whereNotNull('rating')->count(),
        ];

        $stats['pass_rate'] = $stats['total'] > 0
            ? round(($stats['passed'] / $stats['total']) * 100, 1)
            : 0;

        return view('admin.exam-feedback.index', compact('feedbacks', 'stats'));
    }

    public function destroy(ExamFeedback $examFeedback)
    {
        $examFeedback->delete();

        return redirect()->route('admin.exam-feedback.index')
            ->with('success', 'Feedback wurde gelöscht.');
    }
}
