<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtraMatchItem extends Model
{
    protected $fillable = [
        'extra_question_id',
        'text',
        'correct_category_id',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function extraQuestion()
    {
        return $this->belongsTo(ExtraQuestion::class);
    }

    public function correctCategory()
    {
        return $this->belongsTo(ExtraMatchCategory::class, 'correct_category_id');
    }
}
