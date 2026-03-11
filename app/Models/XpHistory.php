<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class XpHistory extends Model
{
    protected $fillable = [
        'user_id',
        'points',
        'reason',
        'source_type',
        'source_id',
        'total_before',
        'total_after',
        'level_before',
        'level_after',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
