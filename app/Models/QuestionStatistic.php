<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionStatistic extends Model
{
    protected $fillable = [
        'question_id',
        'user_id',
        'is_correct',
        'source',
        'exam_statistic_id',
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

    /**
     * Eine Statistik gehört zu einem User (optional)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Eine Statistik gehört zu einer Prüfung (optional)
     */
    public function examStatistic(): BelongsTo
    {
        return $this->belongsTo(ExamStatistic::class);
    }
}

