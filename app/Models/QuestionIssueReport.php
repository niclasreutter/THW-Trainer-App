<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionIssueReport extends Model
{
    protected $table = 'question_issue_reports';

    protected $fillable = [
        'question_issue_id',
        'user_id',
        'message',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function issue()
    {
        return $this->belongsTo(QuestionIssue::class, 'question_issue_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
