<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyChallenge extends Model
{
    protected $fillable = [
        'user_id', 'challenge_month', 'challenge_type', 'challenge_title',
        'target_value', 'current_value', 'milestone_25_claimed', 'milestone_50_claimed',
        'milestone_75_claimed', 'milestone_100_claimed', 'is_completed', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'is_completed' => 'boolean',
            'completed_at' => 'datetime',
            'milestone_25_claimed' => 'boolean',
            'milestone_50_claimed' => 'boolean',
            'milestone_75_claimed' => 'boolean',
            'milestone_100_claimed' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeCurrentMonth($query)
    {
        return $query->where('challenge_month', now()->format('Y-m'));
    }
}
