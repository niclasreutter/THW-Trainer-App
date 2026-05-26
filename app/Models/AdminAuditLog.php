<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AdminAuditLog extends Model
{
    protected $fillable = [
        'admin_id',
        'user_id',
        'action',
        'field',
        'old_value',
        'new_value',
        'ip_address',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function logChange(
        $admin,
        int $userId,
        string $action,
        ?string $field,
        $oldValue,
        $newValue,
        ?Request $request = null
    ): self {
        return self::create([
            'admin_id'   => $admin?->id,
            'user_id'    => $userId,
            'action'     => $action,
            'field'      => $field,
            'old_value'  => $oldValue !== null ? (string) $oldValue : null,
            'new_value'  => $newValue !== null ? (string) $newValue : null,
            'ip_address' => $request?->ip(),
        ]);
    }
}
