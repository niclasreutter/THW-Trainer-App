<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCountHistory extends Model
{
    protected $table = 'user_count_history';

    protected $fillable = [
        'date',
        'total_users',
        'verified_users',
    ];

    protected $casts = [
        'date' => 'date',
        'total_users' => 'integer',
        'verified_users' => 'integer',
    ];
}
