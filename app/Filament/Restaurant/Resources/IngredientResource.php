<?php

namespace App\Filament\Restaurant\Resources;

use App\Filament\Restaurant\Resources\IngredientResource\Pages;
use App\Filament\Restaurant\Resources\IngredientResource\RelationManagers;
use App\Models\Ingredient;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\RawJs;

class IngredientResource extends Resource
{
    protected static ?string $model = Ingredient::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'Bahan Baku';
    protected static ?string $navigationGroup = 'STOK & INVENTORI';
    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return parent::canAccess() && auth()->user()->hasFeature('Inventory Level 2');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Bahan Baku')
                    ->description('Kelola detail bahan baku (Ingredient) yang digunakan untuk menyusun resep.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Bahan Baku')
                            ->placeholder('Contoh: Beras, Kopi Arabica, Daging Sapi')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('unit')
                            ->label('Satuan Ukur (Unit)')
                            ->placeholder('Contoh: gram, kg, pcs, ikat, dll')
                            ->datalist([
                                'gram',
                                'kg',
                                'ml',
                                'liter',
                                'pcs',
                                'slice',
                                'pack',
                                'ikat',
                                'dus',
                                'box'
                            ])
                            ->required()
                            ->default('gram')
                            ->helperText('Pilih satuan terkecil yang digunakan dalam RESEP (misal: gram atau ml). Ini akan menjadi dasar stok & hitungan harga modal porsi.')
                            ->live()
                            ->afterStateUpdated(fn ($state, Forms\Get $get, Forms\Set $set) => self::calculateCost($get, $set)),
                        
                        Forms\Components\Section::make('Kalkulator Harga Masal (Opsional)')
                            ->description('Bantu hitung harga modal jika Anda membeli dalam kemasan besar (Karung, Dus, pack, dll).')
                            ->schema([
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('purchase_unit')
                                            ->label('Dibeli dalam bentuk...')
                                            ->placeholder('Contoh: Karung, Dus, Jerigen, Pack')
                                            ->live(onBlur: true),
                                        Forms\Components\TextInput::make('bulk_quantity')
                                            ->label(fn (Forms\Get $get) => 'Isi per ' . ($get('purchase_unit') ?: 'Kemasan'))
                                            ->placeholder('Contoh: 25')
                                            ->numeric()
                                            ->default(1)
                                            ->live()
                                            ->afterStateUpdated(fn ($state, Forms\Get $get, Forms\Set $set) => self::calculateCost($get, $set)),
                                        Forms\Components\Select::make('bulk_unit_type')
                                            ->label('Satuan Berat/Isi')
                                            ->options([
                                                'gram' => 'Gram',
                                                'kg' => 'Kilogram (kg)',
                                                'ml' => 'Mililiter (ml)',
                                                'liter' => 'Liter',
                                                'pcs' => 'Pcs/Butir/Biji',
                                            ])
                                            ->default('kg')
                                            ->live()
                                            ->afterStateUpdated(fn ($state, Forms\Get $get, Forms\Set $set) => self::calculateCost($get, $set)),
                                    ]),
                                
