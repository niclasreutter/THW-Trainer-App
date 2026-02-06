<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnboardingController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Bereits abgeschlossen? Weiter zum Dashboard
        if ($user->onboarding_completed) {
            return redirect()->route('dashboard');
        }

        $totalQuestions = cache()->remember('total_questions_count', 3600, function () {
            return \App\Models\Question::count();
        });

        return view('onboarding', compact('user', 'totalQuestions'));
    }

    public function complete(Request $request)
    {
        $user = Auth::user();
        $user->onboarding_completed = true;
        $user->save();

        return redirect()->route('dashboard');
    }

    public function skip(Request $request)
    {
        $user = Auth::user();
        $user->onboarding_completed = true;
        $user->save();

        return redirect()->route('dashboard');
    }
}
