<?php

namespace App\Filament\Restaurant\Resources;

use App\Filament\Restaurant\Resources\ExpenseCategoryResource\Pages;
use App\Filament\Restaurant\Resources\ExpenseCategoryResource\RelationManagers;
use App\Models\ExpenseCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExpenseCategoryResource extends Resource
{
    protected static ?string $model = ExpenseCategory::class;


    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'Kategori Biaya';
    protected static ?string $modelLabel = 'Kategori Biaya';
    protected static ?string $pluralModelLabel = 'Kategori Biaya';
    protected static ?string $navigationGroup = 'KEUANGAN';
    protected static ?int $navigationSort = 7;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->can('view_any_expense::category') && auth()->user()->hasExpenseManagement();
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_any_expense::category') && auth()->user()->hasExpenseManagement();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Kategori')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('type')
                            ->label('Tipe Pengeluaran')
                            ->options([
                                'operational' => 'Operasional',
                                'cost_of_goods' => 'HPP (Bahan Baku)',
                                'salary' => 'Gaji / SDM',
                                'marketing' => 'Pemasaran',
                                'utility' => 'Utilitas (Listrik/Air)',
                                'rent' => 'Sewa Tempat',
                                'other' => 'Lain-lain',
                            ])
                            ->required()
                            ->default('operational'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true)
                            ->required(),
                    ])->columns(2),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Kategori')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'operational' => 'Operasional',
                        'cost_of_goods' => 'HPP (Bahan Baku)',
                        'salary' => 'Gaji / SDM',
                        'marketing' => 'Pemasaran',
                        'utility' => 'Utilitas (Listrik/Air)',
                        'rent' => 'Sewa Tempat',
                        'other' => 'Lain-lain',
                        default => $state,
                    })
                    ->colors([
                        'primary' => 'operational',
                        'success' => 'cost_of_goods',
                        'warning' => 'salary',
                        'danger' => 'marketing',
                        'info' => 'utility',
                        'gray' => 'other',
                    ]),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExpenseCategories::route('/'),
            'create' => Pages\CreateExpenseCategory::route('/create'),
            'edit' => Pages\EditExpenseCategory::route('/{record}/edit'),
        ];
    }
}
