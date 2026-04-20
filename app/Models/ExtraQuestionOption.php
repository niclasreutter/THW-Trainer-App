<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtraQuestionOption extends Model
{
    protected $fillable = [
        'extra_question_id',
        'text',
        'image_path',
        'image_source',
        'is_correct',
        'sort_order',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function extraQuestion()
    {
        return $this->belongsTo(ExtraQuestion::class);
    }
}
