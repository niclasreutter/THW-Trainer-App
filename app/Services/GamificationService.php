<?php

namespace App\Services;

use App\Models\LearningSessionParticipant;
use App\Models\User;
use App\Models\XpHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class GamificationService
{
    // Punktesystem
    const POINTS_PER_QUESTION = 10;
    const POINTS_PER_EXAM_PASS = 100;
    const STREAK_BONUS_MULTIPLIER = 2;
    const DAILY_BONUS = 50;
    const MAX_STREAK_FREEZES_PER_WEEK = 2;
    const STREAK_MIN_QUESTIONS = 20;

    // Level-System (Punkte benötigt für nächstes Level)
    const LEVEL_THRESHOLDS = [
        1 => 0,
        2 => 100,
        3 => 300,
        4 => 600,
        5 => 1000,
        6 => 1500,
        7 => 2200,
        8 => 3000,
        9 => 4000,
        10 => 5500,
        11 => 7500,
        12 => 10000,
        13 => 13000,
        14 => 16500,
        15 => 20500,
        16 => 25000,
        17 => 30000,
        18 => 35500,
        19 => 41500,
        20 => 48000,
    ];

    // Achievements
    const ACHIEVEMENTS = [
        'first_question' => [
            'title' => 'Erste Schritte',
            'description' => 'Erste Frage beantwortet',
            'icon' => 'bi-star-fill'
        ],
        'streak_3' => [
            'title' => 'Feuer entfacht',
            'description' => '3 Tage in Folge gelernt',
            'icon' => 'bi-fire'
        ],
        'streak_7' => [
            'title' => 'Durchstarter',
            'description' => '7 Tage in Folge gelernt',
            'icon' => 'bi-rocket-takeoff'
        ],
        'streak_30' => [
            'title' => 'Lernkoenig',
            'description' => '30 Tage in Folge gelernt',
            'icon' => 'bi-crown-fill'
        ],
        'questions_50' => [
            'title' => 'Wissensdurst',
            'description' => '50 Fragen beantwortet',
            'icon' => 'bi-book-fill'
        ],
        'questions_100' => [
            'title' => 'Denker',
            'description' => '100 Fragen beantwortet',
            'icon' => 'bi-lightbulb-fill'
        ],
        'questions_500' => [
            'title' => 'Experte',
            'description' => '500 Fragen beantwortet',
            'icon' => 'bi-mortarboard-fill'
        ],
        'exam_first' => [
            'title' => 'Erste Pruefung',
            'description' => 'Erste Prüfung bestanden',
            'icon' => 'bi-trophy-fill'
        ],
        'exam_perfect' => [
            'title' => 'Perfektionist',
            'description' => 'Prüfung mit 100% bestanden',
            'icon' => 'bi-gem'
        ],
        'speed_demon' => [
            'title' => 'Blitzschnell',
            'description' => '20 Fragen an einem Tag',
            'icon' => 'bi-lightning-fill'
        ],
        'section_master' => [
            'title' => 'Abschnittsmeister',
            'description' => 'Alle Fragen eines Abschnitts gelöst',
            'icon' => 'bi-bullseye'
        ],
        'level_5' => [
            'title' => 'Aufsteiger',
            'description' => 'Level 5 erreicht',
            'icon' => 'bi-star'
        ],
        'level_10' => [
            'title' => 'Meister',
            'description' => 'Level 10 erreicht',
            'icon' => 'bi-star-fill'
        ],
        'level_15' => [
            'title' => 'Experte',
            'description' => 'Level 15 erreicht',
            'icon' => 'bi-stars'
        ],
        'level_20' => [
            'title' => 'Legende',
            'description' => 'Level 20 erreicht',
            'icon' => 'bi-award-fill'
        ]
    ];

    public function awardPoints(User $user, int $points, string $reason = '', ?string $sourceType = null, ?int $sourceId = null)
    {
        $oldPoints = $user->points;
        $oldLevel = $user->level;

        $user->points += $points;
        $user->level = $this->calculateLevel($user->points);

        // Wöchentliche Punkte auch erhöhen
        $this->updateWeeklyPoints($user, $points);

        $user->save();

        // XP-Verlauf protokollieren
        XpHistory::create([
            'user_id' => $user->id,
            'points' => $points,
            'reason' => $reason ?: 'Punkte vergeben',
            'source_type' => $sourceType,
            'source_id' => $sourceId,
            'total_before' => $oldPoints,
            'total_after' => $user->points,
            'level_before' => $oldLevel,
            'level_after' => $user->level,
        ]);

        $notifications = [];

        // Level-Up Check
        if ($user->level > $oldLevel) {
            $this->checkLevelAchievements($user);

            // Erstelle persistente Notification in DB
            $notification = $this->createNotification($user, [
                'type' => 'level_up',
                'title' => 'Level Up!',
                'message' => "Du hast Level {$user->level} erreicht!",
                'icon' => 'bi-arrow-up-circle-fill',
                'data' => [
                    'level' => $user->level,
                    'old_level' => $oldLevel,
                ]
            ]);

            $notifications[] = [
                'type' => 'level_up',
                'title' => 'Level Up!',
                'message' => "Du hast Level {$user->level} erreicht!",
                'level' => $user->level
            ];

            // Milestone Celebration für Level-Up
            session(['milestone_celebration' => [
                'type' => 'level_up',
                'level' => $user->level,
            ]]);
            session()->save();
        }

        // Store notifications in session (für sofortige Anzeige)
        if (!empty($notifications)) {
            $existingNotifications = session('gamification_notifications', []);
            $allNotifications = array_merge($existingNotifications, $notifications);
            session(['gamification_notifications' => $allNotifications]);
            session()->save(); // Force save

            // Debug-Logging
            \Log::info('Gamification notifications stored in session', [
                'user_id' => $user->id,
                'notifications_count' => count($allNotifications),
                'notifications' => $allNotifications,
                'session_id' => session()->getId()
            ]);
        }

        return [
            'points_awarded' => $points,
            'level_up' => $user->level > $oldLevel,
            'new_level' => $user->level,
            'notifications' => $notifications,
            'reason' => $reason
        ];
    }

    public function updateStreak(User $user, bool $examCompleted = false)
    {
        $today = Carbon::today();
        $lastActivity = $user->last_activity_date ? Carbon::parse($user->last_activity_date) : null;

        // Wenn heute schon als qualifiziert markiert, nichts ändern
        if ($lastActivity && $lastActivity->isSameDay($today)) {
            return;
        }

        // Mindestaktivität prüfen: X Fragen beantwortet ODER 1 Prüfung absolviert
        $meetsMinimum = $examCompleted
            || ($user->daily_questions_solved >= self::STREAK_MIN_QUESTIONS);

        if (!$meetsMinimum) {
            return;
        }

        // Mindestaktivität erreicht - Streak verarbeiten
        $this->resetWeeklyFreezesIfNeeded($user);

        if (!$lastActivity) {
            // Erste Aktivität
            $user->streak_days = 0;
        } elseif ($lastActivity->diffInDays($today) > 1) {
            // Tage verpasst - versuche Streak Freeze
            $missedDays = $lastActivity->diffInDays($today) - 1;
            $frozenDays = 0;

            for ($i = 0; $i < $missedDays; $i++) {
                $missedDate = $lastActivity->copy()->addDays($i + 1);
                if ($this->useStreakFreeze($user, $missedDate)) {
                    $frozenDays++;
                } else {
                    break;
                }
            }

            if ($frozenDays >= $missedDays) {
                // Alle verpassten Tage durch Freezes gedeckt
                $user->streak_days += 1;
                $this->flashStreakExtendedCelebration($user);
                $this->checkStreakAchievements($user);
            } else {
                // Streak unterbrochen
                $user->streak_days = 0;
            }
        } elseif ($lastActivity->diffInDays($today) == 1) {
            // Streak fortgesetzt (normaler Folgetag)
            $user->streak_days += 1;
            $this->flashStreakExtendedCelebration($user);
            $this->checkStreakAchievements($user);
        }

        $user->last_activity_date = $today;
        $user->save();
    }

    /**
     * Aktualisiert nur die Benutzer-Aktivität (für falsche Antworten)
     */
    public function updateUserActivity(User $user)
    {
        $this->updateDailyQuestions($user);
        $this->updateStreak($user);
    }

    public function awardQuestionPoints(User $user, bool $isCorrect = true, ?int $questionId = null)
    {
        if (!$isCorrect) {
            // Bei falscher Antwort: Nur Aktivität aktualisieren, keine Punkte
            $this->updateUserActivity($user);
            return null;
        }

        $this->updateDailyQuestions($user);
        $this->updateStreak($user);

        $basePoints = self::POINTS_PER_QUESTION;

        // Prüfe ob es eine Top-Wrong-Frage ist (doppelte Punkte)
        $topWrongBonus = 0;
        $reason = 'Frage beantwortet';

        if ($questionId) {
            $topWrongQuestions = \Cache::get('top_wrong_questions', []);
            if (in_array($questionId, $topWrongQuestions)) {
                $topWrongBonus = $basePoints; // Verdoppelt die Punkte
                $reason = 'Häufig falsche Frage gelöst';
            }
        }

        $streakBonus = $user->streak_days >= 3 ? $basePoints * (self::STREAK_BONUS_MULTIPLIER - 1) : 0;
        $totalPoints = $basePoints + $topWrongBonus + $streakBonus;

        // Boost prüfen (additives Multiplikator-System)
        $hasDoubleXp = $user->double_xp_until && Carbon::parse($user->double_xp_until)->isFuture();
        $hasWeekendBoost = $user->weekend_boost_until && Carbon::parse($user->weekend_boost_until)->isFuture()
            && Carbon::now()->isWeekend();
        $inLernsession = $this->isInActiveLernsession($user);

        $multiplier = 1.0;
        if ($hasDoubleXp) {
            $multiplier += 1.0;
            $reason .= ' (Doppel-XP)';
        } elseif ($hasWeekendBoost) {
            $multiplier += 0.5;
            $reason .= ' (Wochenend-Boost)';
        }

        if ($inLernsession) {
            $multiplier += 0.5;
            $reason .= ' (Lernsession-Boost)';
        }

        $totalPoints = (int) round($totalPoints * $multiplier);

        $result = $this->awardPoints($user, $totalPoints, $reason, 'question', $questionId);

        $this->checkQuestionAchievements($user);
        $this->checkDailyAchievements($user);
        $this->checkSectionAchievements($user);

        return $result;
    }

    public function awardExamPoints(User $user, int $correctAnswers, int $totalQuestions)
    {
        $this->updateStreak($user, examCompleted: true);

        $percentage = ($correctAnswers / $totalQuestions) * 100;
        $passed = $percentage >= 80;

        if ($passed) {
            $basePoints = self::POINTS_PER_EXAM_PASS;
            $perfectBonus = $percentage == 100 ? 50 : 0;
            $totalPoints = $basePoints + $perfectBonus;

            // Boost prüfen (additives Multiplikator-System)
            $reason = 'Prüfung bestanden';
            $hasDoubleXp = $user->double_xp_until && Carbon::parse($user->double_xp_until)->isFuture();
            $hasWeekendBoost = $user->weekend_boost_until && Carbon::parse($user->weekend_boost_until)->isFuture()
                && Carbon::now()->isWeekend();
            $inLernsession = $this->isInActiveLernsession($user);

            $multiplier = 1.0;
            if ($hasDoubleXp) {
                $multiplier += 1.0;
                $reason .= ' (Doppel-XP)';
            } elseif ($hasWeekendBoost) {
                $multiplier += 0.5;
                $reason .= ' (Wochenend-Boost)';
            }

            if ($inLernsession) {
                $multiplier += 0.5;
                $reason .= ' (Lernsession-Boost)';
            }

            $totalPoints = (int) round($totalPoints * $multiplier);

            $result = $this->awardPoints($user, $totalPoints, $reason, 'exam');
            
            $this->checkExamAchievements($user, $percentage);
            
            return $result;
        }

        return null;
    }

    /**
     * Aktualisiert den Streak nach einer abgeschlossenen Prüfung (bestanden oder nicht).
     */
    public function recordExamActivity(User $user)
    {
        $this->updateStreak($user, examCompleted: true);
    }

    private function updateDailyQuestions(User $user)
    {
        $today = Carbon::today();
        
        if (!$user->daily_questions_date || Carbon::parse($user->daily_questions_date)->lt($today)) {
            $user->daily_questions_solved = 1;
            $user->daily_questions_date = $today;
        } else {
            $user->daily_questions_solved += 1;
        }
        
        $user->save();
    }

    private function calculateLevel(int $points)
    {
        $level = 1;
        foreach (self::LEVEL_THRESHOLDS as $levelNum => $threshold) {
            if ($points >= $threshold) {
                $level = $levelNum;
            } else {
                break;
            }
        }
        return $level;
    }

    public function getNextLevelPoints(User $user)
    {
        $nextLevel = $user->level + 1;
        $nextThreshold = self::LEVEL_THRESHOLDS[$nextLevel] ?? null;
        
        if ($nextThreshold) {
            return $nextThreshold - $user->points;
        }
        
        return 0; // Max level erreicht
    }

    public function getLevelProgress(User $user)
    {
        $currentLevel = $user->level;
        $nextLevel = $currentLevel + 1;
        
        $currentThreshold = self::LEVEL_THRESHOLDS[$currentLevel] ?? 0;
        $nextThreshold = self::LEVEL_THRESHOLDS[$nextLevel] ?? null;
        
        // Wenn es kein nächstes Level gibt, sind wir bei 100%
        if (!$nextThreshold) {
            return 100;
        }
        
        $currentPoints = $user->points ?? 0;
        $progressInLevel = $currentPoints - $currentThreshold;
        $pointsNeededForLevel = $nextThreshold - $currentThreshold;
        
        if ($pointsNeededForLevel <= 0) {
            return 100;
        }
        
        $progress = ($progressInLevel / $pointsNeededForLevel) * 100;
        return max(0, min(100, $progress));
    }

    private function checkQuestionAchievements(User $user)
    {
        $solvedQuestions = $this->ensureArray($user->solved_questions);
        $totalQuestions = count($solvedQuestions);
        
        $achievements = [
            1 => 'first_question',
            50 => 'questions_50',
            100 => 'questions_100',
            500 => 'questions_500'
        ];

        foreach ($achievements as $count => $achievement) {
            if ($totalQuestions >= $count) {
                $this->unlockAchievement($user, $achievement);
            }
        }
    }

    /**
     * Zeigt eine Fullscreen-Celebration wenn der Streak verlängert wurde.
     * Wird VOR checkStreakAchievements aufgerufen, damit Meilensteine (7, 30, 100) diese überschreiben.
     */
    private function flashStreakExtendedCelebration(User $user)
    {
        session(['milestone_celebration' => [
            'type' => 'streak_extended',
            'days' => $user->streak_days,
        ]]);
        session()->save();
    }

    private function checkStreakAchievements(User $user)
    {
        $achievements = [
            3 => 'streak_3',
            7 => 'streak_7',
            30 => 'streak_30'
        ];

        foreach ($achievements as $days => $achievement) {
            if ($user->streak_days >= $days) {
                $this->unlockAchievement($user, $achievement);
            }
        }

        // Milestone Celebrations für Streak-Meilensteine
        $milestoneStreaks = [7, 30, 100];
        if (in_array($user->streak_days, $milestoneStreaks)) {
            session(['milestone_celebration' => [
                'type' => 'streak',
                'days' => $user->streak_days,
            ]]);
            session()->save();
        }
    }

    private function checkExamAchievements(User $user, float $percentage)
    {
        if ($user->exam_passed_count == 1) {
            $this->unlockAchievement($user, 'exam_first');
        }

        if ($percentage == 100) {
            $this->unlockAchievement($user, 'exam_perfect');
        }
    }

    private function checkLevelAchievements(User $user)
    {
        if ($user->level >= 5) {
            $this->unlockAchievement($user, 'level_5');
        }
        if ($user->level >= 10) {
            $this->unlockAchievement($user, 'level_10');
        }
        if ($user->level >= 15) {
            $this->unlockAchievement($user, 'level_15');
        }
        if ($user->level >= 20) {
            $this->unlockAchievement($user, 'level_20');
        }
    }

    private function checkDailyAchievements(User $user)
    {
        if ($user->daily_questions_solved >= 20) {
            $this->unlockAchievement($user, 'speed_demon');
        }
    }

    private function checkSectionAchievements(User $user)
    {
        $solved = $this->ensureArray($user->solved_questions);
        
        // Überprüfe jeden Lernabschnitt (1-10)
        for ($section = 1; $section <= 10; $section++) {
            $sectionQuestionIds = \App\Models\Question::where('lernabschnitt', $section)->pluck('id')->toArray();
            
            if (!empty($sectionQuestionIds)) {
                // Prüfe ob alle Fragen des Abschnitts gelöst sind
                $solvedInSection = array_intersect($solved, $sectionQuestionIds);
                
                if (count($solvedInSection) === count($sectionQuestionIds)) {
                    // Alle Fragen dieses Abschnitts sind gelöst
                    // Vergebe das Achievement, wenn es noch nicht vorhanden ist
                    $achievements = $this->ensureArray($user->achievements);
                    if (!in_array('section_master', $achievements)) {
                        $this->unlockAchievement($user, 'section_master');
                        
                        // Debug-Log
                        \Log::info("Abschnittsmeister Achievement vergeben für User {$user->id}, Abschnitt {$section}");
                    }
                }
            }
        }
    }

    public function unlockAchievement(User $user, string $achievementKey)
    {
        $achievements = $this->ensureArray($user->achievements);

        if (!in_array($achievementKey, $achievements)) {
            $achievements[] = $achievementKey;
            $user->achievements = $achievements;
            $user->save();

            // Add achievement notification to session + DB
            $achievement = self::ACHIEVEMENTS[$achievementKey] ?? null;
            if ($achievement) {
                // Erstelle persistente Notification in DB
                $this->createNotification($user, [
                    'type' => 'achievement',
                    'title' => 'Neues Achievement!',
                    'message' => $achievement['title'],
                    'icon' => $achievement['icon'],
                    'data' => [
                        'achievement_key' => $achievementKey,
                        'description' => $achievement['description'],
                    ]
                ]);

                // Auch in Session für sofortige Anzeige
                $notification = [
                    'type' => 'achievement',
                    'title' => 'Neues Achievement!',
                    'message' => $achievement['title'],
                    'description' => $achievement['description'],
                    'icon' => $achievement['icon']
                ];

                $existingNotifications = session('gamification_notifications', []);
                $existingNotifications[] = $notification;
                session(['gamification_notifications' => $existingNotifications]);
                session()->save(); // Force save

                // Debug-Logging
                \Log::info('Achievement notification stored in session', [
                    'user_id' => $user->id,
                    'achievement_key' => $achievementKey,
                    'notification' => $notification,
                    'session_id' => session()->getId()
                ]);
            }

            return true; // Neues Achievement
        }

        return false; // Bereits vorhanden
    }

    public function getUserAchievements(User $user)
    {
        $userAchievements = $this->ensureArray($user->achievements);
        $result = [];
        
        foreach (self::ACHIEVEMENTS as $key => $achievement) {
            $result[] = [
                'key' => $key,
                'unlocked' => in_array($key, $userAchievements),
                'title' => $achievement['title'],
                'description' => $achievement['description'],
                'icon' => $achievement['icon']
            ];
        }
        
        return $result;
    }

    public function getLeaderboard(int $limit = 10)
    {
        return User::where('leaderboard_consent', true)
                   ->select(['id', 'name', 'points', 'level', 'streak_days', 'leaderboard_consent',
                          'glowing_name_until', 'glowing_name_type', 'profile_frame_until', 'profile_frame_type',
                          'rank_color', 'rank_color_until', 'active_title', 'active_title_until',
                          'total_points_spent',
                          DB::raw('(points + COALESCE(total_points_spent, 0)) as total_points_earned')])
                   ->orderBy('total_points_earned', 'desc')
                   ->orderBy('level', 'desc')
                   ->limit($limit)
                   ->get();
    }

    /**
     * Holt das wöchentliche Leaderboard (Montag - Sonntag)
     */
    public function getWeeklyLeaderboard(int $limit = 10)
    {
        // Reset aller User wenn nötig
        $this->resetWeeklyPointsIfNeeded();

        return User::where('leaderboard_consent', true)
                   ->where('weekly_points', '>', 0)
                   ->orderBy('weekly_points', 'desc')
                   ->orderBy('points', 'desc')
                   ->limit($limit)
                   ->get(['id', 'name', 'weekly_points', 'points', 'level', 'streak_days', 'leaderboard_consent',
                          'glowing_name_until', 'glowing_name_type', 'profile_frame_until', 'profile_frame_type',
                          'rank_color', 'rank_color_until', 'active_title', 'active_title_until']);
    }

    /**
     * Aktualisiert die wöchentlichen Punkte eines Users
     */
    private function updateWeeklyPoints(User $user, int $points)
    {
        // Prüfe ob die Woche bereits zurückgesetzt wurde
        $this->resetWeeklyPointsIfNeeded($user);
        
        // Füge Punkte hinzu
        $user->weekly_points += $points;
    }

    /**
     * Setzt wöchentliche Punkte zurück wenn eine neue Woche begonnen hat (Montag)
     */
    private function resetWeeklyPointsIfNeeded(?User $user = null)
    {
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        
        if ($user) {
            // Reset für einzelnen User
            if (!$user->weekly_reset_at || Carbon::parse($user->weekly_reset_at)->lt($startOfWeek)) {
                $user->weekly_points = 0;
                $user->weekly_reset_at = $startOfWeek;
            }
        } else {
            // Reset für alle User die noch nicht zurückgesetzt wurden
            User::where(function($query) use ($startOfWeek) {
                $query->whereNull('weekly_reset_at')
                      ->orWhere('weekly_reset_at', '<', $startOfWeek);
            })
            ->update([
                'weekly_points' => 0,
                'weekly_reset_at' => $startOfWeek
            ]);
        }
    }

    /**
     * Gibt den Start und Ende der aktuellen Woche zurück (Montag - Sonntag)
     */
    public function getCurrentWeekRange()
    {
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = Carbon::now()->endOfWeek(Carbon::SUNDAY);
        
        return [
            'start' => $startOfWeek,
            'end' => $endOfWeek,
            'formatted' => $startOfWeek->format('d.m.Y') . ' - ' . $endOfWeek->format('d.m.Y')
        ];
    }

    /**
     * Versucht einen Streak Freeze für einen verpassten Tag zu verwenden.
     */
    private function useStreakFreeze(User $user, Carbon $missedDate): bool
    {
        $available = $user->streak_freezes_available ?? 0;
        $used = $user->streak_freezes_used ?? 0;

        if ($used >= $available) {
            return false;
        }

        $user->streak_freezes_used = $used + 1;

        $log = $this->ensureArray($user->streak_freeze_log);
        $log[] = [
            'date' => $missedDate->toDateString(),
            'used_at' => Carbon::now()->toDateTimeString(),
        ];
        $user->streak_freeze_log = $log;

        \Log::info('Streak Freeze verwendet', [
            'user_id' => $user->id,
            'missed_date' => $missedDate->toDateString(),
            'freezes_used' => $user->streak_freezes_used,
        ]);

        return true;
    }

    /**
     * Setzt wöchentliche Streak Freezes zurück (Montag).
     */
    private function resetWeeklyFreezesIfNeeded(User $user)
    {
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);

        if (!$user->streak_freeze_reset_at || Carbon::parse($user->streak_freeze_reset_at)->lt($startOfWeek)) {
            $user->streak_freezes_used = 0;
            $user->streak_freeze_reset_at = $startOfWeek;
        }
    }

    /**
     * Gibt Streak-Freeze-Status des Users zurück.
     */
    public function getStreakFreezeStatus(User $user): array
    {
        $this->resetWeeklyFreezesIfNeeded($user);

        $available = $user->streak_freezes_available ?? 0;
        $used = $user->streak_freezes_used ?? 0;

        // Inkonsistenz bereinigen: used darf nie > available sein
        if ($used > $available) {
            $user->streak_freezes_used = 0;
            $used = 0;
            $user->save();
        }

        $log = $this->ensureArray($user->streak_freeze_log);

        // Nur die letzten 4 Wochen im Log
        $fourWeeksAgo = Carbon::now()->subWeeks(4)->toDateString();
        $recentLog = array_filter($log, fn($entry) => ($entry['date'] ?? '') >= $fourWeeksAgo);

        return [
            'available' => $available,
            'used' => $used,
            'remaining' => max(0, $available - $used),
            'recent_log' => array_values($recentLog),
        ];
    }

    /**
     * Setzt manuell einen Streak Freeze für heute ein (Dashboard-Aktion).
     */
    public function applyManualFreeze(User $user): array
    {
        $today = Carbon::today();

        if (($user->streak_days ?? 0) === 0) {
            return ['success' => false, 'message' => 'Kein aktiver Streak vorhanden.'];
        }

        $lastActivity = $user->last_activity_date ? Carbon::parse($user->last_activity_date) : null;
        if ($lastActivity && $lastActivity->isSameDay($today)) {
            return ['success' => false, 'message' => 'Du hast heute bereits gelernt – der Streak ist sicher.'];
        }

        $this->resetWeeklyFreezesIfNeeded($user);

        $available = $user->streak_freezes_available ?? 0;
        $used = $user->streak_freezes_used ?? 0;

        if ($used >= $available) {
            return ['success' => false, 'message' => 'Keine Streak Freezes mehr verfügbar. Kaufe Freezes im Shop.'];
        }

        $log = $this->ensureArray($user->streak_freeze_log);
        $alreadyFrozenToday = array_filter($log, fn($entry) => ($entry['date'] ?? '') === $today->toDateString());
        if (!empty($alreadyFrozenToday)) {
            return ['success' => false, 'message' => 'Heute wurde bereits ein Freeze eingesetzt.'];
        }

        $user->streak_freezes_used = $used + 1;
        $log[] = [
            'date'    => $today->toDateString(),
            'used_at' => Carbon::now()->toDateTimeString(),
            'manual'  => true,
        ];
        $user->streak_freeze_log  = $log;
        $user->last_activity_date = $today;
        $user->save();

        \Log::info('Manueller Streak Freeze eingesetzt', [
            'user_id'           => $user->id,
            'date'              => $today->toDateString(),
            'freezes_remaining' => max(0, $available - $user->streak_freezes_used),
        ]);

        return [
            'success'   => true,
            'message'   => 'Streak Freeze eingesetzt! Dein Streak ist heute geschützt.',
            'remaining' => max(0, $available - $user->streak_freezes_used),
        ];
    }

    /**
     * Stellt sicher, dass ein Wert ein Array ist (für Legacy-Kompatibilität)
     */
    private function ensureArray($value)
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    /**
     * Prüft ob ein User in einer aktiven Lernsession teilnimmt (mit 60s Cache)
     */
    private function isInActiveLernsession(User $user): bool
    {
        $cacheKey = "lernsession_active_bool_{$user->id}";

        return Cache::remember($cacheKey, 60, function () use ($user) {
            return LearningSessionParticipant::whereHas('instance', function ($q) {
                $q->where('status', 'active')
                  ->where('starts_at', '<=', now())
                  ->where('ends_at', '>=', now());
            })->where('user_id', $user->id)->exists();
        });
    }

    /**
     * Erstellt eine persistente Notification in der Datenbank
     */
    private function createNotification(User $user, array $data)
    {
        return \App\Models\Notification::create([
            'user_id' => $user->id,
            'type' => $data['type'],
            'title' => $data['title'],
            'message' => $data['message'],
            'icon' => $data['icon'] ?? null,
            'data' => $data['data'] ?? null,
        ]);
    }
}
