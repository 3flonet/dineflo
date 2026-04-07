<?php

namespace App\Filament\Restaurant\Pages;

use App\Models\MenuItem;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class FinancialInsights extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static string $view = 'filament.restaurant.pages.financial-insights';

    protected static ?string $navigationLabel = 'Wawasan Laba (Profit)';

    protected static ?string $title = 'Wawasan Laba & Analisis Menu';

    protected static ?string $navigationGroup = 'KEUANGAN';

    protected static ?int $navigationSort = 6;

    public static function canAccess(): bool
    {
        return auth()->user()->hasFeature('Profit Margin Insights') && auth()->user()->can('page_FinancialInsights');
    }

    public function mount(): void
    {
        if (! auth()->user()->hasFeature('Profit Margin Insights')) {
            Notification::make()
                ->title('Akses Ditolak')
                ->body('Anda memerlukan paket Pro/Premium untuk mengakses Analisis Margin.')
                ->danger()
                ->send();
            
            $this->redirect(route('filament.restaurant.pages.dashboard', ['tenant' => \Filament\Facades\Filament::getTenant()]));
        }
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(MenuItem::where('restaurant_id', \Filament\Facades\Filament::getTenant()->id))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Menu Item'),
                
                Tables\Columns\TextColumn::make('total_cost')
                    ->label('HPP (Modal)')
                    ->money('IDR'),

                Tables\Columns\TextColumn::make('price')
                    ->label('Harga Jual')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('profit_margin')
                    ->label('Margin %')
                    ->formatStateUsing(fn ($state) => number_not_null($state) ? number_format($state, 1) . '%' : '0%')
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state < 30 => 'danger',
                        $state < 50 => 'warning',
                        default => 'success',
                    }),

                Tables\Columns\TextColumn::make('sold_quantity')
                    ->label('Terjual')
                    ->alignment(\Filament\Support\Enums\Alignment::Center),

                Tables\Columns\TextColumn::make('total_contribution')
                    ->label('Total Profit (Net)')
                    ->money('IDR')
                    ->weight('bold')
                    ->color('success'),

                Tables\Columns\TextColumn::make('menu_insight')
                    ->label('Kategori')
                    ->formatStateUsing(fn ($state, MenuItem $record) => $record->menu_insight_label['icon'] . ' ' . $record->menu_insight_label['label'])
                    ->badge()
                    ->color(fn ($state, MenuItem $record) => $record->menu_insight_label['color'])
                    ->tooltip(fn ($state, MenuItem $record) => $record->menu_insight_label['desc']),
            ])
            ->defaultSort('name')
            ->filters([
                Tables\Filters\SelectFilter::make('menu_insight')
                    ->label('Kategori Engineering')
                    ->options([
                        'star' => 'Star (Star)',
                        'plowhorse' => 'Plowhorse',
                        'puzzle' => 'Puzzle',
                        'dog' => 'Dog',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['value'])) return $query;
                        
                        // Filtering by virtual attribute is hard in SQL.
                        // For now we'll collect IDs of items that match.
                        $restaurantId = \Filament\Facades\Filament::getTenant()->id;
                        $items = MenuItem::where('restaurant_id', $restaurantId)->get();
                        $matchingIds = $items->filter(fn($item) => $item->menu_insight === $data['value'])->pluck('id');
                        
                        return $query->whereIn('id', $matchingIds);
                    }),
                Tables\Filters\SelectFilter::make('menu_category_id')
                    ->relationship('category', 'name')
                    ->label('Kategori Menu'),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('Refresh Data')
                ->action(fn() => $this->dispatch('refresh_insights')),
        ];
    }
}

function number_not_null($val) {
    return !is_null($val);
}
