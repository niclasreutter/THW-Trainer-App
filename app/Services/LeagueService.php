<?php

namespace App\Services;

use App\Models\Lootbox;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LeagueService
{
    /**
     * Alle 5 Ligen von niedrigster bis höchster
     */
    const LEAGUES = [
        'bronze'  => ['name' => 'Bronze',  'icon' => 'bi-shield-fill',  'color' => '#cd7f32', 'order' => 1],
        'silber'  => ['name' => 'Silber',  'icon' => 'bi-shield-fill',  'color' => '#94a3b8', 'order' => 2],
        'gold'    => ['name' => 'Gold',    'icon' => 'bi-shield-fill',  'color' => '#fbbf24', 'order' => 3],
        'platin'  => ['name' => 'Platin',  'icon' => 'bi-shield-fill',  'color' => '#67e8f9', 'order' => 4],
        'diamant' => ['name' => 'Diamant', 'icon' => 'bi-gem',          'color' => '#c084fc', 'order' => 5],
    ];

    /**
     * Aufstieg: einheitlich Top 20%, max 5 Plaetze
     * Schwierigkeit kommt durch steigende Mindestpunkte
     */
    const PROMOTION_PERCENT = 20;
    const MAX_PROMOTION_SLOTS = 5;

    /**
     * Abstieg: einheitlich Bottom 20%, max 5
     */
    const RELEGATION_PERCENT = 20;
    const MAX_RELEGATION_SLOTS = 5;

    /**
     * Mindestens so viele aktive User muessen in einer Liga sein,
     * damit Auf-/Abstiege stattfinden
     */
    const MIN_USERS_FOR_MOVEMENT = 3;

    /**
     * Mindest-Punkte fuer Aufstieg pro Liga (steigend)
     */
    const PROMOTION_MIN_POINTS = [
        'bronze'  => 300,
        'silber'  => 900,
        'gold'    => 2700,
        'platin'  => 7500,
        // diamant: kein Aufstieg moeglich
    ];

    /**
     * Gibt alle Liga-Infos zurück
     */
    public static function getLeagues(): array
    {
        return self::LEAGUES;
    }

    /**
     * Gibt Info zu einer bestimmten Liga zurück
     */
    public static function getLeagueInfo(string $league): array
    {
        return self::LEAGUES[$league] ?? self::LEAGUES['bronze'];
    }

    /**
     * Gibt den Liga-Namen zurück
     */
    public static function getLeagueName(string $league): string
    {
        return self::LEAGUES[$league]['name'] ?? 'Bronze';
    }

    /**
     * Gibt den Aufstiegs-Prozentsatz zurück (einheitlich fuer alle Ligen)
     */
    public static function getPromotionPercent(string $league): int
    {
        return self::PROMOTION_PERCENT;
    }

    /**
     * Gibt die max. Aufstiegsplaetze zurück (einheitlich fuer alle Ligen)
     */
    public static function getMaxPromotionSlots(string $league): int
    {
        return self::MAX_PROMOTION_SLOTS;
    }

    /**
     * Gibt die nächsthöhere Liga zurück (oder null wenn schon Diamant)
     */
    public static function getNextLeague(string $league): ?string
    {
        $keys = array_keys(self::LEAGUES);
        $index = array_search($league, $keys);

        if ($index === false || $index >= count($keys) - 1) {
            return null;
        }

        return $keys[$index + 1];
    }

    /**
     * Gibt die nächstniedrigere Liga zurück (oder null wenn schon Bronze)
     */
    public static function getPreviousLeague(string $league): ?string
    {
        $keys = array_keys(self::LEAGUES);
        $index = array_search($league, $keys);

        if ($index === false || $index <= 0) {
            return null;
        }

        return $keys[$index - 1];
    }

    /**
     * Holt das Liga-Leaderboard (nur User der gleichen Liga)
     */
    public function getLeagueLeaderboard(string $league): \Illuminate\Support\Collection
    {
        return User::where('leaderboard_consent', true)
            ->where('league', $league)
            ->where('weekly_points', '>', 0)
            ->orderBy('weekly_points', 'desc')
            ->orderBy('points', 'desc')
            ->get([
                'id', 'name', 'weekly_points', 'points', 'level', 'streak_days',
                'leaderboard_consent', 'league',
                'glowing_name_until', 'glowing_name_type',
                'profile_frame_until', 'profile_frame_type',
                'rank_color', 'rank_color_until',
                'active_title', 'active_title_until',
            ]);
    }

    /**
     * Verarbeitet wöchentliche Liga-Auf-/Abstiege und Lootbox-Vergabe.
     * Sollte jeden Montag VOR dem Weekly-Reset aufgerufen werden.
     */
    public function processWeeklyLeagues(): array
    {
        $results = [
            'promotions' => 0,
            'relegations' => 0,
            'lootboxes_awarded' => 0,
        ];

        // Duplikat-Schutz: 10 Minuten Zeitfenster gegen doppelte Cronjob-Ausführung
        $recentCutoff = Carbon::now()->subMinutes(10);

        $leagueKeys = array_keys(self::LEAGUES);

        foreach ($leagueKeys as $league) {
            $users = User::where('league', $league)
                ->where('weekly_points', '>', 0)
                ->orderBy('weekly_points', 'desc')
                ->orderBy('points', 'desc')
                ->get(['id', 'name', 'weekly_points', 'league']);

            if ($users->isEmpty()) {
                continue;
            }

            // Lootboxen für Plätze 1-3 (Duplikat-Schutz: nicht doppelt innerhalb 10 Min)
            foreach ($users->take(3) as $index => $user) {
                $recentlyAwarded = Lootbox::where('user_id', $user->id)
                    ->where('created_at', '>=', $recentCutoff)
                    ->exists();

                if ($recentlyAwarded) {
                    Log::info("Lootbox für User {$user->name} bereits kürzlich vergeben - überspringe (Duplikat-Schutz)");
                    continue;
                }

                $lootboxType = match ($index) {
                    0 => 'gold',
                    1 => 'silber',
                    2 => 'bronze',
                };

                Lootbox::create([
                    'user_id' => $user->id,
                    'type' => $lootboxType,
                    'opened' => false,
                ]);

                $this->createLeagueNotification($user, 'lootbox', [
                    'type' => $lootboxType,
                    'rank' => $index + 1,
                    'league' => $league,
                ]);

                $results['lootboxes_awarded']++;
            }

            $userCount = $users->count();
            $promotedIds = collect();

            // Aufstieg: Top X% steigen auf (liga-spezifisch, min Punkte erforderlich)
            $nextLeague = self::getNextLeague($league);
            if ($nextLeague && $userCount >= self::MIN_USERS_FOR_MOVEMENT) {
                $promotionSlots = min(
                    self::MAX_PROMOTION_SLOTS,
                    max(1, (int) floor($userCount * self::PROMOTION_PERCENT / 100))
                );
                $minPoints = self::PROMOTION_MIN_POINTS[$league] ?? 0;

                $promotionUsers = $users->take($promotionSlots)
                    ->filter(fn ($u) => $u->weekly_points >= $minPoints);

                foreach ($promotionUsers as $user) {
                    User::where('id', $user->id)->update(['league' => $nextLeague]);
                    $promotedIds->push($user->id);

                    $this->createLeagueNotification($user, 'promotion', [
                        'from' => $league,
                        'to' => $nextLeague,
                    ]);

                    $results['promotions']++;
                }
            }

            // Abstieg: Bottom X% steigen ab (max 5, nur wenn genug User)
            $prevLeague = self::getPreviousLeague($league);
            if ($prevLeague && $userCount >= self::MIN_USERS_FOR_MOVEMENT) {
                $relegationSlots = min(
                    self::MAX_RELEGATION_SLOTS,
                    max(1, (int) floor($userCount * self::RELEGATION_PERCENT / 100))
                );

                $remainingUsers = $userCount - $promotedIds->count();
                if ($remainingUsers > $relegationSlots) {
                    $relegationUsers = $users->slice(-$relegationSlots);
                    foreach ($relegationUsers as $user) {
                        if ($promotedIds->contains($user->id)) {
                            continue;
                        }

                        User::where('id', $user->id)->update(['league' => $prevLeague]);

                        $this->createLeagueNotification($user, 'relegation', [
                            'from' => $league,
                            'to' => $prevLeague,
                        ]);

                        $results['relegations']++;
                    }
                }
            }
        }

        Log::info('Wöchentliche Liga-Verarbeitung abgeschlossen', $results);

        return $results;
    }

    /**
     * Öffnet eine Lootbox und bestimmt die Belohnung
     */
    public function openLootbox(Lootbox $lootbox, User $user): array
    {
        if ($lootbox->opened) {
            return ['success' => false, 'message' => 'Diese Lootbox wurde bereits geöffnet.'];
        }

        $reward = $this->rollLootboxReward($lootbox->type);

        $lootbox->update([
            'opened' => true,
            'opened_at' => now(),
            'reward_type' => $reward['type'],
            'reward_amount' => $reward['amount'] ?? null,
        ]);

        // Belohnung anwenden
        $this->applyReward($user, $reward);

        return [
            'success' => true,
            'reward' => $reward,
            'lootbox_type' => $lootbox->type,
        ];
    }

    /**
     * Würfelt die Belohnung einer Lootbox aus
     */
    private function rollLootboxReward(string $type): array
    {
        $roll = mt_rand(1, 100);

        return match ($type) {
            'bronze' => $this->rollBronzeReward($roll),
            'silber' => $this->rollSilberReward($roll),
            'gold'   => $this->rollGoldReward($roll),
            default  => ['type' => 'xp', 'amount' => 200, 'label' => '200 XP'],
        };
    }

    /**
     * Bronze Lootbox: 40% 200 XP, 30% 400 XP, 20% 700 XP, 10% Streak Freeze
     */
    private function rollBronzeReward(int $roll): array
    {
        if ($roll <= 40) {
            return ['type' => 'xp', 'amount' => 200, 'label' => '200 XP'];
        }
        if ($roll <= 70) {
            return ['type' => 'xp', 'amount' => 400, 'label' => '400 XP'];
        }
        if ($roll <= 90) {
            return ['type' => 'xp', 'amount' => 700, 'label' => '700 XP'];
        }
        return ['type' => 'streak_freeze', 'amount' => 1, 'label' => 'Streak Freeze'];
    }

    /**
     * Silber Lootbox: 40% 500 XP, 35% 700 XP, 20% Streak Freeze, 5% Doppel-XP
     */
    private function rollSilberReward(int $roll): array
    {
        if ($roll <= 40) {
            return ['type' => 'xp', 'amount' => 500, 'label' => '500 XP'];
        }
        if ($roll <= 75) {
            return ['type' => 'xp', 'amount' => 700, 'label' => '700 XP'];
        }
        if ($roll <= 95) {
            return ['type' => 'streak_freeze', 'amount' => 1, 'label' => 'Streak Freeze'];
        }
        return ['type' => 'double_xp', 'amount' => 24, 'label' => 'Doppel-XP Boost (24h)'];
    }

    /**
     * Gold Lootbox: 20% 1000 XP, 20% 800 XP, 15% 1200 XP, 30% Streak Freeze, 15% Doppel-XP
     */
    private function rollGoldReward(int $roll): array
    {
        if ($roll <= 20) {
            return ['type' => 'xp', 'amount' => 1000, 'label' => '1.000 XP'];
        }
        if ($roll <= 40) {
            return ['type' => 'xp', 'amount' => 800, 'label' => '800 XP'];
        }
        if ($roll <= 55) {
            return ['type' => 'xp', 'amount' => 1200, 'label' => '1.200 XP'];
        }
        if ($roll <= 85) {
            return ['type' => 'streak_freeze', 'amount' => 1, 'label' => 'Streak Freeze'];
        }
        return ['type' => 'double_xp', 'amount' => 24, 'label' => 'Doppel-XP Boost (24h)'];
    }

    /**
     * Wendet die Belohnung einer Lootbox an
     */
    private function applyReward(User $user, array $reward): void
    {
        switch ($reward['type']) {
            case 'xp':
                $user->points += $reward['amount'];
                $user->save();
                break;

            case 'streak_freeze':
                $user->streak_freezes_available = ($user->streak_freezes_available ?? GamificationService::MAX_STREAK_FREEZES_PER_WEEK) + 1;
                $user->save();
                break;

            case 'double_xp':
                $user->double_xp_until = Carbon::now()->addHours($reward['amount']);
                $user->save();
                break;
        }
    }

    /**
     * Gibt die ungeöffneten Lootboxen eines Users zurück
     */
    public function getUnopenedLootboxes(User $user): \Illuminate\Support\Collection
    {
        return Lootbox::where('user_id', $user->id)
            ->where('opened', false)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Erstellt eine Liga-Notification
     */
    private function createLeagueNotification(User $user, string $type, array $data): void
    {
        $title = match ($type) {
            'promotion' => 'Aufgestiegen!',
            'relegation' => 'Abgestiegen',
            'lootbox' => 'Lootbox erhalten!',
            default => 'Liga-Update',
        };

        $message = match ($type) {
            'promotion' => 'Du bist in die ' . self::getLeagueName($data['to']) . '-Liga aufgestiegen!',
            'relegation' => 'Du bist in die ' . self::getLeagueName($data['to']) . '-Liga abgestiegen.',
            'lootbox' => 'Du hast eine ' . ucfirst($data['type']) . '-Lootbox für Platz ' . $data['rank'] . ' in der ' . self::getLeagueName($data['league']) . '-Liga erhalten!',
            default => 'Liga-Update',
        };

        $icon = match ($type) {
            'promotion' => 'bi-arrow-up-circle-fill',
            'relegation' => 'bi-arrow-down-circle-fill',
            'lootbox' => 'bi-gift-fill',
            default => 'bi-shield-fill',
        };

        Notification::create([
            'user_id' => $user->id,
            'type' => 'league_' . $type,
            'title' => $title,
            'message' => $message,
            'icon' => $icon,
            'data' => $data,
        ]);
    }
}
