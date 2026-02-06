<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserQuestionProgress extends Model
{
    protected $table = 'user_question_progress';

    protected $fillable = [
        'user_id',
        'question_id',
        'consecutive_correct',
        'last_answered_at',
        'next_review_at',
        'review_interval',
        'easiness_factor',
        'repetition_count',
    ];

    protected $casts = [
        'last_answered_at' => 'datetime',
        'next_review_at' => 'datetime',
    ];

    /**
     * Fortschritt gehört zu einem User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Fortschritt gehört zu einer Frage
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Prüft ob die Frage gemeistert ist (2x richtig)
     */
    public function isMastered(): bool
    {
        return $this->consecutive_correct >= 2;
    }

    /**
     * Aktualisiert den Fortschritt basierend auf der Antwort
     */
    public function updateProgress(bool $isCorrect): void
    {
        if ($isCorrect) {
            $this->consecutive_correct++;
        } else {
            // Bei falscher Antwort zurück auf 0
            $this->consecutive_correct = 0;
        }
        
        $this->last_answered_at = now();
        $this->save();
    }

    /**
     * Hole oder erstelle Fortschritt für User + Frage
     */
    public static function getOrCreate(int $userId, int $questionId): self
    {
        return self::firstOrCreate(
            [
                'user_id' => $userId,
                'question_id' => $questionId,
            ],
            [
                'consecutive_correct' => 0,
                'last_answered_at' => now(),
            ]
        );
    }

    /**
     * Hole alle nicht-gemeisterten Fragen für einen User
     */
    public static function getUnmasteredQuestions(int $userId)
    {
        return self::where('user_id', $userId)
            ->where('consecutive_correct', '<', 2)
            ->pluck('question_id')
            ->toArray();
    }

    /**
     * Hole alle gemeisterten Fragen für einen User
     */
    public static function getMasteredQuestions(int $userId)
    {
        return self::where('user_id', $userId)
            ->where('consecutive_correct', '>=', 2)
            ->pluck('question_id')
            ->toArray();
    }

    /**
     * Zähle gemeisterte Fragen für einen User
     */
    public static function countMastered(int $userId): int
    {
        return self::where('user_id', $userId)
            ->where('consecutive_correct', '>=', 2)
            ->count();
    }
}

