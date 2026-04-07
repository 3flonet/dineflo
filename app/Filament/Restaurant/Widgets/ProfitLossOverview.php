<?php

namespace App\Filament\Restaurant\Widgets;

use App\Models\Expense;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Facades\Filament;

class ProfitLossOverview extends BaseWidget
{
    protected static ?int $sort = 2; // Tampil setelah StatsOverview
    
    protected int | string | array $columnSpan = 'full'; // Full width
    
    public static function canView(): bool
    {
        // Restoran harus punya fitur expense management (cek subscription)
        // DAN user harus punya permission widget ini
        return auth()->user()->hasExpenseManagement()
            && auth()->user()->can('widget_ProfitLossOverview');
    }

    protected function getStats(): array
    {
        $tenant = Filament::getTenant();
        $cacheKey = "profit_loss_res_{$tenant->id}_" . now()->format('Y_m');

        $stats = \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, function() use ($tenant) {
            // Calculate current month revenue (Net - Refunded)
            $currentMonthRevenue = Order::where('restaurant_id', $tenant->id)
                ->whereIn('payment_status', ['paid', 'partial'])
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->selectRaw('SUM(total_amount - refunded_amount) as net_revenue')
                ->value('net_revenue') ?? 0;
            
            // Calculate previous month revenue for comparison (Net - Refunded)
            $previousMonthRevenue = Order::where('restaurant_id', $tenant->id)
                ->whereIn('payment_status', ['paid', 'partial'])
                ->whereMonth('created_at', now()->subMonth()->month)
                ->whereYear('created_at', now()->subMonth()->year)
                ->selectRaw('SUM(total_amount - refunded_amount) as net_revenue')
                ->value('net_revenue') ?? 0;
            
            // Calculate current month expenses
            $currentMonthExpenses = Expense::where('restaurant_id', $tenant->id)
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->sum('amount');
            
            // Calculate previous month expenses for comparison
            $previousMonthExpenses = Expense::where('restaurant_id', $tenant->id)
                ->whereMonth('date', now()->subMonth()->month)
                ->whereYear('date', now()->subMonth()->year)
                ->sum('amount');
            
            // Calculate net profit
            $netProfit = $currentMonthRevenue - $currentMonthExpenses;
            $previousNetProfit = $previousMonthRevenue - $previousMonthExpenses;
            
            // Calculate percentage changes
            $revenueChange = $previousMonthRevenue > 0 
                ? (($currentMonthRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100 
                : 0;
            
            $expenseChange = $previousMonthExpenses > 0 
                ? (($currentMonthExpenses - $previousMonthExpenses) / $previousMonthExpenses) * 100 
                : 0;
            
            $profitChange = $previousNetProfit > 0 
                ? (($netProfit - $previousNetProfit) / $previousNetProfit) * 100 
                : 0;

            return [
                'currentMonthRevenue' => $currentMonthRevenue,
                'currentMonthExpenses' => $currentMonthExpenses,
                'netProfit' => $netProfit,
                'revenueChange' => $revenueChange,
                'expenseChange' => $expenseChange,
                'profitChange' => $profitChange,
            ];
        });

        $currentMonthRevenue = $stats['currentMonthRevenue'];
        $currentMonthExpenses = $stats['currentMonthExpenses'];
        $netProfit = $stats['netProfit'];
        $revenueChange = $stats['revenueChange'];
        $expenseChange = $stats['expenseChange'];
        $profitChange = $stats['profitChange'];
        
        return [
            Stat::make('Total Pendapatan (Bulan Ini)', 'Rp ' . number_format($currentMonthRevenue, 0, ',', '.'))
                ->description($revenueChange >= 0 ? '+' . number_format($revenueChange, 1) . '% dari bulan lalu' : number_format($revenueChange, 1) . '% dari bulan lalu')
                ->descriptionIcon($revenueChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueChange >= 0 ? 'success' : 'danger')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]),
            
            Stat::make('Total Pengeluaran (Bulan Ini)', 'Rp ' . number_format($currentMonthExpenses, 0, ',', '.'))
                ->description($expenseChange >= 0 ? '+' . number_format($expenseChange, 1) . '% dari bulan lalu' : number_format($expenseChange, 1) . '% dari bulan lalu')
                ->descriptionIcon($expenseChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($expenseChange >= 0 ? 'danger' : 'success')
                ->chart([3, 2, 4, 3, 5, 4, 6, 5]),
            
            Stat::make('Laba Bersih / Net Profit (Bulan Ini)', 'Rp ' . number_format($netProfit, 0, ',', '.'))
                ->description($profitChange >= 0 ? '+' . number_format($profitChange, 1) . '% dari bulan lalu' : number_format($profitChange, 1) . '% dari bulan lalu')
                ->descriptionIcon($profitChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($netProfit >= 0 ? 'success' : 'danger')
                ->chart([4, 1, 0, 2, 3, 1, 2, 0]),
        ];
    }
}
