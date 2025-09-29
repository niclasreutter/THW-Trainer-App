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

    public function leaderboard()
    {
        $gamificationService = new GamificationService();
        $leaderboard = $gamificationService->getLeaderboard(50);
        
        return view('gamification.leaderboard', compact('leaderboard'));
    }
}
