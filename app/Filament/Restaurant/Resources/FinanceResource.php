<?php

namespace App\Filament\Restaurant\Resources;

use App\Models\RestaurantBalanceLedger;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class FinanceResource extends Resource
{
    protected static ?string $model = RestaurantBalanceLedger::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    
    protected static ?string $navigationLabel = 'Buku Kas (Ledger)';

    protected static ?string $navigationGroup = 'KEUANGAN';

    protected static ?int $navigationSort = 10;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tanggal & Waktu')
                    ->dateTime()
                    ->sortable(),
                
                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state === 'credit' ? 'Uang Masuk' : 'Uang Keluar')
                    ->colors([
                        'success' => 'credit',
                        'danger' => 'debit',
                    ]),
                
                TextColumn::make('gross_amount')
                    ->label('Gross (Kotor)')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('fee_amount')
                    ->label('Potongan/Fee')
                    ->money('IDR')
                    ->color('danger')
                    ->formatStateUsing(fn ($state) => $state > 0 ? '- ' . number_format($state, 0, ',', '.') : '0'),

                TextColumn::make('net_amount')
                    ->label('Net (Bersih)')
                    ->money('IDR')
                    ->weight('bold')
                    ->color(fn ($record) => $record->type === 'credit' ? 'success' : 'danger'),

                TextColumn::make('payment_type')
                    ->label('Metode')
                    ->formatStateUsing(fn ($state) => RestaurantBalanceLedger::paymentTypeLabel($state ?: 'cash')),

                TextColumn::make('description')
                    ->label('Keterangan')
                    ->searchable()
                    ->wrap(),
                
                TextColumn::make('order.order_number')
                    ->label('Referansi Order')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipe Transaksi')
                    ->options([
                        'credit' => 'Uang Masuk',
                        'debit' => 'Uang Keluar',
                    ]),
                Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from')->label('Dari Tanggal'),
                        \Filament\Forms\Components\DatePicker::make('until')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index' => FinanceResource\Pages\ListFinances::route('/'),
        ];
    }
}
