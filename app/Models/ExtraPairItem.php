<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtraPairItem extends Model
{
    protected $fillable = [
        'extra_question_id',
        'left_text',
        'right_text',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function extraQuestion()
    {
        return $this->belongsTo(ExtraQuestion::class);
    }
}
