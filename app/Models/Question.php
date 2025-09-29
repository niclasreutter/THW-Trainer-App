<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'lernabschnitt',
        'nummer',
        'frage',
        'antwort_a',
        'antwort_b',
        'antwort_c',
        'loesung',
    ];
}
