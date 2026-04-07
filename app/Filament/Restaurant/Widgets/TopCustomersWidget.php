<?php

namespace App\Filament\Restaurant\Widgets;

use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class TopCustomersWidget extends BaseWidget
{
    public static function canView(): bool
    {
        return auth()->user()->can('widget_TopCustomersWidget');
    }

    protected static ?string $heading = 'Pelanggan Terbaik (Loyal Customers)';

    protected int | string | array $columnSpan = 'half';

    public ?array $filters = null;

    public function table(Table $table): Table
    {
        $startDate = request('date_start', now()->startOfMonth()->toDateString());
        $endDate = request('date_end', now()->endOfMonth()->toDateString());

        $tenantId = \Filament\Facades\Filament::getTenant()->id;

        return $table
            ->query(
                Order::query()
                    ->selectRaw('customer_phone as id, customer_name, customer_phone')
                    ->where('restaurant_id', $tenantId)
                    ->where('status', 'completed')
                    ->whereNotNull('customer_phone')
                    ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                    ->selectRaw('COUNT(*) as total_orders')
                    ->selectRaw('SUM(total_amount) as total_spent')
                    ->groupBy('customer_phone', 'customer_name')
                    ->orderByDesc('total_spent')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Pelanggan')
                    ->description(fn ($record) => $record->customer_phone),
                Tables\Columns\TextColumn::make('total_orders')
                    ->label('Pesanan')
                    ->suffix(' kali')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('total_spent')
                    ->label('Total Spend')
                    ->money('IDR')
                    ->sortable(),
            ]);
    }
}
