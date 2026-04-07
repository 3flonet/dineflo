<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Restaurant;
use App\Models\Subscription;
use App\Models\SubscriptionInvoice;
use App\Models\User;
use App\Models\WithdrawRequest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // ── Restaurants ──────────────────────────────────────────
        $totalRestaurants   = Restaurant::withoutGlobalScopes()->count();
        $activeRestaurants  = Restaurant::withoutGlobalScopes()->where('is_active', true)->count();
        $newRestaurantsMonth = Restaurant::withoutGlobalScopes()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // ── Users ────────────────────────────────────────────────
        $totalUsers   = User::count();
        $newUsersMonth = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // ── Subscriptions ────────────────────────────────────────
        $activeSubscriptions = Subscription::where('status', 'active')
            ->where('expires_at', '>', now())
            ->count();
        $expiringSubscriptions = Subscription::where('status', 'active')
            ->whereBetween('expires_at', [now(), now()->addDays(7)])
            ->count();

        // ── Revenue (Subscription Invoices) ──────────────────────
        $revenueThisMonth = SubscriptionInvoice::where('status', 'paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');
        $revenueLastMonth = SubscriptionInvoice::where('status', 'paid')
            ->whereMonth('paid_at', now()->subMonth()->month)
            ->whereYear('paid_at', now()->subMonth()->year)
            ->sum('amount');
        $revenueGrowth = $revenueLastMonth > 0
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
            : 0;

        // ── Withdraw Requests ────────────────────────────────────
        $pendingWithdraws  = WithdrawRequest::where('status', 'pending')->count();
        $approvedWithdraws = WithdrawRequest::where('status', 'approved')->count();
        $pendingTotal      = WithdrawRequest::whereIn('status', ['pending', 'approved'])->sum('net_transfer_amount');

        return [
            Stat::make('Restoran Terdaftar', number_format($totalRestaurants))
                ->description($activeRestaurants . ' aktif · ＋' . $newRestaurantsMonth . ' bulan ini')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->chart([
                    max(0, $totalRestaurants - 5),
                    max(0, $totalRestaurants - 3),
                    max(0, $totalRestaurants - 2),
                    max(0, $totalRestaurants - 1),
                    $totalRestaurants,
                ])
                ->color('indigo'),

            Stat::make('Total Pengguna', number_format($totalUsers))
                ->description('＋' . $newUsersMonth . ' user baru bulan ini')
                ->descriptionIcon('heroicon-m-users')
                ->color('blue'),

            Stat::make('Subscription Aktif', number_format($activeSubscriptions))
                ->description(
                    $expiringSubscriptions > 0
                        ? $expiringSubscriptions . ' akan berakhir dalam 7 hari!'
                        : 'Semua subscription sehat ✓'
                )
                ->descriptionIcon('heroicon-m-check-badge')
                ->color($expiringSubscriptions > 0 ? 'warning' : 'success'),

            Stat::make('Revenue Subscription', 'Rp ' . number_format($revenueThisMonth, 0, ',', '.'))
                ->description(
                    ($revenueGrowth >= 0 ? '▲ ＋' : '▼ ') . abs($revenueGrowth) . '% vs bulan lalu'
                )
                ->descriptionIcon($revenueGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueGrowth >= 0 ? 'success' : 'warning'),

            Stat::make('Withdraw Pending', $pendingWithdraws . ' permintaan')
                ->description(
                    'Disetujui belum transfer: ' . $approvedWithdraws . ' req'
                )
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($pendingWithdraws > 0 ? 'danger' : 'success'),

            Stat::make('Total Tagihan Withdraw', 'Rp ' . number_format($pendingTotal, 0, ',', '.'))
                ->description('Pending + Approved yang belum ditransfer')
                ->descriptionIcon('heroicon-m-arrow-up-tray')
                ->color('warning'),
        ];
    }
}
