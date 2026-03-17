<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

interface ProgressResolverInterface
{
    /**
     * Get progress record for a user/question pair.
     * Returns object with at least `consecutive_correct` property, or null.
     */
    public function getProgress(int $userId, int $questionId): ?object;

    /**
     * Update progress after an answer. Handles consecutive_correct, solved flag,
     * and any model-specific columns (e.g. total_attempts for Lernpool).
     */
    public function updateProgress(int $userId, int $questionId, bool $correct): object;

    /**
     * Check if user has mastered this question.
     * Global: $progress->isMastered(), Lehrgang/Lernpool: $progress->solved === true
     */
    public function isMastered(int $userId, int $questionId): bool;

    public function getQuestionById(int $id): object;

    public function getQuestionsByIds(array $ids): Collection;

    /**
     * Record a statistic entry (answer history).
     */
    public function createStatistic(int $userId, int $questionId, bool $correct): void;
}
