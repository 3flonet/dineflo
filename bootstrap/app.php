<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(\App\Http\Middleware\InstallationCheck::class);

        $middleware->web(append: [
            \App\Http\Middleware\CheckLicenseStatus::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'payment/notification',
            'livewire/upload-file',
        ]);
    })
    ->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule) {
        $schedule->command('app:backup')->dailyAt('01:00');
        $schedule->command('livewire:prune-temporary-uploads')->daily();
        $schedule->command('logs:clean')->daily();
        $schedule->command('license:ping')->everySixHours();
        $schedule->command('license:send-warnings')->dailyAt('08:00');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
