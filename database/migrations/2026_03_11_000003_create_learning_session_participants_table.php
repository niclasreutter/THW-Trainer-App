<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_session_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instance_id')->constrained('learning_session_instances')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->dateTime('joined_at');
            $table->boolean('ranking_consent')->default(false);
            $table->integer('questions_answered')->default(0);
            $table->integer('questions_correct')->default(0);
            $table->integer('xp_earned')->default(0);
            $table->decimal('accuracy_percent', 5, 2)->default(0);
            $table->integer('avg_answer_time_ms')->nullable();
            $table->decimal('rank_score', 10, 2)->default(0);
            $table->integer('final_rank')->nullable();
            $table->timestamps();

            $table->unique(['instance_id', 'user_id']);
            $table->index(['instance_id', 'rank_score']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_session_participants');
    }
};
