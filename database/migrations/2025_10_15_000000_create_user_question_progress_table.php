<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Erstelle neue Tabelle
        Schema::create('user_question_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->integer('consecutive_correct')->default(0); // 0, 1, oder 2+
            $table->timestamp('last_answered_at')->nullable();
            $table->timestamps();
            
            // Unique constraint: Pro User nur ein Fortschritt pro Frage
            $table->unique(['user_id', 'question_id']);
            
            // Indizes für Performance
            $table->index(['user_id', 'consecutive_correct']);
            $table->index('last_answered_at');
        });

        // 2. Migriere bestehende Daten
        $this->migrateExistingData();
    }

    /**
     * Migriert bestehende solved_questions und exam_failed_questions
     */
    private function migrateExistingData(): void
    {
        $users = DB::table('users')
            ->whereNotNull('solved_questions')
            ->orWhereNotNull('exam_failed_questions')
            ->get();

        foreach ($users as $user) {
            // Migriere solved_questions (diese sind bereits 2x richtig = gemeistert)
            $solved = json_decode($user->solved_questions, true) ?? [];
            foreach ($solved as $questionId) {
                DB::table('user_question_progress')->insertOrIgnore([
                    'user_id' => $user->id,
                    'question_id' => $questionId,
                    'consecutive_correct' => 2, // Als gemeistert markieren
                    'last_answered_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Migriere exam_failed_questions (diese sind 0x richtig = brauchen 2x)
            $failed = json_decode($user->exam_failed_questions, true) ?? [];
            foreach ($failed as $questionId) {
                // Nur hinzufügen, wenn nicht bereits als "solved" migriert
                if (!in_array($questionId, $solved)) {
                    DB::table('user_question_progress')->insertOrIgnore([
                        'user_id' => $user->id,
                        'question_id' => $questionId,
                        'consecutive_correct' => 0, // Noch nicht richtig beantwortet
                        'last_answered_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_question_progress');
    }
};

