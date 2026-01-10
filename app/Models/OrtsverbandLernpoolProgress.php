<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrtsverbandLernpoolProgress extends Model
{
    protected $table = 'ortsverband_lernpool_progress';
    
    protected $fillable = [
        'user_id', 'question_id', 'consecutive_correct',
        'total_attempts', 'correct_attempts', 'solved'
    ];
    
    protected $casts = [
        'solved' => 'boolean',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function question()
    {
        return $this->belongsTo(OrtsverbandLernpoolQuestion::class, 'question_id');
    }

    // Helper Methods
    public function getSuccessRate()
    {
        if ($this->total_attempts === 0) {
            return 0;
        }

        return round(($this->correct_attempts / $this->total_attempts) * 100);
    }

    public function updateProgress($isCorrect)
    {
        $this->increment('total_attempts');

        if ($isCorrect) {
            $this->increment('correct_attempts');
            $this->increment('consecutive_correct');
            
            // Frage ist gemeistert nach 2x richtig
            if ($this->consecutive_correct >= 2) {
                $this->update(['solved' => true]);
            }
        } else {
            $this->update(['consecutive_correct' => 0]);
        }
    }
}
