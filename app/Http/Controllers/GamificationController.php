<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\GamificationService;

class GamificationController extends Controller
{
    public function achievements()
    {
        return view('gamification.achievements');
    }

    public function leaderboard(Request $request)
    {
        $gamificationService = new GamificationService();
        
        // Tab auswählen (default: gesamt)
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
}
