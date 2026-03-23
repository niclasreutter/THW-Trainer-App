<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ExportUserData extends Command
{
    protected $signature = 'user:export {user_id : Die User-ID aus der Produktion} {--connection=mysql : DB-Connection Name}';
    protected $description = 'Exportiert alle Daten eines Users als JSON-Datei (fuer Test-Import)';

    private array $relatedTables = [
        'user_question_progress' => 'user_id',
        'user_lehrgang_progress' => 'user_id',
        'user_lehrgaenge' => 'user_id',
        'exam_statistics' => 'user_id',
        'question_statistics' => 'user_id',
        'ortsverband_lernpool_enrollments' => 'user_id',
        'ortsverband_lernpool_progress' => 'user_id',
        'shop_purchases' => 'user_id',
        'user_avatar_accessories' => 'user_id',
        'lootboxes' => 'user_id',
        'xp_histories' => 'user_id',
        'notifications' => 'user_id',
        'ortsverband_members' => 'user_id',
        'exam_feedbacks' => 'user_id',
        'lehrgang_question_statistics' => 'user_id',
    ];

    public function handle()
    {
        $userId = $this->argument('user_id');
        $connection = $this->option('connection');

        $this->info("Exportiere User #{$userId} von Connection '{$connection}'...");

        // User laden
        $user = DB::connection($connection)->table('users')->where('id', $userId)->first();

        if (!$user) {
            $this->error("User #{$userId} nicht gefunden auf Connection '{$connection}'!");
            return Command::FAILURE;
        }

        $this->info("User gefunden: {$user->name} ({$user->email})");

        $export = [
            'exported_at' => now()->toIso8601String(),
            'source_connection' => $connection,
            'user' => (array) $user,
            'relations' => [],
        ];

        // Alle verwandten Tabellen exportieren
        foreach ($this->relatedTables as $table => $foreignKey) {
            $rows = DB::connection($connection)
                ->table($table)
                ->where($foreignKey, $userId)
                ->get()
                ->map(fn($row) => (array) $row)
                ->toArray();

            $export['relations'][$table] = $rows;
            $count = count($rows);

            if ($count > 0) {
                $this->line("  {$table}: {$count} Eintraege");
            }
        }

        // Exam-Feedbacks ueber exam_statistics verknuepfen
        $examStatIds = collect($export['relations']['exam_statistics'] ?? [])
            ->pluck('id')
            ->filter()
            ->toArray();

        if (!empty($examStatIds)) {
            $feedbacks = DB::connection($connection)
                ->table('exam_feedbacks')
                ->whereIn('exam_statistic_id', $examStatIds)
                ->get()
                ->map(fn($row) => (array) $row)
                ->toArray();

            if (!empty($feedbacks)) {
                // Merge with already exported feedbacks (avoid duplicates)
                $existingIds = collect($export['relations']['exam_feedbacks'] ?? [])->pluck('id')->toArray();
                foreach ($feedbacks as $fb) {
                    if (!in_array($fb['id'], $existingIds)) {
                        $export['relations']['exam_feedbacks'][] = $fb;
                    }
                }
            }
        }

        // Learning Session Participants
        $participants = DB::connection($connection)
            ->table('learning_session_participants')
            ->where('user_id', $userId)
            ->get()
            ->map(fn($row) => (array) $row)
            ->toArray();

        if (!empty($participants)) {
            $export['relations']['learning_session_participants'] = $participants;
            $this->line("  learning_session_participants: " . count($participants) . " Eintraege");
        }

        // Als JSON speichern
        $filename = "user_export_{$userId}_" . now()->format('Y-m-d_H-i-s') . '.json';
        $path = storage_path("app/{$filename}");

        file_put_contents($path, json_encode($export, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $size = $this->formatBytes(filesize($path));
        $this->newLine();
        $this->info("Export gespeichert: {$path}");
        $this->info("Groesse: {$size}");

        return Command::SUCCESS;
    }

    private function formatBytes($size): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        for ($i = 0; $size >= 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        return round($size, 2) . ' ' . $units[$i];
    }
}
