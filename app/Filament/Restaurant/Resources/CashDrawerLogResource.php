<?php

namespace App\Filament\Restaurant\Resources;

use App\Filament\Restaurant\Resources\CashDrawerLogResource\Pages;
use App\Models\CashDrawerLog;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CashDrawerLogResource extends Resource
{
    protected static ?string $model = CashDrawerLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-lock-closed';
    protected static ?string $navigationGroup = 'KEUANGAN';
    protected static ?string $navigationLabel = 'Log Laci Kasir';
    protected static ?int $navigationSort = 5;

    public static function canAccess(): bool
    {
        return parent::canAccess() && auth()->user()->hasFeature('Cash Drawer Integration');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Staff')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'automatic' => 'success',
                        'manual' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                Tables\Columns\TextColumn::make('reason')
                    ->label('Alasan/Kegiatan')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('order.order_number')
                    ->label('Pesanan #')
                    ->searchable()
                    ->placeholder('- No Sale -'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'automatic' => 'Automatic (Sale)',
                        'manual' => 'Manual (No Sale)',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCashDrawerLogs::route('/'),
        ];
    }
}
