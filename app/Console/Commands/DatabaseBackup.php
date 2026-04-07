<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backup {--db-only : Only backup the database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a backup of the database and storage files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting Backup Process...');

        $backupPath = storage_path('app/backups');
        if (!File::exists($backupPath)) {
            File::makeDirectory($backupPath, 0755, true);
        }

        $date = now()->format('Y-m-d_H-i-s');
        
        // 1. Database Backup
        $dbFileName = "db_backup_{$date}.sql";
        $dbFilePath = "{$backupPath}/{$dbFileName}";
        
        $this->info("Dumping database to {$dbFileName}...");
        
        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s %s > %s',
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            config('database.connections.mysql.host'),
            config('database.connections.mysql.database'),
            escapeshellarg($dbFilePath)
        );

        // Note: On Windows, passwords with special chars might need care, 
        // but for Laragon default (empty/simple) it works.
        // We use system() or exec() for simple redirection
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            $this->error('Failed to export database!');
            Log::error('Backup Failed: Database export error.');
            return 1;
        }

        // 2. Storage Backup (Optional)
        if (!$this->option('db-only')) {
            $this->info('Compressing storage files...');
            $storageFileName = "storage_backup_{$date}.zip";
            $storageFilePath = "{$backupPath}/{$storageFileName}";
            
            // We use PHP ZipArchive to be platform independent
            $this->zipStorage($storageFilePath);
        }

        // 3. Cleanup old backups (Keep 7 days)
        $this->info('Cleaning up old backups...');
        $this->cleanup($backupPath);

        $this->info('✅ Backup completed successfully!');
        Log::info("Backup completed: {$date}");
        
        return 0;
    }

    protected function zipStorage($zipPath)
    {
        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            $filesPath = storage_path('app/public');
            if (File::exists($filesPath)) {
                $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($filesPath));
                foreach ($files as $name => $file) {
                    if (!$file->isDir()) {
                        $filePath = $file->getRealPath();
                        $relativePath = substr($filePath, strlen($filesPath) + 1);
                        $zip->addFile($filePath, $relativePath);
                    }
                }
            }
            $zip->close();
        }
    }

    protected function cleanup($path)
    {
        $files = File::files($path);
        $seconds = 7 * 24 * 60 * 60; // 7 days
        $now = time();

        foreach ($files as $file) {
            if ($now - $file->getMTime() > $seconds) {
                File::delete($file->getRealPath());
                $this->line("Deleted old backup: " . $file->getFilename());
            }
        }
    }
}
