<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserQuestionProgress;
use Illuminate\Console\Command;

class CleanupFailedQuestions extends Command
{
    protected $signature = 'app:cleanup-failed-questions {--dry-run : Nur anzeigen, nicht ändern}';
    protected $description = 'Bereinigt exam_failed_questions: Entfernt Fragen die nie gemeistert waren (Bug-Daten)';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $affected = 0;

        $users = User::whereNotNull('exam_failed_questions')
            ->where('exam_failed_questions', '!=', '[]')
            ->get();

        foreach ($users as $user) {
            $failed = is_array($user->exam_failed_questions)
                ? $user->exam_failed_questions
                : json_decode($user->exam_failed_questions, true) ?? [];

            if (empty($failed)) {
                continue;
            }

            // Nur Fragen behalten, die tatsächlich gemeistert sind oder waren
            // (d.h. in solved_questions stehen ODER aktuell gemeistert sind)
            $solved = is_array($user->solved_questions)
                ? $user->solved_questions
                : json_decode($user->solved_questions, true) ?? [];

            $masteredIds = UserQuestionProgress::where('user_id', $user->id)
                ->where('consecutive_correct', '>=', UserQuestionProgress::MASTERY_THRESHOLD)
                ->pluck('question_id')
                ->toArray();

            $validFailed = array_values(array_filter($failed, function ($qId) use ($solved, $masteredIds) {
                return in_array($qId, $solved) || in_array($qId, $masteredIds);
            }));

            $removed = array_diff($failed, $validFailed);

            if (!empty($removed)) {
                $affected++;
                $this->info("User {$user->id} ({$user->name}): " . count($removed) . " ungültige Fehlerfragen entfernt");

                if (!$dryRun) {
                    $user->exam_failed_questions = $validFailed;
                    $user->save();
                }
            }
        }

        $prefix = $dryRun ? '[DRY-RUN] ' : '';
        $this->info("{$prefix}{$affected} User bereinigt.");

        return Command::SUCCESS;
    }
}
