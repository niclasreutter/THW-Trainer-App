<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_quest_chests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('chest_date');
            $table->boolean('is_claimed')->default(false);
            $table->timestamp('claimed_at')->nullable();
            $table->integer('xp_reward');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unique(['user_id', 'chest_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_quest_chests');
    }
};
