<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserQuestionProgress;
use App\Services\SpacedRepetitionService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class QueryUserSrQuestions extends Command
{
    protected $signature = 'app:query-user-sr {user_id : Die User-ID}';

    protected $description = 'Zeigt SR-Status und offene Fragen eines Users an';

    public function handle()
    {
        $userId = (int) $this->argument('user_id');

        $user = User::find($userId);
        if (! $user) {
            $this->error("User mit ID {$userId} nicht gefunden.");
            return Command::FAILURE;
        }

        $this->info("=== SR-Status fuer User: {$user->name} (ID: {$userId}) ===");
        $this->newLine();

        $srService = new SpacedRepetitionService();
        $stats = $srService->getStats($userId);

        // Uebersicht
        $this->table(
            ['Kategorie', 'Anzahl'],
            [
                ['Jetzt faellig', $stats['due_now']],
                ['Morgen faellig', $stats['due_tomorrow']],
                ['Diese Woche faellig', $stats['due_this_week']],
                ['Gesamt im SR-System', $stats['total_in_system']],
                ['Gemeistert (raus aus SR)', UserQuestionProgress::where('user_id', $userId)
                    ->whereNull('next_review_at')
                    ->where('consecutive_correct', '>=', UserQuestionProgress::MASTERY_THRESHOLD)
                    ->count()],
                ['Gesamt beantwortet', UserQuestionProgress::where('user_id', $userId)->count()],
            ]
        );

        // Faellige Fragen Details
        $dueQuestions = UserQuestionProgress::where('user_id', $userId)
            ->whereNotNull('next_review_at')
            ->where('next_review_at', '<=', Carbon::now())
            ->with('question')
            ->orderBy('next_review_at', 'asc')
            ->get();

        if ($dueQuestions->isNotEmpty()) {
            $this->newLine();
            $this->info("=== Aktuell faellige Fragen ({$dueQuestions->count()}) ===");

            $rows = $dueQuestions->map(function ($p) {
                return [
                    $p->question_id,
                    $p->next_review_at->format('d.m.Y H:i'),
                    $p->consecutive_correct,
                    $p->review_interval . ' Tage',
                    $p->easiness_factor,
                    $p->repetition_count,
                ];
            })->toArray();

            $this->table(
                ['Frage-ID', 'Faellig seit', 'Korrekt-Serie', 'Intervall', 'EF', 'Wiederholungen'],
                $rows
            );
        } else {
            $this->newLine();
            $this->info('Keine aktuell faelligen SR-Fragen.');
        }

        // Kommende Wiederholungen
        if (! empty($stats['upcoming_schedule'])) {
            $this->newLine();
            $this->info('=== Kommende Wiederholungen (naechste 7 Tage) ===');

            $scheduleRows = array_map(function ($s) {
                return [$s['date'], $s['label'], $s['count'] . ' Fragen'];
            }, $stats['upcoming_schedule']);

            $this->table(['Datum', 'Wann', 'Anzahl'], $scheduleRows);
        }

        return Command::SUCCESS;
    }
}
