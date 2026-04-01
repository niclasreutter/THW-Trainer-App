<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminPushMessage extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'admin_user_id',
        'title',
        'message',
        'target_type',
        'target_id',
        'recipients_count',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    public function ortsverband()
    {
        return $this->belongsTo(Ortsverband::class, 'target_id');
    }
}
