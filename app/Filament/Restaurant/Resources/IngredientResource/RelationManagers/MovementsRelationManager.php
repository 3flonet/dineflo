<?php

namespace App\Filament\Restaurant\Resources\IngredientResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MovementsRelationManager extends RelationManager
{
    protected static string $relationship = 'movements';

    protected static ?string $title = 'Riwayat Pergerakan Stok';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return auth()->user()->can('view_ingredient_history');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'in' => 'Masuk',
                        'out' => 'Keluar',
                        'adjustment' => 'Penyesuaian',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'in' => 'success',
                        'out' => 'danger',
                        'adjustment' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Jumlah')
                    ->numeric(decimalPlaces: 2)
                    ->icon(fn ($record) => $record->type === 'in' ? 'heroicon-m-arrow-trending-up' : ($record->type === 'out' ? 'heroicon-m-arrow-trending-down' : 'heroicon-m-adjustments-horizontal')),

                Tables\Columns\TextColumn::make('before_quantity')
                    ->label('Stok Sebelumnnya')
                    ->numeric(decimalPlaces: 2)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('after_quantity')
                    ->label('Stok Akhir')
                    ->numeric(decimalPlaces: 2)
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('reason')
                    ->label('Alasan/Sumber')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'purchase' => 'Pembelian Stok',
                        'order_deduction' => 'Pemakaian Pesanan',
                        'order_restore' => 'Pembatalan Pesanan',
                        'breakage' => 'Barang Rusak',
                        'waste' => 'Kedaluwarsa/Basi',
                        'adjustment' => 'Koreksi Manual',
                        'initial' => 'Stok Awal',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Oleh')
                    ->placeholder('System'),

                Tables\Columns\TextColumn::make('notes')
                    ->label('Catatan')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->notes),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipe')
                    ->options([
                        'in' => 'Masuk',
                        'out' => 'Keluar',
                        'adjustment' => 'Penyesuaian',
                    ]),
            ])
            ->headerActions([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
    }
}
