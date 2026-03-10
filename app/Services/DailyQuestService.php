<?php

namespace App\Services;

use App\Models\User;
use App\Models\DailyQuest;
use App\Models\DailyQuestChest;
use App\Models\Notification;

class DailyQuestService
{
    const QUEST_TYPES = [
        'answer_questions'  => ['title_template' => 'Beantworte %d Fragen',                  'min' => 5,  'max' => 30],
        'correct_streak'    => ['title_template' => 'Schaffe %d richtige Antworten in Folge', 'min' => 3,  'max' => 8],
        'practice_section'  => ['title_template' => 'Uebe Abschnitt %d',                     'fixed' => 5],
        'pass_exam'         => ['title_template' => 'Bestehe eine Pruefung',                  'fixed' => 1],
        'spaced_repetition' => ['title_template' => 'Loese %d Wiederholungsfragen',           'min' => 3,  'max' => 10],
    ];

    const QUEST_XP = 25;
    const CHEST_XP = 75;

    public function getOrCreateDailyQuests(User $user): \Illuminate\Support\Collection
    {
        $existing = DailyQuest::where('user_id', $user->id)
            ->where('quest_date', today())
            ->orderBy('slot')
            ->get();

        if ($existing->count() === 3) {
            return $existing;
        }

        // Generate deterministically
        $seed = crc32($user->id . '-' . today()->format('Y-m-d'));
        mt_srand($seed);

        $typeKeys = array_keys(self::QUEST_TYPES);
        $selected = [];
        $shuffled = $typeKeys;

        // Fisher-Yates with seeded random
        for ($i = count($shuffled) - 1; $i > 0; $i--) {
            $j = mt_rand(0, $i);
            [$shuffled[$i], $shuffled[$j]] = [$shuffled[$j], $shuffled[$i]];
        }

        $selected = array_slice($shuffled, 0, 3);
        $userLevel = $user->level ?? 1;

        $quests = collect();
        foreach ($selected as $slot => $type) {
            $config = self::QUEST_TYPES[$type];
            $target = $this->getTarget($config, $userLevel, $type, $seed + $slot);
            $title = sprintf($config['title_template'], $target);

            $quest = DailyQuest::create([
                'user_id' => $user->id,
                'quest_date' => today(),
                'quest_type' => $type,
                'quest_params' => $type === 'practice_section' ? ['section' => mt_rand(1, 10)] : null,
                'quest_title' => $title,
                'target_value' => $target,
                'current_value' => 0,
                'is_completed' => false,
                'xp_reward' => self::QUEST_XP,
                'slot' => $slot + 1,
            ]);
            $quests->push($quest);
        }

        mt_srand(); // Reset seed
        return $quests;
    }

    private function getTarget(array $config, int $userLevel, string $type, int $seed): int
    {
        if (isset($config['fixed'])) {
            return $config['fixed'];
        }

        $min = $config['min'];
        $max = $config['max'];

        // Scale by level
        $levelFactor = min(1.0, ($userLevel - 1) / 19);
        $range = $max - $min;
        $base = (int) round($min + $range * $levelFactor * 0.6);

        return max($min, min($max, $base));
    }

    public function updateQuestProgress(User $user, string $eventType, array $context = []): void
    {
        $quests = $this->getOrCreateDailyQuests($user);

        foreach ($quests as $quest) {
            if ($quest->is_completed) {
                continue;
            }

            $matches = match ($eventType) {
                'question_answered' => $quest->quest_type === 'answer_questions',
                'correct_answer' => $quest->quest_type === 'correct_streak',
                'section_practiced' => $quest->quest_type === 'practice_section'
                    && isset($context['section'])
                    && isset($quest->quest_params['section'])
                    && (int) $context['section'] === (int) $quest->quest_params['section'],
                'exam_passed' => $quest->quest_type === 'pass_exam',
                'sr_question_answered' => $quest->quest_type === 'spaced_repetition',
                default => false,
            };

            if (!$matches) {
                continue;
            }

            $quest->current_value++;

            if ($quest->current_value >= $quest->target_value) {
                $quest->is_completed = true;
                $quest->completed_at = now();
                $quest->save();

                // Award XP
                $gamificationService = new GamificationService();
                $gamificationService->awardPoints($user, self::QUEST_XP, "Tagesaufgabe: {$quest->quest_title}");

                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'quest_completed',
                    'title' => 'Tagesaufgabe erledigt!',
                    'message' => $quest->quest_title,
                    'icon' => 'bi-check-circle-fill',
                    'data' => ['quest_type' => $quest->quest_type, 'xp' => self::QUEST_XP],
                ]);
            } else {
                $quest->save();
            }
        }

        $this->checkChestCompletion($user);
    }

    private function checkChestCompletion(User $user): void
    {
        $todayQuests = DailyQuest::where('user_id', $user->id)
            ->where('quest_date', today())
            ->get();

        if ($todayQuests->count() < 3) {
            return;
        }

        $allCompleted = $todayQuests->every(fn($q) => $q->is_completed);
        if (!$allCompleted) {
            return;
        }

        $existingChest = DailyQuestChest::where('user_id', $user->id)
            ->where('chest_date', today())
            ->first();

        if (!$existingChest) {
            DailyQuestChest::create([
                'user_id' => $user->id,
                'chest_date' => today(),
                'is_claimed' => false,
                'xp_reward' => self::CHEST_XP,
            ]);
        }
    }

    public function claimChest(User $user): array
    {
        $chest = DailyQuestChest::where('user_id', $user->id)
            ->where('chest_date', today())
            ->first();

        if (!$chest) {
            return ['success' => false, 'message' => 'Keine Truhe verfuegbar.'];
        }

        if ($chest->is_claimed) {
            return ['success' => false, 'message' => 'Truhe bereits beansprucht.'];
        }

        $chest->is_claimed = true;
        $chest->claimed_at = now();
        $chest->save();

        $gamificationService = new GamificationService();
        $gamificationService->awardPoints($user, self::CHEST_XP, 'Tagesaufgaben-Truhe');

        return ['success' => true, 'message' => "Truhe geoeffnet! +{self::CHEST_XP} XP", 'xp' => self::CHEST_XP];
    }

    public function getQuestWidgetData(User $user): array
    {
        $quests = $this->getOrCreateDailyQuests($user);

        $chest = DailyQuestChest::where('user_id', $user->id)
            ->where('chest_date', today())
            ->first();

        return [
            'quests' => $quests,
            'chest_available' => $chest && !$chest->is_claimed,
            'chest_claimed' => $chest && $chest->is_claimed,
            'all_completed' => $quests->every(fn($q) => $q->is_completed),
        ];
    }
}
