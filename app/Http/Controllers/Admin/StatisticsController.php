<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Question;
use App\Models\QuestionStatistic;
use App\Models\LehrgangQuestion;
use App\Models\OrtsverbandLernpoolQuestion;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function index()
    {
        // 30-Tage Daten für Charts
        $last30DaysActivity = $this->getLast30DaysData('active');
        $last30DaysRegistrations = $this->getLast30DaysData('registrations');
        $last30DaysQuestions = $this->getLast30DaysData('questions');
        $last30DaysSuccessRate = $this->getLast30DaysSuccessRate();

        // Aktuelle Metriken
        $metrics = [
            'total_users' => User::count(),
            'verified_users' => User::whereNotNull('email_verified_at')->count(),
            'active_users_30d' => User::where('last_activity_date', '>=', now()->subDays(30))->count(),
            'total_questions' => Question::count() + LehrgangQuestion::count() + OrtsverbandLernpoolQuestion::count(),
            'total_answers' => QuestionStatistic::count(),
            'users_with_streak' => User::where('streak_days', '>', 0)->count(),
        ];

        return view('admin.statistics', compact(
            'last30DaysActivity',
            'last30DaysRegistrations',
            'last30DaysQuestions',
            'last30DaysSuccessRate',
            'metrics'
        ));
    }

    private function getLast30DaysData($type)
    {
        $data = [
            'labels' => [],
            'values' => []
        ];

        for ($i = 29; $i >= 0; $i--) {
            $day = now()->subDays($i)->startOfDay();
            $dayEnd = now()->subDays($i)->endOfDay();

            // Label (nur Tag.Monat)
            $data['labels'][] = $day->format('d.m');

            // Wert je nach Typ
            switch ($type) {
                case 'active':
                    $data['values'][] = User::whereBetween('last_activity_date', [$day, $dayEnd])->count();
                    break;
                case 'registrations':
                    $data['values'][] = User::whereBetween('created_at', [$day, $dayEnd])->count();
                    break;
                case 'questions':
                    $data['values'][] = QuestionStatistic::whereBetween('created_at', [$day, $dayEnd])->count();
                    break;
            }
        }

        return $data;
    }

    private function getLast30DaysSuccessRate()
    {
        $data = [
            'labels' => [],
            'values' => []
        ];

        for ($i = 29; $i >= 0; $i--) {
            $day = now()->subDays($i)->startOfDay();
            $dayEnd = now()->subDays($i)->endOfDay();

            $data['labels'][] = $day->format('d.m');

            $total = QuestionStatistic::whereBetween('created_at', [$day, $dayEnd])->count();
            $correct = QuestionStatistic::whereBetween('created_at', [$day, $dayEnd])->where('is_correct', true)->count();

            $data['values'][] = $total > 0 ? round(($correct / $total) * 100, 1) : 0;
        }

        return $data;
    }
}
