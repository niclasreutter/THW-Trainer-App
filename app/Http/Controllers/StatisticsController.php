<?php

namespace App\Http\Controllers;

use App\Models\ExamStatistic;
use App\Models\Question;
use App\Models\QuestionStatistic;
use App\Models\UserQuestionProgress;
use Illuminate\Support\Facades\Cache;

class StatisticsController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $totalQuestions = Cache::remember('total_questions_count', 3600, fn () => Question::count());

        // Overall progress
        $solvedTotal = UserQuestionProgress::where('user_id', $user->id)->count();
        $masteredTotal = UserQuestionProgress::where('user_id', $user->id)
            ->where('consecutive_correct', '>=', 3)->count();
        $progressPercent = $totalQuestions > 0 ? round(($solvedTotal / $totalQuestions) * 100) : 0;
        $masteryPercent = $totalQuestions > 0 ? round(($masteredTotal / $totalQuestions) * 100) : 0;

        // Hit rate
        $totalAnswered = QuestionStatistic::where('user_id', $user->id)->count();
        $totalCorrect = QuestionStatistic::where('user_id', $user->id)->where('is_correct', true)->count();
        $hitRate = $totalAnswered > 0 ? round(($totalCorrect / $totalAnswered) * 100) : 0;

        // Section analysis (batch queries to avoid N+1)
        $questionsBySection = Question::selectRaw('lernabschnitt, COUNT(*) as total')
            ->groupBy('lernabschnitt')->pluck('total', 'lernabschnitt');

        $masteredBySection = UserQuestionProgress::where('user_id', $user->id)
            ->where('consecutive_correct', '>=', 3)
            ->join('questions', 'user_question_progress.question_id', '=', 'questions.id')
            ->selectRaw('questions.lernabschnitt, COUNT(*) as cnt')
            ->groupBy('questions.lernabschnitt')->pluck('cnt', 'lernabschnitt');

        $answeredBySection = QuestionStatistic::where('question_statistics.user_id', $user->id)
            ->join('questions', 'question_statistics.question_id', '=', 'questions.id')
            ->selectRaw('questions.lernabschnitt, COUNT(*) as cnt, SUM(question_statistics.is_correct) as correct_cnt')
            ->groupBy('questions.lernabschnitt')->get()->keyBy('lernabschnitt');

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

        $sectionStats = [];
        for ($s = 1; $s <= 10; $s++) {
            $total = $questionsBySection[$s] ?? 0;
            $mastered = $masteredBySection[$s] ?? 0;
            $answered = $answeredBySection[$s]->cnt ?? 0;
            $correct = $answeredBySection[$s]->correct_cnt ?? 0;

            $sectionStats[] = [
                'section' => $s,
                'name' => $sectionNames[$s] ?? "Abschnitt {$s}",
                'total' => $total,
                'mastered' => $mastered,
                'percent' => $total > 0 ? round(($mastered / $total) * 100) : 0,
                'hit_rate' => $answered > 0 ? round(($correct / $answered) * 100) : 0,
            ];
        }

        $topSections = collect($sectionStats)->sortByDesc('mastered')->take(3)->values()->all();

        // Activity (last 30 days)
        $activity = QuestionStatistic::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(is_correct) as correct')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Weekly activity (last 7 days)
        $weeklyActivity = QuestionStatistic::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(is_correct) as correct')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Exam history
        $examHistory = ExamStatistic::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Spaced repetition stats
        $srStats = app(\App\Services\SpacedRepetitionService::class)->getStats($user->id);

        // SR interval distribution
        $intervalDistribution = UserQuestionProgress::where('user_id', $user->id)
            ->where('review_interval', '>', 0)
            ->selectRaw('review_interval, COUNT(*) as count')
            ->groupBy('review_interval')
            ->orderBy('review_interval')
            ->get();

        // 28-Tage Intensity-Heatmap (für Streak-Card)
        $heatPattern = [];
        for ($i = 0; $i < 28; $i++) {
            $date = now()->subDays(27 - $i)->format('Y-m-d');
            $dayData = $activity->get($date);
            $count = (int) ($dayData->count ?? 0);
            $intensity = $count === 0 ? 0 : ($count >= 20 ? 4 : ($count >= 10 ? 3 : ($count >= 5 ? 2 : 1)));
            $heatPattern[] = $intensity;
        }
        $streakDays = $user->streak_days ?? 0;

        return view('statistics', compact(
            'totalQuestions', 'solvedTotal', 'masteredTotal',
            'progressPercent', 'masteryPercent', 'hitRate',
            'sectionStats', 'topSections', 'activity', 'weeklyActivity',
            'examHistory', 'srStats', 'intervalDistribution',
            'heatPattern', 'streakDays'
        ));
    }
}
