<?php

namespace App\Filament\Restaurant\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Order;
use App\Filament\Restaurant\Resources\FinanceResource;

class StatsOverview extends BaseWidget
{
    public static function canView(): bool
    {
        return auth()->user()->can('widget_StatsOverview');
    }

    // Polling interval to refresh stats
    protected static ?string $pollingInterval = '15s';
    
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $tenantId = \Filament\Facades\Filament::getTenant()->id;
        
        $stats = \Illuminate\Support\Facades\Cache::remember("stats_overview_res_{$tenantId}", 60, function() {
            // Revenue (All Time) - Truly Net
            $totalRevStat = Order::whereIn('payment_status', ['paid', 'partial'])
                ->selectRaw('
                    SUM(total_amount - tax_amount - additional_fees_amount - refunded_amount) as earnings,
                    SUM(CASE WHEN payment_method != "cash" THEN (total_amount - refunded_amount) * 0.01 ELSE 0 END) as est_fees
                ')
                ->first();
            $totalRevenue = ($totalRevStat->earnings ?? 0) - ($totalRevStat->est_fees ?? 0);
            
            // Sales Today - Truly Net
            $salesTodayStat = Order::whereIn('payment_status', ['paid', 'partial'])
                ->whereDate('created_at', now()->today())
                 ->selectRaw('
                    SUM(total_amount - tax_amount - additional_fees_amount - refunded_amount) as earnings,
                    SUM(CASE WHEN payment_method != "cash" THEN (total_amount - refunded_amount) * 0.01 ELSE 0 END) as est_fees
                ')
                ->first();
            $salesToday = ($salesTodayStat->earnings ?? 0) - ($salesTodayStat->est_fees ?? 0);

            // Order Counts
            $totalOrders = Order::count();
            $paidOrdersCount = Order::whereIn('payment_status', ['paid', 'partial'])->count();
            $pendingOrders = Order::where('status', 'pending')->count();
            
            return [
                'salesToday' => $salesToday,
                'totalRevenue' => $totalRevenue,
                'pendingOrders' => $pendingOrders,
            ];
        });

        $salesToday = $stats['salesToday'];
        $totalRevenue = $stats['totalRevenue'];
        $pendingOrders = $stats['pendingOrders'];

        return [
            Stat::make('Penjualan Hari Ini', 'Rp ' . number_format($salesToday, 0, ',', '.'))
                ->description('Klik rincian hari ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->url(FinanceResource::getUrl('index', [
                    'tableFilters[created_at][from]' => now()->today()->format('Y-m-d'),
                    'tableFilters[created_at][until]' => now()->today()->format('Y-m-d'),
                ])),

            Stat::make('Total Pendapatan', 'Rp ' . number_format($totalRevenue, 0, ',', '.'))
                ->description('Buku Kas (Eks. Pajak & Gateway)')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary')
                ->url(FinanceResource::getUrl('index', [
                    'tableFilters[type][value]' => 'credit',
                ])),
                
            Stat::make('Pesanan Pending', $pendingOrders)
                ->description('Perlu diproses')
                ->descriptionIcon('heroicon-m-clock')
                ->chart([$pendingOrders, $pendingOrders > 0 ? $pendingOrders-1 : 0])
                ->color($pendingOrders > 0 ? 'danger' : 'success'),
        ];
    }
}
