<?php

namespace App\Http\Controllers;

use App\Models\Lootbox;
use App\Services\LeagueService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\GamificationService;
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
        $leagueService = new LeagueService();

        // Tab auswählen (default: liga)
        $tab = $request->get('tab', 'liga');

        if ($tab === 'liga') {
            $currentUser = Auth::user();
            $userLeague = $currentUser->league ?? 'bronze';
            $leaderboard = $leagueService->getLeagueLeaderboard($userLeague);
            $weekRange = $gamificationService->getCurrentWeekRange();
            $leagueInfo = LeagueService::getLeagueInfo($userLeague);
            $nextLeague = LeagueService::getNextLeague($userLeague);
            $prevLeague = LeagueService::getPreviousLeague($userLeague);
        } else {
            $leaderboard = $gamificationService->getLeaderboard(50);
            $weekRange = null;
            $userLeague = Auth::user()->league ?? 'bronze';
            $leagueInfo = LeagueService::getLeagueInfo($userLeague);
            $nextLeague = null;
            $prevLeague = null;
        }

        $leagues = LeagueService::getLeagues();

        return view('gamification.leaderboard', compact(
            'leaderboard', 'tab', 'weekRange', 'userLeague', 'leagueInfo',
            'nextLeague', 'prevLeague', 'leagues'
        ));
    }

    public function openLootbox(Request $request): JsonResponse
    {
        $user = Auth::user();
        $lootbox = Lootbox::where('id', $request->input('lootbox_id'))
            ->where('user_id', $user->id)
            ->where('opened', false)
            ->first();

        if (!$lootbox) {
            return response()->json(['success' => false, 'message' => 'Lootbox nicht gefunden.'], 404);
        }

        $result = (new LeagueService())->openLootbox($lootbox, $user);

        return response()->json($result);
    }

    public function lootboxes()
    {
        $user = Auth::user();
        $unopened = (new LeagueService())->getUnopenedLootboxes($user);
        $opened = Lootbox::where('user_id', $user->id)
            ->where('opened', true)
            ->orderBy('opened_at', 'desc')
            ->limit(20)
            ->get();

        return view('gamification.lootboxes', compact('unopened', 'opened'));
    }
}
