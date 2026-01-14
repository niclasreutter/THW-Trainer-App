<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'title',
        'description',
        'icon',
        'category',
        'trigger_type',
        'trigger_config',
        'requirement_value',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'requirement_value' => 'integer',
        'sort_order' => 'integer',
        'trigger_config' => 'array',
    ];

    /**
     * Verfügbare Trigger-Typen
     */
    const TRIGGER_TYPES = [
        'question_count' => 'Anzahl gelöster Fragen',
        'question_percent' => 'Prozent aller Fragen gelöst',
        'streak_days' => 'Streak-Tage erreicht',
        'level_reached' => 'Level erreicht',
        'exam_passed_count' => 'Anzahl Prüfungen bestanden',
        'exam_perfect' => 'Perfekte Prüfung (100%)',
        'daily_questions' => 'Fragen an einem Tag',
        'section_complete' => 'Alle Fragen eines Abschnitts',
    ];

    /**
     * Users die dieses Achievement freigeschaltet haben
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_achievements')
            ->withPivot('unlocked_at')
            ->withTimestamps();
    }

    /**
     * User Achievement Pivot Records
     */
    public function userAchievements()
    {
        return $this->hasMany(UserAchievement::class);
    }

    /**
     * Scope: Nur aktive Achievements
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Nach Kategorie filtern
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope: Sortiert nach sort_order
     */
    public function scopeSorted($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
