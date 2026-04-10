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
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Assets\Js;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class RestaurantPanelProvider extends PanelProvider
{
    public function boot(): void
    {
    }

    public function panel(Panel $panel): Panel
    {
        $favicon = null;
        $brandName = 'Dineflo Restaurant';

        try {
            $settings = app(\App\Settings\GeneralSettings::class);
            $favicon = $settings->site_favicon ? \Illuminate\Support\Facades\Storage::url($settings->site_favicon) : null;
            $brandName = ($settings->site_name ?: config('app.name', 'Dineflo')) . ' Restaurant';
        } catch (\Throwable $e) {
            $brandName = config('app.name', 'Dineflo') . ' Restaurant';
        }

        return $panel
            ->id('restaurant')
            ->path('restaurants')
            ->login(\App\Filament\Restaurant\Pages\Auth\Login::class)
            ->passwordReset()
            ->favicon($favicon)
            ->brandName($brandName)
            ->databaseNotifications()
            ->registration(\App\Filament\Restaurant\Pages\Auth\Register::class)
            // Enable Native Tenancy
            ->tenant(\App\Models\Restaurant::class, slugAttribute: 'slug')
            ->tenantRegistration(\App\Filament\Restaurant\Pages\Tenancy\RegisterRestaurant::class)
            ->tenantProfile(\App\Filament\Restaurant\Pages\Tenancy\EditRestaurantProfile::class)
            ->tenantMiddleware([
                \App\Http\Middleware\SyncSpatiePermissionsTeamId::class,
                \App\Http\Middleware\CheckActiveSubscription::class,
            ], isPersistent: true)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Restaurant/Resources'), for: 'App\\Filament\\Restaurant\\Resources')
            ->discoverPages(in: app_path('Filament/Restaurant/Pages'), for: 'App\\Filament\\Restaurant\\Pages')
            ->navigationItems([
                \Filament\Navigation\NavigationItem::make('Profil Restoran')
                    ->url(fn (): string => ($tenant = \Filament\Facades\Filament::getTenant()) ? route('filament.restaurant.tenant.profile', ['tenant' => $tenant->slug]) : '#')
                    ->icon('heroicon-o-home-modern')
                    ->group('PENGATURAN TOKO')
                    ->sort(0)
                    ->visible(fn (): bool => auth()->user() && (auth()->user()->hasRole(['super_admin', 'restaurant_owner']) || auth()->user()->can('view_restaurant'))),
            ])
            ->pages([
                \App\Filament\Restaurant\Pages\Dashboard::class,
                \App\Filament\Restaurant\Pages\MySubscription::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Restaurant/Widgets'), for: 'App\\Filament\\Restaurant\\Widgets')
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
            ])
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
            ])
            ->tenantMenuItems([
                'hq' => \Filament\Navigation\MenuItem::make()
                    ->label('HQ Analytics')
                    ->url('/hq')
                    ->icon('heroicon-o-building-office-2')
                    ->sort(5) // Antara Restaurant Settings (biasanya sort 1) dan Register New Branch
                    ->visible(fn (): bool => auth()->user()->hasRole('super_admin') || (auth()->user()->hasRole('restaurant_owner') && auth()->user()->hasFeature('Dashboard HQ'))),
                'register' => \Filament\Navigation\MenuItem::make()
                    ->label('Register New Branch')
                    ->visible(fn (): bool => auth()->user()->hasRole('super_admin') || auth()->user()->hasFeature('Multi-Restaurant Support')),
                'profile' => \Filament\Navigation\MenuItem::make()
                    ->label('Profil Saya')
                    ->icon('heroicon-o-user-circle')
                    ->url(fn (): string => \App\Filament\Restaurant\Pages\MyProfile::getUrl()),
            ])
            ->renderHook(
                'panels::topbar.start',
                fn (): string => \Illuminate\Support\Facades\Blade::render('@livewire(\'restaurant.subscription-warning-badge\')'),
            )
            ->renderHook(
                'panels::content.start',
                fn (): \Illuminate\Contracts\View\View => view('filament.restaurant.components.trial-banner'),
            )
            ->renderHook(
                'panels::body.start',
                fn (): string => \Illuminate\Support\Facades\Blade::render('@livewire(\'restaurant.global-notification-listener\')'),
            )
            ->renderHook(
                'panels::body.end',
                fn (): string => \Illuminate\Support\Facades\Blade::render('<x-priority-support-widget />'),
            );
    }
}
