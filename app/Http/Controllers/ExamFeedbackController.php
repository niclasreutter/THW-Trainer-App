<?php

namespace App\Http\Controllers;

use App\Models\ExamFeedback;
use Illuminate\Http\Request;

class ExamFeedbackController extends Controller
{
    public function show(string $token)
    {
        $feedback = ExamFeedback::where('token', $token)->firstOrFail();

        // Bereits ausgefüllt
        if ($feedback->passed !== null) {
            return view('exam-feedback', [
                'feedback' => $feedback,
                'alreadySubmitted' => true,
            ]);
        }

        return view('exam-feedback', [
            'feedback' => $feedback,
            'alreadySubmitted' => false,
        ]);
    }

    public function store(Request $request, string $token)
    {
        $feedback = ExamFeedback::where('token', $token)->firstOrFail();

        if ($feedback->passed !== null) {
            return redirect()->route('exam-feedback.show', $token)
                ->with('info', 'Du hast bereits Feedback gegeben. Vielen Dank!');
        }

        $validated = $request->validate([
            'passed' => ['required', 'boolean'],
            'feedback' => ['nullable', 'string', 'max:2000'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'publish_mode' => ['required', 'in:anonymous,named'],
        ]);

        $feedback->update($validated);

        return view('exam-feedback', [
            'feedback' => $feedback->fresh(),
            'alreadySubmitted' => true,
            'justSubmitted' => true,
        ]);
    }
}
