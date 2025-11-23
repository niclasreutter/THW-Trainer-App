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
        Schema::create('lehrgang_question_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('lehrgang_question_id')->constrained('lehrgaenge_questions')->onDelete('cascade');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
            
            // Index für schnelle Abfragen
            $table->index(['user_id', 'lehrgang_question_id']);
            $table->index(['lehrgang_question_id']);
            $table->index(['is_correct']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lehrgang_question_statistics');
    }
};
