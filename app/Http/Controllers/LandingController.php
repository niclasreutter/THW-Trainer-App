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

        $stats = cache()->get('landing_stats');

        if (! $stats || $stats['users'] === 0) {
            $totalExams = ExamStatistic::count();
            $passedExams = ExamStatistic::where('is_passed', true)->count();
            $users = User::count();

            $questionsAnswered = QuestionStatistic::count()
                + LehrgangQuestionStatistic::count()
                + OrtsverbandLernpoolQuestionStatistic::count();

            $stats = [
                'users' => (int) floor($users / 50) * 50,
                'questions_answered' => (int) floor($questionsAnswered / 1000) * 1000,
                'exams_passed' => (int) floor($passedExams / 10) * 10,
                'pass_rate' => $totalExams > 0
                    ? round(($passedExams / $totalExams) * 100)
                    : 0,
            ];

            // Nur cachen wenn echte Daten vorhanden
            if ($users > 0) {
                cache()->put('landing_stats', $stats, 3600);
            }
        }

        return view('landing.home', compact('stats'));
    }
}
