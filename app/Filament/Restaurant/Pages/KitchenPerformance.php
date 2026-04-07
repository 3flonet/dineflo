<?php

namespace App\Filament\Restaurant\Pages;

use Filament\Pages\Page;
use App\Models\Order;
use App\Filament\Restaurant\Resources\OrderResource;
use Filament\Notifications\Notification;

class KitchenPerformance extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-fire';

    protected static string $view = 'filament.restaurant.resources.order-resource.pages.kitchen-analytics';

    protected static ?string $title = 'Analisis Performa Dapur';

    protected static ?string $navigationLabel = 'Performa Dapur';

    protected static ?string $navigationGroup = 'OPERASIONAL';

    protected static ?int $navigationSort = 4;

    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()->hasFeature('Advanced Kitchen Analytics') && 
               (auth()->user()->can('page_KitchenPerformance') || auth()->user()->hasRole('restaurant_owner') || auth()->user()->hasRole('super_admin'));
    }

    public function mount(): void
    {
        if (! auth()->user()->hasFeature('Advanced Kitchen Analytics')) {
            Notification::make()
                ->title('Akses Ditolak')
                ->body('Fitur ini eksklusif untuk paket Empire.')
                ->danger()
                ->send();
            
            $this->redirect(OrderResource::getUrl('index'));
        }
    }

    protected function getViewData(): array
    {
        $tenantId = \Filament\Facades\Filament::getTenant()->id;

        // Base query - jangan dimodifikasi langsung, gunakan clone
        $baseQuery = Order::where('restaurant_id', $tenantId)
            ->whereNotNull('cooking_started_at')
            ->whereNotNull('cooking_finished_at');

        // 1. Rata-rata Prep Time (Semua)
        $avgPrepTime = (clone $baseQuery)->getQuery() // Drop down ke base query builder untuk menghindari polusi Eloquent
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, cooking_started_at, cooking_finished_at)) as avg_time')
            ->value('avg_time') ?? 0;

        // 2. Data Grafik: Prep Time per Jam
        $hourlyData = (clone $baseQuery)->getQuery() // Drop down ke base query builder
            ->selectRaw('HOUR(cooking_started_at) as hour, AVG(TIMESTAMPDIFF(MINUTE, cooking_started_at, cooking_finished_at)) as avg_time')
            ->groupByRaw('HOUR(cooking_started_at)')
            ->orderByRaw('HOUR(cooking_started_at)')
            ->get()
            ->pluck('avg_time', 'hour')
            ->all();

        // 3. Order Terlama
        $extremeOrders = (clone $baseQuery)
            ->select('*')
            ->selectRaw('TIMESTAMPDIFF(MINUTE, cooking_started_at, cooking_finished_at) as duration')
            ->with(['table'])
            ->orderByRaw('TIMESTAMPDIFF(MINUTE, cooking_started_at, cooking_finished_at) DESC')
            ->limit(10)
            ->get();

        // 4. Analisis Efisiensi per Menu
        $menuStats = \App\Models\OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
            ->where('orders.restaurant_id', $tenantId)
            ->whereNotNull('orders.cooking_started_at')
            ->whereNotNull('orders.cooking_finished_at')
            ->select('menu_items.name')
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, orders.cooking_started_at, orders.cooking_finished_at)) as avg_prep_time')
            ->selectRaw('COUNT(*) as total_sold')
            ->groupBy('order_items.menu_item_id', 'menu_items.name')
            ->orderByDesc('avg_prep_time')
            ->get();

        return [
            'avgPrepTime' => round($avgPrepTime, 1),
            'hourlyData' => $hourlyData,
            'extremeOrders' => $extremeOrders,
            'menuStats' => $menuStats,
        ];
    }
}
