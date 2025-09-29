<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('points')->default(0);
            $table->integer('level')->default(1);
            $table->integer('streak_days')->default(0);
            $table->date('last_activity_date')->nullable();
            $table->json('achievements')->nullable();
            $table->integer('daily_questions_solved')->default(0);
            $table->date('daily_questions_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'points', 
                'level', 
                'streak_days', 
                'last_activity_date', 
                'achievements', 
                'daily_questions_solved', 
                'daily_questions_date'
            ]);
        });
    }
};
