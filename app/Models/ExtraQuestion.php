<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtraQuestion extends Model
{
    protected $fillable = [
        'typ',
        'lernabschnitt',
        'frage',
        'image_path',
    ];

    protected $casts = [
        'typ' => 'string',
    ];

    // Typ-Konstanten
    public const TYP_MATCHING = 'matching';
    public const TYP_IMAGE_NAME = 'image_name';
    public const TYP_IMAGE_SELECT = 'image_select';

    /**
     * Antwort-Optionen (für image_name und image_select)
     */
    public function options()
    {
        return $this->hasMany(ExtraQuestionOption::class)->orderBy('sort_order');
    }

    /**
     * Kategorien für Zuordnungsfragen
     */
    public function matchCategories()
    {
        return $this->hasMany(ExtraMatchCategory::class)->orderBy('sort_order');
    }

    /**
     * Items für Zuordnungsfragen
     */
    public function matchItems()
    {
        return $this->hasMany(ExtraMatchItem::class)->orderBy('sort_order');
    }

    /**
     * Fortschritte der User zu dieser Frage
     */
    public function userProgress()
    {
        return $this->hasMany(UserExtraQuestionProgress::class);
    }

    // Helper Methods
    public function isMatching(): bool
    {
        return $this->typ === self::TYP_MATCHING;
    }

    public function isImageName(): bool
    {
        return $this->typ === self::TYP_IMAGE_NAME;
    }

    public function isImageSelect(): bool
    {
        return $this->typ === self::TYP_IMAGE_SELECT;
    }
}
