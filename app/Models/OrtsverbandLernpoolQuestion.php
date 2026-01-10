<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrtsverbandLernpoolQuestion extends Model
{
    protected $table = 'ortsverband_lernpool_questions';
    
    protected $fillable = [
        'lernpool_id', 'created_by', 'lernabschnitt', 'nummer',
        'frage', 'antwort_a', 'antwort_b', 'antwort_c', 'loesung'
    ];

    // Relationships
    public function lernpool()
    {
        return $this->belongsTo(OrtsverbandLernpool::class, 'lernpool_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function progress()
    {
        return $this->hasMany(OrtsverbandLernpoolProgress::class, 'question_id');
    }

    // Helper Methods
    public function getLosungArray()
    {
        return array_map('trim', explode(',', $this->loesung));
    }

    public function isCorrectAnswer($answers)
    {
        if (is_string($answers)) {
            $answers = [$answers];
        }

        $correctAnswers = $this->getLosungArray();
        $userAnswers = array_map('strtoupper', $answers);

        return count(array_diff($userAnswers, $correctAnswers)) === 0 && count(array_diff($correctAnswers, $userAnswers)) === 0;
    }
}
