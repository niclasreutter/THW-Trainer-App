<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamFeedback extends Model
{
    protected $table = 'exam_feedbacks';

    protected $fillable = [
        'user_id',
        'token',
        'passed',
        'feedback',
        'rating',
        'publish_mode',
    ];

    protected function casts(): array
    {
        return [
            'passed' => 'boolean',
            'rating' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
