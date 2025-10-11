<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class PerformanceOptimization extends Command
{
    protected $signature = 'system:performance-optimization';
    protected $description = 'Optimiert System-Performance durch Cache-Bereinigung und Statistiken-Updates';

    public function handle()
    {
        $this->info('🚀 Starte Performance-Optimierung...');
        
        $startTime = microtime(true);
        $optimizations = [];

        try {
            // 1. Cache bereinigen
            $this->info('📦 Bereinige Cache...');
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            $optimizations[] = 'Cache bereinigt';

            // 2. Top-Wrong-Questions Cache aktualisieren
            $this->info('📊 Aktualisiere Statistiken-Cache...');
            $this->updateTopWrongQuestionsCache();
            $optimizations[] = 'Top-Wrong-Questions Cache aktualisiert';

            // 3. Datenbank-Indizes optimieren
            $this->info('🗃️ Optimiere Datenbank-Indizes...');
            $this->optimizeDatabaseIndexes();
            $optimizations[] = 'Datenbank-Indizes optimiert';

            // 4. Session-Cleanup
            $this->info('🧹 Bereinige alte Sessions...');
            $this->cleanupOldSessions();
            $optimizations[] = 'Alte Sessions bereinigt';

            $endTime = microtime(true);
            $duration = round($endTime - $startTime, 2);

            $this->info("✅ Performance-Optimierung abgeschlossen in {$duration}s");
            $this->info('Durchgeführte Optimierungen:');
            foreach ($optimizations as $optimization) {
                $this->line("  • {$optimization}");
            }

            // Log für Monitoring
            \Log::info('Performance-Optimierung abgeschlossen', [
                'duration' => $duration,
                'optimizations' => $optimizations,
                'memory_usage' => memory_get_peak_usage(true)
            ]);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Fehler bei Performance-Optimierung: ' . $e->getMessage());
            \Log::error('Performance-Optimierung fehlgeschlagen', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
    }

    private function updateTopWrongQuestionsCache()
    {
        // Berechne Top 10 falsch beantwortete Fragen
        $topWrongQuestions = DB::table('question_statistics')
            ->select('question_id', DB::raw('COUNT(*) as wrong_count'))
            ->where('is_correct', false)
            ->groupBy('question_id')
            ->orderByDesc('wrong_count')
            ->limit(10)
            ->get();

        $topWrongQuestionIds = $topWrongQuestions->pluck('question_id')->toArray();
        
        // Cache für 1 Stunde speichern
        Cache::put('top_wrong_questions', $topWrongQuestionIds, 3600);
        
        $this->line("  → {$topWrongQuestions->count()} Top-Wrong-Questions gecacht");
    }

    private function optimizeDatabaseIndexes()
    {
        // Prüfe und optimiere wichtige Tabellen
        $tables = ['users', 'questions', 'question_statistics', 'exam_statistics'];
        
        foreach ($tables as $table) {
            try {
                DB::statement("OPTIMIZE TABLE {$table}");
                $this->line("  → Tabelle {$table} optimiert");
            } catch (\Exception $e) {
                $this->warn("  → Fehler bei {$table}: " . $e->getMessage());
            }
        }
    }

    private function cleanupOldSessions()
    {
        // Lösche Sessions älter als 7 Tage
        $deleted = DB::table('sessions')
            ->where('last_activity', '<', now()->subDays(7)->timestamp)
            ->delete();
            
        $this->line("  → {$deleted} alte Sessions gelöscht");
    }
}
