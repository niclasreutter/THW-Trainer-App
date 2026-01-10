<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrtsverbandLernpoolEnrollment extends Model
{
    protected $table = 'ortsverband_lernpool_enrollments';
    
    protected $fillable = ['user_id', 'lernpool_id', 'enrolled_at', 'completed_at'];
    
    protected $casts = [
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lernpool()
    {
        return $this->belongsTo(OrtsverbandLernpool::class, 'lernpool_id');
    }

    // Helper Methods
    public function isCompleted()
    {
        return $this->completed_at !== null;
    }

    public function getProgress()
    {
        $questionCount = $this->lernpool->questions()->count();
        $solvedCount = $this->user->lernpoolProgress()
            ->whereHas('question', fn($q) => $q->where('lernpool_id', $this->lernpool_id))
            ->where('solved', true)
            ->count();

        if ($questionCount === 0) {
            return 0;
        }

        return round(($solvedCount / $questionCount) * 100);
    }

    public function getSolvedCount()
    {
        return $this->user->lernpoolProgress()
            ->whereHas('question', fn($q) => $q->where('lernpool_id', $this->lernpool_id))
            ->where('solved', true)
            ->count();
    }
}
