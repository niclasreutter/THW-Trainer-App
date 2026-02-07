<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Bestehende next_review_at Timestamps auf Tagesbeginn (00:00:00) normalisieren.
     * Behebt den Bug, dass Fragen erst zur exakten Uhrzeit fällig wurden
     * statt ab Tagesbeginn.
     */
    public function up(): void
    {
        DB::table('user_question_progress')
            ->whereNotNull('next_review_at')
            ->update([
                'next_review_at' => DB::raw("DATE(next_review_at)"),
            ]);
    }

    /**
     * Nicht rückgängig machbar - die exakten Uhrzeiten sind verloren.
     */
    public function down(): void
    {
        // Timestamps können nicht wiederhergestellt werden
    }
};
