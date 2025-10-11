<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class SystemMaintenance extends Command
{
    protected $signature = 'system:maintenance';
    protected $description = 'Führt System-Wartung und Speicher-Optimierung durch';

    public function handle()
    {
        $this->info('🔧 Starte System-Wartung...');
        
        $startTime = microtime(true);
        $maintenanceTasks = [];

        try {
            // 1. Log-Dateien bereinigen
            $this->info('📝 Bereinige Log-Dateien...');
            $this->cleanupLogFiles();
            $maintenanceTasks[] = 'Log-Dateien bereinigt';

            // 2. Cache vollständig leeren
            $this->info('🗑️ Leere alle Caches...');
            $this->clearAllCaches();
            $maintenanceTasks[] = 'Alle Caches geleert';

            // 3. Temporäre Dateien löschen
            $this->info('🧹 Lösche temporäre Dateien...');
            $this->cleanupTempFiles();
            $maintenanceTasks[] = 'Temporäre Dateien gelöscht';

            // 4. Datenbank bereinigen
            $this->info('🗃️ Bereinige Datenbank...');
            $this->cleanupDatabase();
            $maintenanceTasks[] = 'Datenbank bereinigt';

            // 5. Speicherplatz analysieren
            $this->info('📊 Analysiere Speicherplatz...');
            $storageInfo = $this->analyzeStorage();
            $maintenanceTasks[] = 'Speicherplatz analysiert';

            // 6. Session-Cleanup
            $this->info('🔐 Bereinige Sessions...');
            $this->cleanupSessions();
            $maintenanceTasks[] = 'Sessions bereinigt';

            // 7. Queue-Cleanup
            $this->info('📋 Bereinige Queue-Jobs...');
            $this->cleanupQueueJobs();
            $maintenanceTasks[] = 'Queue-Jobs bereinigt';

            $endTime = microtime(true);
            $duration = round($endTime - $startTime, 2);

            $this->info("✅ System-Wartung abgeschlossen in {$duration}s");
            $this->info('Durchgeführte Wartungsaufgaben:');
            foreach ($maintenanceTasks as $task) {
                $this->line("  • {$task}");
            }

            // Speicher-Info anzeigen
            $this->displayStorageInfo($storageInfo);

            // Log für Monitoring
            \Log::info('System-Wartung abgeschlossen', [
                'duration' => $duration,
                'tasks' => $maintenanceTasks,
                'storage_info' => $storageInfo,
                'memory_usage' => memory_get_peak_usage(true)
            ]);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Fehler bei System-Wartung: ' . $e->getMessage());
            \Log::error('System-Wartung fehlgeschlagen', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
    }

    private function cleanupLogFiles()
    {
        $logPath = storage_path('logs');
        $files = glob($logPath . '/*.log');
        
        $totalSize = 0;
        $deletedCount = 0;

        foreach ($files as $file) {
            $fileSize = filesize($file);
            $totalSize += $fileSize;
            
            // Lösche Logs älter als 30 Tage oder größer als 50MB
            if (filemtime($file) < now()->subDays(30)->timestamp || $fileSize > 50 * 1024 * 1024) {
                if (unlink($file)) {
                    $deletedCount++;
                    $this->line("  → Gelöscht: " . basename($file) . " ({$this->formatBytes($fileSize)})");
                }
            }
        }

        $this->line("  → {$deletedCount} Log-Dateien gelöscht, {$this->formatBytes($totalSize)} gespart");
    }

    private function clearAllCaches()
    {
        $caches = [
            'cache:clear' => 'Application Cache',
            'config:clear' => 'Configuration Cache',
            'route:clear' => 'Route Cache',
            'view:clear' => 'View Cache',
            'event:clear' => 'Event Cache'
        ];

        foreach ($caches as $command => $description) {
            Artisan::call($command);
            $this->line("  → {$description} geleert");
        }
    }

    private function cleanupTempFiles()
    {
        $tempPaths = [
            storage_path('framework/cache/data'),
            storage_path('framework/sessions'),
            storage_path('framework/views'),
            public_path('temp'),
            base_path('temp')
        ];

        $deletedFiles = 0;
        $freedSpace = 0;

        foreach ($tempPaths as $path) {
            if (File::exists($path)) {
                $files = File::allFiles($path);
                
                foreach ($files as $file) {
                    // Lösche Dateien älter als 1 Tag
                    if ($file->getMTime() < now()->subDay()->timestamp) {
                        $size = $file->getSize();
                        if (File::delete($file->getPathname())) {
                            $deletedFiles++;
                            $freedSpace += $size;
                        }
                    }
                }
            }
        }

        $this->line("  → {$deletedFiles} temporäre Dateien gelöscht, {$this->formatBytes($freedSpace)} freigegeben");
    }

    private function cleanupDatabase()
    {
        // Lösche alte Sessions (älter als 7 Tage)
        $deletedSessions = DB::table('sessions')
            ->where('last_activity', '<', now()->subDays(7)->timestamp)
            ->delete();
        $this->line("  → {$deletedSessions} alte Sessions gelöscht");

        // Lösche alte Job-Failures (älter als 30 Tage)
        $deletedJobs = DB::table('failed_jobs')
            ->where('failed_at', '<', now()->subDays(30)->timestamp)
            ->delete();
        $this->line("  → {$deletedJobs} alte Failed Jobs gelöscht");

        // Optimiere Tabellen
        $tables = ['users', 'questions', 'question_statistics', 'exam_statistics', 'sessions'];
        foreach ($tables as $table) {
            try {
                DB::statement("OPTIMIZE TABLE {$table}");
                $this->line("  → Tabelle {$table} optimiert");
            } catch (\Exception $e) {
                $this->warn("  → Fehler bei {$table}: " . $e->getMessage());
            }
        }
    }

    private function analyzeStorage()
    {
        $paths = [
            'storage/app' => storage_path('app'),
            'storage/logs' => storage_path('logs'),
            'storage/framework' => storage_path('framework'),
            'public/build' => public_path('build'),
            'temp' => base_path('temp')
        ];

        $storageInfo = [];
        $totalSize = 0;

        foreach ($paths as $name => $path) {
            if (File::exists($path)) {
                $size = $this->getDirectorySize($path);
                $storageInfo[$name] = $size;
                $totalSize += $size;
            }
        }

        $storageInfo['total'] = $totalSize;
        return $storageInfo;
    }

    private function cleanupSessions()
    {
        // Zusätzliche Session-Bereinigung
        $deleted = DB::table('sessions')
            ->where('last_activity', '<', now()->subDays(3)->timestamp)
            ->delete();
        
        $this->line("  → {$deleted} inaktive Sessions gelöscht");
    }

    private function cleanupQueueJobs()
    {
        // Lösche alte Queue-Jobs (falls vorhanden)
        try {
            $deleted = DB::table('jobs')
                ->where('created_at', '<', now()->subDays(7)->timestamp)
                ->delete();
            $this->line("  → {$deleted} alte Queue-Jobs gelöscht");
        } catch (\Exception $e) {
            // Jobs-Tabelle existiert möglicherweise nicht
            $this->line("  → Queue-Jobs-Bereinigung übersprungen (Tabelle nicht vorhanden)");
        }
    }

    private function getDirectorySize($directory)
    {
        $size = 0;
        if (File::exists($directory)) {
            foreach (File::allFiles($directory) as $file) {
                $size += $file->getSize();
            }
        }
        return $size;
    }

    private function displayStorageInfo($storageInfo)
    {
        $this->info('📊 Speicherplatz-Übersicht:');
        foreach ($storageInfo as $name => $size) {
            if ($name !== 'total') {
                $this->line("  • {$name}: {$this->formatBytes($size)}");
            }
        }
        $this->line("  • Gesamt: {$this->formatBytes($storageInfo['total'])}");
    }

    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $size >= 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }
}
