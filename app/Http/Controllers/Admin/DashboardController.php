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

        // Chart-Daten für 30 Tage
        $chartData = $this->getChartData();

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
            'leaderboard',
            'chartData'
        ));
    }
    
    private function getSystemStatus()
    {
        return [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'storage' => $this->checkStorage(),
            'online_users' => $this->getOnlineUsers()
        ];
    }
    
    private function checkDatabase()
    {
        try {
            DB::connection()->getPdo();
            
            // Datenbankgröße abrufen
            $databaseName = config('database.connections.mysql.database');
            $result = DB::select("
                SELECT 
                    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
                FROM information_schema.TABLES
                WHERE table_schema = ?
            ", [$databaseName]);
            
            $sizeMB = $result[0]->size_mb ?? 0;
            
            return ['status' => 'ok', 'message' => $sizeMB . ' MB'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Fehler'];
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
    
    private function getOnlineUsers()
    {
        // Zähle Nutzer, die in den letzten 5 Minuten aktiv waren
        // Nutze updated_at als Proxy für Aktivität (wird bei jeder Aktion aktualisiert)
        $onlineCount = User::where('updated_at', '>=', now()->subMinutes(5))->count();

        return [
            'count' => $onlineCount,
            'status' => $onlineCount > 0 ? 'ok' : 'warning',
            'message' => $onlineCount . ($onlineCount === 1 ? ' Nutzer' : ' Nutzer') . ' (letzte 5 Min)'
        ];
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

    private function getChartData()
    {
        $labels = [];
        $activeData = [];
        $registrationsData = [];
        $questionsTotal = [];
        $questionsCorrect = [];
        $questionsWrong = [];
        $userCountData = [];
        $unverifiedCountData = [];

        // Sammle Daten für die letzten 30 Tage (inkl. heute)
        for ($i = 29; $i >= 0; $i--) {
            $day = now()->subDays($i)->startOfDay();
            $dayEnd = now()->subDays($i)->endOfDay();

            // Label (nur Tag.Monat)
            $labels[] = $day->format('d.m');

            // Chart 1: Aktive Benutzer + Neue Registrierungen (kombiniert)
            $activeData[] = QuestionStatistic::whereBetween('created_at', [$day, $dayEnd])
                ->distinct('user_id')
                ->count('user_id');
            $registrationsData[] = User::whereBetween('created_at', [$day, $dayEnd])->count();

            // Chart 2: Beantwortete Fragen (Total, Richtig, Falsch)
            $totalQuestions = QuestionStatistic::whereBetween('created_at', [$day, $dayEnd])->count();
            $correctQuestions = QuestionStatistic::whereBetween('created_at', [$day, $dayEnd])->where('is_correct', true)->count();
            $wrongQuestions = $totalQuestions - $correctQuestions;

            $questionsTotal[] = $totalQuestions;
            $questionsCorrect[] = $correctQuestions;
            $questionsWrong[] = $wrongQuestions;

            // Chart 3: User-Verlauf (aus user_count_history Tabelle)
            $userCount = \App\Models\UserCountHistory::whereDate('date', $day->format('Y-m-d'))->first();

            if ($userCount) {
                // Historische Daten vorhanden
                $userCountData[] = $userCount->total_users;
                $unverifiedCountData[] = $userCount->total_users - $userCount->verified_users;
            } else {
                // Für heute (noch kein History-Eintrag): Live-Daten verwenden
                $totalUsers = User::where('created_at', '<=', $dayEnd)->count();
                $verifiedUsers = User::whereNotNull('email_verified_at')->where('created_at', '<=', $dayEnd)->count();
                $userCountData[] = $totalUsers;
                $unverifiedCountData[] = $totalUsers - $verifiedUsers;
            }
        }

        return [
            'labels' => $labels,
            'active' => $activeData,
            'registrations' => $registrationsData,
            'questionsTotal' => $questionsTotal,
            'questionsCorrect' => $questionsCorrect,
            'questionsWrong' => $questionsWrong,
            'userCount' => $userCountData,
            'unverifiedCount' => $unverifiedCountData,
        ];
    }
}
