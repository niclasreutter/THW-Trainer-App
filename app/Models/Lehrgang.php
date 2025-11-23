<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lehrgang extends Model
{
    use HasFactory;

    protected $table = 'lehrgaenge';

    protected $fillable = [
        'lehrgang',
        'slug',
        'beschreibung',
    ];

    /**
     * Ein Lehrgang hat viele Fragen
     */
    public function questions()
    {
        return $this->hasMany(LehrgangQuestion::class);
    }

    /**
     * Ein Lehrgang hat viele Lernabschnitte
     */
    public function lernabschnitte()
    {
        return $this->hasMany(LehrgangLernabschnitt::class);
    }

    /**
     * Viele User sind in einem Lehrgang eingeschrieben
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_lehrgaenge')
            ->withPivot('punkte', 'completed', 'enrolled_at', 'completed_at')
            ->withTimestamps();
    }

    /**
     * Hole alle Lernabschnitte sortiert
     */
    public function getLernabschnitteGrouped()
    {
        return $this->lernabschnitte()
            ->orderBy('lernabschnitt_nr')
            ->get()
            ->groupBy('lernabschnitt_nr');
    }
}
