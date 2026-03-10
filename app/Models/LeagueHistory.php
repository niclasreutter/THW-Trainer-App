<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeagueHistory extends Model
{
    protected $table = 'league_history';

    protected $fillable = [
        'user_id', 'week_start', 'league', 'weekly_points',
        'rank_in_league', 'result',
    ];

    protected function casts(): array
    {
        return [
            'week_start' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
