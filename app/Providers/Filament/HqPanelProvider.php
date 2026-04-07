<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class HqPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $favicon = null;
        $brandName = 'Dineflo HQ';

        try {
            $settings = app(\App\Settings\GeneralSettings::class);
            $favicon  = $settings->site_favicon
                ? \Illuminate\Support\Facades\Storage::url($settings->site_favicon)
                : null;
            $brandName = ($settings->site_name ?: config('app.name', 'Dineflo')) . ' HQ';
        } catch (\Throwable $e) {
            $brandName = config('app.name', 'Dineflo') . ' HQ';
        }

        return $panel
            ->id('hq')
            ->path('hq')
            ->login()
            ->favicon($favicon)
            ->brandName($brandName)
            ->brandLogo(null)
            ->colors([
                'primary' => Color::Violet,
            ])
            ->discoverResources(in: app_path('Filament/Hq/Resources'), for: 'App\\Filament\\Hq\\Resources')
            ->discoverPages(in: app_path('Filament/Hq/Pages'), for: 'App\\Filament\\Hq\\Pages')
            ->pages([
                \App\Filament\Hq\Pages\HqDashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Hq/Widgets'), for: 'App\\Filament\\Hq\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
