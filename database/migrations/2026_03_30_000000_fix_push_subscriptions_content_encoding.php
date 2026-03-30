<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * iOS Safari meldet faelschlicherweise aesgcm als supportedContentEncodings,
     * aber Apple Push Service verlangt aes128gcm. Alle Eintraege korrigieren.
     */
    public function up(): void
    {
        DB::table('push_subscriptions')
            ->where('content_encoding', 'aesgcm')
            ->update(['content_encoding' => 'aes128gcm']);
    }

    public function down(): void
    {
        // Nicht reversibel — aesgcm war ohnehin falsch
    }
};
