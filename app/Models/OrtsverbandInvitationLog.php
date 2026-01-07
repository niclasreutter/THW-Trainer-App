<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrtsverbandInvitationLog extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'invitation_id',
        'user_id',
        'used_at',
        'ip_address'
    ];

    protected $casts = [
        'used_at' => 'datetime'
    ];

    /**
     * Die Einladung
     */
    public function invitation(): BelongsTo
    {
        return $this->belongsTo(OrtsverbandInvitation::class);
    }

    /**
     * Der User der die Einladung verwendet hat
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
