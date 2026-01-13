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
        Schema::create('ortsverband_lernpool_question_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('lernpool_question_id')->constrained('ortsverband_lernpool_questions')->onDelete('cascade');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();

            // Index für schnelle Abfragen
            $table->index(['user_id', 'lernpool_question_id']);
            $table->index(['lernpool_question_id']);
            $table->index(['is_correct']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ortsverband_lernpool_question_statistics');
    }
};
