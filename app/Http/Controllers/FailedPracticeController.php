<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Question;

class FailedPracticeController extends Controller
{
    public function show(Request $request)
    {
        $user = Auth::user();
        $failed = $this->ensureArray($user->exam_failed_questions);
        $progress = 0;
        $total = count($failed);
        $question = $total ? Question::find($failed[0]) : null;
        return view('failed_practice', compact('question', 'progress', 'total'));
    }

    public function submit(Request $request)
    {
        $user = Auth::user();
        $failed = $this->ensureArray($user->exam_failed_questions);
        $question = Question::findOrFail($request->question_id);
        $userAnswer = collect($request->answer ?? []);
        $solution = collect(explode(',', $question->loesung))->map(fn($s) => trim($s));
        $isCorrect = $userAnswer->sort()->values()->all() === $solution->sort()->values()->all();
        if ($isCorrect) {
            $failed = array_diff($failed, [$question->id]);
            $user->exam_failed_questions = array_values($failed);
            $user->save();
            return redirect()->route('failed.index');
        }
        $progress = 0;
        $total = count($failed);
        return view('failed_practice', [
            'question' => $question,
            'isCorrect' => $isCorrect,
            'userAnswer' => $userAnswer,
            'progress' => $progress,
            'total' => $total,
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
}
