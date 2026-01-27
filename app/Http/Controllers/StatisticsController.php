<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\QuestionStatistic;
use App\Models\LehrgangQuestionStatistic;
use App\Models\OrtsverbandLernpoolQuestionStatistic;
use App\Models\ExamStatistic;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    /**
     * Zeige öffentliche Statistiken basierend auf question_statistics, lehrgang_question_statistics und lernpool_question_statistics
     */
    public function index()
    {
        // Chart-Daten für die letzten 30 Tage
        $chartData = $this->getChartData();
        // Gesamt-Statistiken (Grundausbildung + Lehrgänge + Lernpools kombinieren)
        $totalAnswered = QuestionStatistic::count() +
                         LehrgangQuestionStatistic::count() +
                         OrtsverbandLernpoolQuestionStatistic::count();
        $totalAnsweredToday = QuestionStatistic::whereDate('created_at', today())->count() +
                              LehrgangQuestionStatistic::whereDate('created_at', today())->count() +
                              OrtsverbandLernpoolQuestionStatistic::whereDate('created_at', today())->count();
        $totalCorrect = QuestionStatistic::where('is_correct', true)->count() +
                        LehrgangQuestionStatistic::where('is_correct', true)->count() +
                        OrtsverbandLernpoolQuestionStatistic::where('is_correct', true)->count();
        $totalWrong = QuestionStatistic::where('is_correct', false)->count() +
                      LehrgangQuestionStatistic::where('is_correct', false)->count() +
                      OrtsverbandLernpoolQuestionStatistic::where('is_correct', false)->count();
        $successRate = $totalAnswered > 0 ? round(($totalCorrect / $totalAnswered) * 100, 1) : 0;
        $errorRate = $totalAnswered > 0 ? round(($totalWrong / $totalAnswered) * 100, 1) : 0;
        
        // Prüfungsstatistiken
        $totalExams = ExamStatistic::count();
        $passedExams = ExamStatistic::where('is_passed', true)->count();
        $failedExams = ExamStatistic::where('is_passed', false)->count();
        $examPassRate = $totalExams > 0 ? round(($passedExams / $totalExams) * 100, 1) : 0;
        
        // Top 10 der am häufigsten falsch beantworteten Fragen
        $topWrongQuestions = DB::table('question_statistics')
            ->select(
                'question_id',
                DB::raw('COUNT(*) as total_attempts'),
                DB::raw('SUM(CASE WHEN is_correct = 0 THEN 1 ELSE 0 END) as wrong_count'),
                DB::raw('SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) as correct_count'),
                DB::raw('ROUND((SUM(CASE WHEN is_correct = 0 THEN 1 ELSE 0 END) * 100.0 / COUNT(*)), 1) as error_rate')
            )
            ->groupBy('question_id')
            ->having('total_attempts', '>=', 5) // Mindestens 5 Versuche für aussagekräftige Statistik
            ->orderByDesc('error_rate')
            ->orderByDesc('total_attempts')
            ->limit(10)
            ->get();
        
        // Hole die Fragen-Details für Top Wrong
        $topWrongQuestionsWithDetails = $topWrongQuestions->map(function ($stat) {
            $question = Question::find($stat->question_id);
            return [
                'question' => $question,
                'total_attempts' => $stat->total_attempts,
                'wrong_count' => $stat->wrong_count,
                'correct_count' => $stat->correct_count,
                'error_rate' => $stat->error_rate,
            ];
        })->filter(function ($item) {
            return $item['question'] !== null; // Nur Fragen die noch existieren
        });
        
        // Top 10 der am häufigsten richtig beantworteten Fragen
        $topCorrectQuestions = DB::table('question_statistics')
            ->select(
                'question_id',
                DB::raw('COUNT(*) as total_attempts'),
                DB::raw('SUM(CASE WHEN is_correct = 0 THEN 1 ELSE 0 END) as wrong_count'),
                DB::raw('SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) as correct_count'),
                DB::raw('ROUND((SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) * 100.0 / COUNT(*)), 1) as success_rate')
            )
            ->groupBy('question_id')
            ->having('total_attempts', '>=', 5) // Mindestens 5 Versuche für aussagekräftige Statistik
            ->orderByDesc('success_rate')
            ->orderByDesc('total_attempts')
            ->limit(10)
            ->get();
        
        // Hole die Fragen-Details für Top Correct
        $topCorrectQuestionsWithDetails = $topCorrectQuestions->map(function ($stat) {
            $question = Question::find($stat->question_id);
            return [
                'question' => $question,
                'total_attempts' => $stat->total_attempts,
                'wrong_count' => $stat->wrong_count,
                'correct_count' => $stat->correct_count,
                'success_rate' => $stat->success_rate,
            ];
        })->filter(function ($item) {
            return $item['question'] !== null; // Nur Fragen die noch existieren
        });
        
        // Statistik nach Lernabschnitten
        $sectionStats = DB::table('question_statistics')
            ->join('questions', 'question_statistics.question_id', '=', 'questions.id')
            ->select(
                'questions.lernabschnitt',
                DB::raw('COUNT(*) as total_attempts'),
                DB::raw('SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) as correct_count'),
                DB::raw('SUM(CASE WHEN is_correct = 0 THEN 1 ELSE 0 END) as wrong_count'),
                DB::raw('ROUND((SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) * 100.0 / COUNT(*)), 1) as success_rate')
            )
            ->groupBy('questions.lernabschnitt')
            ->orderByRaw('CAST(questions.lernabschnitt AS UNSIGNED)')
            ->get();
        
        // Top-10 falsch beantwortete Fragen für doppelte Punkte cachen
        $topWrongQuestionIds = $topWrongQuestions->pluck('question_id')->toArray();
        \Cache::put('top_wrong_questions', $topWrongQuestionIds, 3600); // 1 Stunde Cache
        
        // Lehrgang-Statistiken - anonyme Daten
        $lehrgangStats = \App\Models\Lehrgang::withCount(['users'])
            ->with([
                'questions' => function($q) {
                    $q->select('id', 'lehrgang_id');
                }
            ])
            ->get()
            ->map(function($lehrgang) {
                // Berechne Statistiken für diesen Lehrgang
                $stats = DB::table('lehrgang_question_statistics')
                    ->whereIn('lehrgang_question_id', 
                        \App\Models\LehrgangQuestion::where('lehrgang_id', $lehrgang->id)->pluck('id')
                    )
                    ->get();
                
                $totalAnswered = $stats->count();
                $totalCorrect = $stats->where('is_correct', true)->count();
                $successRate = $totalAnswered > 0 ? round(($totalCorrect / $totalAnswered) * 100, 1) : 0;
                
                return (object)[
                    'id' => $lehrgang->id,
                    'name' => $lehrgang->lehrgang,
                    'users_count' => $lehrgang->users_count,
                    'questions_count' => $lehrgang->questions->count(),
                    'total_answered' => $totalAnswered,
                    'total_correct' => $totalCorrect,
                    'success_rate' => $successRate,
                ];
            })
            ->sortByDesc('total_answered')
            ->values();
        
        // Zusätzliche Statistiken für moderne Ansicht
        $examsToday = ExamStatistic::whereDate('created_at', today())->count();
        $examsThisWeek = ExamStatistic::where('created_at', '>=', now()->startOfWeek())->count();

        // Aktivität pro Wochentag (basierend auf beantworteten Fragen)
        $activityByWeekday = QuestionStatistic::select(
            DB::raw('DAYOFWEEK(created_at) as weekday'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('weekday')
            ->orderBy('weekday')
            ->get()
            ->pluck('count', 'weekday')
            ->toArray();

        // Peak-Stunden (wann wird am meisten gelernt)
        $peakHours = QuestionStatistic::select(
            DB::raw('HOUR(created_at) as hour'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->pluck('count', 'hour')
            ->toArray();

        return view('statistics', compact(
            'totalAnswered',
            'totalAnsweredToday',
            'totalCorrect',
            'totalWrong',
            'successRate',
            'errorRate',
            'totalExams',
            'passedExams',
            'failedExams',
            'examPassRate',
            'examsToday',
            'examsThisWeek',
            'topWrongQuestionsWithDetails',
            'topCorrectQuestionsWithDetails',
            'sectionStats',
            'lehrgangStats',
            'chartData',
            'activityByWeekday',
            'peakHours'
        ));
    }

    /**
     * Generiere Chart-Daten für die letzten 30 Tage
     */
    private function getChartData(): array
    {
        $labels = [];
        $questionsTotal = [];
        $questionsCorrect = [];
        $questionsWrong = [];
        $examsTotal = [];
        $examsPassed = [];

        // Letzte 30 Tage inkl. heute
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('d.m.');

            // Fragen-Statistiken für diesen Tag (alle Quellen kombiniert)
            $dayQuestionsGA = QuestionStatistic::whereDate('created_at', $date)->get();
            $dayQuestionsLG = LehrgangQuestionStatistic::whereDate('created_at', $date)->get();
            $dayQuestionsLP = OrtsverbandLernpoolQuestionStatistic::whereDate('created_at', $date)->get();

            $totalDay = $dayQuestionsGA->count() + $dayQuestionsLG->count() + $dayQuestionsLP->count();
            $correctDay = $dayQuestionsGA->where('is_correct', true)->count()
                        + $dayQuestionsLG->where('is_correct', true)->count()
                        + $dayQuestionsLP->where('is_correct', true)->count();

            $questionsTotal[] = $totalDay;
            $questionsCorrect[] = $correctDay;
            $questionsWrong[] = $totalDay - $correctDay;

            // Prüfungs-Statistiken für diesen Tag
            $dayExams = ExamStatistic::whereDate('created_at', $date)->get();
            $examsTotal[] = $dayExams->count();
            $examsPassed[] = $dayExams->where('is_passed', true)->count();
        }

        return [
            'labels' => $labels,
            'questionsTotal' => $questionsTotal,
            'questionsCorrect' => $questionsCorrect,
            'questionsWrong' => $questionsWrong,
            'examsTotal' => $examsTotal,
            'examsPassed' => $examsPassed,
        ];
    }
}

