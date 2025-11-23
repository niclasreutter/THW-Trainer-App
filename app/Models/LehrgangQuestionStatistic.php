<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LehrgangQuestionStatistic extends Model
{
    protected $table = 'lehrgang_question_statistics';
    
    protected $fillable = [
        'user_id',
        'lehrgang_question_id',
        'is_correct',
    ];
    
    protected $casts = [
        'is_correct' => 'boolean',
    ];
    
    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function lehrgangQuestion()
    {
        return $this->belongsTo(LehrgangQuestion::class, 'lehrgang_question_id');
    }
}

