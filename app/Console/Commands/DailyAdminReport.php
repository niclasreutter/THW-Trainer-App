<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Question;
use App\Models\QuestionStatistic;

class DailyAdminReport extends Command
{
    protected $signature = 'admin:daily-report {email=protokolle@thw-trainer.de}';
    protected $description = 'Sendet tägliche Admin-Übersicht per E-Mail';

    public function handle()
    {
        $email = $this->argument('email');
        $this->info("📊 Erstelle tägliche Admin-Übersicht für {$email}...");

        try {
            $this->info('📊 Generiere Report-Daten...');
            $reportData = $this->generateReportData();
            
            $this->info('📧 Sende E-Mail...');
            $this->sendReportEmail($email, $reportData);

            $this->info('✅ Admin-Report erfolgreich gesendet');
            
            \Log::info('Täglicher Admin-Report gesendet', [
                'email' => $email,
                'date' => $reportData['date'],
                'users_total' => $reportData['users']['total'],
                'active_yesterday' => $reportData['users']['active_yesterday'],
                'questions_answered_yesterday' => $reportData['activity']['questions_answered_yesterday'],
                'warnings_count' => count($reportData['warnings'])
            ]);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Fehler beim Senden des Admin-Reports: ' . $e->getMessage());
            \Log::error('Admin-Report fehlgeschlagen', [
                'error' => $e->getMessage(),
                'email' => $email
            ]);
            return Command::FAILURE;
        }
    }

    private function generateReportData()
    {
        // Report zeigt GESTERN (kompletter Tag), da er um 08:00 Uhr läuft
        $yesterday = now()->subDay()->startOfDay();
        $yesterdayEnd = now()->subDay()->endOfDay();
        $twoDaysAgo = now()->subDays(2)->startOfDay();
        $twoDaysAgoEnd = now()->subDays(2)->endOfDay();
        $lastWeek = now()->subWeek()->startOfDay();
        $lastMonth = now()->subMonth()->startOfDay();

        // Benutzer-Metriken
        $totalUsers = User::count();
        $verifiedUsers = User::whereNotNull('email_verified_at')->count();

        // Aktive Benutzer = unique users die Fragen beantwortet haben (aus question_statistics)
        $activeYesterday = QuestionStatistic::whereBetween('created_at', [$yesterday, $yesterdayEnd])
            ->distinct('user_id')
            ->count('user_id');
        $activeTwoDaysAgo = QuestionStatistic::whereBetween('created_at', [$twoDaysAgo, $twoDaysAgoEnd])
            ->distinct('user_id')
            ->count('user_id');

        $newYesterday = User::whereBetween('created_at', [$yesterday, $yesterdayEnd])->count();
        $newTwoDaysAgo = User::whereBetween('created_at', [$twoDaysAgo, $twoDaysAgoEnd])->count();

        // Aktivitäts-Metriken
        $questionsYesterday = QuestionStatistic::whereBetween('created_at', [$yesterday, $yesterdayEnd])->count();
        $questionsTwoDaysAgo = QuestionStatistic::whereBetween('created_at', [$twoDaysAgo, $twoDaysAgoEnd])->count();
        $correctYesterday = QuestionStatistic::whereBetween('created_at', [$yesterday, $yesterdayEnd])->where('is_correct', true)->count();
        $correctTwoDaysAgo = QuestionStatistic::whereBetween('created_at', [$twoDaysAgo, $twoDaysAgoEnd])->where('is_correct', true)->count();

        // Erfolgsquoten
        $successRateYesterday = $questionsYesterday > 0 ? round(($correctYesterday / $questionsYesterday) * 100, 1) : 0;
        $successRateTwoDaysAgo = $questionsTwoDaysAgo > 0 ? round(($correctTwoDaysAgo / $questionsTwoDaysAgo) * 100, 1) : 0;

        // Gamification
        $usersWithStreak = User::where('streak_days', '>', 0)->count();
        $avgStreak = User::where('streak_days', '>', 0)->avg('streak_days');

        // 7-Tage Daten für Sparklines
        $activityLast7Days = $this->getLast7DaysData('active');
        $registrationsLast7Days = $this->getLast7DaysData('registrations');
        $questionsLast7Days = $this->getLast7DaysData('questions');

        return [
            'date' => now()->subDay()->format('d.m.Y'),
            'report_day' => 'Gestern',

            // Benutzer mit Trends
            'users' => [
                'total' => $totalUsers,
                'verified' => $verifiedUsers,
                'verification_rate' => $totalUsers > 0 ? round(($verifiedUsers / $totalUsers) * 100, 1) : 0,
                'active_yesterday' => $activeYesterday,
                'active_2_days_ago' => $activeTwoDaysAgo,
                'active_trend' => $this->getTrend($activeYesterday, $activeTwoDaysAgo),
                'active_last_7_days' => QuestionStatistic::where('created_at', '>=', $lastWeek)->distinct('user_id')->count('user_id'),
                'active_last_30_days' => QuestionStatistic::where('created_at', '>=', $lastMonth)->distinct('user_id')->count('user_id'),
                'active_sparkline' => $this->generateSparkline($activityLast7Days),
                'new_yesterday' => $newYesterday,
                'new_2_days_ago' => $newTwoDaysAgo,
                'new_trend' => $this->getTrend($newYesterday, $newTwoDaysAgo),
                'new_last_7_days' => User::where('created_at', '>=', $lastWeek)->count(),
                'new_sparkline' => $this->generateSparkline($registrationsLast7Days),
            ],

            // Aktivität mit Trends
            'activity' => [
                'questions_answered_yesterday' => $questionsYesterday,
                'questions_answered_2_days_ago' => $questionsTwoDaysAgo,
                'questions_trend' => $this->getTrend($questionsYesterday, $questionsTwoDaysAgo),
                'questions_sparkline' => $this->generateSparkline($questionsLast7Days),
                'correct_answers_yesterday' => $correctYesterday,
                'correct_answers_2_days_ago' => $correctTwoDaysAgo,
                'success_rate_yesterday' => $successRateYesterday,
                'success_rate_2_days_ago' => $successRateTwoDaysAgo,
                'success_rate_trend' => $this->getTrend($successRateYesterday, $successRateTwoDaysAgo),
                'avg_questions_per_user' => $activeYesterday > 0 ? round($questionsYesterday / $activeYesterday, 1) : 0,
                'total_questions_answered' => QuestionStatistic::count(),
            ],

            // Gamification
            'gamification' => [
                'users_with_streak' => $usersWithStreak,
                'avg_streak_length' => round($avgStreak ?? 0, 1),
                'longest_streak' => User::max('streak_days') ?? 0,
                'total_points_awarded' => User::sum('points'),
                'avg_points_per_user' => round(User::avg('points') ?? 0, 0),
                'users_level_5_plus' => User::where('level', '>=', 5)->count(),
            ],

            // Top Performer
            'top_users' => User::where('points', '>', 0)
                ->orderByDesc('points')
                ->limit(5)
                ->get(['name', 'points', 'level', 'streak_days'])
                ->toArray(),

            // System
            'system' => [
                'total_questions' => Question::count(),
                'lehrgang_questions' => \App\Models\LehrgangQuestion::count(),
                'lernpool_questions' => \App\Models\OrtsverbandLernpoolQuestion::count(),
                'database_size' => $this->getDatabaseSize(),
            ],

            // Warnungen
            'warnings' => $this->getWarnings($activeYesterday, $activeTwoDaysAgo, $newYesterday, $successRateYesterday)
        ];
    }

