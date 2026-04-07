<?php

namespace App\Filament\Restaurant\Widgets;

use Filament\Widgets\ChartWidget;

class ExpenseChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Pengeluaran Bulanan';
    
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()->hasExpenseManagement()
            && auth()->user()->can('widget_ExpenseChart');
    }

    protected function getData(): array
    {
        $tenant = \Filament\Facades\Filament::getTenant();
        
        $data = \App\Models\Expense::where('restaurant_id', $tenant->id)
            ->where('date', '>=', now()->subYear())
            ->selectRaw('SUM(amount) as total, MONTH(date) as month, YEAR(date) as year')
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $labels = [];
        $values = [];

        foreach ($data as $item) {
            $labels[] = \Carbon\Carbon::create()->month($item->month)->translatedFormat('M Y');
            $values[] = (float) $item->total;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Pengeluaran (Rp)',
                    'data' => $values,
                    'backgroundColor' => '#f87171',
                    'borderColor' => '#ef4444',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
