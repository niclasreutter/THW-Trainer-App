<?php

namespace App\Services;

use App\Contracts\ProgressResolverInterface;

class PracticeSessionService
{
    private ProgressResolverInterface $resolver;
    private GamificationService $gamification;

    public function __construct(ProgressResolverInterface $resolver, GamificationService $gamification)
    {
        $this->resolver = $resolver;
        $this->gamification = $gamification;
    }

    /**
     * Generate context-specific session key prefix.
     * Global: 'practice', Lehrgang: 'practice_lehrgang_5', Lernpool: 'practice_lernpool_3'
     */
    private function prefix(string $context, ?string $contextId): string
    {
        if ($context === 'global') {
            return 'practice';
        }
        return "practice_{$context}_{$contextId}";
    }

    private function sessionKey(string $context, ?string $contextId, string $key): string
    {
        return $this->prefix($context, $contextId) . "_{$key}";
    }

    public function startSession(
        string $context,
        ?string $contextId,
        array $questionIds,
        string $mode,
        string $requeueStrategy = 'requeue'
    ): void {
        $prefix = $this->prefix($context, $contextId);

        // Clean up any existing session for this context
        $this->cleanSession($context, $contextId);

        session([
            "{$prefix}_ids" => $questionIds,
            "{$prefix}_mode" => $mode,
            "{$prefix}_total" => count($questionIds),
            "{$prefix}_requeue" => $requeueStrategy,
            "{$prefix}_stats" => [
                'correct' => 0,
                'incorrect' => 0,
                'points' => 0,
                'mastered' => 0,
                'started_at' => now()->timestamp,
            ],
        ]);
    }

    /**
     * Get current question ID from session, or null if session is done.
     */
    public function getCurrentQuestionId(string $context, ?string $contextId): ?int
    {
        $ids = session($this->sessionKey($context, $contextId, 'ids'), []);
        return count($ids) > 0 ? (int) $ids[0] : null;
    }

    /**
     * Get the answer result from last submission (for feedback display), then clear it.
     */
    public function getAndClearAnswerResult(string $context, ?string $contextId): ?array
    {
        $key = $this->sessionKey($context, $contextId, 'answer_result');
        $result = session($key);
        session()->forget($key);
        return $result;
    }

    /**
     * Get gamification result from last submission, then clear it.
     */
    public function getAndClearGamificationResult(string $context, ?string $contextId): ?array
    {
        $key = $this->sessionKey($context, $contextId, 'gamification_result');
        $result = session($key);
        session()->forget($key);
        return $result;
    }

    /**
     * Submit an answer. Returns the answer_result array for feedback.
     *
     * @param array $userAnswer Already mapped letter array, e.g. ['A', 'B']
     * @param array $answerMapping Position-to-letter mapping from the view
     */
    public function submitAnswer(
        string $context,
        ?string $contextId,
        int $questionId,
        array $userAnswer,
        array $answerMapping
    ): array {
        $prefix = $this->prefix($context, $contextId);
        $userId = auth()->id();
        $question = $this->resolver->getQuestionById($questionId);

        // Compare answers
        $correctAnswer = array_map('trim', explode(',', $question->loesung));
        sort($correctAnswer);
        sort($userAnswer);
        $isCorrect = $userAnswer === $correctAnswer;

        // Update progress
        $progress = $this->resolver->updateProgress($userId, $questionId, $isCorrect);

        // Create statistic
        $this->resolver->createStatistic($userId, $questionId, $isCorrect);

        // Check mastery
        $mastered = $this->resolver->isMastered($userId, $questionId);

        // Gamification
        $gamificationResult = $this->gamification->awardQuestionPoints(auth()->user(), $isCorrect, $questionId);

        // Update session stats
        $stats = session("{$prefix}_stats", []);
        if ($isCorrect) {
            $stats['correct'] = ($stats['correct'] ?? 0) + 1;
        } else {
            $stats['incorrect'] = ($stats['incorrect'] ?? 0) + 1;
        }
        if ($gamificationResult && isset($gamificationResult['points_awarded'])) {
            $stats['points'] = ($stats['points'] ?? 0) + $gamificationResult['points_awarded'];
        }
        if ($mastered) {
            $stats['mastered'] = ($stats['mastered'] ?? 0) + 1;
        }
        session(["{$prefix}_stats" => $stats]);

        // Handle queue: remove or requeue
        $ids = session("{$prefix}_ids", []);
        $requeueStrategy = session("{$prefix}_requeue", 'requeue');
        $currentIndex = array_search($questionId, $ids);

        if ($currentIndex !== false) {
            unset($ids[$currentIndex]);
            $ids = array_values($ids);

            // Requeue if not mastered and strategy is 'requeue'
            if (!$mastered && $requeueStrategy === 'requeue') {
                $ids[] = $questionId;
            }

            session(["{$prefix}_ids" => $ids]);
        }

        // Build answer result for feedback display
        $answerResult = [
            'question_id' => $questionId,
            'is_correct' => $isCorrect,
            'user_answer' => $userAnswer,
            'correct_answer' => $correctAnswer,
            'question_progress' => $progress->consecutive_correct,
            'answer_mapping' => $answerMapping,
            'mastered' => $mastered,
        ];

        // Store in session for feedback display
        session([
            "{$prefix}_answer_result" => $answerResult,
            "{$prefix}_gamification_result" => $gamificationResult,
        ]);

        return $answerResult;
    }

