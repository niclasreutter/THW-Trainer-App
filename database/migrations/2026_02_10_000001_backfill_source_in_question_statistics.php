<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Backfill: Markiere question_statistics-Einträge die zu Prüfungen gehören.
        // Strategie: Für jede exam_statistics finde question_statistics mit gleichem user_id
        // und created_at innerhalb von ±5 Sekunden.
        $examStats = DB::table('exam_statistics')->get();

        foreach ($examStats as $exam) {
            DB::table('question_statistics')
                ->where('user_id', $exam->user_id)
                ->whereNull('source')
                ->whereBetween('created_at', [
                    date('Y-m-d H:i:s', strtotime($exam->created_at) - 5),
                    date('Y-m-d H:i:s', strtotime($exam->created_at) + 5),
                ])
                ->update(['source' => 'exam']);
        }

        // Alle verbleibenden NULL-Einträge sind Practice
        DB::table('question_statistics')
            ->whereNull('source')
            ->update(['source' => 'practice']);
    }

    public function down(): void
    {
        // Nicht rückgängig machbar - wir wissen nicht mehr welche NULL waren
    }
};
