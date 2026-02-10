<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillExamLinks extends Command
{
    protected $signature = 'app:backfill-exam-links';
    protected $description = 'Verknüpft question_statistics mit exam_statistics anhand zeitlicher Nähe';

    public function handle(): int
    {
        // Reset vorherige Zuordnungen
        DB::table('question_statistics')
            ->whereNotNull('exam_statistic_id')
            ->update(['exam_statistic_id' => null]);

        $users = DB::table('exam_statistics')
            ->select('user_id')
            ->distinct()
            ->whereNotNull('user_id')
            ->pluck('user_id');

        $totalLinked = 0;
        $totalExams = 0;
        $skippedTooFew = 0;
        $skippedSpread = 0;

        foreach ($users as $userId) {
            // Prüfungen von NEUESTER zu ÄLTESTER verarbeiten
            $exams = DB::table('exam_statistics')
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($exams as $exam) {
                $totalExams++;
                $examTime = Carbon::parse($exam->created_at)->addSeconds(10);

                // Die 40 jüngsten ungematchten Records VOR dieser Prüfung
                $candidates = DB::table('question_statistics')
                    ->where('user_id', $userId)
                    ->whereNull('exam_statistic_id')
                    ->where('created_at', '<=', $examTime->format('Y-m-d H:i:s'))
                    ->orderBy('created_at', 'desc')
                    ->limit(40)
                    ->get(['id', 'created_at']);

                if ($candidates->count() < 30) {
                    $skippedTooFew++;
                    $this->line("  Übersprungen: Prüfung #{$exam->id} ({$exam->created_at}) - nur {$candidates->count()} Records gefunden (User {$userId})");
                    continue;
                }

                // Cluster-Check: Records müssen zeitlich zusammengehören
                $timestamps = $candidates->pluck('created_at')->map(fn($t) => Carbon::parse($t)->timestamp);
                $timeSpread = $timestamps->max() - $timestamps->min();

                // Nur zuordnen wenn die Records innerhalb von 5 Minuten liegen
                if ($timeSpread > 300) {
                    $skippedSpread++;
                    $spreadMinutes = round($timeSpread / 60, 1);
                    $this->line("  Übersprungen: Prüfung #{$exam->id} ({$exam->created_at}) - Zeitspanne {$spreadMinutes} Min (User {$userId})");
                    continue;
                }

                $ids = $candidates->pluck('id')->toArray();
                DB::table('question_statistics')
                    ->whereIn('id', $ids)
                    ->update([
                        'exam_statistic_id' => $exam->id,
                        'source' => 'exam',
                    ]);

                $totalLinked += count($ids);
            }
        }

        $skipped = $skippedTooFew + $skippedSpread;
        $this->info("Fertig: {$totalLinked} question_statistics zu {$totalExams} Prüfungen verknüpft ({$skipped} übersprungen).");
        if ($skippedTooFew > 0) {
            $this->warn("  {$skippedTooFew}x zu wenige Records (<30 question_statistics vor Prüfung)");
        }
        if ($skippedSpread > 0) {
            $this->warn("  {$skippedSpread}x Zeitspanne zu gross (>5 Min zwischen Records)");
        }

        return self::SUCCESS;
    }
}
