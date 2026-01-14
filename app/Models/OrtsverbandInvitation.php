<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class OrtsverbandInvitation extends Model
{
    protected $fillable = [
        'ortsverband_id',
        'code',
        'name',
        'created_by',
        'max_uses',
        'current_uses',
        'expires_at',
        'is_active'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    /**
     * Der Ortsverband zu dem die Einladung gehört
     */
    public function ortsverband(): BelongsTo
    {
        return $this->belongsTo(Ortsverband::class);
    }

    /**
     * Der Ersteller der Einladung
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Logs der Einladungsnutzung
     */
    public function logs(): HasMany
    {
        return $this->hasMany(OrtsverbandInvitationLog::class, 'invitation_id');
    }

    /**
     * Prüft ob die Einladung gültig ist
     */
    public function isValid(): bool
    {
        // Nicht aktiv
        if (!$this->is_active) {
            return false;
        }
        
        // Abgelaufen
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }
        
        // Maximale Nutzungen erreicht
        if ($this->max_uses && $this->current_uses >= $this->max_uses) {
            return false;
        }
        
        return true;
    }

    /**
     * Verwendet die Einladung für einen User
     */
    public function use(User $user): void
    {
        if (!$this->isValid()) {
            throw new \Exception('Diese Einladung ist nicht mehr gültig.');
        }

        // Prüfe ob User bereits in einem Ortsverband ist
        if ($user->ortsverbände->count() > 0) {
            throw new \Exception('Du bist bereits Mitglied eines Ortsverbands. Ein User kann nur einem Ortsverband angehören.');
        }
        
        // Increment usage counter
        $this->increment('current_uses');
        
        // Log the usage
        OrtsverbandInvitationLog::create([
            'invitation_id' => $this->id,
            'user_id' => $user->id,
            'ip_address' => request()->ip()
        ]);
        
        // Add user to ortsverband as member
        $this->ortsverband->members()->attach($user->id, [
            'role' => 'member',
            'joined_at' => now()
        ]);

        // Benachrichtige Ausbildungsbeauftragten
        $this->notifyAusbildungsbeauftragter($user);
    }

    /**
     * Benachrichtigt den Ausbildungsbeauftragten über neues Mitglied
     */
    private function notifyAusbildungsbeauftragter(User $newMember): void
    {
        $ausbildungsbeauftragter = $this->ortsverband->creator;

        if ($ausbildungsbeauftragter && $ausbildungsbeauftragter->id !== $newMember->id) {
            Notification::create([
                'user_id' => $ausbildungsbeauftragter->id,
                'type' => 'ortsverband_new_member',
                'title' => '👥 Neues Mitglied',
                'message' => "{$newMember->name} ist deinem Ortsverband beigetreten",
                'icon' => '👥',
                'data' => [
                    'ortsverband_id' => $this->ortsverband->id,
                    'ortsverband_name' => $this->ortsverband->name,
                    'new_member_id' => $newMember->id,
                    'new_member_name' => $newMember->name,
                ]
            ]);
        }
    }

    /**
     * Generiert einen eindeutigen Einladungscode
     */
    public static function generateCode(): string
    {
        do {
            $code = 'THW-' . strtoupper(Str::random(8));
        } while (self::where('code', $code)->exists());
        
        return $code;
    }

    /**
     * Findet eine Einladung anhand des Codes
     */
    public static function findByCode(string $code): ?self
    {
        return self::where('code', $code)->first();
    }

    /**
     * Status-Attribute für die Anzeige
     */
    public function getStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'deaktiviert';
        }
        
        if ($this->expires_at && $this->expires_at->isPast()) {
            return 'abgelaufen';
        }
        
        if ($this->max_uses && $this->current_uses >= $this->max_uses) {
            return 'aufgebraucht';
        }
        
        return 'aktiv';
    }

    /**
     * Einladungs-URL
     */
    public function getUrlAttribute(): string
    {
        return route('ortsverband.join', $this->code);
    }
}
