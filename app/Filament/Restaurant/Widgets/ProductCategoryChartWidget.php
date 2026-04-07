<?php

namespace App\Filament\Restaurant\Widgets;

use App\Models\MenuCategory;
use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ProductCategoryChartWidget extends ChartWidget
{
    public static function canView(): bool
    {
        return auth()->user()->can('widget_ProductCategoryChartWidget');
    }

    protected static ?string $heading = 'Penjualan per Kategori (Revenue)';
    
    protected static ?int $sort = 6;
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $startDate = request('date_start', now()->startOfMonth()->toDateString());
        $endDate = request('date_end', now()->endOfMonth()->toDateString());
        
        $tenant = \Filament\Facades\Filament::getTenant();

        $data = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->join('menu_categories', 'menu_items.menu_category_id', '=', 'menu_categories.id')
            ->where('orders.restaurant_id', $tenant->id)
            ->where('orders.status', 'completed')
            ->whereBetween('orders.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->select('menu_categories.name', DB::raw('SUM(order_items.total_price) as total_revenue'))
            ->groupBy('menu_categories.id', 'menu_categories.name')
            ->orderByDesc('total_revenue')
            ->limit(8)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Total Pendapatan',
                    'data' => $data->pluck('total_revenue')->toArray(),
                    'backgroundColor' => [
                        '#6366f1', '#ec4899', '#f59e0b', '#10b981', '#3b82f6', '#8b5cf6', '#ef4444', '#64748b'
                    ],
                    'hoverOffset' => 4,
                ],
            ],
            'labels' => $data->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
