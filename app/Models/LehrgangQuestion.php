<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LehrgangQuestion extends Model
{
    use HasFactory;

    protected $table = 'lehrgaenge_questions';

    protected $fillable = [
        'lehrgang_id',
        'lernabschnitt',
        'nummer',
        'frage',
        'antwort_a',
        'antwort_b',
        'antwort_c',
        'loesung',
    ];

    /**
     * Eine Frage gehört zu einem Lehrgang
     */
    public function lehrgang()
    {
        return $this->belongsTo(Lehrgang::class);
    }

    /**
     * Eine Frage hat viele Fortschritte von Usern
     */
    public function userProgress()
    {
        return $this->hasMany(UserLehrgangProgress::class);
    }

    /**
     * Hole die Lösung als Array
     */
    public function getSolutionArray()
    {
        return collect(explode(',', $this->loesung))->map(fn($s) => trim($s));
    }
}
