<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Question;
use App\Models\QuestionStatistic;
use App\Models\ExamResult;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        // System Status
        $systemStatus = $this->getSystemStatus();
        
        // Benutzer Statistiken
        $totalUsers = User::count();
        $newUsersToday = User::whereDate('created_at', today())->count();
        $verifiedUsers = User::whereNotNull('email_verified_at')->count();
        $verificationRate = $totalUsers > 0 ? round(($verifiedUsers / $totalUsers) * 100, 1) : 0;
        
        // Fragen Statistiken
        $totalQuestions = Question::count();
        $learningSections = Question::distinct('lernabschnitt')->count();
        
        // Statistiken aus question_statistics Tabelle
        $totalAnsweredQuestions = QuestionStatistic::count();
        $totalCorrectAnswers = QuestionStatistic::where('is_correct', true)->count();
        $totalWrongAnswers = QuestionStatistic::where('is_correct', false)->count();
        $wrongAnswerRate = $totalAnsweredQuestions > 0 ? round(($totalWrongAnswers / $totalAnsweredQuestions) * 100, 1) : 0;
        
        // Benutzer Aktivität (30 Tage)
        $userActivity = $this->getUserActivity();
        
        // Lernfortschritt
        $learningProgress = $this->getLearningProgress();
        
        // Leaderboard Top-10
        $leaderboard = $this->getLeaderboard();
        
        return view('admin.dashboard', compact(
            'systemStatus',
            'totalUsers',
            'newUsersToday',
            'verifiedUsers',
            'verificationRate',
            'totalQuestions',
            'learningSections',
            'totalAnsweredQuestions',
            'totalCorrectAnswers',
            'totalWrongAnswers',
            'wrongAnswerRate',
            'userActivity',
            'learningProgress',
            'leaderboard'
        ));
    }
    
    private function getSystemStatus()
    {
        return [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'storage' => $this->checkStorage(),
            'backup' => $this->getLastBackup()
        ];
    }
    
    private function checkDatabase()
    {
        try {
            DB::connection()->getPdo();
            return ['status' => 'ok', 'message' => 'Verbindung erfolgreich'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Verbindung fehlgeschlagen'];
        }
    }
    
    private function checkCache()
    {
        try {
            Cache::put('test_key', 'test_value', 1);
            $value = Cache::get('test_key');
            Cache::forget('test_key');
            return ['status' => $value === 'test_value' ? 'ok' : 'error', 'message' => $value === 'test_value' ? 'Funktioniert' : 'Fehler'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Fehler'];
        }
    }
    
    private function checkStorage()
    {
        try {
            $disk = Storage::disk('local');
            $disk->put('test.txt', 'test');
            $exists = $disk->exists('test.txt');
            $disk->delete('test.txt');
            return ['status' => $exists ? 'ok' : 'error', 'message' => $exists ? 'Verfügbar' : 'Nicht verfügbar'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Fehler'];
        }
    }
    
    private function getLastBackup()
    {
        // Hier könnte man den letzten Backup-Zeitpunkt aus der Datenbank oder einer Datei lesen
        // Für jetzt verwenden wir das aktuelle Datum
        return now()->format('d.m.Y H:i');
    }
    
    private function getUserActivity()
    {
        // Verwende last_activity_date für echte Benutzer-Aktivität (auch falsche Antworten)
        // Das vermeidet Verfälschung durch Cronjob-Updates
        $today = User::whereDate('last_activity_date', today())->count();
        $thisWeek = User::whereBetween('last_activity_date', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $thisMonth = User::whereBetween('last_activity_date', [now()->startOfMonth(), now()->endOfMonth()])->count();
        
        return [
            'today' => $today,
            'this_week' => $thisWeek,
            'this_month' => $thisMonth
        ];
    }
    
    private function getLearningProgress()
    {
        $totalPoints = User::sum(DB::raw("JSON_LENGTH(achievements) * 100")); // Beispiel-Berechnung
        $usersWithAchievements = User::whereRaw("JSON_LENGTH(achievements) > 0")->count();
        
        // Durchschnittlicher Fortschritt basierend auf gelösten Fragen
        $totalPossibleQuestions = Question::count();
        $averageProgress = 0;
        
        if ($totalPossibleQuestions > 0) {
            $totalSolvedQuestions = User::sum(DB::raw("JSON_LENGTH(solved_questions)"));
            $totalUsers = User::count();
            if ($totalUsers > 0) {
                $averageSolvedPerUser = $totalSolvedQuestions / $totalUsers;
                $averageProgress = round(($averageSolvedPerUser / $totalPossibleQuestions) * 100, 1);
            }
        }
        
        return [
            'total_points' => $totalPoints,
            'users_with_achievements' => $usersWithAchievements,
            'average_progress' => $averageProgress
        ];
    }
    
    private function getLeaderboard()
    {
        // Hole Top-10 Benutzer nach Punkten und gelösten Fragen
        $users = User::select('id', 'name', 'email', 'solved_questions', 'exam_passed_count', 'points', 'level', 'streak_days')
            ->whereNotNull('solved_questions')
            ->get()
            ->map(function ($user) {
                $solvedCount = is_array($user->solved_questions) 
                    ? count($user->solved_questions) 
                    : (is_string($user->solved_questions) ? count(json_decode($user->solved_questions, true) ?? []) : 0);
                
                // Score berechnen: Punkte + (gelöste Fragen * 10) + (Prüfungen bestanden * 50) + (Streak * 5)
                $score = ($user->points ?? 0) + 
                        ($solvedCount * 10) + 
                        (($user->exam_passed_count ?? 0) * 50) + 
                        (($user->streak_days ?? 0) * 5);
                
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'points' => $user->points ?? 0,
                    'solved_questions' => $solvedCount,
                    'exam_passed' => $user->exam_passed_count ?? 0,
                    'level' => $user->level ?? 1,
                    'streak_days' => $user->streak_days ?? 0,
                    'score' => $score
                ];
            })
            ->sortByDesc('score')
            ->take(10)
            ->values();
        
        return $users;
    }
}
