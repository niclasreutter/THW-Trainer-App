<?php

namespace App\Http\Controllers;

use App\Models\ExamStatistic;
use App\Models\LehrgangQuestionStatistic;
use App\Models\OrtsverbandLernpoolQuestionStatistic;
use App\Models\QuestionStatistic;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
        cache()->forget('landing_stats_v2');
        cache()->forget('landing_stats_v3');

        // Kurzer Cache (5 Min) mit frischen DB-Daten
        $stats = cache()->remember('landing_stats_v4', 300, function () {
            $totalExams = ExamStatistic::count();
            $passedExams = ExamStatistic::where('is_passed', true)->count();
            $users = User::count();

            $questionsAnswered = QuestionStatistic::count()
                + LehrgangQuestionStatistic::count()
                + OrtsverbandLernpoolQuestionStatistic::count();

            // Chart: Fragen beantwortet pro Tag (letzte 15 Tage)
            $chartData = [];
            for ($i = 14; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $dateStr = $date->format('Y-m-d');

                $count = QuestionStatistic::whereDate('created_at', $dateStr)->count()
                    + LehrgangQuestionStatistic::whereDate('created_at', $dateStr)->count()
                    + OrtsverbandLernpoolQuestionStatistic::whereDate('created_at', $dateStr)->count();

                $chartData[] = [
                    'label' => $date->format('d.m.'),
                    'value' => (int) $count,
                ];
            }

            return [
                'users' => (int) (floor($users / 10) * 10),
                'questions_answered' => (int) (floor($questionsAnswered / 100) * 100),
                'exams_passed' => (int) (floor($passedExams / 10) * 10),
                'pass_rate' => $totalExams > 0
                    ? (int) round(($passedExams / $totalExams) * 100)
                    : 0,
                'chart' => $chartData,
            ];
        });

        return view('landing.home', compact('stats'));
    }
}
