<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserExtraQuestionSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'typ',
        'lernabschnitt',
        'frage',
        'payload',
        'status',
        'admin_notes',
        'reviewed_at',
        'reviewed_by',
        'extra_question_id',
    ];

    protected $casts = [
        'payload' => 'array',
        'reviewed_at' => 'datetime',
    ];

    public const TYP_MATCHING = 'matching';
    public const TYP_IMAGE_NAME = 'image_name';
    public const TYP_IMAGE_SELECT = 'image_select';

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CHANGED = 'changed';

    public const TYP_LABELS = [
        self::TYP_MATCHING => 'Zuordnung',
        self::TYP_IMAGE_NAME => 'Bild benennen',
        self::TYP_IMAGE_SELECT => 'Bild auswählen',
    ];

    public const STATUS_LABELS = [
        self::STATUS_PENDING => 'Offen',
        self::STATUS_APPROVED => 'Übernommen',
        self::STATUS_REJECTED => 'Abgelehnt',
        self::STATUS_CHANGED => 'Mit Änderungen übernommen',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function extraQuestion(): BelongsTo
    {
        return $this->belongsTo(ExtraQuestion::class);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function typLabel(): string
    {
        return self::TYP_LABELS[$this->typ] ?? $this->typ;
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }
}
