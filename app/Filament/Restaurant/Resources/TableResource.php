<?php

namespace App\Filament\Restaurant\Resources;

use App\Filament\Restaurant\Resources\TableResource\Pages;
use App\Models\Table as TableModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Facades\Filament;

class TableResource extends Resource
{
    protected static ?string $model = TableModel::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    protected static ?string $navigationLabel = 'Manajemen Meja';

    protected static ?string $navigationGroup = 'PENGATURAN TOKO';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('area')
                    ->placeholder('e.g. Indoor, Terrace'),
                Forms\Components\TextInput::make('capacity')
                    ->numeric(),
                Forms\Components\TextInput::make('qr_code')
                    ->unique(ignoreRecord: true)
                    ->helperText('Will be auto-generated if left empty'),
                Forms\Components\Select::make('status')
                    ->options([
                        'available' => 'Available',
                        'occupied'  => 'Occupied',
                        'dirty'     => 'Dirty',
                        'reserved'  => 'Reserved',
                    ])
                    ->default('available')
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        $tenant = Filament::getTenant();

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->description(fn (TableModel $record) => $record->area . ($record->capacity ? ' • Kapasitas: ' . $record->capacity : '')),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'available' => 'success',
                        'occupied'  => 'danger',
                        'dirty'     => 'warning',
                        'reserved'  => 'info',
                        default     => 'gray'
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state)),

                Tables\Columns\TextColumn::make('area')
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('capacity')
                    ->numeric()
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active')
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'available' => 'Available',
                        'occupied'  => 'Occupied',
                        'dirty'     => 'Dirty',
                        'reserved'  => 'Reserved',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('All Tables')
                    ->trueLabel('Active Only')
                    ->falseLabel('Inactive Only'),
            ])
            ->headerActions([
                // ── QR Card Designer Global ──
                Tables\Actions\Action::make('qr_card_global')
                    ->label('Desain Kartu QR')
                    ->icon('heroicon-o-paint-brush')
                    ->color('primary')
                    ->url(fn () => route('restaurant.tables.qr-designer', ['restaurant' => $tenant->slug]))
                    ->openUrlInNewTab()
                    ->tooltip('Buka Global QR Card Designer untuk memperbarui template desain QR'),

                // ── Bulk Print Button ──
                Tables\Actions\Action::make('bulk_print_qr')
                    ->label('Print Semua QR')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn () => route('restaurant.tables.qr-bulk-print', ['restaurant' => $tenant->slug]))
                    ->openUrlInNewTab()
                    ->tooltip('Cetak kartu QR untuk semua meja aktif berdasarkan desain global'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->striped()
            ->defaultSort('name')
            ->persistFiltersInSession()
            ->filtersFormColumns(1);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTables::route('/'),
            'create' => Pages\CreateTable::route('/create'),
            'edit'   => Pages\EditTable::route('/{record}/edit'),
        ];
    }
}
