<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class DatabaseRestore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:restore {filename? : Specific backup date/filename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore the database and storage from a backup file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->warn('🚨 WARNING: This command will OVERWRITE your current database and public storage files!');
        
        if (!$this->confirm('Are you absolutely sure you want to proceed?', false)) {
            $this->info('Restore cancelled.');
            return;
        }

        $backupPath = storage_path('app/backups');
        if (!File::exists($backupPath)) {
            $this->error('Backup directory not found!');
            return;
        }

        $files = File::files($backupPath);
        if (empty($files)) {
            $this->error('No backup files found!');
            return;
        }

        // Sort by time descending
        usort($files, fn($a, $b) => $b->getMTime() <=> $a->getMTime());

        $targetFile = $this->argument('filename');
        
        if (!$targetFile) {
            // Pick latest if not specified
            $latestDb = collect($files)->first(fn($f) => str_starts_with($f->getFilename(), 'db_backup_'));
            if (!$latestDb) {
                $this->error('No database backup found.');
                return;
            }
            $targetFile = str_replace(['db_backup_', '.sql'], '', $latestDb->getFilename());
        }

        $dbFileName = "db_backup_{$targetFile}.sql";
        $zipFileName = "storage_backup_{$targetFile}.zip";

        $dbPath = "{$backupPath}/{$dbFileName}";
        $zipPath = "{$backupPath}/{$zipFileName}";

        if (!File::exists($dbPath)) {
            $this->error("Backup file not found: {$dbFileName}");
            return;
        }

        // 1. Restore Database
        $this->info("Restoring database from {$dbFileName}...");
        
        // Wipe current DB
        $this->wipeDatabase();

        $command = sprintf(
            'mysql --user=%s --password=%s --host=%s %s < %s',
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            config('database.connections.mysql.host'),
            config('database.connections.mysql.database'),
            escapeshellarg($dbPath)
        );

        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            $this->error('Failed to import database SQL!');
            return 1;
        }

        // 2. Restore Storage
        if (File::exists($zipPath)) {
            $this->info("Extracting media assets from {$zipFileName}...");
            $this->unzipStorage($zipPath);
        } else {
            $this->warn("Storage backup ZIP not found, skipping media restore.");
        }

        $this->info('✅ Restore completed successfully!');
    }

    protected function wipeDatabase()
    {
        // For MySQL, drop and recreate is easiest
        $dbName = config('database.connections.mysql.database');
        DB::statement("DROP DATABASE IF EXISTS `{$dbName}`");
        DB::statement("CREATE DATABASE `{$dbName}`");
        DB::statement("USE `{$dbName}`");
    }

    protected function unzipStorage($zipPath)
    {
        $zip = new \ZipArchive();
        if ($zip->open($zipPath) === TRUE) {
            $destPath = storage_path('app/public');
            // Optional: clean dest first
            // File::cleanDirectory($destPath);
            $zip->extractTo($destPath);
            $zip->close();
        }
    }
}
