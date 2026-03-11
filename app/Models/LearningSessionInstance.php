<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LearningSessionInstance extends Model
{
    protected $fillable = [
        'learning_session_id',
        'starts_at',
        'ends_at',
        'status',
        'winner_user_id',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function learningSession(): BelongsTo
    {
        return $this->belongsTo(LearningSession::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(LearningSessionParticipant::class, 'instance_id');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'winner_user_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active'
            && now()->between($this->starts_at, $this->ends_at);
    }

    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function getTimeRemainingMinutes(): int
    {
        if (!$this->isActive()) {
            return 0;
        }

        return (int) now()->diffInMinutes($this->ends_at, false);
    }

    public function getTimeRemainingSeconds(): int
    {
        if (!$this->isActive()) {
            return 0;
        }

        return (int) max(0, now()->diffInSeconds($this->ends_at, false));
    }

    public function getRanking()
    {
        return $this->participants()
            ->with('user')
            ->orderByDesc('rank_score')
            ->get();
    }

    public function getParticipantCount(): int
    {
        return $this->participants()->count();
    }
}
