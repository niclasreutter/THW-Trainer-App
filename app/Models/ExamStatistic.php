<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamStatistic extends Model
{
    protected $fillable = [
        'user_id',
        'is_passed',
        'correct_answers',
    ];

    protected $casts = [
        'is_passed' => 'boolean',
        'correct_answers' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function questionStatistics()
    {
        return $this->hasMany(QuestionStatistic::class);
    }
}
