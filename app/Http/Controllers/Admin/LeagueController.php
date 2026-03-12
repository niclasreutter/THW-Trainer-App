<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\LeagueService;

class LeagueController extends Controller
{
    /**
     * Übersicht aller Ligen mit Nutzeranzahl und Top-Spielern.
     */
    public function index()
    {
        $leagues = LeagueService::getLeagues();

        $allUsers = User::whereIn('league', array_keys($leagues))->get();
        $grouped = $allUsers->groupBy('league');

        $leagueStats = [];
        foreach ($leagues as $key => $league) {
            $users = $grouped->get($key, collect());

            $leagueStats[$key] = [
                'info' => $league,
                'total_users' => $users->count(),
                'active_users' => $users->where('weekly_points', '>', 0)->count(),
                'total_weekly_points' => $users->sum('weekly_points'),
                'avg_weekly_points' => $users->count() > 0
                    ? round($users->avg('weekly_points'))
                    : 0,
                'top_user' => $users->sortByDesc('weekly_points')->first(),
            ];
        }

        return view('admin.leagues.index', compact('leagueStats'));
    }

    /**
     * Detailansicht einer bestimmten Liga mit allen Nutzern.
     */
    public function show(string $league)
    {
        $leagues = LeagueService::getLeagues();

        if (!array_key_exists($league, $leagues)) {
            abort(404);
        }

        $leagueInfo = $leagues[$league];

        $users = User::where('league', $league)
            ->orderBy('weekly_points', 'desc')
            ->orderBy('points', 'desc')
            ->get();

        $stats = [
            'total' => $users->count(),
            'active' => $users->where('weekly_points', '>', 0)->count(),
            'total_weekly_points' => $users->sum('weekly_points'),
            'avg_weekly_points' => $users->count() > 0
                ? round($users->avg('weekly_points'))
                : 0,
            'avg_total_points' => $users->count() > 0
                ? round($users->avg('points'))
                : 0,
        ];

        $promotionMinPoints = LeagueService::PROMOTION_MIN_POINTS[$league] ?? null;
        $nextLeague = LeagueService::getNextLeague($league);
        $previousLeague = LeagueService::getPreviousLeague($league);

        return view('admin.leagues.show', compact(
            'league', 'leagueInfo', 'users', 'stats',
            'promotionMinPoints', 'nextLeague', 'previousLeague', 'leagues'
        ));
    }
}
