<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('streak_freezes_available')->default(0)->change();
        });

        // Bestehende User: Freezes zurücksetzen (ab jetzt nur kaufbar)
        DB::table('users')->update(['streak_freezes_available' => 0]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('streak_freezes_available')->default(2)->change();
        });

        DB::table('users')->update(['streak_freezes_available' => 2]);
    }
};
