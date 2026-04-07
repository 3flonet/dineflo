<?php

namespace App\Filament\Restaurant\Widgets;

use Filament\Widgets\ChartWidget;

class ReportChartWidget extends ChartWidget
{
    public static function canView(): bool
    {
        return auth()->user()->can('widget_ReportChartWidget');
    }

    protected static ?string $heading = 'Revenue Harian';
    
    protected static ?int $sort = 7;
    
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $tenant = \Filament\Facades\Filament::getTenant();
        
        $start = request('date_start', now()->startOfMonth()->format('Y-m-d'));
        $end = request('date_end', now()->endOfMonth()->format('Y-m-d'));

        /* 
         * Query Revenue per Hari
         * Note: Ini basic group by date. Jika hari kosong, data akan skip.
         * Idealnya generate date range array dan merge.
         */
        $data = \App\Models\Order::where('restaurant_id', $tenant->id)
            ->where('status', 'completed')
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $end)
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();

        // Initialize all dates with 0
        $allDates = [];
        $period = \Carbon\CarbonPeriod::create($start, $end);
        foreach ($period as $date) {
            $allDates[$date->format('Y-m-d')] = 0;
        }

        // Merge actual data
        foreach ($data as $date => $total) {
            if (isset($allDates[$date])) {
                $allDates[$date] = (float) $total;
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue (Rp)',
                    'data' => array_values($allDates),
                    'borderColor' => '#10B981', // Emerald 500
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => 'start',
                    'tension' => 0.4,
                ],
            ],
            'labels' => array_map(function($date) {
                return \Carbon\Carbon::parse($date)->format('d M');
            }, array_keys($allDates)),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
