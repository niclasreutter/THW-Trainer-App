<?php

namespace App\Http\Controllers;

use App\Models\ExamStatistic;
use App\Models\LehrgangQuestionStatistic;
use App\Models\OrtsverbandLernpoolQuestionStatistic;
use App\Models\QuestionStatistic;
use App\Models\User;

class LandingController extends Controller
{
    /**
     * Landing Page mit gecachten, anonymisierten Statistiken.
     * Nur aggregierte Zahlen — keine personenbezogenen Daten.
     */
    public function home()
    {
        // In Development: Redirect eingeloggte User zum Dashboard
        if (config('domains.development') && auth()->check()) {
            return redirect()->route('dashboard');
        }

        $stats = cache()->remember('landing_stats', 3600, function () {
            $totalExams = ExamStatistic::count();
            $passedExams = ExamStatistic::where('is_passed', true)->count();

            return [
                'users' => User::count(),
                'questions_answered' => QuestionStatistic::count()
                    + LehrgangQuestionStatistic::count()
                    + OrtsverbandLernpoolQuestionStatistic::count(),
                'exams_passed' => $passedExams,
                'pass_rate' => $totalExams > 0
                    ? round(($passedExams / $totalExams) * 100)
                    : 0,
            ];
        });

        return view('landing.home', compact('stats'));
    }
}
