<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LehrgangQuestionIssueReport extends Model
{
    protected $table = 'lehrgang_question_issue_reports';
    
    public $timestamps = true;
    
    protected $fillable = [
        'lehrgang_question_issue_id',
        'user_id',
        'message',
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    // Relationships
    public function issue()
    {
        return $this->belongsTo(LehrgangQuestionIssue::class, 'lehrgang_question_issue_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
