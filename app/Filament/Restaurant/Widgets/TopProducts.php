<?php

namespace App\Filament\Restaurant\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Builder;

class TopProducts extends BaseWidget
{
    public static function canView(): bool
    {
        return auth()->user()->can('widget_TopProducts');
    }

    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = '1'; // Takes 1 column out of default 2? Or use 'half'? '1' usually means 1 grid column.
    // Filament default dashboard grid is 2 cols on lg.
    
    protected static ?string $heading = 'Top Selling Items';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                 OrderItem::query()
                    ->selectRaw('menu_item_id as id, menu_item_id')
                    ->selectRaw('SUM(quantity) as total_qty')
                    ->selectRaw('SUM(total_price) as revenue')
                    ->with(['menuItem', 'menuItem.category'])
                    ->whereHas('order', function (Builder $query) {
                        $query->where('restaurant_id', \Filament\Facades\Filament::getTenant()->id)
                              ->where('payment_status', 'paid');
                    })
                    ->groupBy('menu_item_id')
                    ->orderByDesc('total_qty')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('menuItem.name')
                    ->label('Product')
                    ->description(fn (OrderItem $record) => $record->menuItem->category->name ?? '')
                    ->limit(20),
                    
                Tables\Columns\TextColumn::make('total_qty')
                    ->label('Sold')
                    ->alignCenter(),
                    
                Tables\Columns\TextColumn::make('revenue')
                    ->label('Revenue')
                    ->money('IDR'),
            ])
            ->paginated(false);
    }
}
