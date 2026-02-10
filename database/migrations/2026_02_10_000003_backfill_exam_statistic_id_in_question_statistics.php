<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Backfill: Verknüpfe question_statistics mit exam_statistics.
        // Für jede Prüfung finde die 40 question_statistics desselben Users
        // die zeitlich am nächsten liegen (innerhalb ±60 Sekunden).
        $examStats = DB::table('exam_statistics')
            ->orderBy('created_at', 'asc')
            ->get();

        foreach ($examStats as $exam) {
            $examTime = strtotime($exam->created_at);

            // Finde question_statistics ohne exam_statistic_id innerhalb ±60 Sekunden
            $questionIds = DB::table('question_statistics')
                ->where('user_id', $exam->user_id)
                ->whereNull('exam_statistic_id')
                ->whereBetween('created_at', [
                    date('Y-m-d H:i:s', $examTime - 60),
                    date('Y-m-d H:i:s', $examTime + 60),
                ])
                ->orderBy('created_at', 'asc')
                ->limit(40)
                ->pluck('id')
                ->toArray();

            if (!empty($questionIds)) {
                DB::table('question_statistics')
                    ->whereIn('id', $questionIds)
                    ->update([
                        'exam_statistic_id' => $exam->id,
                        'source' => 'exam',
                    ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('question_statistics')
            ->whereNotNull('exam_statistic_id')
            ->update(['exam_statistic_id' => null]);
    }
};
