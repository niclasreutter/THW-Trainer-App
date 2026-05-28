<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtraQuestionIssue extends Model
{
    protected $table = 'extra_question_issues';

    protected $fillable = [
        'extra_question_id',
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

    public function extraQuestion()
    {
        return $this->belongsTo(ExtraQuestion::class, 'extra_question_id');
    }

    public function reportedByUser()
    {
        return $this->belongsTo(User::class, 'reported_by_user_id');
    }

    public function reports()
    {
        return $this->hasMany(ExtraQuestionIssueReport::class, 'extra_question_issue_id');
    }
}
