<?php

namespace App\Services\ProgressResolvers;

use App\Contracts\ProgressResolverInterface;
use App\Models\LehrgangQuestion;
use App\Models\LehrgangQuestionStatistic;
use App\Models\UserLehrgangProgress;
use Illuminate\Support\Collection;

class LehrgangProgressResolver implements ProgressResolverInterface
{
    public function getProgress(int $userId, int $questionId): ?object
    {
        return UserLehrgangProgress::where('user_id', $userId)
            ->where('lehrgang_question_id', $questionId)
            ->first();
    }

    public function updateProgress(int $userId, int $questionId, bool $correct): object
    {
        $progress = UserLehrgangProgress::firstOrCreate(
            ['user_id' => $userId, 'lehrgang_question_id' => $questionId],
            ['consecutive_correct' => 0, 'solved' => false, 'failed' => false]
        );

        if ($correct) {
            $progress->consecutive_correct++;
            $progress->failed = false;
            if ($progress->consecutive_correct >= 3) {
                $progress->solved = true;
            }
        } else {
            $progress->consecutive_correct = 0;
            $progress->failed = true;
        }

        $progress->save();

        return $progress;
    }

    public function isMastered(int $userId, int $questionId): bool
    {
        $progress = $this->getProgress($userId, $questionId);
        return $progress ? (bool) $progress->solved : false;
    }

    public function getQuestionById(int $id): object
    {
        return LehrgangQuestion::findOrFail($id);
    }

    public function getQuestionsByIds(array $ids): Collection
    {
        return LehrgangQuestion::whereIn('id', $ids)->get();
    }

    public function createStatistic(int $userId, int $questionId, bool $correct): void
    {
        LehrgangQuestionStatistic::create([
            'user_id' => $userId,
            'lehrgang_question_id' => $questionId,
            'is_correct' => $correct,
        ]);
    }
}
