<?php

namespace App\Filament\Restaurant\Widgets;

use App\Models\Ingredient;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RestaurantLowStockWidget extends BaseWidget
{
    public static function canView(): bool
    {
        return auth()->user()->hasFeature('Inventory Level 2')
            && auth()->user()->can('widget_RestaurantLowStockWidget');
    }

    protected function getStats(): array
    {
        $lowStockCount = Ingredient::whereColumn('stock', '<=', 'min_stock_alert')->count();

        if ($lowStockCount === 0) {
            return [];
        }

        return [
            Stat::make('Bahan Baku Menipis', $lowStockCount)
                ->description('Jumlah bahan baku yang perlu di-restock segera')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger')
                ->url(\App\Filament\Restaurant\Resources\IngredientResource::getUrl('index')),
        ];
    }
}
