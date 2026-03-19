<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAvatarAccessory extends Model
{
    protected $fillable = [
        'user_id',
        'accessory_slug',
        'purchased_at',
    ];

    protected function casts(): array
    {
        return [
            'purchased_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
