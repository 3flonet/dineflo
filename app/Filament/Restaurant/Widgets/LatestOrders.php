<?php

namespace App\Filament\Restaurant\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Order;
use App\Filament\Restaurant\Resources\OrderResource;

class LatestOrders extends BaseWidget
{
    public static function canView(): bool
    {
        return auth()->user()->can('widget_LatestOrders');
    }

    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = '1';

    protected static ?string $heading = 'Recent Orders';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->formatStateUsing(fn (string $state): string => '#' . $state)
                    ->color('primary')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Customer')
                    ->limit(15),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        'ready' => 'success',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->paginated(false)
            ->recordUrl(
                fn (Order $record): string => OrderResource::getUrl('edit', ['record' => $record])
            );
    }
}
