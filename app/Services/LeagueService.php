<?php

namespace App\Services;

use App\Models\User;
use App\Models\LeagueHistory;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class LeagueService
{
    const LEAGUES = [
        'bronze', 'silber', 'gold', 'saphir', 'rubin', 'smaragd', 'amethyst', 'diamant',
    ];

    const LEAGUE_LABELS = [
        'bronze'   => 'Bronze',
        'silber'   => 'Silber',
        'gold'     => 'Gold',
        'saphir'   => 'Saphir',
        'rubin'    => 'Rubin',
        'smaragd'  => 'Smaragd',
        'amethyst' => 'Amethyst',
        'diamant'  => 'Diamant',
    ];

    const LEAGUE_CONFIG = [
        'bronze'   => ['promote_top' => 5, 'demote_bottom' => 0, 'icon' => 'bi-shield-fill',   'color' => '#cd7f32'],
        'silber'   => ['promote_top' => 5, 'demote_bottom' => 3, 'icon' => 'bi-shield-fill',   'color' => '#c0c0c0'],
        'gold'     => ['promote_top' => 3, 'demote_bottom' => 3, 'icon' => 'bi-shield-shaded', 'color' => '#fbbf24'],
        'saphir'   => ['promote_top' => 3, 'demote_bottom' => 3, 'icon' => 'bi-gem',           'color' => '#3b82f6'],
        'rubin'    => ['promote_top' => 2, 'demote_bottom' => 3, 'icon' => 'bi-gem',           'color' => '#ef4444'],
        'smaragd'  => ['promote_top' => 2, 'demote_bottom' => 3, 'icon' => 'bi-gem',           'color' => '#10b981'],
        'amethyst' => ['promote_top' => 1, 'demote_bottom' => 2, 'icon' => 'bi-gem',           'color' => '#a855f7'],
        'diamant'  => ['promote_top' => 0, 'demote_bottom' => 3, 'icon' => 'bi-diamond-fill',  'color' => '#67e8f9'],
    ];

    public function processWeeklyPromotions(): void
    {
        $weekStart = Carbon::now()->startOfWeek(Carbon::MONDAY)->subWeek();

        $users = User::where('weekly_points', '>', 0)
            ->orWhereNotNull('league')
            ->get();

        $grouped = $users->groupBy(fn($u) => $u->league ?? 'bronze');

        foreach (self::LEAGUES as $index => $league) {
            $leagueUsers = $grouped->get($league, collect());
            if ($leagueUsers->isEmpty()) {
                continue;
            }

            $sorted = $leagueUsers->sortByDesc('weekly_points')->values();
            $config = self::LEAGUE_CONFIG[$league];
            $total = $sorted->count();

            foreach ($sorted as $rank => $user) {
                $rankPosition = $rank + 1;
                $result = 'stayed';

                // Promotion check (not for diamant)
                if ($config['promote_top'] > 0 && $index < count(self::LEAGUES) - 1) {
                    $promoteThreshold = min($config['promote_top'], $total);
                    if ($rankPosition <= $promoteThreshold && $user->weekly_points > 0) {
                        // Check for ties at the boundary
                        $boundaryPoints = $sorted[$promoteThreshold - 1]->weekly_points ?? 0;
                        if ($user->weekly_points >= $boundaryPoints) {
                            $result = 'promoted';
                        }
                    }
                }

                // Demotion check (not for bronze, not for inactive users)
                if ($result === 'stayed' && $config['demote_bottom'] > 0 && $index > 0 && $user->weekly_points > 0) {
                    $demoteStart = $total - $config['demote_bottom'];
                    if ($demoteStart >= 0 && $rankPosition > $demoteStart) {
                        $result = 'demoted';
                    }
                }

                // Apply result
                $newLeague = $league;
                if ($result === 'promoted') {
                    $newLeague = self::LEAGUES[$index + 1];
                } elseif ($result === 'demoted') {
                    $newLeague = self::LEAGUES[$index - 1];
                }

                if ($result !== 'stayed') {
                    $user->previous_league = $league;
                    $user->league = $newLeague;
                    $user->league_change = $result;
                    $user->league_updated_at = now();
                    $user->save();

                    $label = self::LEAGUE_LABELS[$newLeague];
                    $icon = $result === 'promoted' ? 'bi-arrow-up-circle-fill' : 'bi-arrow-down-circle-fill';
                    $msg = $result === 'promoted'
                        ? "Aufstieg in die {$label}-Liga!"
                        : "Abstieg in die {$label}-Liga.";

                    Notification::create([
                        'user_id' => $user->id,
                        'type' => 'league_change',
                        'title' => $result === 'promoted' ? 'Liga-Aufstieg' : 'Liga-Abstieg',
                        'message' => $msg,
                        'icon' => $icon,
                        'data' => ['league' => $newLeague, 'previous' => $league, 'result' => $result],
                    ]);
                }

                LeagueHistory::create([
                    'user_id' => $user->id,
                    'week_start' => $weekStart,
                    'league' => $league,
                    'weekly_points' => $user->weekly_points,
                    'rank_in_league' => $rankPosition,
                    'result' => $result,
                ]);
            }
        }
    }

    public function getLeagueLeaderboard(string $league, int $limit = 30): Collection
    {
        return User::where('league', $league)
            ->where('leaderboard_consent', true)
            ->where('weekly_points', '>', 0)
            ->orderByDesc('weekly_points')
            ->limit($limit)
            ->get(['id', 'name', 'weekly_points', 'points', 'level', 'streak_days', 'league',
                   'glowing_name_until', 'glowing_name_type', 'profile_frame_until', 'profile_frame_type',
                   'rank_color', 'rank_color_until', 'active_title', 'active_title_until']);
    }

    public function getUserLeagueInfo(User $user): array
    {
        $league = $user->league ?? 'bronze';
        $config = self::LEAGUE_CONFIG[$league] ?? self::LEAGUE_CONFIG['bronze'];

        // Calculate rank
        $rank = User::where('league', $league)
            ->where('weekly_points', '>', $user->weekly_points ?? 0)
            ->count() + 1;

        $totalInLeague = User::where('league', $league)->where('weekly_points', '>', 0)->count();

        $promoZone = $config['promote_top'] > 0 && $rank <= $config['promote_top'];
        $demoZone = $config['demote_bottom'] > 0 && $totalInLeague > 0
            && $rank > ($totalInLeague - $config['demote_bottom']);

        return [
            'league' => $league,
            'label' => self::LEAGUE_LABELS[$league] ?? 'Bronze',
            'icon' => $config['icon'],
            'color' => $config['color'],
            'rank' => $rank,
            'total_in_league' => $totalInLeague,
            'promo_zone' => $promoZone,
            'demo_zone' => $demoZone,
            'change' => $user->league_change,
        ];
    }

    public function getLeagueBadgeData(string $league): array
    {
        $config = self::LEAGUE_CONFIG[$league] ?? self::LEAGUE_CONFIG['bronze'];
        return [
            'label' => self::LEAGUE_LABELS[$league] ?? 'Bronze',
            'icon' => $config['icon'],
            'color' => $config['color'],
        ];
    }

    public function clearLeagueChange(User $user): void
    {
        $user->league_change = null;
        $user->previous_league = null;
        $user->save();
    }
}
