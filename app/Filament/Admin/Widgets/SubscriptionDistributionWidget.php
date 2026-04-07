<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Filament\Widgets\ChartWidget;

class SubscriptionDistributionWidget extends ChartWidget
{
    protected static ?string $heading = '🍩 Distribusi Paket Berlangganan';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $plans = SubscriptionPlan::withoutGlobalScopes()
            ->where('is_active', true)
            ->get();

        $labels     = [];
        $data       = [];
        $colors     = [
            'rgba(99, 102, 241, 0.85)',   // Indigo
            'rgba(16, 185, 129, 0.85)',   // Emerald
            'rgba(245, 158, 11, 0.85)',   // Amber
            'rgba(239, 68, 68, 0.85)',    // Red
            'rgba(59, 130, 246, 0.85)',   // Blue
            'rgba(168, 85, 247, 0.85)',   // Purple
        ];
        $borders = [
            'rgba(99, 102, 241, 1)',
            'rgba(16, 185, 129, 1)',
            'rgba(245, 158, 11, 1)',
            'rgba(239, 68, 68, 1)',
            'rgba(59, 130, 246, 1)',
            'rgba(168, 85, 247, 1)',
        ];

        foreach ($plans as $index => $plan) {
            $count = Subscription::where('subscription_plan_id', $plan->id)
                ->where('status', 'active')
                ->where('expires_at', '>', now())
                ->count();

            $labels[] = $plan->name . ' (' . $count . ')';
            $data[]   = $count;
        }

        // Add "Expired / No Plan" count
        $noActive = Subscription::where(function ($q) {
            $q->where('status', '!=', 'active')
              ->orWhere('expires_at', '<=', now());
        })->count();

        if ($noActive > 0) {
            $labels[] = 'Expired / Tidak Aktif (' . $noActive . ')';
            $data[]   = $noActive;
        }

        return [
            'datasets' => [
                [
                    'data'            => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                    'borderColor'     => array_slice($borders, 0, count($data)),
                    'borderWidth'     => 2,
                    'hoverOffset'     => 6,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'right',
                    'labels'   => ['padding' => 16],
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => "function(ctx) {
                            return ' ' + ctx.label + ': ' + ctx.parsed + ' pengguna';
                        }",
                    ],
                ],
            ],
            'cutout' => '65%',
        ];
    }
}