                                Forms\Components\TextInput::make('bulk_purchase_price')
                                    ->label(fn (Forms\Get $get) => 'Harga Total 1 ' . ($get('purchase_unit') ?: 'Kemasan'))
                                    ->prefix('Rp')
                                    ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                                    ->formatStateUsing(fn ($state) => $state ? number_format((float) $state, 0, ',', '.') : null)
                                    ->dehydrateStateUsing(fn ($state) => (float) str_replace('.', '', $state ?? 0))
                                    ->helperText(fn (Forms\Get $get) => 'Masukkan harga beli ' . ($get('purchase_unit') ?: 'kemasan') . ' tersebut.')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, Forms\Get $get, Forms\Set $set) => self::calculateCost($get, $set)),

                                Forms\Components\Placeholder::make('calculation_preview')
                                    ->label('📊 Ringkasan Konversi')
                                    ->content(function (Forms\Get $get) {
                                        $price = (float) str_replace('.', '', $get('bulk_purchase_price') ?? 0);
                                        $qty = (float) ($get('bulk_quantity') ?? 1);
                                        $bulkUnit = $get('bulk_unit_type');
                                        $baseUnit = $get('unit');
                                        $purchaseUnit = $get('purchase_unit') ?: 'Kemasan';

                                        if (!$price || !$qty) return 'Silakan masukkan jumlah isi dan harga beli di atas.';

                                        $factor = self::getConversionFactor($bulkUnit, $baseUnit);
                                        $totalBaseQty = $qty * $factor;
                                        $costPerUnit = $price / $totalBaseQty;

                                        return new \Illuminate\Support\HtmlString("
                                            <div class='p-3 bg-primary-50 border border-primary-200 rounded-lg text-sm'>
                                                1 <strong>{$purchaseUnit}</strong> isi <strong>{$qty} {$bulkUnit}</strong> setara dengan <strong>" . number_format($totalBaseQty, 0, ',', '.') . " {$baseUnit}</strong>.<br>
                                                <span class='text-primary-600 font-bold'>Harga Modal: Rp " . number_format($costPerUnit, 2, ',', '.') . " / {$baseUnit}</span>
                                            </div>
                                        ");
                                    })
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->columnSpanFull()
                            ->collapsible()
                            ->collapsed(fn ($record) => $record === null),

                        Forms\Components\TextInput::make('cost_per_unit')
                            ->label('Harga Modal per Satuan')
                            ->helperText(fn (Forms\Get $get) => 'Harga modal untuk 1 ' . ($get('unit') ?? 'unit') . '. Akan terupdate otomatis jika mengisi kalkulator masal di atas.')
                            ->required()
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                            ->formatStateUsing(fn ($state) => $state ? number_format((float) $state, 0, ',', '.') : null)
                            ->dehydrateStateUsing(fn ($state) => (float) str_replace('.', '', $state ?? 0))
                            ->default(0),
                    ])->columns(2),

                Forms\Components\Section::make('Manajemen Inventaris')
                    ->schema([
                        Forms\Components\TextInput::make('stock')
                            ->label('Sisa Stok Saat Ini')
                            ->suffix(fn (Forms\Get $get) => $get('unit') ?: 'unit')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->disabled(fn ($record) => $record !== null)
                            ->helperText(fn (Forms\Get $get, $record) => $record !== null 
                                ? 'Ubah stok melalui tombol "Adjust Stock" di tabel.' 
                                : 'Masukkan jumlah stok awal dalam SATUAN RESEP (' . ($get('unit') ?: 'gram/ml') . ').'),
                        Forms\Components\TextInput::make('min_stock_alert')
                            ->label('Batas Minimum Stok (Alert)')
                            ->suffix(fn (Forms\Get $get) => $get('unit') ?: 'unit')
                            ->helperText(fn (Forms\Get $get) => 'Notifikasi akan muncul bila stok kurang dari angka ' . ($get('unit') ?: 'ini') . '.')
                            ->required()
                            ->numeric()
                            ->default(0),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Bahan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Sisa Stok')
                    ->numeric()
                    ->sortable()
                    ->description(fn (Ingredient $record) => $record->unit)
                    ->badge()
                    ->color(fn (Ingredient $record) => $record->stock <= $record->min_stock_alert ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('cost_per_unit')
                    ->label('Harga Modal')
                    ->money('IDR')
                    ->description(fn (Ingredient $record) => 'per ' . $record->unit)
                    ->sortable(),
                Tables\Columns\TextColumn::make('min_stock_alert')
                    ->label('Batas Stok Rendah')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Update')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('adjust_stock')
                    ->label('Adjust Stok')
                    ->icon('heroicon-m-adjustments-horizontal')
                    ->color('warning')
                    ->visible(fn () => auth()->user()->can('adjust_ingredient_stock'))
                    ->form([
                        Forms\Components\Select::make('type')
                            ->label('Tipe Penyesuaian')
                            ->options([
                                'in' => 'Tambah Stok (Masuk)',
                                'out' => 'Kurangi Stok (Keluar)',
                                'adjustment' => 'Koreksi Total (Set ke...)',
                            ])
                            ->required()
                            ->live(),
                        Forms\Components\TextInput::make('quantity')
                            ->label(fn (Forms\Get $get) => $get('type') === 'adjustment' ? 'Stok Baru' : 'Jumlah')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('unit_price')
                            ->label('Harga Beli per Satuan')
                            ->prefix('Rp')
                            ->numeric()
                            ->helperText('Biarkan kosong jika harga tidak berubah. Digunakan untuk hitung HPP Rata-rata.')
                            ->visible(fn (Forms\Get $get) => $get('type') === 'in'),
                        Forms\Components\Select::make('reason')
                            ->label('Alasan')
                            ->options([
                                'purchase' => 'Pembelian Stok',
                                'breakage' => 'Barang Rusak',
                                'waste' => 'Kedaluwarsa/Basi',
                                'adjustment' => 'Lain-lain / Stok Opname',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan Tambahan')
                            ->placeholder('Opsional...'),
                    ])
                    ->action(function (Ingredient $record, array $data) {
                        $record->adjustStock(
                            quantity: $data['quantity'],
                            type: $data['type'],
                            reason: $data['reason'],
                            notes: $data['notes'],
                            newUnitPrice: $data['unit_price'] ?? null,
                        );

                        \Filament\Notifications\Notification::make()
                            ->title('Stok Berhasil Diperbarui')
                            ->success()
                            ->send();
                    })
                    ->after(fn ($livewire) => $livewire->dispatch('refresh-low-stock-widget')),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\MovementsRelationManager::class,
        ];
    }

    public static function calculateCost(Forms\Get $get, Forms\Set $set): void
    {
        $price = (float) str_replace('.', '', $get('bulk_purchase_price') ?? 0);
        $qty = (float) ($get('bulk_quantity') ?? 1);
        $bulkUnit = $get('bulk_unit_type');
        $baseUnit = $get('unit');

        if ($price && $qty > 0) {
            $factor = self::getConversionFactor($bulkUnit, $baseUnit);
            $totalBaseQty = $qty * $factor;
            $set('cost_per_unit', round($price / $totalBaseQty, 2));
        }
    }

    public static function getConversionFactor(string|null $from, string|null $to): float
    {
        if (!$from || !$to || $from === $to) {
            return 1.0;
        }

        $bulk = strtolower($from);
        $base = strtolower($to);

        // Logika konversi 1000 (Besar ke Kecil)
        $isBig = in_array($bulk, ['kg', 'liter']);
        $isSmall = in_array($base, ['gram', 'ml']);

        if ($isBig && $isSmall) {
            return 1000.0;
        }

        return 1.0;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIngredients::route('/'),
            'create' => Pages\CreateIngredient::route('/create'),
            'edit' => Pages\EditIngredient::route('/{record}/edit'),
        ];
    }
}
