<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LearningSession extends Model
{
    protected $fillable = [
        'title',
        'description',
        'created_by',
        'ortsverband_id',
        'scope',
        'question_source',
        'question_sections',
        'lernpool_id',
        'schedule_type',
        'starts_at',
        'ends_at',
        'recurrence_days',
        'recurrence_start_time',
        'recurrence_end_time',
        'recurrence_valid_from',
        'recurrence_valid_until',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'question_sections' => 'array',
            'recurrence_days' => 'array',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'recurrence_valid_from' => 'date',
            'recurrence_valid_until' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function ortsverband(): BelongsTo
    {
        return $this->belongsTo(Ortsverband::class);
    }

    public function lernpool(): BelongsTo
    {
        return $this->belongsTo(OrtsverbandLernpool::class, 'lernpool_id');
    }

    public function instances(): HasMany
    {
        return $this->hasMany(LearningSessionInstance::class);
    }

    public function isGlobal(): bool
    {
        return $this->scope === 'global';
    }

    public function isOvSession(): bool
    {
        return $this->scope === 'ortsverband';
    }

    public function getActiveInstance(): ?LearningSessionInstance
    {
        return $this->instances()
            ->where('status', 'active')
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now())
            ->first();
    }

    public function getNextInstance(): ?LearningSessionInstance
    {
        return $this->instances()
            ->where('status', 'scheduled')
            ->where('starts_at', '>', now())
            ->orderBy('starts_at')
            ->first();
    }

    public function scopeForUser($query, User $user)
    {
        return $query->where(function ($q) use ($user) {
            $q->where('scope', 'global')
              ->orWhereIn('ortsverband_id', $user->ortsverbände()->pluck('ortsverbände.id'));
        })->where('is_active', true);
    }
}
