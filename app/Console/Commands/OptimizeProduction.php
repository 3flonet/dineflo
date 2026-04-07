<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class OptimizeProduction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:optimize-prod';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run all production optimization commands in one go';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting Production Optimization...');

        // 1. Framework Caching
        $this->task('Caching config...', fn() => Artisan::call('config:cache'));
        $this->task('Caching routes...', fn() => Artisan::call('route:cache'));
        $this->task('Caching views...', fn() => Artisan::call('view:cache'));
        $this->task('Caching events...', fn() => Artisan::call('event:cache'));

        // 2. Filament Specific Caching
        if (class_exists(\Filament\Commands\OptimizeCommand::class)) {
            $this->task('Caching Filament components...', fn() => Artisan::call('filament:optimize'));
        }
        $this->task('Caching Blade icons...', fn() => Artisan::call('icons:cache'));

        // 3. Clear transient data
        $this->task('Clearing old session and cache files...', function() {
            Artisan::call('cache:clear');
            return true;
        });

        $this->newLine();
        $this->info('✅ All systems optimized for Production!');
        $this->info('Tip: Run this command every time you deploy new code.');
    }

    protected function task($title, $callback)
    {
        $this->output->write("$title ");
        
        $result = $callback();
        
        if ($result !== false) {
            $this->output->writeln('<info>DONE</info>');
        } else {
            $this->output->writeln('<error>FAILED</error>');
        }
    }
}
