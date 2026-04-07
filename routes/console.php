<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

use Illuminate\Support\Facades\Schedule;

Schedule::command('subscription:check-status')->daily();
Schedule::command('app:auto-close-cashier')->everyMinute();
Schedule::job(new \App\Jobs\ProcessEmailCampaigns)->dailyAt('08:00');
Schedule::job(new \App\Jobs\ProcessWhatsAppCampaigns)->dailyAt('09:00');

// System Broadcast Scheduling
Schedule::call(function () {
    \App\Models\SystemBroadcast::where('status', 'scheduled')
        ->where('scheduled_at', '<=', now())
        ->get()
        ->each(fn ($b) => \App\Jobs\ProcessSystemBroadcast::dispatch($b));
})->everyMinute();

// Jadwalkan sync perizinan setiap minggu untuk menjaga konsistensi di seluruh restoran
Schedule::command('dineflo:sync-permissions')
    ->weekly()
    ->sundays()
    ->at('02:00')
    ->runInBackground();
