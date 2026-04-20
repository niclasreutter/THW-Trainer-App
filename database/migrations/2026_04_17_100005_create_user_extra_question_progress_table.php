<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_extra_question_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('extra_question_id')->constrained('extra_questions')->cascadeOnDelete();
            $table->integer('consecutive_correct')->default(0);
            $table->timestamp('last_answered_at')->nullable();
            $table->timestamp('next_review_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'extra_question_id'], 'user_extra_question_unique');
            $table->index(['user_id', 'consecutive_correct']);
            $table->index('next_review_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_extra_question_progress');
    }
};
