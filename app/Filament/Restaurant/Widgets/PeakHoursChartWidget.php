<?php

namespace App\Filament\Restaurant\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PeakHoursChartWidget extends ChartWidget
{
    public static function canView(): bool
    {
        return auth()->user()->can('widget_PeakHoursChartWidget');
    }

    protected static ?string $heading = 'Analisis Jam Sibuk (Peak Hours)';
    
    protected static ?int $sort = 5;
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?string $maxHeight = '300px';

    public ?array $filters = null;

    protected function getData(): array
    {
        $startDate = request('date_start', now()->startOfMonth()->toDateString());
        $endDate = request('date_end', now()->endOfMonth()->toDateString());

        $tenantId = \Filament\Facades\Filament::getTenant()->id;

        // Get hourly order count with timezone adjustment (+07:00 for WIB)
        $hourlyData = Order::query()
            ->where('restaurant_id', $tenantId)
            ->where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->selectRaw("HOUR(CONVERT_TZ(created_at, '+00:00', '+07:00')) as hour, COUNT(*) as count")
            ->groupByRaw('hour')
            ->orderBy('hour')
            ->get()
            ->pluck('count', 'hour')
            ->toArray();

        // Fill all 24 hours with 0 if no data
        $data = [];
        $labels = [];
        for ($i = 0; $i < 24; $i++) {
            $labels[] = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
            $data[] = $hourlyData[$i] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pesanan',
                    'data' => $data,
                    'backgroundColor' => '#f59e0b',
                    'borderColor' => '#d97706',
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
