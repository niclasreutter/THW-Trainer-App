<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionStatistic extends Model
{
    protected $fillable = [
        'question_id',
        'is_correct',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    /**
     * Eine Statistik gehört zu einer Frage
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}

