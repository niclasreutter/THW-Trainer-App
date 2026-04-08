<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyResponse extends Model
{
    protected $fillable = [
        'survey_id', 'user_id',
        'rating_overall', 'rating_usability', 'rating_design',
        'found_via',
        'feedback_general', 'feedback_wishes', 'feedback_changes',
        'hermine_interest', 'publish_mode',
        'consent_given', 'consent_given_at',
    ];

    protected $casts = [
        'consent_given' => 'boolean',
        'consent_given_at' => 'datetime',
    ];

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePublishable($query)
    {
        return $query->whereIn('publish_mode', ['name', 'anonymous']);
    }
}
