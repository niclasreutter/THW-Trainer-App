<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LehrgangLernabschnitt extends Model
{
    use HasFactory;

    protected $table = 'lehrgaenge_lernabschnitte';

    protected $fillable = [
        'lehrgang_id',
        'lernabschnitt_nr',
        'lernabschnitt',
    ];

    /**
     * Ein Lernabschnitt gehört zu einem Lehrgang
     */
    public function lehrgang()
    {
        return $this->belongsTo(Lehrgang::class);
    }
}
