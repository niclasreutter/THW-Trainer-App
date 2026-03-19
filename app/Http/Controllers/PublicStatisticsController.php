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
use Illuminate\Support\Facades\DB;

class PublicStatisticsController extends Controller
{
    public function index()
    {
        $stats = Cache::remember('public_statistics_v3', 300, function () {
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

            $thirtyDaysAgo = Carbon::today()->subDays(29);

            // --- 30-day trend: Active users per day ---
            $activeByDay = $this->getActiveUsersByDay($thirtyDaysAgo);

            // --- 30-day trend: Questions answered per day ---
            $qsByDay = QuestionStatistic::where('created_at', '>=', $thirtyDaysAgo)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->pluck('count', 'date');

            $lehrgangByDay = LehrgangQuestionStatistic::where('created_at', '>=', $thirtyDaysAgo)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->pluck('count', 'date');

            $lernpoolByDay = OrtsverbandLernpoolQuestionStatistic::where('created_at', '>=', $thirtyDaysAgo)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->pluck('count', 'date');

            // --- 30-day trend: Success rate per day ---
            $totalByDay = QuestionStatistic::where('created_at', '>=', $thirtyDaysAgo)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
                ->groupBy('date')
                ->pluck('total', 'date');

            $correctByDay = QuestionStatistic::where('created_at', '>=', $thirtyDaysAgo)
                ->where('is_correct', true)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as correct')
                ->groupBy('date')
                ->pluck('correct', 'date');

            // --- 30-day trend: Exams per day ---
            $examsByDay = ExamStatistic::where('created_at', '>=', $thirtyDaysAgo)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
                ->groupBy('date')
                ->pluck('total', 'date');

            $examsPassedByDay = ExamStatistic::where('created_at', '>=', $thirtyDaysAgo)
                ->where('is_passed', true)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as passed')
                ->groupBy('date')
                ->pluck('passed', 'date');

            // Build 30-day arrays
            $chartData = [];
            $questionsPerDay = ['labels' => [], 'values' => []];
            $successRatePerDay = ['labels' => [], 'values' => []];
            $examsPerDay = ['labels' => [], 'total' => [], 'passed' => []];

            for ($i = 29; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $dateStr = $date->format('Y-m-d');
                $label = $date->format('d.m.');

                // Active users
                $chartData[] = [
                    'label' => $label,
                    'value' => (int) ($activeByDay[$dateStr] ?? 0),
                ];

                // Questions answered
                $dayQuestions = (int) ($qsByDay[$dateStr] ?? 0)
                    + (int) ($lehrgangByDay[$dateStr] ?? 0)
                    + (int) ($lernpoolByDay[$dateStr] ?? 0);
                $questionsPerDay['labels'][] = $label;
                $questionsPerDay['values'][] = $dayQuestions;

                // Success rate
                $dayTotal = (int) ($totalByDay[$dateStr] ?? 0);
                $dayCorrect = (int) ($correctByDay[$dateStr] ?? 0);
                $successRatePerDay['labels'][] = $label;
                $successRatePerDay['values'][] = $dayTotal > 0
                    ? round(($dayCorrect / $dayTotal) * 100, 1)
                    : null;

                // Exams per day
                $examsPerDay['labels'][] = $label;
                $examsPerDay['total'][] = (int) ($examsByDay[$dateStr] ?? 0);
                $examsPerDay['passed'][] = (int) ($examsPassedByDay[$dateStr] ?? 0);
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
                'questions_per_day' => $questionsPerDay,
                'success_rate_per_day' => $successRatePerDay,
                'exams_per_day' => $examsPerDay,
            ];
        });

        return view('landing.statistik', compact('stats'));
    }

    /**
     * Get unique active users per day using UNION query for efficiency.
     */
    private function getActiveUsersByDay(Carbon $since): array
    {
        $results = DB::select("
            SELECT date, COUNT(DISTINCT user_id) as active_users
            FROM (
                SELECT DATE(created_at) as date, user_id FROM question_statistics WHERE created_at >= ?
                UNION ALL
                SELECT DATE(created_at) as date, user_id FROM lehrgang_question_statistics WHERE created_at >= ?
                UNION ALL
                SELECT DATE(created_at) as date, user_id FROM ortsverband_lernpool_question_statistics WHERE created_at >= ?
            ) combined
            GROUP BY date
        ", [$since, $since, $since]);

        $map = [];
        foreach ($results as $row) {
            $map[$row->date] = (int) $row->active_users;
        }

        return $map;
    }
}
