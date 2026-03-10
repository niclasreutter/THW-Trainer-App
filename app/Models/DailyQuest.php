<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyQuest extends Model
{
    protected $fillable = [
        'user_id', 'quest_date', 'quest_type', 'quest_params', 'quest_title',
        'target_value', 'current_value', 'is_completed', 'completed_at',
        'xp_reward', 'slot',
    ];

    protected function casts(): array
    {
        return [
            'quest_params' => 'array',
            'quest_date' => 'date',
            'is_completed' => 'boolean',
            'completed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForToday($query)
    {
        return $query->where('quest_date', today());
    }

    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }
}
