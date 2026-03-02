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

        $stats = cache()->get('landing_stats');

        // Validate cached structure has all required keys with int values
        $requiredKeys = ['users', 'questions_answered', 'exams_passed', 'pass_rate'];
        $validCache = is_array($stats) && !array_diff($requiredKeys, array_keys($stats));

        if (! $validCache || ($stats['users'] ?? 0) === 0) {
            $totalExams = ExamStatistic::count();
            $passedExams = ExamStatistic::where('is_passed', true)->count();
            $users = User::count();

            $questionsAnswered = QuestionStatistic::count()
                + LehrgangQuestionStatistic::count()
                + OrtsverbandLernpoolQuestionStatistic::count();

            // Show real count when rounding would yield 0
            $stats = [
                'users' => (int) max($users, floor($users / 50) * 50),
                'questions_answered' => (int) max($questionsAnswered, floor($questionsAnswered / 1000) * 1000),
                'exams_passed' => (int) max($passedExams, floor($passedExams / 10) * 10),
                'pass_rate' => $totalExams > 0
                    ? (int) round(($passedExams / $totalExams) * 100)
                    : 0,
            ];

            // Nur cachen wenn echte Daten vorhanden
            if ($users > 0) {
                cache()->put('landing_stats', $stats, 3600);
            }
        }

        // Ensure all values are valid integers
        $stats = [
            'users' => (int) ($stats['users'] ?? 0),
            'questions_answered' => (int) ($stats['questions_answered'] ?? 0),
            'exams_passed' => (int) ($stats['exams_passed'] ?? 0),
            'pass_rate' => (int) ($stats['pass_rate'] ?? 0),
        ];

        return view('landing.home', compact('stats'));
    }
}
