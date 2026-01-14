<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class GamificationService
{
    // Punktesystem
    const POINTS_PER_QUESTION = 10;
    const POINTS_PER_EXAM_PASS = 100;
    const STREAK_BONUS_MULTIPLIER = 2;
    const DAILY_BONUS = 50;

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
            'title' => '🌟 Erste Schritte',
            'description' => 'Erste Frage beantwortet',
            'icon' => '🎯'
        ],
        'streak_3' => [
            'title' => '🔥 Feuer entfacht',
            'description' => '3 Tage in Folge gelernt',
            'icon' => '🔥'
        ],
        'streak_7' => [
            'title' => '🚀 Durchstarter',
            'description' => '7 Tage in Folge gelernt',
            'icon' => '🚀'
        ],
        'streak_30' => [
            'title' => '👑 Lernkönig',
            'description' => '30 Tage in Folge gelernt',
            'icon' => '👑'
        ],
        'questions_50' => [
            'title' => '📚 Wissensdurst',
            'description' => '50 Fragen beantwortet',
            'icon' => '📚'
        ],
        'questions_100' => [
            'title' => '🧠 Denker',
            'description' => '100 Fragen beantwortet',
            'icon' => '🧠'
        ],
        'questions_500' => [
            'title' => '🎓 Experte',
            'description' => '500 Fragen beantwortet',
            'icon' => '🎓'
        ],
        'exam_first' => [
            'title' => '🏆 Erste Prüfung',
            'description' => 'Erste Prüfung bestanden',
            'icon' => '🏆'
        ],
        'exam_perfect' => [
            'title' => '💎 Perfektionist',
            'description' => 'Prüfung mit 100% bestanden',
            'icon' => '💎'
        ],
        'speed_demon' => [
            'title' => '⚡ Blitzschnell',
            'description' => '20 Fragen an einem Tag',
            'icon' => '⚡'
        ],
        'section_master' => [
            'title' => '🎯 Abschnittsmeister',
            'description' => 'Alle Fragen eines Abschnitts gelöst',
            'icon' => '🎯'
        ],
        'level_5' => [
            'title' => '⭐ Aufsteiger',
            'description' => 'Level 5 erreicht',
            'icon' => '⭐'
        ],
        'level_10' => [
            'title' => '🌟 Meister',
            'description' => 'Level 10 erreicht',
            'icon' => '🌟'
        ],
        'level_15' => [
            'title' => '💫 Experte',
            'description' => 'Level 15 erreicht',
            'icon' => '💫'
        ],
        'level_20' => [
            'title' => '🏅 Legende',
            'description' => 'Level 20 erreicht',
            'icon' => '🏅'
        ]
    ];

    public function awardPoints(User $user, int $points, string $reason = '')
    {
        $oldPoints = $user->points;
        $oldLevel = $user->level;

        $user->points += $points;
        $user->level = $this->calculateLevel($user->points);

        // Wöchentliche Punkte auch erhöhen
        $this->updateWeeklyPoints($user, $points);

        $user->save();

        $notifications = [];

        // Level-Up Check
        if ($user->level > $oldLevel) {
            $this->checkLevelAchievements($user);

            // Erstelle persistente Notification in DB
            $notification = $this->createNotification($user, [
                'type' => 'level_up',
                'title' => '🎉 Level Up!',
                'message' => "Du hast Level {$user->level} erreicht!",
                'icon' => '🎉',
                'data' => [
                    'level' => $user->level,
                    'old_level' => $oldLevel,
                ]
            ]);

            $notifications[] = [
                'type' => 'level_up',
                'title' => '🎉 Level Up!',
                'message' => "Du hast Level {$user->level} erreicht!",
                'level' => $user->level
            ];
        }

        // Store notifications in session (für sofortige Anzeige)
        if (!empty($notifications)) {
            $existingNotifications = session('gamification_notifications', []);
            $allNotifications = array_merge($existingNotifications, $notifications);
            session(['gamification_notifications' => $allNotifications]);
            session()->save(); // Force save

            // Debug-Logging
            \Log::info('🎉 Gamification notifications stored in session', [
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

    public function updateStreak(User $user)
    {
        $today = Carbon::today();
        $lastActivity = $user->last_activity_date ? Carbon::parse($user->last_activity_date) : null;

        if (!$lastActivity || $lastActivity->diffInDays($today) > 1) {
            // Streak unterbrochen oder erste Aktivität
            $user->streak_days = 0; // Erste Aktivität = 0 Tage Streak
        } elseif ($lastActivity->diffInDays($today) == 1) {
            // Streak fortgesetzt
            $user->streak_days += 1;
            $this->checkStreakAchievements($user);
        }
        // Wenn heute schon aktiv war, nichts ändern

        $user->last_activity_date = $today;
        $user->save();
    }

    /**
     * Aktualisiert nur die Benutzer-Aktivität (für falsche Antworten)
     */
    public function updateUserActivity(User $user)
    {
        $this->updateStreak($user);
        $this->updateDailyQuestions($user);
    }

    public function awardQuestionPoints(User $user, bool $isCorrect = true, int $questionId = null)
    {
        if (!$isCorrect) {
            // Bei falscher Antwort: Nur Aktivität aktualisieren, keine Punkte
            $this->updateUserActivity($user);
            return null;
        }

        $this->updateStreak($user);
        $this->updateDailyQuestions($user);

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

        $result = $this->awardPoints($user, $totalPoints, $reason);
        
        $this->checkQuestionAchievements($user);
        $this->checkDailyAchievements($user);
        $this->checkSectionAchievements($user);

        return $result;
    }

    public function awardExamPoints(User $user, int $correctAnswers, int $totalQuestions)
    {
        $this->updateStreak($user);
        
        $percentage = ($correctAnswers / $totalQuestions) * 100;
        $passed = $percentage >= 80;

        if ($passed) {
            $basePoints = self::POINTS_PER_EXAM_PASS;
            $perfectBonus = $percentage == 100 ? 50 : 0;
            $totalPoints = $basePoints + $perfectBonus;

            $result = $this->awardPoints($user, $totalPoints, 'Prüfung bestanden');
            
            $this->checkExamAchievements($user, $percentage);
            
            return $result;
        }

        return null;
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
                    'title' => '🏆 Neues Achievement!',
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
                    'title' => '🏆 Neues Achievement!',
                    'message' => $achievement['title'],
                    'description' => $achievement['description'],
                    'icon' => $achievement['icon']
                ];

                $existingNotifications = session('gamification_notifications', []);
                $existingNotifications[] = $notification;
                session(['gamification_notifications' => $existingNotifications]);
                session()->save(); // Force save

                // Debug-Logging
                \Log::info('🏆 Achievement notification stored in session', [
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
                   ->orderBy('points', 'desc')
                   ->orderBy('level', 'desc')
                   ->limit($limit)
                   ->get(['name', 'points', 'level', 'streak_days', 'leaderboard_consent']);
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
                   ->get(['name', 'weekly_points', 'points', 'level', 'streak_days', 'leaderboard_consent']);
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
