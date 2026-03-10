<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyQuestChest extends Model
{
    protected $fillable = [
        'user_id', 'chest_date', 'is_claimed', 'claimed_at', 'xp_reward',
    ];

    protected function casts(): array
    {
        return [
            'chest_date' => 'date',
            'is_claimed' => 'boolean',
            'claimed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
