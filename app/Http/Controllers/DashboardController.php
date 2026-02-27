<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ExamStatistic;
use App\Models\Question;
use App\Services\GamificationService;
use App\Services\SpacedRepetitionService;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user()->fresh();

        if (!$user->onboarding_completed) {
            return redirect()->route('onboarding');
        }

        $totalQuestions = cache()->remember('total_questions_count', 3600, function () {
            return Question::count();
        });

        $recentExams = ExamStatistic::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $srService = new SpacedRepetitionService();
        $spacedRepetitionDue = $srService->getDueCount($user->id);

        $weeklyActivity = DB::table('question_statistics')
            ->where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(is_correct) as correct')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $sectionStats = DB::table('question_statistics')
            ->join('questions', 'question_statistics.question_id', '=', 'questions.id')
            ->where('question_statistics.user_id', $user->id)
            ->selectRaw('questions.lernabschnitt, COUNT(*) as total, SUM(question_statistics.is_correct) as correct')
            ->groupBy('questions.lernabschnitt')
            ->orderBy('questions.lernabschnitt')
            ->get();

        $gamificationService = new GamificationService();
        $streakFreezeStatus = $gamificationService->getStreakFreezeStatus($user);

        return view('dashboard', compact(
            'user', 'recentExams', 'totalQuestions', 'spacedRepetitionDue',
            'weeklyActivity', 'sectionStats', 'streakFreezeStatus'
        ));
    }

    public function dismissEmailConsentBanner()
    {
        session(['email_consent_banner_dismissed' => true]);
        return response()->json(['success' => true]);
    }
}
