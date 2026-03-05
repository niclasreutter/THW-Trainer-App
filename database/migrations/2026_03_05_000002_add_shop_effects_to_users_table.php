<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('glowing_name_until')->nullable();
            $table->string('glowing_name_type', 20)->nullable();
            $table->timestamp('double_xp_until')->nullable();
            $table->timestamp('profile_frame_until')->nullable();
            $table->string('rank_color', 20)->nullable();
            $table->timestamp('rank_color_until')->nullable();
            $table->string('active_title', 50)->nullable();
            $table->timestamp('active_title_until')->nullable();
            $table->integer('total_points_spent')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'glowing_name_until',
                'glowing_name_type',
                'double_xp_until',
                'profile_frame_until',
                'rank_color',
                'rank_color_until',
                'active_title',
                'active_title_until',
                'total_points_spent',
            ]);
        });
    }
};
