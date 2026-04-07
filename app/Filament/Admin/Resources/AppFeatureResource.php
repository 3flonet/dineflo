<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AppFeatureResource\Pages;
use App\Models\AppFeature;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class AppFeatureResource extends Resource
{
    protected static ?string $model = AppFeature::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    
    protected static ?string $navigationLabel = 'Fitur Aplikasi';

    protected static ?string $navigationGroup = 'SISTEM';

    protected static ?int $navigationSort = 50;

    protected static function getCategoryOptions(): array
    {
        $categories = [
            'order' => ['label' => 'Pemesanan', 'icon' => 'shopping-cart'],
            'kitchen' => ['label' => 'Dapur & KDS', 'icon' => 'hat-chef'],
            'pos' => ['label' => 'POS & Kasir', 'icon' => 'credit-card'],
            'kiosk' => ['label' => 'Kiosk', 'icon' => 'smartphone'],
            'loyalty' => ['label' => 'Loyalitas', 'icon' => 'heart'],
            'finance' => ['label' => 'Keuangan', 'icon' => 'cash-register'],
            'analytics' => ['label' => 'Analitik', 'icon' => 'stats'],
            'notif' => ['label' => 'Notifikasi', 'icon' => 'bell-concierge'],
            'pwa' => ['label' => 'PWA', 'icon' => 'smartphone'],
            'support' => ['label' => 'Bantuan', 'icon' => 'settings'],
            'admin' => ['label' => 'Admin', 'icon' => 'settings'],
        ];

        return collect($categories)->mapWithKeys(function ($meta, $name) {
            $svgPath = public_path("vendor/uicons-regular-rounded/svg/fi-rr-{$meta['icon']}.svg");
            $svgHtml = '';
            if (file_exists($svgPath)) {
                $svg = file_get_contents($svgPath);
                $svg = preg_replace('/<\?xml.*?\?>/i', '', $svg);
                $svg = str_replace('<svg ', '<svg class="w-5 h-5 inline-block mr-2" style="width:20px; height:20px; display:inline-block; vertical-align:middle; fill:#6366f1;" ', $svg);
                $svgHtml = '<div class="flex items-center gap-2">' . $svg . ' <span>' . $meta['label'] . '</span></div>';
            } else {
                $svgHtml = $meta['label'];
            }
            return [$name => $svgHtml];
        })->toArray();
    }

    protected static function getIconOptions(): array
    {
        // Berikan ikon umum sebagai pilihan awal agar tidak kosong saat baru dibuka
        $commonIcons = [
            'star' => 'Bintang / Unggulan',
            'check' => 'Centang',
            'shopping-cart' => 'Keranjang Belanja',
            'utensils' => 'Alat Makan',
            'credit-card' => 'Kartu Kredit',
            'qrcode' => 'QR Code',
            'smartphone' => 'Smartphone',
            'heart' => 'Favorit',
            'settings' => 'Pengaturan',
        ];

        return collect($commonIcons)->mapWithKeys(function ($label, $name) {
            $svgPath = public_path("vendor/uicons-regular-rounded/svg/fi-rr-{$name}.svg");
            $svgHtml = '';
            if (file_exists($svgPath)) {
                $svg = file_get_contents($svgPath);
                $svg = str_replace('<svg ', '<svg class="w-5 h-5 inline-block mr-2" style="width:20px; height:20px; display:inline-block; vertical-align:middle; fill:#0ea5e9;" ', $svg);
                $svgHtml = '<div class="flex items-center gap-2">' . $svg . ' <span>' . $label . '</span></div>';
            } else {
                $svgHtml = $label;
            }
            return [$name => $svgHtml];
        })->toArray();
    }

    protected static function renderIconHtml($name, $color = '#0ea5e9'): string
    {
        $svgPath = public_path("vendor/uicons-regular-rounded/svg/fi-rr-{$name}.svg");
        if (file_exists($svgPath)) {
            $label = ucwords(str_replace(['fi-rr-', '.svg', '-'], ['', '', ' '], $name));
            $svg = file_get_contents($svgPath);
            $svg = preg_replace('/<\?xml.*?\?>/i', '', $svg);
            $svg = str_replace('<svg ', '<svg class="w-5 h-5 inline-block mr-2" style="width:20px; height:20px; display:inline-block; vertical-align:middle; fill:'.$color.';" ', $svg);
            return '<div class="flex items-center gap-2">' . $svg . ' <span>' . $label . '</span></div>';
        }
        return $name;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Dasar')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('tab')
                                    ->label('Kategori (Tab)')
                                    ->options(static::getCategoryOptions())
                                    ->allowHtml()
                                    ->searchable()
                                    ->required(),
                                Forms\Components\TextInput::make('title')
                                    ->label('Judul Fitur')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                                Forms\Components\TextInput::make('slug')
                                    ->label('URL Slug')
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->unique(AppFeature::class, 'slug', ignoreRecord: true),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('badge')
                                    ->label('Badge')
                                    ->options([
                                        'Premium' => 'Premium',
                                        'Standar' => 'Standar',
                                    ])
                                    ->default('Standar')
                                    ->required(),
                                Forms\Components\TextInput::make('order_index')
                                    ->label('Urutan Tampil')
                                    ->numeric()
                                    ->default(0),
                            ]),
                        Forms\Components\Toggle::make('is_active')
                                ->label('Status Aktif')
                                ->default(true),
                        ]),

                Forms\Components\Section::make('Konten & Deskripsi')
                    ->schema([
                        Forms\Components\Textarea::make('short_description')
                            ->label('Deskripsi Singkat (Card)')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Repeater::make('bullets')
                            ->label('Poin-Poin Fitur')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('bullet')
                                            ->label('Poin Detail')
                                            ->required(),
                                        Forms\Components\Select::make('icon')
                                            ->label('Pilih Ikon (SVG)')
                                            ->options(static::getIconOptions())
                                            ->getSearchResultsUsing(function (string $search) {
                                                $path = public_path("vendor/uicons-regular-rounded/svg");
                                                $files = glob($path . "/fi-rr-*{$search}*.svg");
                                                
                                                return collect($files)->take(30)->mapWithKeys(function ($file) {
                                                    $name = str_replace(['fi-rr-', '.svg'], '', basename($file));
                                                    return [$name => static::renderIconHtml($name)];
                                                })->toArray();
                                            })
                                            ->getOptionLabelUsing(fn ($value) => static::renderIconHtml($value))
                                            ->allowHtml()
                                            ->searchable()
                                            ->placeholder('Cari di antara 4000+ ikon...')
                                            ->required(),
                                    ]),
                            ])
                            ->afterStateHydrated(function ($component, $state) {
                                if (is_array($state) && isset($state[0]) && !is_array($state[0])) {
                                    $component->state(collect($state)->map(fn($item) => ['bullet' => $item, 'icon' => 'star'])->toArray());
                                }
                            })
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('long_description')
                            ->label('Penjelasan Lengkap (Detail Page)')
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('image_url')
                            ->label('Gambar Utama / Screenshot')
                            ->image()
                            ->directory('features'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tab')
                    ->label('Kategori')
                    ->formatStateUsing(fn (?string $state): \Illuminate\Support\HtmlString => new \Illuminate\Support\HtmlString(
                        (string) collect(static::getCategoryOptions())->get($state, $state ?? '-')
                    ))
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Nama Fitur')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('badge')
                    ->label('Badge')
                    ->badge()
                    ->color(fn ($state) => $state === 'Premium' ? 'warning' : 'info'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('order_index')
                    ->label('Urutan')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tab')
                    ->label('Kategori')
                    ->options([
                        'order' => 'Pemesanan',
                        'kitchen' => 'Dapur & KDS',
                        'pos' => 'POS & Kasir',
                        'kiosk' => 'Kiosk',
                        'loyalty' => 'Loyalitas',
                        'finance' => 'Keuangan',
                        'analytics' => 'Analitik',
                        'notif' => 'Notifikasi',
                        'pwa' => 'PWA',
                        'support' => 'Bantuan',
                        'admin' => 'Admin',
                    ]),
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
            ->reorderable('order_index')
            ->defaultSort('order_index');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAppFeatures::route('/'),
        ];
    }
}
