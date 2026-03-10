<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('league_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('week_start');
            $table->string('league');
            $table->integer('weekly_points');
            $table->integer('rank_in_league');
            $table->string('result');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->index(['user_id', 'week_start']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('league_history');
    }
};
