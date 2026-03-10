<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\GamificationService;
use App\Services\LeagueService;
use App\Services\DailyQuestService;
use App\Services\MonthlyChallengeService;
use Illuminate\Http\JsonResponse;

class GamificationController extends Controller
{
    public function achievements()
    {
        return view('gamification.achievements');
    }

    public function useStreakFreeze(Request $request): JsonResponse
    {
        $user = Auth::user();
        $result = (new GamificationService())->applyManualFreeze($user);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    public function leaderboard(Request $request)
    {
        $gamificationService = new GamificationService();

        $tab = $request->get('tab', 'gesamt');

        if ($tab === 'woche') {
            $leaderboard = $gamificationService->getWeeklyLeaderboard(50);
            $weekRange = $gamificationService->getCurrentWeekRange();
        } else {
            $leaderboard = $gamificationService->getLeaderboard(50);
            $weekRange = null;
        }

        return view('gamification.leaderboard', compact('leaderboard', 'tab', 'weekRange'));
    }

    public function leagueLeaderboard(Request $request)
    {
        $user = Auth::user();
        $leagueService = new LeagueService();

        $selectedLeague = $request->get('league', $user->league ?? 'bronze');
        $leaderboard = $leagueService->getLeagueLeaderboard($selectedLeague);
        $userLeagueInfo = $leagueService->getUserLeagueInfo($user);
        $leagues = LeagueService::LEAGUES;
        $leagueConfig = LeagueService::LEAGUE_CONFIG;

        if ($user->league_change) {
            $leagueService->clearLeagueChange($user);
        }

        return view('gamification.leagues', compact(
            'leaderboard', 'selectedLeague', 'userLeagueInfo', 'leagues', 'leagueConfig'
        ));
    }

    public function dailyQuests()
    {
        $user = Auth::user();
        $questService = new DailyQuestService();
        $questData = $questService->getQuestWidgetData($user);

        return view('gamification.daily-quests', compact('questData'));
    }

    public function claimQuestChest(Request $request): JsonResponse
    {
        $user = Auth::user();
        $result = (new DailyQuestService())->claimChest($user);
        return response()->json($result, $result['success'] ? 200 : 422);
    }

    public function monthlyChallenge()
    {
        $user = Auth::user();
        $challengeService = new MonthlyChallengeService();
        $challengeData = $challengeService->getChallengeWidgetData($user);
        $history = $challengeService->getCompletedChallenges($user);

        return view('gamification.monthly-challenge', compact('challengeData', 'history'));
    }

    public function claimMilestone(Request $request): JsonResponse
    {
        $request->validate(['milestone' => 'required|integer|in:25,50,75,100']);
        $user = Auth::user();
        $result = (new MonthlyChallengeService())->claimMilestone($user, $request->milestone);
        return response()->json($result, $result['success'] ? 200 : 422);
    }
}
