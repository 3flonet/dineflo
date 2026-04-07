<?php

namespace App\Filament\Restaurant\Resources;

use App\Filament\Restaurant\Resources\MenuItemResource\Pages;
use App\Filament\Restaurant\Resources\MenuItemResource\RelationManagers;
use App\Models\MenuItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\RawJs;

class MenuItemResource extends Resource
{
    protected static ?string $model = MenuItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Menu Digital';

    protected static ?string $navigationGroup = 'KATALOG & PROMO';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Item Details')
                    ->schema([
                        Forms\Components\Select::make('menu_category_id')
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Category'),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\Toggle::make('has_variants')
                            ->label('Has Variants')
                            ->live()
                            ->dehydrated(false)
                            ->default(false)
                            ->formatStateUsing(function ($state, $record) {
                                // Jika edit record yang sudah punya variants → otomatis ON
                                if ($record && $record->variants()->exists()) {
                                    return true;
                                }
                                return $state;
                            })
                            ->afterStateUpdated(function (Forms\Set $set, $state) {
                                if (!$state) {
                                    $set('variants', []);
                                }
                            }),

                        Forms\Components\TextInput::make('price')
                            ->label('Base Price')
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                            ->formatStateUsing(fn ($state) => $state ? number_format((float) $state, 0, ',', '.') : null)
                            ->dehydrateStateUsing(fn ($state) => (float) str_replace('.', '', $state ?? 0))
                            ->required(fn (Forms\Get $get) => !$get('has_variants'))
                            ->hidden(fn (Forms\Get $get) => $get('has_variants'))
                            ->dehydrated()
                            ->columnSpan(1),

                        Forms\Components\Section::make('Variants')
                            ->schema([
                                Forms\Components\Repeater::make('variants')
                                    ->relationship()
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->placeholder('e.g. Small, Large'),
                                        Forms\Components\TextInput::make('price')
                                            ->required()
                                            ->prefix('Rp')
                                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                                            ->formatStateUsing(fn ($state) => $state ? number_format((float) $state, 0, ',', '.') : null)
                                            ->dehydrateStateUsing(fn ($state) => (float) str_replace('.', '', $state ?? 0))
                                            ->label('Variant Price'),
                                    ])
                                    ->columns(2)
                                    ->minItems(1)
                                    ->defaultItems(1),
                            ])
                            ->visible(fn (Forms\Get $get) => $get('has_variants'))
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('prep_time')
                            ->numeric()
                            ->suffix('mins')
                            ->label('Preparation Time'),
                        Forms\Components\FileUpload::make('image')
                            ->image()
                            ->imageEditor()
                            ->imageResizeTargetWidth('800')
                            ->imageResizeMode('cover')
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->disk('public')
                            ->directory('menu-items')
                            ->visibility('public')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull(),
                        Forms\Components\TagsInput::make('allergens')
                            ->suggestions([
                                'Vegan',
                                'Vegetarian',
                                'Gluten Free',
                                'Nut Free',
                                'Dairy Free',
                                'Spicy',
                                'Halal',
                            ])
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_available')
                            ->default(true),
                        Forms\Components\Toggle::make('manage_stock')
                            ->label('Manage Stock')
                            ->live(),
                        Forms\Components\TextInput::make('stock_quantity')
                            ->numeric()
                            ->label('Stock Quantity')
                            ->visible(fn (Forms\Get $get) => $get('manage_stock')),
                        Forms\Components\TextInput::make('low_stock_threshold')
                            ->numeric()
                            ->label('Low Stock Alert')
                            ->default(5)
                            ->visible(fn (Forms\Get $get) => $get('manage_stock')),
                    ])->columns(2),

                Forms\Components\Section::make('Add-ons')
                    ->schema([
                        Forms\Components\Repeater::make('addons')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->placeholder('e.g. Extra Cheese'),
                                Forms\Components\TextInput::make('price')
                                    ->required()
                                    ->prefix('Rp')
                                    ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                                    ->formatStateUsing(fn ($state) => $state ? number_format((float) $state, 0, ',', '.') : null)
                                    ->dehydrateStateUsing(fn ($state) => (float) str_replace('.', '', $state ?? 0))
                                    ->label('Additional Price'),
                            ])
                            ->columns(2)
                            ->defaultItems(0),
                    ]),

                Forms\Components\Section::make('Smart Upselling (Recommendations)')
                    ->description('Pilih menu pendamping (Upsell) yang akan disarankan saat pelanggan memesan menu ini.')
                    ->schema([
                        Forms\Components\Toggle::make('is_reciprocal')
                            ->label('Terapkan Timbal Balik (Two-way Recommendation)')
                            ->helperText('Jika diaktifkan, menu yang dipilih sebagai rekomendasi di bawah juga akan otomatis merekomendasikan menu ini.')
                            ->default(true),

                        Forms\Components\Repeater::make('upsells')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('upsell_item_id')
                                    ->relationship('upsellItem', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->label('Menu Rekomendasi (Upsell)')
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                            ])
                            ->columns(1)
                            ->defaultItems(0)
                            ->addActionLabel('Tambah Rekomendasi Menu'),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->visible(fn () => auth()->user()->hasFeature('Smart Upselling')),

                Forms\Components\Section::make('Resep (Bahan Baku)')
                    ->description('Pilih bahan baku utama dan porsi takarannya. Sistem akan mengurangi stok bahan ini otomatis setiap ada pesanan baru.')
                    ->schema([
                        Forms\Components\Repeater::make('menuItemIngredients')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('ingredient_id')
                                    ->relationship('ingredient', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->label('Bahan Baku')
                                    ->live(),
                                Forms\Components\Group::make([
                                    Forms\Components\TextInput::make('bulk_total_ingredients')
                                        ->label('Total Bahan (Batch)')
                                        ->numeric()
                                        ->live(onBlur: true)
                                        ->helperText('Misal: 2000 (jika masak 2kg sekaligus).')
                                        ->suffix(function (Forms\Get $get) {
                                            $ingredientId = $get('ingredient_id');
                                            if (!$ingredientId) return null;
                                            return \App\Models\Ingredient::find($ingredientId)?->unit;
                                        })
                                        ->afterStateUpdated(function ($state, Forms\Get $get, Forms\Set $set) {
                                            $portions = (float) $get('bulk_portions');
                                            if ($state > 0 && $portions > 0) {
                                                $set('quantity', round($state / $portions, 2));
                                            }
                                        }),
                                        
                                    Forms\Components\TextInput::make('bulk_portions')
                                        ->label('Menghasilkan (Porsi)')
                                        ->numeric()
                                        ->live(onBlur: true)
                                        ->helperText('Misal: 10 (jika panci/batch itu untuk 10 porsi).')
                                        ->afterStateUpdated(function ($state, Forms\Get $get, Forms\Set $set) {
                                            $total = (float) $get('bulk_total_ingredients');
                                            if ($state > 0 && $total > 0) {
                                                $set('quantity', round($total / $state, 2));
                                            }
                                        }),
                                ])->columns(2)->columnSpanFull()
                                ->visible(fn(Forms\Get $get) => filled($get('ingredient_id'))),

                                Forms\Components\TextInput::make('quantity')
                                    ->required()
                                    ->numeric()
                                    ->placeholder('Misal: 150')
                                    ->label('Takaran per Sajian')
                                    ->helperText('Jumlah bahan yang digunakan untuk 1 porsi menu ini. (Bisa manual atau dihitung via kalkulator Batch di atas)')
                                    ->suffix(function (Forms\Get $get) {
                                        $ingredientId = $get('ingredient_id');
                                        if (!$ingredientId) return null;
                                        
                                        $ingredient = \App\Models\Ingredient::find($ingredientId);
                                        return $ingredient?->unit;
                                    })
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('Tambahkan Bahan Baku ke Resep'),
                    ])
                    ->visible(fn () => auth()->user()->hasFeature('Inventory Level 2')),

                Forms\Components\Section::make('Food Cost & Profit Analysis')
                    ->description('Estimasi keuntungan berdasarkan harga modal bahan baku (Resep).')
                    ->schema([
                        Forms\Components\Placeholder::make('total_cost_display')
                            ->label('Total Modal Bahan (HPP)')
                            ->content(fn (MenuItem $record) => 'IDR ' . number_format($record->total_cost, 2, ',', '.')),
                        
                        Forms\Components\Placeholder::make('profit_margin_display')
                            ->label('Estimasi Gross Margin')
                            ->content(function (MenuItem $record) {
                                $margin = $record->profit_margin;
                                $color = $margin < 30 ? 'text-danger-600' : ($margin < 50 ? 'text-warning-600' : 'text-success-600');
                                return new \Illuminate\Support\HtmlString("<span class='font-bold {$color}'>" . number_format($margin, 2) . "%</span>");
                            }),
                        
                        Forms\Components\Placeholder::make('note')
                            ->label('')
                            ->content('Analisis ini berdasarkan "Base Price". Jika menu memiliki varian, margin mungkin berbeda per varian.')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->visible(fn ($record) => $record && auth()->user()->hasFeature('Inventory Level 2')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->circular()
                    ->toggleable(isToggledHiddenByDefault: false),
                    
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->description(function (MenuItem $record) {
                        return $record->category?->name . ' • ' . $record->formatted_price;
                    }),
                    
                Tables\Columns\TextColumn::make('category.name')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->toggleable(isToggledHiddenByDefault: false),
                    
                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->sortable()
                    ->formatStateUsing(function ($state, MenuItem $record) {
                        return $record->formatted_price;
                    })
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('total_cost')
                    ->label('HPP')
                    ->money('IDR')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visible(fn () => auth()->user()->hasFeature('Inventory Level 2')),

                Tables\Columns\TextColumn::make('profit_margin')
                    ->label('Margin')
                    ->formatStateUsing(fn ($state) => number_format($state, 1) . '%')
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state < 30 => 'danger',
                        $state < 50 => 'warning',
                        default => 'success',
                    })
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->visible(fn () => auth()->user()->hasFeature('Inventory Level 2')),
                    
                Tables\Columns\IconColumn::make('is_available')
                    ->boolean()
                    ->label('Available')
                    ->toggleable(isToggledHiddenByDefault: false),
                    
                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Stock')
                    ->badge()
                    ->color(fn (MenuItem $record) => match (true) {
                        !$record->manage_stock => 'gray',
                        $record->stock_quantity <= $record->low_stock_threshold => 'danger',
                        $record->stock_quantity <= ($record->low_stock_threshold * 2) => 'warning',
                        default => 'success',
                    })
                    ->formatStateUsing(fn ($state, MenuItem $record) => $record->manage_stock ? $state : '∞')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\TernaryFilter::make('is_available')
                    ->label('Availability'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ReplicateAction::make()
                    ->excludeAttributes(['slug'])
                    ->beforeReplicaSaved(function (MenuItem $replica) {
                        $replica->name = $replica->name . ' (Copy)';
                        $replica->slug = \Illuminate\Support\Str::slug($replica->name);
                    }),
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenuItems::route('/'),
            'create' => Pages\CreateMenuItem::route('/create'),
            'edit' => Pages\EditMenuItem::route('/{record}/edit'),
        ];
    }
}
