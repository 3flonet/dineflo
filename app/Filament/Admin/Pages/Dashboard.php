<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets\AdminStatsOverviewWidget;
use App\Filament\Admin\Widgets\PlatformGrowthChartWidget;
use App\Filament\Admin\Widgets\SubscriptionDistributionWidget;
use App\Filament\Admin\Widgets\PendingWithdrawTableWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $routePath = '/';
    protected static string $view = 'filament.admin.pages.dashboard';

    public function getWidgets(): array
    {
        return [
            AdminStatsOverviewWidget::class,
            PlatformGrowthChartWidget::class,
            SubscriptionDistributionWidget::class,
            PendingWithdrawTableWidget::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return 2;
    }
}
