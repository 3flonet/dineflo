<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SubscriptionPlanResource\Pages;
use App\Filament\Admin\Resources\SubscriptionPlanResource\RelationManagers;
use App\Models\SubscriptionPlan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\RawJs;

class SubscriptionPlanResource extends Resource
{
    protected static ?string $model = SubscriptionPlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Plan Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('description')
                            ->label('Tagline Paket')
                            ->placeholder('Contoh: Cocok untuk UMKM, Ideal untuk multi-cabang')
                            ->helperText('Teks pendek yang tampil di bawah nama paket di landing page.')
                            ->maxLength(120)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                            ->formatStateUsing(fn ($state) => $state ? number_format((float) $state, 0, ',', '.') : null)
                            ->dehydrateStateUsing(fn ($state) => (float) str_replace('.', '', $state ?? 0)),
                        Forms\Components\TextInput::make('duration_days')
                            ->required()
                            ->numeric()
                            ->default(30)
                            ->suffix('days'),
                        Forms\Components\Select::make('billing_period')
                            ->label('Periode Billing')
                            ->options(['monthly' => 'Bulanan', 'yearly' => 'Tahunan'])
                            ->default('monthly')
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->required()
                            ->default(true),
                        Forms\Components\Toggle::make('is_highlighted')
                            ->label('Tampilkan Badge "Paling Laris"')
                            ->helperText('Aktifkan untuk menampilkan badge "PALING LARIS" di landing page. Disarankan hanya 1 plan.')
                            ->default(false)
                            ->onColor('warning')
                            ->offColor('gray'),
                        Forms\Components\Toggle::make('is_trial')
                            ->label('Jadikan Paket Trial Otomatis')
                            ->helperText('Jika aktif, restoran baru akan otomatis berlangganan paket ini.')
                            ->default(false)
                            ->onColor('success'),
                    ])->columns(2),

                Forms\Components\Section::make('Features & Limits')
                    ->schema([
                        Forms\Components\CheckboxList::make('features')
                            ->options([
                                'Multi-Restaurant Support' => 'Multi-Restaurant Support',
                                // 'Unlimited Menu Items' => 'Unlimited Menu Items', // REMOVED: Redundant with limits.max_menus
                                'QR Code Ordering' => 'QR Code Ordering',
                                'Kitchen Display System' => 'Kitchen Display System',
                                'Waiter Call System' => 'Waiter Call System',
                                'POS System' => 'POS System',
                                'Sales Reports' => 'Sales Reports',
                                'Expense Management' => 'Expense Management (Profit/Loss Tracking)',
                                'Membership & Loyalty' => 'Membership & Loyalty',
                                'WhatsApp Marketing' => 'WhatsApp Marketing (Fonnte/Gateway)',
                                'Email Marketing' => 'Email Marketing (Automation)',
                                'Payment Gateway' => '💳 Payment Gateway (QRIS/E-Wallet Settings)',
                                'Payment Gateway Withdraw' => '💰 Payment Gateway Withdraw (Saldo & Penarikan Dana)',
                                'Inventory Level 2' => 'Inventory Level 2 (Recipe Management)',
                                'Table Reservation' => 'Table Reservation & Waitlist',
                                'Priority Support' => 'Priority Support',
                                'Remove Branding' => 'Remove Branding',
                                'Smart Upselling' => 'Smart Upselling (Recommendations)',
                                'Kiosk Mode' => 'Kiosk Mode (Self-Service)',
                                'Split Bill' => 'Split Bill (Partial Payment)',
                                'Dynamic Pricing' => 'Dynamic Pricing (Discounts & Happy Hour)',
                                'Voucher & Marketing' => 'Voucher & Marketing (Promo Codes & Targeting)',
                                'Profit Margin Insights' => 'Profit Margin Insights (Menu Engineering)',
                                'Customer Feedback & Ratings' => 'Customer Feedback & Ratings (Engagement)',
                                'Feedback Reward Automation' => 'Feedback Reward Automation (Auto Point/Voucher)',
                                'Advanced Kitchen Analytics' => 'Advanced Kitchen Analytics (KDS Performance)',
                                'Staff Performance Tracking' => 'Staff Performance Tracking',
                                'Refund Handling' => 'Refund Handling & Audit Trail',
                                'Loss Prevention' => 'Loss Prevention Tools',
                                'Cash Drawer Integration' => 'Cash Drawer Integration',
                                'Gift Cards' => '🎁 Gift Cards (Digital Voucher Hadiah)',
                                'EDC Integration' => '💳 EDC Payment Integration (Bank & MDR Config)',
                                'Dashboard HQ' => 'Dashboard HQ (Franchise View)',
                                'Queue Management System' => 'Queue Management System (TV Display & Waitlist)',
                            ])
                            ->columns(2)
                            ->helperText('Select features visible to customers')
                            ->live() // Aktifkan reaktivitas
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                // Jika 'Membership & Loyalty' TIDAK dipilih, reset max_members ke 0
                                $features = $get('features') ?? [];
                                if (!in_array('Membership & Loyalty', $features)) {
                                    $set('limits.max_members', 0);
                                }
                            }),
                        
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Placeholder::make('limits_header')
                                    ->label('Usage Limits')
                                    ->content('Set limits for this plan. Use -1 for unlimited access.'),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('limits.max_restaurants')
                                            ->label('Max Restaurants')
                                            ->numeric()
                                            ->default(1)
                                            ->required()
                                            ->helperText('Maximum number of restaurants allowed.'),
                                        
                                        Forms\Components\TextInput::make('limits.max_menus')
                                            ->label('Max Menu Items')
                                            ->numeric()
                                            ->default(50)
                                            ->required()
                                            ->helperText('Per restaurant.'),
                                        
                                        Forms\Components\TextInput::make('limits.max_orders')
                                            ->label('Max Orders (Monthly)')
                                            ->numeric()
                                            ->default(500)
                                            ->required()
                                            ->helperText('Total orders processing per month.'),

                                        Forms\Components\TextInput::make('limits.max_members')
                                            ->label('Max CRM Members')
                                            ->numeric()
                                            ->default(0) // Default 0 jika tidak aktif
                                            ->required()
                                            ->visible(fn (Forms\Get $get) => in_array('Membership & Loyalty', $get('features') ?? [])) // Hanya muncul jika feature aktif
                                            ->helperText('Maximum customers in loyalty program.'),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->description),
                Tables\Columns\TextColumn::make('price')
                    ->money('IDR')
                    ->sortable()
                    ->description(fn ($record) => $record->billing_period === 'yearly' ? 'per tahun' : 'per bulan'),
                Tables\Columns\TextColumn::make('duration_days')
                    ->numeric()
                    ->sortable()
                    ->suffix(' days'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_highlighted')
                    ->label('Paling Laris')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('warning')
                    ->falseColor('gray'),
                Tables\Columns\IconColumn::make('is_trial')
                    ->label('Trial')
                    ->boolean()
                    ->trueIcon('heroicon-o-gift')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('success')
                    ->falseColor('gray'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscriptionPlans::route('/'),
            'create' => Pages\CreateSubscriptionPlan::route('/create'),
            'edit' => Pages\EditSubscriptionPlan::route('/{record}/edit'),
        ];
    }
}
