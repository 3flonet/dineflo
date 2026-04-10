<?php

namespace App\Filament\Restaurant\Widgets;

use App\Models\OrderItem;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;
use App\Models\MenuItem;

class TopSellingItemsWidget extends BaseWidget
{
    public static function canView(): bool
    {
        return auth()->user()->can('widget_TopSellingItemsWidget');
    }

    protected static ?string $heading = 'Menu Paling Laku (Top Selling)';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 1;

    public ?array $filters = null;

    public function table(Table $table): Table
    {
        $startDate = request('date_start', now()->startOfMonth()->toDateString());
        $endDate = request('date_end', now()->endOfMonth()->toDateString());

        $tenantId = \Filament\Facades\Filament::getTenant()->id;

        return $table
            ->query(
                MenuItem::query()
                    ->select('menu_items.id', 'menu_items.name', 'menu_items.image')
                    ->join('order_items', 'menu_items.id', '=', 'order_items.menu_item_id')
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->where('orders.restaurant_id', $tenantId)
                    ->where('orders.status', 'completed')
                    ->whereBetween('orders.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                    ->selectRaw('SUM(order_items.quantity) as total_sold')
                    ->selectRaw('SUM(order_items.total_price) as total_revenue')
                    ->groupBy('menu_items.id', 'menu_items.name', 'menu_items.image')
                    ->orderByDesc('total_sold')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Foto')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Menu')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_sold')
                    ->label('Terjual')
                    ->suffix(' porsi')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_revenue')
                    ->label('Total Pendapatan')
                    ->money('IDR')
                    ->sortable(),
            ]);
    }
}
