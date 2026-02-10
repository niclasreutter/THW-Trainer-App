<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Korrigiert consecutive_correct für bereits gelöste Fragen.
     *
     * Die initiale Migration setzte consecutive_correct=2 für solved_questions,
     * aber MASTERY_THRESHOLD wurde auf 3 erhöht. Dadurch wurden gelöste Fragen
     * fälschlicherweise als "nicht gemeistert" eingestuft und im Practice-Modus
     * vor wirklich ungelösten Fragen angezeigt.
     */
    public function up(): void
    {
        $users = DB::table('users')
            ->whereNotNull('solved_questions')
            ->get();

        $updatedCount = 0;

        foreach ($users as $user) {
            $raw = $user->solved_questions;

            // Doppelt-encodiertes JSON behandeln (z.B. "\"[1,2,3]\"")
            $solved = json_decode($raw, true);
            if (is_string($solved)) {
                $solved = json_decode($solved, true);
            }

            if (!is_array($solved) || empty($solved)) {
                continue;
            }

            // In Chunks aufteilen um SQLite-Limit für Parameter zu respektieren
            foreach (array_chunk($solved, 500) as $chunk) {
                $affected = DB::table('user_question_progress')
                    ->where('user_id', $user->id)
                    ->whereIn('question_id', $chunk)
                    ->where('consecutive_correct', '<', 3)
                    ->update(['consecutive_correct' => 3]);

                $updatedCount += $affected;
            }
        }

        if ($updatedCount > 0) {
            \Log::info("Fixed mastery threshold for {$updatedCount} solved question progress records.");
        }
    }

    public function down(): void
    {
        // Nicht rückgängig machbar - wir wissen nicht welche Einträge
        // vorher consecutive_correct=2 hatten vs. natürlich auf 3 kamen
    }
};
