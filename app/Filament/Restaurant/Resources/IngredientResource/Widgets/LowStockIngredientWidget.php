<?php

namespace App\Filament\Restaurant\Resources\IngredientResource\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockIngredientWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected $listeners = ['refresh-low-stock-widget' => '$refresh'];

    public function table(Table $table): Table
    {
        return $table
            ->query(
                \App\Models\Ingredient::whereColumn('stock', '<=', 'min_stock_alert')
            )
            ->heading('Peringatan: Stok Bahan Baku Rendah / Habis')
            ->description('Daftar bahan baku di bawah ini sudah menyentuh atau melewati batas minimum. Segera lakukan restok ulang.')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Bahan')
                    ->weight('bold')
                    ->searchable(),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Sisa Stok Saat Ini')
                    ->badge()
                    ->color('danger')
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('min_stock_alert')
                    ->label('Batas Minimum')
                    ->numeric()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('unit')
                    ->label('Satuan')
                    ->badge()
                    ->color('gray'),
            ])
            ->actions([
                Tables\Actions\Action::make('restock')
                    ->label('Isi Ulang (Restock)')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('quantity')
                            ->label('Jumlah Masuk')
                            ->numeric()
                            ->required()
                            ->suffix(fn ($record) => $record->unit),
                        \Filament\Forms\Components\TextInput::make('unit_price')
                            ->label('Harga Beli per Satuan')
                            ->prefix('Rp')
                            ->numeric()
                            ->helperText('Biarkan kosong jika harga tidak berubah. Digunakan untuk hitung HPP Rata-rata.'),
                        \Filament\Forms\Components\Select::make('reason')
                            ->label('Alasan')
                            ->options([
                                'purchase' => 'Pembelian Stok',
                                'adjustment' => 'Penyesuaian Stok',
                            ])
                            ->default('purchase')
                            ->required(),
                        \Filament\Forms\Components\Textarea::make('notes')
                            ->label('Catatan Tambahan')
                            ->placeholder('Opsional...'),
                    ])
                    ->action(function (\App\Models\Ingredient $record, array $data) {
                        $record->adjustStock(
                            quantity: $data['quantity'],
                            type: 'in',
                            reason: $data['reason'],
                            notes: $data['notes'],
                            newUnitPrice: $data['unit_price'] ?? null,
                        );

                        \Filament\Notifications\Notification::make()
                            ->title('Stok Berhasil Ditambah')
                            ->success()
                            ->send();
                    })
                    ->after(fn ($livewire) => $livewire->dispatch('refresh-ingredients-list')),
            ])
            ->paginated(false);
    }
}