    private function getTrend($current, $previous)
    {
        if ($previous == 0) {
            return ['direction' => 'neutral', 'percentage' => 0];
        }

        $change = (($current - $previous) / $previous) * 100;
        $direction = $change > 5 ? 'up' : ($change < -5 ? 'down' : 'neutral');

        return [
            'direction' => $direction,
            'percentage' => round(abs($change), 1)
        ];
    }

    private function getWarnings($activeYesterday, $activeTwoDaysAgo, $newYesterday, $successRate)
    {
        $warnings = [];

        // Warnung: Starker Aktivitätsrückgang
        if ($activeTwoDaysAgo > 0 && $activeYesterday < ($activeTwoDaysAgo * 0.7)) {
            $warnings[] = [
                'type' => 'danger',
                'message' => 'Aktivität stark gesunken (-' . round((1 - ($activeYesterday / $activeTwoDaysAgo)) * 100) . '%)'
            ];
        }

        // Warnung: Keine neuen User
        if ($newYesterday == 0) {
            $warnings[] = [
                'type' => 'warning',
                'message' => 'Keine neuen Registrierungen gestern'
            ];
        }

        // Warnung: Niedrige Erfolgsquote
        if ($successRate > 0 && $successRate < 60) {
            $warnings[] = [
                'type' => 'warning',
                'message' => 'Niedrige Erfolgsquote (' . $successRate . '%)'
            ];
        }

        // Positiv: Hohe Aktivität
        if ($activeYesterday > $activeTwoDaysAgo * 1.2) {
            $warnings[] = [
                'type' => 'success',
                'message' => 'Aktivität steigt (+' . round((($activeYesterday / $activeTwoDaysAgo) - 1) * 100) . '%)'
            ];
        }

        return $warnings;
    }

    private function sendReportEmail($email, $reportData)
    {
        try {
            Mail::send('emails.admin-daily-report', $reportData, function ($message) use ($email, $reportData) {
                $message->to($email)
                        ->subject("THW-Trainer Tagesreport - {$reportData['date']} (Gestern)");
            });

            $this->info("📧 E-Mail erfolgreich gesendet an: {$email}");
            
        } catch (\Exception $e) {
            $this->error("❌ E-Mail-Versand fehlgeschlagen: " . $e->getMessage());
            \Log::error('Admin-Report E-Mail-Versand fehlgeschlagen', [
                'email' => $email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function getDatabaseSize()
    {
        try {
            $result = DB::select("SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'size_mb' FROM information_schema.tables WHERE table_schema = DATABASE()");
            return $result[0]->size_mb . ' MB';
        } catch (\Exception $e) {
            return 'Unbekannt';
        }
    }

    private function getLast7DaysData($type)
    {
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i)->startOfDay();
            $dayEnd = now()->subDays($i)->endOfDay();

            switch ($type) {
                case 'active':
                    // Aktive Benutzer = unique users die an dem Tag Fragen beantwortet haben
                    // Verwende question_statistics als Activity Log
                    $data[] = QuestionStatistic::whereBetween('created_at', [$day, $dayEnd])
                        ->distinct('user_id')
                        ->count('user_id');
                    break;
                case 'registrations':
                    $data[] = User::whereBetween('created_at', [$day, $dayEnd])->count();
                    break;
                case 'questions':
                    $data[] = QuestionStatistic::whereBetween('created_at', [$day, $dayEnd])->count();
                    break;
            }
        }

        return $data;
    }

    private function generateSparkline($data)
    {
        if (empty($data) || max($data) == 0) {
            return '▁▁▁▁▁▁▁';
        }

        $chars = ['▁', '▂', '▃', '▄', '▅', '▆', '▇', '█'];
        $max = max($data);
        $sparkline = '';

        foreach ($data as $value) {
            $index = $max > 0 ? floor(($value / $max) * (count($chars) - 1)) : 0;
            $sparkline .= $chars[$index];
        }

        return $sparkline;
    }

}
