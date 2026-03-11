<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearningSessionParticipant extends Model
{
    protected $fillable = [
        'instance_id',
        'user_id',
        'joined_at',
        'ranking_consent',
        'questions_answered',
        'questions_correct',
        'xp_earned',
        'accuracy_percent',
        'avg_answer_time_ms',
        'rank_score',
        'final_rank',
    ];

    protected function casts(): array
    {
        return [
            'joined_at' => 'datetime',
            'ranking_consent' => 'boolean',
            'accuracy_percent' => 'decimal:2',
            'rank_score' => 'decimal:2',
        ];
    }

    public function instance(): BelongsTo
    {
        return $this->belongsTo(LearningSessionInstance::class, 'instance_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function calculateRankScore(): float
    {
        // Basis: Richtige Antworten (10 Punkte pro richtige Antwort)
        // Verwendet questions_correct statt xp_earned, damit XP-Multiplikatoren
        // (Double XP, Wochenend-Boost, Lernsession-Boost) das Ranking nicht verzerren
        $baseScore = $this->questions_correct * 10;

        $speedBonus = 0;
        if ($this->avg_answer_time_ms !== null && $this->avg_answer_time_ms < 30000) {
            $speedBonus = max(0, (30000 - $this->avg_answer_time_ms) / 1000);
        }

        $accuracyBonus = (float) $this->accuracy_percent * 0.5;

        return round($baseScore + $speedBonus + $accuracyBonus, 2);
    }
}
