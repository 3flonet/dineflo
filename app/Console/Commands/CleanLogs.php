<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CleanLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:clean {--days=7 : Keep logs for this many days}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old log files to save space';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $logPath = storage_path('logs');
        
        if (!File::exists($logPath)) {
            $this->error("Log path does not exist.");
            return;
        }

        $files = File::files($logPath);
        $now = time();
        $seconds = $days * 24 * 60 * 60;
        $count = 0;

        foreach ($files as $file) {
            // Keep .gitignore
            if ($file->getFilename() === '.gitignore') continue;

            if ($now - $file->getMTime() > $seconds) {
                File::delete($file->getRealPath());
                $count++;
            }
        }

        $this->info("Successfully deleted {$count} old log files.");
    }
}
