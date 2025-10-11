<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DatabaseBackup extends Command
{
    protected $signature = 'database:backup {--compress=true}';
    protected $description = 'Erstellt ein wöchentliches Backup der Datenbank';

    public function handle()
    {
        $this->info('💾 Starte Datenbank-Backup...');
        
        $startTime = microtime(true);
        $compress = $this->option('compress');

        try {
            $backupData = $this->createBackup($compress);
            $this->cleanupOldBackups();
            
            $endTime = microtime(true);
            $duration = round($endTime - $startTime, 2);
            $size = $this->formatBytes($backupData['size']);

            $this->info("✅ Backup erfolgreich erstellt in {$duration}s");
            $this->info("📁 Datei: {$backupData['filename']}");
            $this->info("📏 Größe: {$size}");

            \Log::info('Datenbank-Backup erstellt', [
                'filename' => $backupData['filename'],
                'size' => $backupData['size'],
                'duration' => $duration,
                'compressed' => $compress
            ]);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Fehler beim Backup: ' . $e->getMessage());
            \Log::error('Datenbank-Backup fehlgeschlagen', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
    }

    private function createBackup($compress)
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "backup_thw_trainer_{$timestamp}.sql";
        $backupPath = storage_path("app/backups/{$filename}");

        // Erstelle Backups-Verzeichnis falls es nicht existiert
        $backupDir = storage_path('app/backups');
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        // Datenbank-Konfiguration
        $config = config('database.connections.mysql');
        $host = $config['host'];
        $port = $config['port'];
        $database = $config['database'];
        $username = $config['username'];
        $password = $config['password'];

        // mysqldump Befehl
        $command = sprintf(
            'mysqldump --host=%s --port=%s --user=%s --password=%s --single-transaction --routines --triggers %s > %s',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($database),
            escapeshellarg($backupPath)
        );

        // Führe Backup aus
        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \Exception('mysqldump fehlgeschlagen: ' . implode("\n", $output));
        }

        $fileSize = filesize($backupPath);

        // Komprimierung falls gewünscht
        if ($compress && $compress !== 'false') {
            $compressedFilename = $filename . '.gz';
            $compressedPath = storage_path("app/backups/{$compressedFilename}");
            
            $compressed = gzopen($compressedPath, 'w9');
            $source = fopen($backupPath, 'rb');
            
            while (!feof($source)) {
                gzwrite($compressed, fread($source, 8192));
            }
            
            fclose($source);
            gzclose($compressed);
            
            // Lösche unkomprimierte Datei
            unlink($backupPath);
            
            $filename = $compressedFilename;
            $fileSize = filesize($compressedPath);
            $this->line("  → Backup komprimiert: {$this->formatBytes($fileSize)}");
        }

        return [
            'filename' => $filename,
            'size' => $fileSize,
            'path' => $backupPath
        ];
    }

    private function cleanupOldBackups()
    {
        $backupDir = storage_path('app/backups');
        $files = glob($backupDir . '/backup_thw_trainer_*.sql*');
        
        // Sortiere nach Änderungsdatum (neueste zuerst)
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        // Behalte nur die letzten 4 Backups
        $filesToDelete = array_slice($files, 4);
        
        foreach ($filesToDelete as $file) {
            if (unlink($file)) {
                $this->line("  → Altes Backup gelöscht: " . basename($file));
            }
        }

        if (count($filesToDelete) > 0) {
            $this->info("🗑️ " . count($filesToDelete) . " alte Backups bereinigt");
        }
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
