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
        'requirement_value',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'requirement_value' => 'integer',
        'sort_order' => 'integer',
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
