<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OrtsverbandLernpool extends Model
{
    protected $table = 'ortsverband_lernpools';
    
    protected $fillable = ['ortsverband_id', 'created_by', 'name', 'slug', 'description', 'is_active'];
    
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (!$model->slug) {
                $model->slug = Str::slug($model->name) . '-' . time();
            }
        });
    }

    // Relationships
    public function ortsverband()
    {
        return $this->belongsTo(Ortsverband::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions()
    {
        return $this->hasMany(OrtsverbandLernpoolQuestion::class, 'lernpool_id');
    }

    public function enrollments()
    {
        return $this->hasMany(OrtsverbandLernpoolEnrollment::class, 'lernpool_id');
    }

    public function progress()
    {
        return $this->hasManyThrough(
            OrtsverbandLernpoolProgress::class,
            OrtsverbandLernpoolQuestion::class,
            'lernpool_id',
            'question_id'
        );
    }

    // Helper Methods
    public function getQuestionCount()
    {
        return $this->questions()->count();
    }

    public function getEnrollmentCount()
    {
        return $this->enrollments()->count();
    }

    public function getAverageProgress()
    {
        $enrollments = $this->enrollments()->with(['user.lernpoolProgress' => function($q) {
            $q->where('solved', true);
        }])->get();

        if ($enrollments->isEmpty()) {
            return 0;
        }

        $totalProgress = 0;
        foreach ($enrollments as $enrollment) {
            $questionCount = $this->questions()->count();
            $solvedCount = $enrollment->user->lernpoolProgress()
                ->whereHas('question', fn($q) => $q->where('lernpool_id', $this->id))
                ->where('solved', true)
                ->count();
            
            if ($questionCount > 0) {
                $totalProgress += ($solvedCount / $questionCount) * 100;
            }
        }

        return round($totalProgress / $enrollments->count());
    }
}
