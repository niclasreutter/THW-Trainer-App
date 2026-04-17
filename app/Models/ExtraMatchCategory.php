<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtraMatchCategory extends Model
{
    protected $fillable = [
        'extra_question_id',
        'name',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function extraQuestion()
    {
        return $this->belongsTo(ExtraQuestion::class);
    }

    /**
     * Items, die dieser Kategorie (korrekt) zugeordnet sind
     */
    public function items()
    {
        return $this->hasMany(ExtraMatchItem::class, 'correct_category_id')->orderBy('sort_order');
    }
}
