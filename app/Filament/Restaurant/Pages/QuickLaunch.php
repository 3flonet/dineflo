<?php

namespace App\Filament\Restaurant\Pages;

use Filament\Pages\Page;
use Filament\Facades\Filament;

class QuickLaunch extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-rocket-launch';
    protected static ?string $navigationLabel = 'Quick Launch';
    protected static ?string $title           = 'Quick Launch';
    protected static ?string $slug            = 'quick-launch';
    protected static ?string $navigationGroup = null; 
    protected static ?int    $navigationSort  = -1;   

    protected static string $view = 'filament.restaurant.pages.quick-launch';

    public static function canAccess(): bool
    {
        return auth()->user()->can('page_QuickLaunch') || auth()->user()->hasRole('restaurant_owner');
    }

    /**
     * Membangun daftar "launcher cards" yang akan ditampilkan ke view,
     * sudah difilter berdasarkan permission user yang sedang login.
     */
    public function getLauncherCards(): array
    {
        $user       = auth()->user();
        $restaurant = Filament::getTenant();
        $slug       = $restaurant?->slug;

        $cards = [];

        // ── Kitchen Display ─────────────────────────────────────────────────
        $canKitchen = ($user->can('page_KitchenDisplay') || $user->hasRole('restaurant_owner'))
            && ($user->hasRole('super_admin') || $user->hasFeature('Kitchen Display System'));

        if ($canKitchen) {
            $cards[] = [
                'id'          => 'kitchen',
                'title'       => 'Kitchen Display',
                'description' => 'Monitor antrian pesanan secara real-time di layar dapur. Update status masak & siap saji.',
                'url'         => route('restaurant.kitchen', ['restaurant' => $slug]),
                'icon'        => 'heroicon-o-fire',
                'color'       => 'orange',
                'badge'       => 'KDS',
            ];
        }

        // ── Service / Staff Panel ────────────────────────────────────────────
        $canService = $user->can('view_any_order') || $user->hasRole('restaurant_owner');

        if ($canService) {
            $cards[] = [
                'id'          => 'service',
                'title'       => 'Service Panel',
                'description' => 'Panel pelayan untuk memantau & mengelola pesanan aktif, status meja, dan panggilan tamu.',
                'url'         => route('restaurant.service', ['restaurant' => $slug]),
                'icon'        => 'heroicon-o-bell-alert',
                'color'       => 'blue',
                'badge'       => 'Waiter',
            ];
        }

        // ── POS Kasir ────────────────────────────────────────────────────────
        $canPos = ($user->can('page_Pos') || $user->hasRole('restaurant_owner'))
            && ($user->hasRole('super_admin') || $user->hasFeature('POS System'));

        if ($canPos) {
            $cards[] = [
                'id'          => 'pos',
                'title'       => 'POS Kasir',
                'description' => 'Kasir Point-of-Sale untuk buat & proses pesanan langsung dengan pembayaran tunai atau QRIS.',
                'url'         => '/restaurants/' . $slug . '/pos',
                'icon'        => 'heroicon-o-computer-desktop',
                'color'       => 'violet',
                'badge'       => 'POS',
            ];
        }

        // ── Kiosk Mode ───────────────────────────────────────────────────────
        $canKiosk = ($user->can('view_any_order') || $user->hasRole('restaurant_owner'))
            && ($user->hasRole('super_admin') || $user->hasFeature('Kiosk Mode'));

        if ($canKiosk) {
            $cards[] = [
                'id'          => 'kiosk',
                'title'       => 'Kiosk Mode',
                'description' => 'Self-ordering kiosk untuk tamu memesan sendiri langsung di perangkat layar sentuh restoran.',
                'url'         => route('restaurant.kiosk', ['restaurant' => $slug]),
                'icon'        => 'heroicon-o-device-tablet',
                'color'       => 'emerald',
                'badge'       => 'Kiosk',
            ];
        }

        // ── Queue Display ──────────────────────────────────────────────────
        $canQueue = ($user->can('view_any_order') || $user->hasRole('restaurant_owner'))
            && ($user->hasRole('super_admin') || $user->hasFeature('Queue Management System'));

        if ($canQueue) {
            $cards[] = [
                'id'          => 'queue-display',
                'title'       => 'Display Antrean',
                'description' => 'Layar TV antrean untuk pelanggan. Menampilkan panggilan nomor antrean dan status meja secara real-time.',
                'url'         => route('restaurant.queue_display', ['restaurant' => $slug]),
                'icon'        => 'heroicon-o-tv',
                'color'       => 'orange',
                'badge'       => 'TV',
            ];
        }

        // ── Mini Website / Profile ──────────────────────────────────────────
        $canProfile = true;

        if ($canProfile) {
            $cards[] = [
                'id'          => 'profile',
                'title'       => 'Mini Website',
                'description' => 'Lihat profil publik restoran Anda. Bagikan link ini ke pelanggan untuk info jam buka, menu unggulan, dan testimoni.',
                'url'         => route('frontend.restaurants.show', ['restaurant' => $slug]),
                'icon'        => 'heroicon-o-globe-alt',
                'color'       => 'sky',
                'badge'       => 'Web',
            ];
        }

        return $cards;
    }
}

