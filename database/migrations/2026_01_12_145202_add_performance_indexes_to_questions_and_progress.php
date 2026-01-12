<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Fügt Performance-Indexes zu häufig abgefragten Spalten hinzu:
     * - questions.lernabschnitt (wird in fast jedem WHERE verwendet)
     * - questions.[lernabschnitt, nummer] (für sortierte Abfragen)
     * - user_question_progress.consecutive_correct (für Mastered-Queries)
     * - user_question_progress.[question_id, user_id] (für umgekehrte Lookups)
     */
    public function up(): void
    {
        // Indexes für questions Tabelle
        Schema::table('questions', function (Blueprint $table) {
            // Einzelner Index für Lernabschnitt-Filterung
            $table->index('lernabschnitt', 'idx_questions_lernabschnitt');

            // Composite Index für sortierte Abfragen innerhalb eines Lernabschnitts
            $table->index(['lernabschnitt', 'nummer'], 'idx_questions_lernabschnitt_nummer');
        });

        // Indexes für user_question_progress Tabelle
        Schema::table('user_question_progress', function (Blueprint $table) {
            // Index für Mastered-Queries (consecutive_correct >= 2)
            $table->index('consecutive_correct', 'idx_progress_consecutive_correct');

            // Umgekehrter Composite Index für question → user Lookups
            $table->index(['question_id', 'user_id'], 'idx_progress_question_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropIndex('idx_questions_lernabschnitt');
            $table->dropIndex('idx_questions_lernabschnitt_nummer');
        });

        Schema::table('user_question_progress', function (Blueprint $table) {
            $table->dropIndex('idx_progress_consecutive_correct');
            $table->dropIndex('idx_progress_question_user');
        });
    }
};
