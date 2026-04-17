<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserExtraQuestionProgress extends Model
{
    protected $table = 'user_extra_question_progress';

    protected $fillable = [
        'user_id',
        'extra_question_id',
        'consecutive_correct',
        'last_answered_at',
        'next_review_at',
    ];

    protected $casts = [
        'consecutive_correct' => 'integer',
        'last_answered_at' => 'datetime',
        'next_review_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function extraQuestion()
    {
        return $this->belongsTo(ExtraQuestion::class);
    }
}
