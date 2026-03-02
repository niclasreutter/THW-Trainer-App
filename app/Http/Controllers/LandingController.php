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
        // In Development: Redirect eingeloggte User zum Dashboard (nur auf /, nicht auf /home)
        if (config('domains.development') && auth()->check() && ! request()->is('home')) {
            return redirect()->route('dashboard');
        }

        // Alten Cache löschen (Migration zu neuem Key)
        cache()->forget('landing_stats');

        // Kurzer Cache (5 Min) mit frischen DB-Daten — gleicher Ansatz wie StatisticsController
        $stats = cache()->remember('landing_stats_v2', 300, function () {
            $totalExams = ExamStatistic::count();
            $passedExams = ExamStatistic::where('is_passed', true)->count();
            $users = User::count();

            $questionsAnswered = QuestionStatistic::count()
                + LehrgangQuestionStatistic::count()
                + OrtsverbandLernpoolQuestionStatistic::count();

            return [
                'users' => (int) $users,
                'questions_answered' => (int) $questionsAnswered,
                'exams_passed' => (int) $passedExams,
                'pass_rate' => $totalExams > 0
                    ? (int) round(($passedExams / $totalExams) * 100)
                    : 0,
            ];
        });

        return view('landing.home', compact('stats'));
    }
}
