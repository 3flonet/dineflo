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
                            ->label('Harga Bulanan')
                            ->required()
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                            ->formatStateUsing(fn ($state) => $state ? number_format((float) $state, 0, ',', '.') : null)
                            ->dehydrateStateUsing(fn ($state) => (float) str_replace('.', '', $state ?? 0))
                            ->helperText('Harga yang ditagih setiap bulan.'),
                        Forms\Components\TextInput::make('duration_days')
                            ->required()
                            ->numeric()
                            ->default(30)
                            ->suffix('days'),

                        // Row: has_yearly (kiri) | yearly_price (kanan, conditional)
                        Forms\Components\Toggle::make('has_yearly')
                            ->label('Aktifkan Periode Tahunan')
                            ->helperText('Jika aktif, tampilkan opsi bayar tahunan dengan harga khusus di landing page.')
                            ->default(false)
                            ->onColor('success')
                            ->live(),
                        Forms\Components\TextInput::make('yearly_price')
                            ->label('Harga Tahunan')
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                            ->formatStateUsing(fn ($state) => $state ? number_format((float) $state, 0, ',', '.') : null)
                            ->dehydrateStateUsing(fn ($state) => $state ? (float) str_replace('.', '', $state) : null)
                            ->helperText(function (Forms\Get $get) {
                                $monthly = (float) str_replace('.', '', $get('price') ?? 0);
                                $yearly  = (float) str_replace('.', '', $get('yearly_price') ?? 0);
                                if ($monthly > 0 && $yearly > 0) {
                                    $saving = round((($monthly * 12 - $yearly) / ($monthly * 12)) * 100);
                                    return $saving > 0 ? "💰 Pelanggan hemat {$saving}% dibanding bayar bulanan." : 'Masukkan harga tahunan lebih rendah dari harga bulanan × 12.';
                                }
                                return 'Contoh: jika bulanan Rp 175.000, tahunan bisa Rp 1.750.000 (hemat ~17%).';
                            })
                            ->visible(fn (Forms\Get $get) => (bool) $get('has_yearly'))
                            ->required(fn (Forms\Get $get) => (bool) $get('has_yearly')),

                        // Baris bawah: toggle-toggle
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
                        Forms\Components\Toggle::make('is_active')
                            ->label('Is active')
                            ->required()
                            ->default(true)
                            ->onColor('success')
                            ->columnSpanFull(),
                    ])->columns(2),


                Forms\Components\Section::make('Features & Limits')
                    ->schema([
                        Forms\Components\CheckboxList::make('features')
                            ->hintAction(
                                Forms\Components\Actions\Action::make('feature_guide')
                                    ->label('Bantuan Fitur')
                                    ->icon('heroicon-o-information-circle')
                                    ->modalHeading('Penjelasan Fungsi Fitur')
                                    ->modalWidth('4xl')
                                    ->modalSubmitAction(false)
                                    ->modalCancelAction(false)
                                    ->modalContent(fn () => view('filament.admin.modals.features-guide'))
                            )
                            ->options(function (Forms\Get $get) {
                                $allFeatures = \App\Models\AppFeature::orderBy('title')->get();
                                $selected = (array) ($get('features') ?? []);
                                $siteName = app(\App\Settings\GeneralSettings::class)->site_name ?? config('app.name', 'Dineflo');
                                
                                // 1. Map Hubungan Induk -> Anak
                                $hierarchy = [
                                    'Payment Gateway'           => ['Payment Gateway Withdraw'],
                                    'Payment Gateway Withdraw'  => ['Admin Fee Withdraw'],
                                    'Multi-Restaurant HQ'       => ['Dashboard HQ (Franchise)'],
                                    'Membership & Loyalty'      => ['WhatsApp Marketing', 'Email Marketing'],
                                    'Stock Guard Real-time'     => ['Food Cost & Recipe Insight'],
                                ];

                                // 2. Tentukan urutan manual agar Anak selalu di bawah Induk
                                $orderMap = [
                                    'Payment Gateway'           => 10,
                                    'Payment Gateway Withdraw'  => 11,
                                    'Admin Fee Withdraw'        => 12,
                                    'Multi-Restaurant HQ'       => 20,
                                    'Dashboard HQ (Franchise)'  => 21,
                                    'Membership & Loyalty'      => 30,
                                    'WhatsApp Marketing'        => 31,
                                    'Email Marketing'           => 32,
                                    'Stock Guard Real-time'     => 40,
                                    'Food Cost & Recipe Insight'=> 41,
                                ];

                                // 3. Filter pilihan berdasarkan Induk yang terceklis
                                $filteredFeatures = $allFeatures->reject(function ($f) use ($hierarchy, $selected) {
                                    foreach ($hierarchy as $parent => $children) {
                                        if (!in_array($parent, $selected) && in_array($f->title, $children)) {
                                            return true;
                                        }
                                    }
                                    return false;
                                });

                                // 4. Tambahkan Indentasi Visual/Symbol & Tooltip Icon ⓘ
                                $finalOptions = [];
                                foreach ($filteredFeatures as $feature) {
                                    $isChild = false;
                                    foreach($hierarchy as $p => $cs) if(in_array($feature->title, $cs)) $isChild = true;
                                    
                                    $label = ($isChild ? '↳ ' : '') . $feature->title;
                                    
                                    // Dynamize short description for tooltip
                                    $tooltipText = str_replace([':site_name', 'Dineflo'], $siteName, $feature->short_description ?? '');

                                    // Bikin HTML label dengan Ikon ⓘ yang punya tooltip bawaan browser
                                    $htmlLabel = new \Illuminate\Support\HtmlString(
                                        $label . ' <span class="text-gray-400 cursor-help ml-0.5" title="' . e($tooltipText) . '">ⓘ</span>'
                                    );
                                    
                                    $finalOptions[$feature->title] = $htmlLabel;
                                }

                                // 5. Sortir akhir berdasarkan orderMap + default abjad untuk sisanya
                                uksort($finalOptions, function($a, $b) use ($orderMap) {
                                    $posA = $orderMap[$a] ?? 999;
                                    $posB = $orderMap[$b] ?? 999;
                                    if ($posA === $posB) return strcasecmp($a, $b);
                                    return $posA <=> $posB;
                                });

                                return $finalOptions;
                            })
                            ->allowHtml() // Gunakan allowHtml agar ikon ⓘ terbaca
                            ->columns(3)
                            ->helperText('Select features visible to customers. Use "Bantuan Fitur" above for detailed explanation.')
                            ->live() // Reaktivitas instan
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                $features = (array) ($get('features') ?? []);
                                $originalCount = count($features);

                                // Aturan Pembersihan Berjenjang
                                $dependencies = [
                                    'Payment Gateway'           => ['Payment Gateway Withdraw', 'Admin Fee Withdraw'],
                                    'Payment Gateway Withdraw'  => ['Admin Fee Withdraw'],
                                    'Multi-Restaurant HQ'       => ['Dashboard HQ (Franchise)'],
                                    'Membership & Loyalty'      => ['WhatsApp Marketing', 'Email Marketing'],
                                    'Stock Guard Real-time'     => ['Food Cost & Recipe Insight'],
                                ];

                                foreach ($dependencies as $parent => $children) {
                                    if (!in_array($parent, $features)) {
                                        foreach ($children as $child) {
                                            if (($key = array_search($child, $features)) !== false) {
                                                unset($features[$key]);
                                            }
                                        }
                                    }
                                }

                                if (!in_array('Membership & Loyalty', $features)) {
                                    $set('limits.max_members', 0);
                                }

                                if (count($features) !== $originalCount) {
                                    $set('features', array_values($features));
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
                    ->label('Harga Bulanan')
                    ->money('IDR')
                    ->sortable()
                    ->description(fn ($record) => $record->has_yearly
                        ? 'Tahunan: Rp '.number_format($record->yearly_price, 0, ',', '.').' (hemat '.$record->yearlySavingsPercent().'%)'
                        : 'Belum ada harga tahunan'),
                Tables\Columns\IconColumn::make('has_yearly')
                    ->label('Ada Harga Tahunan')
                    ->boolean()
                    ->trueIcon('heroicon-o-calendar-days')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('success')
                    ->falseColor('gray'),
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
