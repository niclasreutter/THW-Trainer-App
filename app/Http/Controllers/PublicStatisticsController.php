<?php

namespace App\Http\Controllers;

use App\Models\ExamStatistic;
use App\Models\LehrgangQuestionStatistic;
use App\Models\OrtsverbandLernpoolQuestionStatistic;
use App\Models\Question;
use App\Models\QuestionStatistic;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class PublicStatisticsController extends Controller
{
    public function index()
    {
        $stats = Cache::remember('public_statistics_v1', 300, function () {
            $totalExams = ExamStatistic::count();
            $passedExams = ExamStatistic::where('is_passed', true)->count();
            $users = User::count();

            $questionsAnswered = QuestionStatistic::count()
                + LehrgangQuestionStatistic::count()
                + OrtsverbandLernpoolQuestionStatistic::count();

            $totalCorrect = QuestionStatistic::where('is_correct', true)->count();
            $totalAttempts = QuestionStatistic::count();
            $avgHitRate = $totalAttempts > 0 ? (int) round(($totalCorrect / $totalAttempts) * 100) : 0;

            // Questions per section
            $sectionCounts = Question::selectRaw('lernabschnitt, COUNT(*) as total')
                ->groupBy('lernabschnitt')
                ->orderBy('lernabschnitt')
                ->pluck('total', 'lernabschnitt')
                ->toArray();

            // Active users per day (last 15 days)
            $chartData = [];
            for ($i = 14; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $dateStr = $date->format('Y-m-d');

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
                'pass_rate' => $totalExams > 0 ? (int) round(($passedExams / $totalExams) * 100) : 0,
                'avg_hit_rate' => $avgHitRate,
                'total_questions' => Question::count(),
                'section_counts' => $sectionCounts,
                'chart' => $chartData,
            ];
        });

        return view('landing.statistik', compact('stats'));
    }
}
