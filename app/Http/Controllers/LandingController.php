<?php

namespace App\Http\Controllers;

use App\Models\ExamStatistic;
use App\Models\LehrgangQuestionStatistic;
use App\Models\OrtsverbandLernpoolQuestionStatistic;
use App\Models\Question;
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
        // In Development: Redirect eingeloggte User zum Dashboard (nur auf /, nicht auf /home)
        if (config('domains.development') && auth()->check() && ! request()->is('home')) {
            return redirect()->route('dashboard');
        }

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

    /**
     * THW Theorie Sub-Landingpage — SEO-optimiert für "thw theorie" Keywords.
     */
    public function thwTheorie()
    {
        $sectionNames = [
            1 => 'Das THW im Gefüge des Zivil- und Katastrophenschutzes',
            2 => 'Arbeitssicherheit und Gesundheitsschutz',
            3 => 'Arbeiten mit Leinen, Drahtseilen, Ketten, Rund- und Bandschlingen',
            4 => 'Arbeiten mit Leitern',
            5 => 'Stromerzeugung und Beleuchtung',
            6 => 'Metall-, Holz- und Steinbearbeitung',
            7 => 'Bewegen von Lasten',
            8 => 'Arbeiten am und auf dem Wasser',
            9 => 'Einsatzgrundlagen',
            10 => 'Grundlagen der Rettung und Bergung',
        ];

        $sections = cache()->remember('landing_thw_theorie_sections', 3600, function () use ($sectionNames) {
            $counts = Question::selectRaw('lernabschnitt, COUNT(*) as total')
                ->groupBy('lernabschnitt')
                ->pluck('total', 'lernabschnitt');

            $result = [];
            foreach ($sectionNames as $nr => $name) {
                $result[] = [
                    'nr' => $nr,
                    'name' => $name,
                    'count' => $counts->get((string) $nr, 0),
                ];
            }

            return $result;
        });

        $totalQuestions = collect($sections)->sum('count');

        // Stats aus dem gleichen Cache wie Startseite
        $stats = cache()->get('landing_stats_v4');

        return view('landing.thw-theorie', compact('sections', 'totalQuestions', 'stats'));
    }

    /**
     * THW Prüfungsfragen Sub-Landingpage — SEO-optimiert für "thw prüfungsfragen" Keywords.
     */
    public function thwPruefungsfragen()
    {
        $totalQuestions = cache()->remember('landing_total_questions', 3600, function () {
            return Question::count();
        });

        $stats = cache()->get('landing_stats_v4');

        return view('landing.thw-pruefungsfragen', compact('totalQuestions', 'stats'));
    }

    /**
     * THW Theorieprüfung Sub-Landingpage — SEO-optimiert für "thw theorieprüfung" Keywords.
     */
    public function thwTheoriepruefung()
    {
        $totalQuestions = cache()->remember('landing_total_questions', 3600, function () {
            return Question::count();
        });

        $stats = cache()->get('landing_stats_v4');

        return view('landing.thw-theoriepruefung', compact('totalQuestions', 'stats'));
    }

    /**
     * THW Grundausbildung Sub-Landingpage — SEO-optimiert für "thw grundausbildung" Keywords.
     */
    public function thwGrundausbildung()
    {
        $sectionNames = [
            1 => 'Das THW im Gefüge des Zivil- und Katastrophenschutzes',
            2 => 'Arbeitssicherheit und Gesundheitsschutz',
            3 => 'Arbeiten mit Leinen, Drahtseilen, Ketten, Rund- und Bandschlingen',
            4 => 'Arbeiten mit Leitern',
            5 => 'Stromerzeugung und Beleuchtung',
            6 => 'Metall-, Holz- und Steinbearbeitung',
            7 => 'Bewegen von Lasten',
            8 => 'Arbeiten am und auf dem Wasser',
            9 => 'Einsatzgrundlagen',
            10 => 'Grundlagen der Rettung und Bergung',
        ];

        $sections = cache()->remember('landing_thw_theorie_sections', 3600, function () use ($sectionNames) {
            $counts = Question::selectRaw('lernabschnitt, COUNT(*) as total')
                ->groupBy('lernabschnitt')
                ->pluck('total', 'lernabschnitt');

            $result = [];
            foreach ($sectionNames as $nr => $name) {
                $result[] = [
                    'nr' => $nr,
                    'name' => $name,
                    'count' => $counts->get((string) $nr, 0),
                ];
            }

            return $result;
        });

        $totalQuestions = collect($sections)->sum('count');
        $stats = cache()->get('landing_stats_v4');

        return view('landing.thw-grundausbildung', compact('sections', 'totalQuestions', 'stats'));
    }
}
