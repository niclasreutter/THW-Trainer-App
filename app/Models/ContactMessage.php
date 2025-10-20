<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactMessage extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'email',
        'hermine_contact',
        'vorname',
        'nachname',
        'ortsverband',
        'error_location',
        'message',
        'ip_address',
        'user_agent',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'hermine_contact' => 'boolean',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that sent the message.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'feedback' => '💭 Feedback',
            'feature' => '✨ Feature-Wunsch',
            'bug' => '🐛 Fehler melden',
            'other' => '📧 Sonstiges',
            default => $this->type,
        };
    }

    /**
     * Mark as read.
     */
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }
}

