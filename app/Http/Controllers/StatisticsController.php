<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\QuestionStatistic;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    /**
     * Zeige öffentliche Statistiken basierend auf question_statistics
     */
    public function index()
    {
        // Gesamt-Statistiken
        $totalAnswered = QuestionStatistic::count();
        $totalCorrect = QuestionStatistic::where('is_correct', true)->count();
        $totalWrong = QuestionStatistic::where('is_correct', false)->count();
        $successRate = $totalAnswered > 0 ? round(($totalCorrect / $totalAnswered) * 100, 1) : 0;
        $errorRate = $totalAnswered > 0 ? round(($totalWrong / $totalAnswered) * 100, 1) : 0;
        
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
            ->orderBy('questions.lernabschnitt')
            ->get();
        
        return view('statistics', compact(
            'totalAnswered',
            'totalCorrect',
            'totalWrong',
            'successRate',
            'errorRate',
            'topWrongQuestionsWithDetails',
            'topCorrectQuestionsWithDetails',
            'sectionStats'
        ));
    }
}

