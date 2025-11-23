<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LehrgangQuestionIssue extends Model
{
    protected $table = 'lehrgang_question_issues';
    
    public $timestamps = true;
    
    protected $keyType = 'int';
    
    protected $fillable = [
        'lehrgang_question_id',
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
    
    // Relationships
    public function lehrgangQuestion()
    {
        return $this->belongsTo(LehrgangQuestion::class, 'lehrgang_question_id');
    }
    
    public function reportedByUser()
    {
        return $this->belongsTo(User::class, 'reported_by_user_id');
    }
    
    public function reports()
    {
        return $this->hasMany(LehrgangQuestionIssueReport::class, 'lehrgang_question_issue_id');
    }
}

