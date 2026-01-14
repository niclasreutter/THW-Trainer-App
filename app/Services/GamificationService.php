<?php

namespace App\Services;

use App\Models\User;
use App\Models\Achievement;
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
        $totalQuestionsInDb = \App\Models\Question::count();
        $questionPercent = $totalQuestionsInDb > 0 ? ($totalQuestions / $totalQuestionsInDb) * 100 : 0;

        // Hole alle aktiven question_count und question_percent Achievements
        $achievements = Achievement::active()
            ->whereIn('trigger_type', ['question_count', 'question_percent'])
            ->get();

        foreach ($achievements as $achievement) {
            $config = $achievement->trigger_config ?? [];
            $requiredValue = $config['value'] ?? 0;

            $shouldUnlock = false;

            if ($achievement->trigger_type === 'question_count') {
                $shouldUnlock = $totalQuestions >= $requiredValue;
            } elseif ($achievement->trigger_type === 'question_percent') {
                $shouldUnlock = $questionPercent >= $requiredValue;
            }

            if ($shouldUnlock) {
                $this->unlockAchievement($user, $achievement->key);
            }
        }
    }

    private function checkStreakAchievements(User $user)
    {
        // Hole alle aktiven streak_days Achievements
        $achievements = Achievement::active()
            ->where('trigger_type', 'streak_days')
            ->get();

        foreach ($achievements as $achievement) {
            $config = $achievement->trigger_config ?? [];
            $requiredDays = $config['value'] ?? 0;

            if ($user->streak_days >= $requiredDays) {
                $this->unlockAchievement($user, $achievement->key);
            }
        }
    }

    private function checkExamAchievements(User $user, float $percentage)
    {
        // Hole alle aktiven exam Achievements
        $achievements = Achievement::active()
            ->whereIn('trigger_type', ['exam_passed_count', 'exam_perfect'])
            ->get();

        foreach ($achievements as $achievement) {
            $config = $achievement->trigger_config ?? [];

            if ($achievement->trigger_type === 'exam_passed_count') {
                $requiredCount = $config['value'] ?? 1;
                if ($user->exam_passed_count >= $requiredCount) {
                    $this->unlockAchievement($user, $achievement->key);
                }
            } elseif ($achievement->trigger_type === 'exam_perfect') {
                if ($percentage == 100) {
                    $this->unlockAchievement($user, $achievement->key);
                }
            }
        }
    }

    private function checkLevelAchievements(User $user)
    {
        // Hole alle aktiven level_reached Achievements
        $achievements = Achievement::active()
            ->where('trigger_type', 'level_reached')
            ->get();

        foreach ($achievements as $achievement) {
            $config = $achievement->trigger_config ?? [];
            $requiredLevel = $config['value'] ?? 0;

            if ($user->level >= $requiredLevel) {
                $this->unlockAchievement($user, $achievement->key);
            }
        }
    }

    private function checkDailyAchievements(User $user)
    {
        // Hole alle aktiven daily_questions Achievements
        $achievements = Achievement::active()
            ->where('trigger_type', 'daily_questions')
            ->get();

        foreach ($achievements as $achievement) {
            $config = $achievement->trigger_config ?? [];
            $requiredQuestions = $config['value'] ?? 0;

            if ($user->daily_questions_solved >= $requiredQuestions) {
                $this->unlockAchievement($user, $achievement->key);
            }
        }
    }

    private function checkSectionAchievements(User $user)
    {
        $solved = $this->ensureArray($user->solved_questions);

        // Hole alle aktiven section_complete Achievements
        $achievements = Achievement::active()
            ->where('trigger_type', 'section_complete')
            ->get();

        foreach ($achievements as $achievement) {
            $config = $achievement->trigger_config ?? [];
            $specificSection = $config['section'] ?? null;
            $anySection = $config['any_section'] ?? false;

            if ($anySection) {
                // Prüfe ob irgendein Abschnitt komplett gelöst ist
                for ($section = 1; $section <= 10; $section++) {
                    if ($this->isSectionComplete($user, $section, $solved)) {
                        $this->unlockAchievement($user, $achievement->key);
                        break; // Ein Abschnitt reicht
                    }
                }
            } elseif ($specificSection) {
                // Prüfe nur einen spezifischen Abschnitt
                if ($this->isSectionComplete($user, $specificSection, $solved)) {
                    $this->unlockAchievement($user, $achievement->key);
                }
            }
        }
    }

    /**
     * Prüft ob ein Abschnitt vollständig gelöst ist
     */
    private function isSectionComplete(User $user, int $section, array $solved = null): bool
    {
        if ($solved === null) {
            $solved = $this->ensureArray($user->solved_questions);
        }

        $sectionQuestionIds = \App\Models\Question::where('lernabschnitt', $section)->pluck('id')->toArray();

        if (empty($sectionQuestionIds)) {
            return false;
        }

        $solvedInSection = array_intersect($solved, $sectionQuestionIds);
        return count($solvedInSection) === count($sectionQuestionIds);
    }

    public function unlockAchievement(User $user, string $achievementKey)
    {
        // Hole Achievement aus Datenbank
        $achievement = Achievement::where('key', $achievementKey)
            ->where('is_active', true)
            ->first();

        if (!$achievement) {
            \Log::warning("Achievement with key '{$achievementKey}' not found or inactive");
            return false;
        }

        // Prüfe ob User das Achievement bereits hat
        $hasAchievement = $user->userAchievements()
            ->where('achievement_id', $achievement->id)
            ->exists();

        if ($hasAchievement) {
            return false; // Bereits vorhanden
        }

        // Füge Achievement zum User hinzu
        $user->userAchievements()->attach($achievement->id, [
            'unlocked_at' => now(),
        ]);

        // LEGACY: Auch in der alten JSON-Spalte speichern (für Abwärtskompatibilität)
        $legacyAchievements = $this->ensureArray($user->achievements);
        if (!in_array($achievementKey, $legacyAchievements)) {
            $legacyAchievements[] = $achievementKey;
            $user->achievements = $legacyAchievements;
            $user->save();
        }

        // Erstelle persistente Notification in DB
        $this->createNotification($user, [
            'type' => 'achievement',
            'title' => '🏆 Neues Achievement!',
            'message' => $achievement->title,
            'icon' => $achievement->icon,
            'data' => [
                'achievement_key' => $achievementKey,
                'description' => $achievement->description,
            ]
        ]);

        // Auch in Session für sofortige Anzeige
        $notification = [
            'type' => 'achievement',
            'title' => '🏆 Neues Achievement!',
            'message' => $achievement->title,
            'description' => $achievement->description,
            'icon' => $achievement->icon
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

        return true; // Neues Achievement
    }

    public function getUserAchievements(User $user)
    {
        // Hole alle aktiven Achievements aus Datenbank
        $allAchievements = Achievement::active()
            ->sorted()
            ->get();

        // Hole User-Achievement-IDs
        $unlockedIds = $user->userAchievements()->pluck('achievements.id')->toArray();

        $result = [];
        foreach ($allAchievements as $achievement) {
            $result[] = [
                'key' => $achievement->key,
                'unlocked' => in_array($achievement->id, $unlockedIds),
                'title' => $achievement->title,
                'description' => $achievement->description,
                'icon' => $achievement->icon ?? '🏆'
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
