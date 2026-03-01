<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionIssue extends Model
{
    protected $table = 'question_issues';

    protected $fillable = [
        'question_id',
        'report_count',
        'latest_message',
        'reported_by_user_id',
        'admin_notes',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function reportedByUser()
    {
        return $this->belongsTo(User::class, 'reported_by_user_id');
    }

    public function reports()
    {
        return $this->hasMany(QuestionIssueReport::class, 'question_issue_id');
    }
}
