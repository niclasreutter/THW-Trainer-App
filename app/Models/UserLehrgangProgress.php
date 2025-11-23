<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLehrgangProgress extends Model
{
    use HasFactory;

    protected $table = 'user_lehrgang_progress';

    protected $fillable = [
        'user_id',
        'lehrgang_question_id',
        'consecutive_correct',
        'solved',
        'failed',
    ];

    protected $casts = [
        'solved' => 'boolean',
        'failed' => 'boolean',
    ];

    /**
     * Fortschritt gehört zu einem User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Fortschritt gehört zu einer Lehrgang-Frage
     */
    public function lehrgangQuestion()
    {
        return $this->belongsTo(LehrgangQuestion::class);
    }
}
