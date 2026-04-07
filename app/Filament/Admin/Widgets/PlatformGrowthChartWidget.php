<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Restaurant;
use App\Models\Subscription;
use App\Models\SubscriptionInvoice;
use Filament\Widgets\ChartWidget;

class PlatformGrowthChartWidget extends ChartWidget
{
    protected static ?string $heading = '📈 Pertumbuhan Platform (6 Bulan Terakhir)';
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $labels       = [];
        $restaurants  = [];
        $subscriptions = [];
        $revenues     = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);

            $labels[]        = $date->translatedFormat('M Y');
            $restaurants[]   = Restaurant::withoutGlobalScopes()
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $subscriptions[] = Subscription::where('status', 'active')
                ->whereYear('starts_at', $date->year)
                ->whereMonth('starts_at', $date->month)
                ->count();
            $revenues[]      = (float) SubscriptionInvoice::where('status', 'paid')
                ->whereYear('paid_at', $date->year)
                ->whereMonth('paid_at', $date->month)
                ->sum('amount');
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Restoran Baru',
                    'data'            => $restaurants,
                    'backgroundColor' => 'rgba(99, 102, 241, 0.15)',
                    'borderColor'     => 'rgba(99, 102, 241, 1)',
                    'borderWidth'     => 2,
                    'tension'         => 0.4,
                    'fill'            => true,
                    'pointBackgroundColor' => 'rgba(99, 102, 241, 1)',
                    'pointRadius'     => 4,
                ],
                [
                    'label'           => 'Subscription Aktif Baru',
                    'data'            => $subscriptions,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.10)',
                    'borderColor'     => 'rgba(16, 185, 129, 1)',
                    'borderWidth'     => 2,
                    'tension'         => 0.4,
                    'fill'            => true,
                    'pointBackgroundColor' => 'rgba(16, 185, 129, 1)',
                    'pointRadius'     => 4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['position' => 'top'],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => ['stepSize' => 1],
                    'grid' => ['color' => 'rgba(255,255,255,0.05)'],
                ],
                'x' => [
                    'grid' => ['color' => 'rgba(255,255,255,0.05)'],
                ],
            ],
        ];
    }
}