    /**
     * Get progress data for the view.
     */
    public function getProgress(string $context, ?string $contextId): array
    {
        $prefix = $this->prefix($context, $contextId);
        $stats = session("{$prefix}_stats", []);
        $ids = session("{$prefix}_ids", []);
        $total = session("{$prefix}_total", 0);

        $answered = ($stats['correct'] ?? 0) + ($stats['incorrect'] ?? 0);

        return [
            'current' => $answered + 1,
            'total' => $total,
            'correct' => $stats['correct'] ?? 0,
            'incorrect' => $stats['incorrect'] ?? 0,
            'points' => $stats['points'] ?? 0,
            'mastered' => $stats['mastered'] ?? 0,
            'remaining' => count($ids),
        ];
    }

    /**
     * End session and return summary data.
     */
    public function endSession(string $context, ?string $contextId): array
    {
        $prefix = $this->prefix($context, $contextId);
        $stats = session("{$prefix}_stats", []);
        $mode = session("{$prefix}_mode", 'all');

        $totalAnswered = ($stats['correct'] ?? 0) + ($stats['incorrect'] ?? 0);
        $accuracy = $totalAnswered > 0
            ? round(($stats['correct'] ?? 0) / $totalAnswered * 100)
            : 0;

        $startedAt = $stats['started_at'] ?? now()->timestamp;
        $durationMinutes = max(1, (int) round((now()->timestamp - $startedAt) / 60));

        $modeNames = [
            'all' => 'Alle Fragen',
            'unsolved' => 'Ungelöste Fragen',
            'section' => 'Lernabschnitt',
            'failed' => 'Fehlgeschlagene Fragen',
            'search' => 'Suche',
            'spaced_repetition' => 'Wiederholung',
            'bookmarked' => 'Lesezeichen',
        ];

        $summary = [
            'correct' => $stats['correct'] ?? 0,
            'incorrect' => $stats['incorrect'] ?? 0,
            'accuracy' => $accuracy,
            'points' => $stats['points'] ?? 0,
            'mastered' => $stats['mastered'] ?? 0,
            'totalAnswered' => $totalAnswered,
            'durationMinutes' => $durationMinutes,
            'modeName' => $modeNames[$mode] ?? $mode,
        ];

        // Clean up session
        $this->cleanSession($context, $contextId);

        return $summary;
    }

    /**
     * Check if session has questions remaining.
     */
    public function hasQuestionsRemaining(string $context, ?string $contextId): bool
    {
        $ids = session($this->sessionKey($context, $contextId, 'ids'), []);
        return count($ids) > 0;
    }

    /**
     * Check if a session exists for this context.
     */
    public function hasActiveSession(string $context, ?string $contextId): bool
    {
        return session()->has($this->sessionKey($context, $contextId, 'mode'));
    }

    /**
     * Get the current mode for a context.
     */
    public function getMode(string $context, ?string $contextId): ?string
    {
        return session($this->sessionKey($context, $contextId, 'mode'));
    }

    /**
     * Clean up all session keys for a context.
     */
    public function cleanSession(string $context, ?string $contextId): void
    {
        $prefix = $this->prefix($context, $contextId);
        $keys = [
            '_ids', '_mode', '_total', '_requeue', '_stats',
            '_answer_result', '_gamification_result',
            '_skipped', '_session_stats', '_total_in_mode', '_parameter',
        ];

        foreach ($keys as $key) {
            session()->forget($prefix . $key);
        }
    }
}
