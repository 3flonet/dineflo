<?php

namespace App\Filament\Restaurant\Widgets;

use App\Models\Expense;
use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;
use Filament\Facades\Filament;

class IncomeExpenseChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Analisis Arus Kas & Kerugian (Income, Expense, Wastage)';
    
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()->hasExpenseManagement()
            && auth()->user()->can('widget_IncomeExpenseChartWidget');
    }

    protected function getData(): array
    {
        $tenantId = Filament::getTenant()->id;
        
        // Months for the last 12 months
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $months[] = now()->subMonths($i);
        }

        $startDate = now()->subMonths(11)->startOfMonth();

        // 1. Income Data (Orders)
        $incomeData = Order::where('restaurant_id', $tenantId)
            ->whereIn('payment_status', ['paid', 'partial'])
            ->where('created_at', '>=', $startDate)
            ->selectRaw('SUM(total_amount - refunded_amount) as total, MONTH(created_at) as month, YEAR(created_at) as year')
            ->groupBy('year', 'month')
            ->get()
            ->keyBy(fn ($item) => $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT));

        // 2. Expense Data (Operational Expenses)
        $expenseData = Expense::where('restaurant_id', $tenantId)
            ->where('date', '>=', $startDate)
            ->selectRaw('SUM(amount) as total, MONTH(date) as month, YEAR(date) as year')
            ->groupBy('year', 'month')
            ->get()
            ->keyBy(fn ($item) => $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT));

        // 3. Wastage Data (Ingredient Loss)
        $wastageMovements = \App\Models\IngredientStockMovement::where('restaurant_id', $tenantId)
            ->where('type', 'out')
            ->whereIn('reason', ['waste', 'breakage', 'expired'])
            ->where('created_at', '>=', $startDate)
            ->with('ingredient')
            ->get();

        $wastageByMonth = [];
        foreach ($wastageMovements as $movement) {
            $key = $movement->created_at->format('Y-m');
            $value = $movement->quantity * ($movement->ingredient?->cost_per_unit ?? 0);
            $wastageByMonth[$key] = ($wastageByMonth[$key] ?? 0) + $value;
        }

        $labels = [];
        $incomeValues = [];
        $expenseValues = [];
        $wastageValues = [];

        foreach ($months as $month) {
            $key = $month->format('Y-m');
            $labels[] = $month->translatedFormat('M Y');
            $incomeValues[] = (float) ($incomeData[$key]->total ?? 0);
            $expenseValues[] = (float) ($expenseData[$key]->total ?? 0);
            $wastageValues[] = (float) ($wastageByMonth[$key] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan (Rp)',
                    'data' => $incomeValues,
                    'borderColor' => '#10b981', // Green
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => 'start',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Pengeluaran (Rp)',
                    'data' => $expenseValues,
                    'borderColor' => '#ef4444', // Red
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill' => 'start',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Kerugian/Wastage (Rp)',
                    'data' => $wastageValues,
                    'borderColor' => '#f59e0b', // Amber/Orange
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'fill' => 'start',
                    'tension' => 0.4,
                    'borderDash' => [5, 5], // Dotted line for wastage
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
