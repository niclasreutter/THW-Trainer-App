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

            // Chart: Aktive Nutzer pro Tag (letzte 15 Tage)
            // Ein Nutzer zählt als aktiv wenn er mindestens eine Frage beantwortet hat
            $chartData = [];
            for ($i = 14; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $dateStr = $date->format('Y-m-d');

                // Eindeutige User-IDs die an diesem Tag aktiv waren
                $activeUserIds = collect();

                $activeUserIds = $activeUserIds->merge(
                    QuestionStatistic::whereDate('created_at', $dateStr)
                        ->distinct()->pluck('user_id')
                );
                $activeUserIds = $activeUserIds->merge(
                    LehrgangQuestionStatistic::whereDate('created_at', $dateStr)
                        ->distinct()->pluck('user_id')
                );
                $activeUserIds = $activeUserIds->merge(
                    OrtsverbandLernpoolQuestionStatistic::whereDate('created_at', $dateStr)
                        ->distinct()->pluck('user_id')
                );

                $chartData[] = [
                    'label' => $date->format('d.m.'),
                    'value' => (int) $activeUserIds->unique()->count(),
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

    /**
     * Neue Startseite (Dark Mode Landing Page).
     * Nutzt gleichen Stats-Cache wie home().
     */
    public function startseite()
    {
        // Alten Cache löschen (Migration zu neuem Key)
        cache()->forget('landing_stats');
        cache()->forget('landing_stats_v2');
        cache()->forget('landing_stats_v3');

        // Gleicher Cache-Key wie home() → identische Datenstruktur
        $stats = cache()->remember('landing_stats_v4', 300, function () {
            $totalExams = ExamStatistic::count();
            $passedExams = ExamStatistic::where('is_passed', true)->count();
            $users = User::count();

            $questionsAnswered = QuestionStatistic::count()
                + LehrgangQuestionStatistic::count()
                + OrtsverbandLernpoolQuestionStatistic::count();

            $chartData = [];
            for ($i = 14; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $dateStr = $date->format('Y-m-d');

                $activeUserIds = collect();
                $activeUserIds = $activeUserIds->merge(
                    QuestionStatistic::whereDate('created_at', $dateStr)->distinct()->pluck('user_id')
                );
                $activeUserIds = $activeUserIds->merge(
                    LehrgangQuestionStatistic::whereDate('created_at', $dateStr)->distinct()->pluck('user_id')
                );
                $activeUserIds = $activeUserIds->merge(
                    OrtsverbandLernpoolQuestionStatistic::whereDate('created_at', $dateStr)->distinct()->pluck('user_id')
                );

                $chartData[] = [
                    'label' => $date->format('d.m.'),
                    'value' => (int) $activeUserIds->unique()->count(),
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

        return view('landing.startseite', compact('stats'));
    }
}
