<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserExtraQuestionProgress extends Model
{
    /**
     * Anzahl aufeinanderfolgender richtiger Antworten für "Gemeistert"
     * (analog zu UserQuestionProgress::MASTERY_THRESHOLD)
     */
    public const MASTERY_THRESHOLD = 3;

    protected $table = 'user_extra_question_progress';

    protected $fillable = [
        'user_id',
        'extra_question_id',
        'consecutive_correct',
        'last_answered_at',
        'next_review_at',
        'review_interval',
        'easiness_factor',
        'repetition_count',
    ];

    protected $casts = [
        'consecutive_correct' => 'integer',
        'last_answered_at' => 'datetime',
        'next_review_at' => 'datetime',
        'review_interval' => 'integer',
        'easiness_factor' => 'decimal:2',
        'repetition_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function extraQuestion()
    {
        return $this->belongsTo(ExtraQuestion::class);
    }

    /**
     * Prüft ob die Frage gemeistert ist
     */
    public function isMastered(): bool
    {
        return $this->consecutive_correct >= self::MASTERY_THRESHOLD;
    }

    /**
     * Zähle gemeisterte Zusatzfragen für einen User
     */
    public static function countMastered(int $userId): int
    {
        return self::where('user_id', $userId)
            ->where('consecutive_correct', '>=', self::MASTERY_THRESHOLD)
            ->count();
    }

    /**
     * Zähle Zusatzfragen mit genau X aufeinanderfolgenden richtigen Antworten
     */
    public static function countAtLevel(int $userId, int $level): int
    {
        return self::where('user_id', $userId)
            ->where('consecutive_correct', $level)
            ->count();
    }
}
