<?php

namespace App\Services;

use App\Models\User;
use App\Models\MonthlyChallenge;
use App\Models\Notification;
use Illuminate\Support\Collection;

class MonthlyChallengeService
{
    const CHALLENGE_TYPES = [
        'total_questions' => [
            'title_template' => '%d Fragen diesen Monat',
            'targets_by_level' => ['low' => 200, 'mid' => 350, 'high' => 500],
        ],
        'active_days' => [
            'title_template' => '%d Tage aktiv diesen Monat',
            'targets_by_level' => ['low' => 12, 'mid' => 16, 'high' => 20],
        ],
        'exams_passed' => [
            'title_template' => '%d Pruefungen diesen Monat bestehen',
            'targets_by_level' => ['low' => 2, 'mid' => 3, 'high' => 5],
        ],
        'correct_answers' => [
            'title_template' => '%d richtige Antworten diesen Monat',
            'targets_by_level' => ['low' => 150, 'mid' => 250, 'high' => 400],
        ],
    ];

    const MILESTONE_REWARDS = [
        25  => 25,
        50  => 50,
        75  => 75,
        100 => 150,
    ];

    public function getOrCreateMonthlyChallenge(User $user): MonthlyChallenge
    {
        $month = now()->format('Y-m');

        $existing = MonthlyChallenge::where('user_id', $user->id)
            ->where('challenge_month', $month)
            ->first();

        if ($existing) {
            return $existing;
        }

        $seed = crc32($user->id . '-' . $month);
        mt_srand($seed);

        $types = array_keys(self::CHALLENGE_TYPES);
        $type = $types[mt_rand(0, count($types) - 1)];
        mt_srand(); // Reset

        $config = self::CHALLENGE_TYPES[$type];
        $level = $user->level ?? 1;
        $tier = $level <= 5 ? 'low' : ($level <= 12 ? 'mid' : 'high');
        $target = $config['targets_by_level'][$tier];
        $title = sprintf($config['title_template'], $target);

        return MonthlyChallenge::create([
            'user_id' => $user->id,
            'challenge_month' => $month,
            'challenge_type' => $type,
            'challenge_title' => $title,
            'target_value' => $target,
            'current_value' => 0,
        ]);
    }

    public function updateProgress(User $user, string $eventType, int $increment = 1): void
    {
        $challenge = $this->getOrCreateMonthlyChallenge($user);

        if ($challenge->is_completed) {
            return;
        }

        $matches = match ($eventType) {
            'question_answered' => $challenge->challenge_type === 'total_questions',
            'correct_answer' => $challenge->challenge_type === 'correct_answers',
            'exam_passed' => $challenge->challenge_type === 'exams_passed',
            'active_day' => $challenge->challenge_type === 'active_days',
            default => false,
        };

        if (!$matches) {
            return;
        }

        $challenge->current_value += $increment;

        if ($challenge->current_value >= $challenge->target_value && !$challenge->is_completed) {
            $challenge->is_completed = true;
            $challenge->completed_at = now();
        }

        $challenge->save();

        // Check for newly reached milestones
        $percent = min(100, (int) round(($challenge->current_value / $challenge->target_value) * 100));
        foreach (self::MILESTONE_REWARDS as $milestone => $xp) {
            $claimedField = "milestone_{$milestone}_claimed";
            if ($percent >= $milestone && !$challenge->$claimedField) {
                // Milestone reached but not auto-claimed; user must claim manually
            }
        }
    }

    public function claimMilestone(User $user, int $milestonePercent): array
    {
        if (!isset(self::MILESTONE_REWARDS[$milestonePercent])) {
            return ['success' => false, 'message' => 'Ungueltiger Meilenstein.'];
        }

        $challenge = $this->getOrCreateMonthlyChallenge($user);
        $percent = $challenge->target_value > 0
            ? min(100, (int) round(($challenge->current_value / $challenge->target_value) * 100))
            : 0;

        if ($percent < $milestonePercent) {
            return ['success' => false, 'message' => 'Meilenstein noch nicht erreicht.'];
        }

        $field = "milestone_{$milestonePercent}_claimed";
        if ($challenge->$field) {
            return ['success' => false, 'message' => 'Meilenstein bereits beansprucht.'];
        }

        $xp = self::MILESTONE_REWARDS[$milestonePercent];
        $challenge->$field = true;
        $challenge->save();

        $gamificationService = new GamificationService();
        $gamificationService->awardPoints($user, $xp, "Monats-Meilenstein {$milestonePercent}%");

        return ['success' => true, 'message' => "+{$xp} XP fuer {$milestonePercent}%-Meilenstein!", 'xp' => $xp];
    }

    public function getChallengeWidgetData(User $user): array
    {
        $challenge = $this->getOrCreateMonthlyChallenge($user);
        $percent = $challenge->target_value > 0
            ? min(100, (int) round(($challenge->current_value / $challenge->target_value) * 100))
            : 0;

        $claimable = [];
        foreach (self::MILESTONE_REWARDS as $milestone => $xp) {
            $field = "milestone_{$milestone}_claimed";
            if ($percent >= $milestone && !$challenge->$field) {
                $claimable[] = $milestone;
            }
        }

        $daysRemaining = (int) now()->daysUntil(now()->endOfMonth())->count();

        return [
            'challenge' => $challenge,
            'progress_percent' => $percent,
            'claimable_milestones' => $claimable,
            'days_remaining' => $daysRemaining,
        ];
    }

    public function getCompletedChallenges(User $user, int $limit = 12): Collection
    {
        return MonthlyChallenge::where('user_id', $user->id)
            ->where('challenge_month', '<', now()->format('Y-m'))
            ->orderByDesc('challenge_month')
            ->limit($limit)
            ->get();
    }
}
