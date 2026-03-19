<?php

namespace App\Services\ProgressResolvers;

use App\Contracts\ProgressResolverInterface;
use App\Models\Question;
use App\Models\QuestionStatistic;
use App\Models\UserQuestionProgress;
use Illuminate\Support\Collection;

class GlobalProgressResolver implements ProgressResolverInterface
{
    public function getProgress(int $userId, int $questionId): ?object
    {
        return UserQuestionProgress::where('user_id', $userId)
            ->where('question_id', $questionId)
            ->first();
    }

    public function updateProgress(int $userId, int $questionId, bool $correct): object
    {
        $progress = UserQuestionProgress::firstOrCreate(
            ['user_id' => $userId, 'question_id' => $questionId],
            ['consecutive_correct' => 0]
        );

        if ($correct) {
            $progress->consecutive_correct++;
        } else {
            $progress->consecutive_correct = 0;
        }

        $progress->last_answered_at = now();
        $progress->save();

        return $progress;
    }

    public function isMastered(int $userId, int $questionId): bool
    {
        $progress = $this->getProgress($userId, $questionId);
        return $progress ? $progress->isMastered() : false;
    }

    public function getQuestionById(int $id): object
    {
        return Question::findOrFail($id);
    }

    public function getQuestionsByIds(array $ids): Collection
    {
        return Question::whereIn('id', $ids)->get();
    }

    public function createStatistic(int $userId, int $questionId, bool $correct): void
    {
        QuestionStatistic::create([
            'user_id' => $userId,
            'question_id' => $questionId,
            'is_correct' => $correct,
            'source' => 'practice',
        ]);
    }
}
