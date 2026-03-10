<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_challenges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('challenge_month');
            $table->string('challenge_type');
            $table->string('challenge_title');
            $table->integer('target_value');
            $table->integer('current_value')->default(0);
            $table->boolean('milestone_25_claimed')->default(false);
            $table->boolean('milestone_50_claimed')->default(false);
            $table->boolean('milestone_75_claimed')->default(false);
            $table->boolean('milestone_100_claimed')->default(false);
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unique(['user_id', 'challenge_month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_challenges');
    }
};
