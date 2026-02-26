<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('daily_goal')->nullable();
            $table->integer('daily_goal_progress')->default(0);
            $table->date('daily_goal_date')->nullable();
            $table->integer('weekly_goal_days')->nullable();
            $table->integer('goals_completed_count')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'daily_goal',
                'daily_goal_progress',
                'daily_goal_date',
                'weekly_goal_days',
                'goals_completed_count',
            ]);
        });
    }
};
