<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Newsletter extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject',
        'content',
        'recipients_count',
        'sent_at',
        'sent_by',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    /**
     * Beziehung zum User der den Newsletter gesendet hat
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    /**
     * Ersetze Platzhalter mit User-Daten
     */
    public static function replacePlaceholders(string $content, User $user): string
    {
        $placeholders = [
            '{{name}}' => $user->name,
            '{{email}}' => $user->email,
            '{{level}}' => $user->level ?? 1,
            '{{points}}' => $user->points ?? 0,
            '{{streak}}' => $user->streak_days ?? 0,
        ];

        return str_replace(array_keys($placeholders), array_values($placeholders), $content);
    }
}

