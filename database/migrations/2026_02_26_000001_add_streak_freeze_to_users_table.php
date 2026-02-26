<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('streak_freezes_available')->default(2);
            $table->integer('streak_freezes_used')->default(0);
            $table->date('streak_freeze_reset_at')->nullable();
            $table->json('streak_freeze_log')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'streak_freezes_available',
                'streak_freezes_used',
                'streak_freeze_reset_at',
                'streak_freeze_log',
            ]);
        });
    }
};
