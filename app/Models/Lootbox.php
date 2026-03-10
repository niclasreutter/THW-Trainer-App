<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lootbox extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'reward_type',
        'reward_amount',
        'opened',
        'opened_at',
    ];

    protected function casts(): array
    {
        return [
            'opened' => 'boolean',
            'opened_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
