<?php

namespace App\Filament\Restaurant\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Order;
use Carbon\Carbon;

class SalesChart extends ChartWidget
{
    public static function canView(): bool
    {
        return auth()->user()->can('widget_SalesChart');
    }

    protected static ?string $heading = 'Sales Last 30 Days';
    protected static ?string $pollingInterval = '30s';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        // Get data using Collection grouping to be timezone safe
        $data = Order::where('payment_status', 'paid')
            ->where('created_at', '>=', now()->subDays(30))
            ->get()
            ->groupBy(function ($order) {
                return $order->created_at->format('Y-m-d');
            })
            ->map(function ($dayOrders) {
                return $dayOrders->sum('total_amount');
            });
            
        // Fill missing dates with 0
        $labels = [];
        $values = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('d M');
            $values[] = $data[$date] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue (Rp)',
                    'data' => $values,
                    'borderColor' => '#10B981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
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
