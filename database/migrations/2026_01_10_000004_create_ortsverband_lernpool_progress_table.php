<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ortsverband_lernpool_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('ortsverband_lernpool_questions')->onDelete('cascade');
            $table->integer('consecutive_correct')->default(0);
            $table->integer('total_attempts')->default(0);
            $table->integer('correct_attempts')->default(0);
            $table->boolean('solved')->default(false);
            $table->timestamps();
            
            $table->unique(['user_id', 'question_id']);
            $table->index(['user_id', 'solved']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ortsverband_lernpool_progress');
    }
};
