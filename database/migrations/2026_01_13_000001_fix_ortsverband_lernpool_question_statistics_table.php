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
        // Lösche die alte (fehlerhafte) Tabelle falls sie existiert
        Schema::dropIfExists('ortsverband_lernpool_question_statistics');

        // Erstelle sie neu mit korrekten Constraint-Namen
        Schema::create('ortsverband_lernpool_question_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('lernpool_question_id');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();

            // Foreign Key mit kurzem Namen
            $table->foreign('lernpool_question_id', 'ov_lp_q_stats_lp_q_id_fk')
                ->references('id')
                ->on('ortsverband_lernpool_questions')
                ->onDelete('cascade');

            // Index für schnelle Abfragen
            $table->index(['user_id', 'lernpool_question_id'], 'ov_lp_q_stats_user_lp_q_idx');
            $table->index(['lernpool_question_id'], 'ov_lp_q_stats_lp_q_idx');
            $table->index(['is_correct'], 'ov_lp_q_stats_correct_idx');
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
