<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtraQuestionIssueReport extends Model
{
    protected $table = 'extra_question_issue_reports';

    protected $fillable = [
        'extra_question_issue_id',
        'user_id',
        'message',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function issue()
    {
        return $this->belongsTo(ExtraQuestionIssue::class, 'extra_question_issue_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
