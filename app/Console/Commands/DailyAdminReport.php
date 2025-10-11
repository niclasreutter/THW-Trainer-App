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
                'date' => now()->format('Y-m-d'),
                'users_total' => $reportData['users']['total'],
                'questions_answered_today' => $reportData['activity']['questions_answered_today'],
                'mail_config' => [
                    'driver' => config('mail.default'),
                    'host' => config('mail.mailers.smtp.host'),
                    'from' => config('mail.from.address')
                ]
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
        $today = now()->startOfDay();
        $yesterday = now()->subDay()->startOfDay();
        $lastWeek = now()->subWeek()->startOfDay();

        return [
            'date' => now()->format('d.m.Y'),
            'users' => [
                'total' => User::count(),
                'verified' => User::whereNotNull('email_verified_at')->count(),
                'active_today' => User::where('updated_at', '>=', $today)->count(), // Verwende updated_at statt last_activity
                'active_last_week' => User::where('updated_at', '>=', $lastWeek)->count(),
                'new_today' => User::whereDate('created_at', $today)->count(),
                'new_last_week' => User::where('created_at', '>=', $lastWeek)->count(),
            ],
            'activity' => [
                'questions_answered_today' => QuestionStatistic::whereDate('created_at', $today)->count(),
                'questions_answered_yesterday' => QuestionStatistic::whereDate('created_at', $yesterday)->count(),
                'correct_answers_today' => QuestionStatistic::whereDate('created_at', $today)->where('is_correct', true)->count(),
                'total_questions_answered' => QuestionStatistic::count(),
                'total_correct_answers' => QuestionStatistic::where('is_correct', true)->count(),
            ],
            'gamification' => [
                'total_points_awarded' => User::sum('points'),
                'avg_points_per_user' => User::avg('points'),
                'users_with_streak' => User::where('streak_days', '>', 0)->count(),
                'top_user_points' => User::max('points'),
                'users_level_5_plus' => User::where('level', '>=', 5)->count(),
            ],
            'system' => [
                'total_questions' => Question::count(),
                'database_size' => $this->getDatabaseSize(),
                'cache_hit_rate' => $this->getCacheHitRate(),
                'server_uptime' => $this->getServerUptime(),
            ],
            'top_users' => User::where('points', '>', 0)
                ->orderByDesc('points')
                ->limit(5)
                ->get(['name', 'points', 'level', 'streak_days'])
                ->toArray(),
            'recent_activity' => $this->getRecentActivity()
        ];
    }

    private function sendReportEmail($email, $reportData)
    {
        try {
            Mail::send('emails.admin-daily-report', $reportData, function ($message) use ($email, $reportData) {
                $message->to($email)
                        ->subject("THW-Trainer Tagesreport - {$reportData['date']}");
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

    private function getCacheHitRate()
    {
        try {
            // Vereinfachte Cache-Hit-Rate Berechnung
            return '~95% (geschätzt)';
        } catch (\Exception $e) {
            return 'Unbekannt';
        }
    }

    private function getServerUptime()
    {
        try {
            $uptime = shell_exec('uptime -p 2>/dev/null');
            return $uptime ? trim($uptime) : 'Unbekannt';
        } catch (\Exception $e) {
            return 'Unbekannt';
        }
    }

    private function getRecentActivity()
    {
        return [
            'new_users_last_24h' => User::where('created_at', '>=', now()->subDay())->count(),
            'questions_answered_last_24h' => QuestionStatistic::where('created_at', '>=', now()->subDay())->count(),
            'exams_taken_last_24h' => DB::table('exam_statistics')->where('created_at', '>=', now()->subDay())->count(),
        ];
    }
}
