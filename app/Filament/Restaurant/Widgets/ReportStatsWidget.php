<?php

namespace App\Filament\Restaurant\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Filament\Restaurant\Resources\FinanceResource;

class ReportStatsWidget extends BaseWidget
{
    public static function canView(): bool
    {
        return auth()->user()->can('widget_ReportStatsWidget');
    }

    protected function getStats(): array
    {
        $tenant = \Filament\Facades\Filament::getTenant();
        
        // Dapatkan filter dari request query string
        $start = request('date_start', now()->startOfMonth()->format('Y-m-d'));
        $end = request('date_end', now()->endOfMonth()->format('Y-m-d'));

        // Query dasar
        $query = \App\Models\Order::where('restaurant_id', $tenant->id)
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $end);

        // Revenue (Truly Net: Exclude Tax, Fees, and Gateway Fees)
        $revenueStat = (clone $query)->whereIn('status', ['completed', 'ready_to_serve', 'cooking', 'confirmed'])
            ->whereIn('payment_status', ['paid', 'partial'])
            ->selectRaw('
                SUM(total_amount - tax_amount - additional_fees_amount - refunded_amount) as earnings,
                SUM(CASE WHEN payment_method != "cash" THEN (total_amount - refunded_amount) * 0.01 ELSE 0 END) as est_gateway_fees
            ')
            ->first();

        $revenue = ($revenueStat->earnings ?? 0) - ($revenueStat->est_gateway_fees ?? 0);

        $totalRefunded = (clone $query)->sum('refunded_amount');

        // Discount Calculation
        // 1. Item level discounts (Hanya untuk item yang tidak di-refund)
        $itemDiscounts = \App\Models\OrderItem::whereHas('order', function($q) use ($tenant, $start, $end) {
            $q->where('restaurant_id', $tenant->id)
              ->whereIn('status', ['completed', 'ready_to_serve', 'cooking', 'confirmed'])
              ->whereDate('created_at', '>=', $start)
              ->whereDate('created_at', '<=', $end);
        })->where('is_refunded', false)
          ->selectRaw('SUM((COALESCE(original_unit_price, unit_price) - unit_price) * quantity) as total')
          ->value('total') ?? 0;

        // 2. Order level discounts (Voucher + Points)
        $orderDiscounts = (clone $query)->whereIn('status', ['completed', 'ready_to_serve', 'cooking', 'confirmed'])
            ->whereIn('payment_status', ['paid', 'partial'])
            ->selectRaw('SUM(COALESCE(voucher_discount_amount, 0) + COALESCE(points_discount_amount, 0)) as total')
            ->value('total') ?? 0;
            
        $totalDiscount = $itemDiscounts + $orderDiscounts;

        $grossRevenue = $revenue + $totalDiscount + $totalRefunded;

        // Total Orders (Semua order masuk kecuali cancelled murni tanpa refund?)
        $ordersCount = (clone $query)->where('status', '!=', 'cancelled')->count();

        // Avg Order Value (Net Revenue / Completed/Paid Orders)
        $paidCount = (clone $query)->whereIn('payment_status', ['paid', 'partial'])->count();
        $avgOrder = $paidCount > 0 ? $revenue / $paidCount : 0;

        // Total Members (New members in this period)
        $newMembers = \App\Models\Member::where('restaurant_id', $tenant->id)
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $end)
            ->count();

        return [
            Stat::make('Total Revenue (Net)', 'Rp ' . number_format($revenue, 0, ',', '.'))
                ->description("Klik untuk rincian (Eks. Pajak & Gateway)")
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->url(FinanceResource::getUrl('index', [
                    'tableFilters[created_at][from]' => $start,
                    'tableFilters[created_at][until]' => $end,
                    'tableFilters[type][value]' => 'credit',
                ])),

            Stat::make('Total Refund', 'Rp ' . number_format($totalRefunded, 0, ',', '.'))
                ->description("Dana yang dikembalikan")
                ->descriptionIcon('heroicon-m-arrow-uturn-left')
                ->color('danger')
                ->url(FinanceResource::getUrl('index', [
                    'tableFilters[created_at][from]' => $start,
                    'tableFilters[created_at][until]' => $end,
                    'tableFilters[type][value]' => 'debit',
                ])),

            Stat::make('Gross Revenue', 'Rp ' . number_format($grossRevenue, 0, ',', '.'))
                ->description("Pendapatan kotor")
                ->descriptionIcon('heroicon-m-presentation-chart-line')
                ->color('info'),

            Stat::make('Total Potongan Diskon', 'Rp ' . number_format($totalDiscount, 0, ',', '.'))
                ->description("Total promo item & voucher")
                ->descriptionIcon('heroicon-m-receipt-percent')
                ->color('warning'),

            Stat::make('Rata-rata Transaksi', 'Rp ' . number_format($avgOrder, 0, ',', '.'))
                ->description('Berdasarkan Net Revenue')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('primary'),

            Stat::make('New Members/Orders', $newMembers . ' / ' . $ordersCount)
                ->description('Member baru & Total pesanan')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
        ];
    }
}
