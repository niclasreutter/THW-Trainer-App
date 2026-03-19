<?php

namespace App\Services\ProgressResolvers;

use App\Contracts\ProgressResolverInterface;
use App\Models\OrtsverbandLernpoolQuestion;
use App\Models\OrtsverbandLernpoolQuestionStatistic;
use App\Models\OrtsverbandLernpoolProgress;
use Illuminate\Support\Collection;

class LernpoolProgressResolver implements ProgressResolverInterface
{
    public function getProgress(int $userId, int $questionId): ?object
    {
        return OrtsverbandLernpoolProgress::where('user_id', $userId)
            ->where('question_id', $questionId)
            ->first();
    }

    public function updateProgress(int $userId, int $questionId, bool $correct): object
    {
        $progress = OrtsverbandLernpoolProgress::firstOrCreate(
            ['user_id' => $userId, 'question_id' => $questionId],
            ['consecutive_correct' => 0, 'total_attempts' => 0, 'correct_attempts' => 0, 'solved' => false]
        );

        $progress->total_attempts++;

        if ($correct) {
            $progress->correct_attempts++;
            $progress->consecutive_correct++;
            if ($progress->consecutive_correct >= 3) {
                $progress->solved = true;
            }
        } else {
            $progress->consecutive_correct = 0;
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
        return OrtsverbandLernpoolQuestion::findOrFail($id);
    }

    public function getQuestionsByIds(array $ids): Collection
    {
        return OrtsverbandLernpoolQuestion::whereIn('id', $ids)->get();
    }

    public function createStatistic(int $userId, int $questionId, bool $correct): void
    {
        OrtsverbandLernpoolQuestionStatistic::create([
            'user_id' => $userId,
            'lernpool_question_id' => $questionId,
            'is_correct' => $correct,
        ]);
    }
}
