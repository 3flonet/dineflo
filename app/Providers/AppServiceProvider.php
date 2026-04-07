<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fix for "public/" in URL on some hosting environments
        if (isset($_SERVER['SCRIPT_NAME']) && str_contains($_SERVER['SCRIPT_NAME'], '/public/index.php')) {
            $_SERVER['SCRIPT_NAME'] = str_replace('/public/index.php', '/index.php', $_SERVER['SCRIPT_NAME']);
            $_SERVER['PHP_SELF'] = str_replace('/public/index.php', '/index.php', $_SERVER['PHP_SELF']);
        }

        // Force root URL from config to prevent /public/ in redirects
        // Only force if it's NOT the default localhost
        if ($appUrl = config('app.url')) {
            if (!str_contains($appUrl, 'localhost')) {
                \Illuminate\Support\Facades\URL::forceRootUrl($appUrl);
            }
        }

        // Performance Monitoring: Log Slow Queries (> 500ms)
        \Illuminate\Support\Facades\DB::listen(function ($query) {
            if ($query->time > 500) {
                \Illuminate\Support\Facades\Log::warning("Slow Query Detected ({$query->time} ms)", [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                ]);
            }
        });

        // Failed Job Monitoring
        \Illuminate\Support\Facades\Queue::failing(function (\Illuminate\Queue\Events\JobFailed $event) {
            $settings = app(\App\Settings\GeneralSettings::class);
            $supportEmail = $settings->support_email ?? 'admin@dineflo.test';

            \Illuminate\Support\Facades\Mail::to($supportEmail)->send(
                new \App\Mail\SystemAlertMail(
                    "Background Job Failed: " . $event->job->resolveName(),
                    "Telah terjadi kegagalan pada proses latar belakang (Background Job). Harap periksa dashboard atau log sistem segera.",
                    [
                        'job_name'   => $event->job->resolveName(),
                        'connection' => $event->connectionName,
                        'queue'      => $event->job->getQueue(),
                        'error'      => $event->exception->getMessage(),
                        'timestamp'  => now()->format('d M Y H:i:s'),
                    ]
                )
            );
        });

        // Dynamic Force HTTPS & Debug Mode in Production
        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
            config(['app.debug' => false]);
        }

        FilamentView::registerRenderHook(
            'panels::head.start',
            fn (): string => \Illuminate\Support\Facades\Blade::render("@vite(['resources/js/app.js'])"),
        );
        
        FilamentView::registerRenderHook(
            \Filament\View\PanelsRenderHook::HEAD_END,
            fn (): \Illuminate\Contracts\View\View => view('filament.pwa-head'),
        );
        
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });

        \App\Models\Restaurant::observe(\App\Observers\RestaurantObserver::class);
        \App\Models\Order::observe(\App\Observers\OrderObserver::class);
        \App\Models\MenuItem::observe(\App\Observers\MenuItemObserver::class);
        \App\Models\Ingredient::observe(\App\Observers\IngredientObserver::class);
        \App\Models\Reservation::observe(\App\Observers\ReservationObserver::class);
        \App\Models\Member::observe(\App\Observers\MemberObserver::class);

        try {
            if (class_exists(\App\Settings\GeneralSettings::class)) {
                $settings = app(\App\Settings\GeneralSettings::class);

                // Override SMTP
                if ($settings->smtp_host) {
                    $password = $settings->smtp_password;
                    try { $password = \Illuminate\Support\Facades\Crypt::decryptString($password); } catch (\Exception $e) {}
                    
                    config([
                        'mail.mailers.smtp.host' => $settings->smtp_host,
                        'mail.mailers.smtp.port' => $settings->smtp_port,
                        'mail.mailers.smtp.username' => $settings->smtp_username,
                        'mail.mailers.smtp.password' => $password,
                        'mail.mailers.smtp.encryption' => $settings->smtp_encryption,
                        'mail.from.address' => $settings->smtp_from_address,
                        'mail.from.name' => $settings->smtp_from_name,
                    ]);
                }

                // Override Midtrans
                if ($settings->midtrans_server_key) {
                    $serverKey = $settings->midtrans_server_key;
                    $clientKey = $settings->midtrans_client_key;
                    try { $serverKey = \Illuminate\Support\Facades\Crypt::decryptString($serverKey); } catch (\Exception $e) {}
                    try { $clientKey = \Illuminate\Support\Facades\Crypt::decryptString($clientKey); } catch (\Exception $e) {}

                    config([
                        'midtrans.is_production' => $settings->midtrans_is_production,
                        'midtrans.merchant_id' => $settings->midtrans_merchant_id,
                        'midtrans.server_key' => $serverKey,
                        'midtrans.client_key' => $clientKey,
                    ]);
                }

                // Site Name
                if ($settings->site_name) {
                    config(['app.name' => $settings->site_name]);
                }
            }
        } catch (\Exception $e) {
            // Settings table not migrated yet
        }
    }
}
