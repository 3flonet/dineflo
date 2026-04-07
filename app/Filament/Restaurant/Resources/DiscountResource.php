<?php

namespace App\Filament\Restaurant\Resources;

use App\Filament\Restaurant\Resources\DiscountResource\Pages;
use App\Models\Discount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DiscountResource extends Resource
{
    protected static ?string $model = Discount::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';
    protected static ?string $navigationLabel = 'Promo & Voucher';
    protected static ?string $navigationGroup = 'KATALOG & PROMO';
    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return parent::canAccess() && auth()->user()->hasFeature('Dynamic Pricing');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Promo Aktif')
                            ->helperText('Matikan saklar ini untuk menonaktifkan promo ini secara total di semua tempat.')
                            ->default(true)
                            ->inline(false)
                            ->onColor('success')
                            ->offColor('danger'),
                    ])->compact(),

                Forms\Components\Tabs::make('Discount Configuration')
                    ->tabs([
                        // TAB 1: GENERAL
                        Forms\Components\Tabs\Tab::make('General Information')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                
                                Forms\Components\Textarea::make('description')
                                    ->rows(2),
                                
                                Forms\Components\Grid::make(2)->schema([
                                    Forms\Components\Select::make('type')
                                        ->label('Discount Type')
                                        ->options([
                                            'percentage' => 'Percentage (%)',
                                            'fixed' => 'Fixed Amount (Rp)'
                                        ])
                                        ->required()
                                        ->default('percentage')
                                        ->live(),
                                    
                                    Forms\Components\TextInput::make('value')
                                        ->label('Discount Value')
                                        ->required()
                                        ->prefix(fn (Forms\Get $get) => $get('type') === 'fixed' ? 'Rp' : null)
                                        ->suffix(fn (Forms\Get $get) => $get('type') === 'percentage' ? '%' : null)
                                        ->mask(fn (Forms\Get $get) => $get('type') === 'fixed' 
                                            ? \Filament\Support\RawJs::make('$money($input, \',\', \'.\', 0)') 
                                            : null
                                        )
                                        ->formatStateUsing(function ($state, Forms\Get $get) {
                                            if ($get('type') === 'fixed' && $state) {
                                                return number_format((float) $state, 0, ',', '.');
                                            }
                                            return $state;
                                        })
                                        ->dehydrateStateUsing(function ($state, Forms\Get $get) {
                                            if ($get('type') === 'fixed' && $state) {
                                                return (float) str_replace('.', '', $state);
                                            }
                                            return $state;
                                        })
                                        ->numeric(fn (Forms\Get $get) => $get('type') === 'percentage')
                                        ->minValue(0),
                                ]),

                                Forms\Components\Section::make('Metode Aplikasi')
                                    ->description('Pilih apakah diskon ini berlaku otomatis atau menggunakan kode voucher.')
                                    ->schema([
                                        Forms\Components\Toggle::make('use_voucher')
                                            ->label('Gunakan Kode Voucher')
                                            ->helperText('Jika aktif, pelanggan harus memasukkan kode untuk mendapatkan diskon.')
                                            ->live()
                                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                                if (!$state) $set('code', null);
                                            })
                                            ->formatStateUsing(fn ($record) => $record && $record->code ? true : false)
                                            ->dehydrated(false), // Field ini hanya untuk UI perantara

                                        Forms\Components\TextInput::make('code')
                                            ->label('Voucher Code')
                                            ->placeholder('CONTOH: HEMAT50')
                                            ->required(fn (Forms\Get $get) => $get('use_voucher'))
                                            ->visible(fn (Forms\Get $get) => $get('use_voucher'))
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255),
                                    ])
                                    ->visible(fn () => auth()->user()->hasFeature('Voucher & Marketing')),
                            ]),

                        // TAB 2: SCOPE & AUDIENCE
                        Forms\Components\Tabs\Tab::make('Scope & Audience')
                            ->icon('heroicon-o-users')
                            ->schema([
                                Forms\Components\Group::make([
                                    Forms\Components\Select::make('scope')
                                        ->label('Menu Scope (Applies To)')
                                        ->options([
                                            'all' => 'Entire Restaurant',
                                            'categories' => 'Specific Categories',
                                            'items' => 'Specific Menu Items',
                                        ])
                                        ->required()
                                        ->default('all')
                                        ->live(),
                                    
                                    Forms\Components\Select::make('menuCategories')
                                        ->relationship('menuCategories', 'name')
                                        ->multiple()
                                        ->preload()
                                        ->visible(fn (\Filament\Forms\Get $get) => $get('scope') === 'categories')
                                        ->required(fn (\Filament\Forms\Get $get) => $get('scope') === 'categories'),

                                    Forms\Components\Select::make('menuItems')
                                        ->relationship('menuItems', 'name')
                                        ->multiple()
                                        ->searchable()
                                        ->preload()
                                        ->visible(fn (\Filament\Forms\Get $get) => $get('scope') === 'items')
                                        ->required(fn (\Filament\Forms\Get $get) => $get('scope') === 'items'),
                                ])->columnSpanFull(),

                                Forms\Components\Section::make('Customer Targeting')
                                    ->description('Tentukan siapa saja yang boleh menggunakan diskon ini.')
                                    ->schema([
                                        Forms\Components\Select::make('target_type')
                                            ->label('Target Audience')
                                            ->options([
                                                'all' => 'All Customers (Public)',
                                                'members_only' => 'All Registered Members',
                                                'tiers_only' => 'Specific Membership Tiers',
                                            ])
                                            ->required()
                                            ->default('all')
                                            ->live(),
                                        
                                        Forms\Components\CheckboxList::make('target_tiers')
                                            ->label('Eligible Tiers')
                                            ->options([
                                                'bronze' => 'Bronze Member',
                                                'silver' => 'Silver Member',
                                                'gold' => 'Gold Member',
                                            ])
                                            ->visible(fn (\Filament\Forms\Get $get) => $get('target_type') === 'tiers_only')
                                            ->required(fn (\Filament\Forms\Get $get) => $get('target_type') === 'tiers_only')
                                            ->columns(3),
                                    ])
                                    ->visible(fn () => auth()->user()->hasFeature('Voucher & Marketing')),
                            ]),

                        // TAB 3: LIMITS & SCHEDULE
                        Forms\Components\Tabs\Tab::make('Limits & Schedule')
                            ->icon('heroicon-o-clock')
                            ->schema([
                                Forms\Components\Grid::make(3)->schema([
                                    Forms\Components\TextInput::make('min_order_amount')
                                        ->label('Min. Order')
                                        ->prefix('Rp')
                                        ->mask(\Filament\Support\RawJs::make('$money($input, \',\', \'.\', 0)'))
                                        ->formatStateUsing(fn ($state) => $state ? number_format((float) $state, 0, ',', '.') : null)
                                        ->dehydrateStateUsing(fn ($state) => $state ? (float) str_replace('.', '', $state) : 0),

                                    Forms\Components\TextInput::make('usage_limit')
                                        ->label('Total Usage (Quota)')
                                        ->numeric()
                                        ->minValue(1)
                                        ->visible(fn (Forms\Get $get) => $get('use_voucher')),
                                    
                                    Forms\Components\TextInput::make('usage_per_customer')
                                        ->label('Customer Limit')
                                        ->numeric()
                                        ->default(1)
                                        ->minValue(1)
                                        ->required()
                                        ->visible(fn (Forms\Get $get) => $get('use_voucher')),
                                ]),

                                Forms\Components\Section::make('Scheduling')
                                    ->schema([
                                        Forms\Components\Toggle::make('is_recurring')
                                            ->label('Happy Hour / Recurring Mode')
                                            ->helperText('Aktifkan jika diskon ini berulang pada jam/hari tertentu.')
                                            ->default(false)
                                            ->live(),
                                        
                                        Forms\Components\Grid::make(2)->schema([
                                            // Non-recurring dates
                                            Forms\Components\DatePicker::make('start_date')
                                                ->label('Start Date')
                                                ->visible(fn (\Filament\Forms\Get $get) => !$get('is_recurring')),
                                            
                                            Forms\Components\DatePicker::make('end_date')
                                                ->label('End Date')
                                                ->visible(fn (\Filament\Forms\Get $get) => !$get('is_recurring')),
                                        ]),

                                        // Recurring scheduling
                                        Forms\Components\Group::make([
                                            Forms\Components\CheckboxList::make('days_of_week')
                                                ->label('Active Days')
                                                ->options([
                                                    'Monday' => 'Mon',
                                                    'Tuesday' => 'Tue',
                                                    'Wednesday' => 'Wed',
                                                    'Thursday' => 'Thu',
                                                    'Friday' => 'Fri',
                                                    'Saturday' => 'Sat',
                                                    'Sunday' => 'Sun',
                                                ])
                                                ->columns(7),

                                            Forms\Components\Grid::make(2)->schema([
                                                Forms\Components\TimePicker::make('start_time')
                                                    ->label('Start Time')
                                                    ->seconds(false),

                                                Forms\Components\TimePicker::make('end_time')
                                                    ->label('End Time')
                                                    ->seconds(false),
                                            ]),
                                        ])->visible(fn (\Filament\Forms\Get $get) => $get('is_recurring')),
                                    ]),
                            ]),
                    ])->columnSpanFull(),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Current Status')
                    ->badge()
                    ->getStateUsing(function ($record) {
                        if (!$record->is_active) return 'Disabled';
                        
                        $now = now();
                        if (!$record->is_recurring) {
                            if ($record->end_date && $now->startOfDay()->gt($record->end_date->startOfDay())) {
                                return 'Expired';
                            }
                            if ($record->start_date && $now->startOfDay()->lt($record->start_date->startOfDay())) {
                                return 'Scheduled';
                            }
                        }

                        if (!$record->isValidNow()) return 'Outside Schedule';
                        
                        return 'Active';
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Active' => 'success',
                        'Disabled' => 'gray',
                        'Expired' => 'danger',
                        'Scheduled' => 'warning',
                        'Outside Schedule' => 'info',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'percentage' => 'info',
                        'fixed' => 'success',
                        default => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('value')
                    ->formatStateUsing(fn ($record) => $record->type === 'percentage' ? $record->value . '%' : 'Rp ' . number_format($record->value, 0, ',', '.'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->placeholder('Automatic')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('Valid Until')
                    ->date('d M Y')
                    ->placeholder('Always Active')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('total_usage')
                    ->label('Used')
                    ->suffix(fn ($record) => $record->usage_limit ? " / " . $record->usage_limit : ""),
            ])
            ->filters([
                //
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDiscounts::route('/'),
            'create' => Pages\CreateDiscount::route('/create'),
            'edit' => Pages\EditDiscount::route('/{record}/edit'),
        ];
    }
}
