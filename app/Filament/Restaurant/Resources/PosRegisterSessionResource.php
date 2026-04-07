<?php

namespace App\Filament\Restaurant\Resources;

use App\Models\PosRegisterSession;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PosRegisterSessionResource extends Resource
{
    protected static ?string $model = PosRegisterSession::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationGroup = 'KEUANGAN';
    protected static ?string $navigationLabel = 'Laporan Shift Kasir';
    protected static ?int $navigationSort = 4;

    public static function canAccess(): bool
    {
        return parent::canAccess() && auth()->user()->hasFeature('Cash Drawer Integration');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('opened_at')
                    ->label('Waktu Buka')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Kasir')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open' => 'success',
                        'closed' => 'gray',
                    }),
                Tables\Columns\TextColumn::make('opening_cash')
                    ->label('Modal Awal')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('expected_cash')
                    ->label('Saldo Sistem')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('closing_cash')
                    ->label('Saldo Fisik')
                    ->money('IDR')
                    ->placeholder('Active Shift'),
                Tables\Columns\TextColumn::make('diff')
                    ->label('Selisih')
                    ->state(function (PosRegisterSession $record): ?float {
                        if ($record->status === 'open') return null;
                        return (float) $record->closing_cash - (float) $record->expected_cash;
                    })
                    ->money('IDR')
                    ->color(fn ($state) => $state < 0 ? 'danger' : ($state > 0 ? 'warning' : 'success')),
            ])
            ->defaultSort('opened_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'open' => 'Active Shift',
                        'closed' => 'Past Shift',
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
            'index' => PosRegisterSessionResource\Pages\ListPosRegisterSessions::route('/'),
        ];
    }
}
