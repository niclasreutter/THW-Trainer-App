<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Gemeisterte Fragen (consecutive_correct >= 3) aus der SR-Queue entfernen.
     * next_review_at = null bedeutet: kein weiteres SR-Scheduling, keine E-Mails.
     */
    public function up(): void
    {
        DB::table('user_question_progress')
            ->where('consecutive_correct', '>=', 3)
            ->update(['next_review_at' => null]);
    }

    public function down(): void
    {
        // Nicht umkehrbar – originale Daten sind nicht gespeichert
    }
};
