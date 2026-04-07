<?php

namespace App\Filament\Restaurant\Pages\Tenancy;

use Filament\Forms\Form;
use Filament\Pages\Tenancy\EditTenantProfile;
use Filament\Forms;
use Illuminate\Database\Eloquent\Model;
use Filament\Support\RawJs;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class EditRestaurantProfile extends EditTenantProfile
{
    protected function getSiteName(): string
    {
        try {
            return app(\App\Settings\GeneralSettings::class)->site_name;
        } catch (\Throwable $e) {
            return config('app.name', 'Dineflo');
        }
    }

    public static function getLabel(): string
    {
        return 'Profil Restoran';
    }

    protected static ?string $navigationGroup = 'PENGATURAN TOKO';
    protected static ?int $navigationSort = 0;
    protected static ?string $navigationLabel = 'Profil Restoran';
    protected static ?string $navigationIcon = 'heroicon-o-home-modern';
    
    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }
    
    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        if ($user->hasRole(['super_admin', 'restaurant_owner'])) {
            return true;
        }

        return $user->can('page_EditRestaurantProfile');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Restaurant Settings')
                    ->tabs([
                        // Tab 1: General Info & Contact
                        Forms\Components\Tabs\Tab::make('General')
                            ->icon('heroicon-m-information-circle')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('slug')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->maxLength(255),
                                        Forms\Components\Select::make('currency')
                                            ->options([
                                                'IDR' => 'IDR - Rupiah Indonesia',
                                                'USD' => 'USD - US Dollar',
                                                'SGD' => 'SGD - Singapore Dollar',
                                            ])
                                            ->default('IDR')
                                            ->required(),
                                        Forms\Components\TextInput::make('email')->email(),
                                        Forms\Components\TextInput::make('phone')->tel(),
                                        Forms\Components\TextInput::make('city'),
                                    ]),
                                Forms\Components\Textarea::make('address')
                                    ->rows(2),
                                Forms\Components\Textarea::make('description')
                                    ->rows(3),
                                Forms\Components\Textarea::make('google_map_embed')
                                    ->label('Google Map Embed (iFrame)')
                                    ->placeholder('<iframe src="https://www.google.com/maps/embed?..."></iframe>')
                                    ->helperText('Paste kode embed iframe HTML dari Google Maps di sini.')
                                    ->rows(4),
                                
                                Forms\Components\Section::make('Sosial Media')
                                    ->description('Tambahkan semua link sosial media restoran Anda.')
                                    ->schema([
                                        Forms\Components\Repeater::make('social_links')
                                            ->label('')
                                            ->schema([
                                                Forms\Components\Select::make('platform')
                                                    ->options([
                                                        'instagram' => 'Instagram',
                                                        'facebook' => 'Facebook',
                                                        'tiktok' => 'TikTok',
                                                        'twitter' => 'Twitter/X',
                                                        'youtube' => 'YouTube',
                                                        'website' => 'Website Lainnya',
                                                    ])
                                                    ->required()
                                                    ->prefixIcon('heroicon-m-globe-alt'),
                                                Forms\Components\TextInput::make('url')
                                                    ->label('URL')
                                                    ->url()
                                                    ->required()
                                                    ->placeholder('https://...')
                                                    ->prefixIcon('heroicon-m-link'),
                                            ])
                                            ->columns(2)
                                            ->addActionLabel('Tambah Sosial Media')
                                            ->defaultItems(0)
                                            ->reorderableWithButtons()
                                    ]),
                            ]),

                        // Tab 2: Opening Hours
                        Forms\Components\Tabs\Tab::make('Operations')
                            ->icon('heroicon-m-clock')
                            ->schema([
                                Forms\Components\Repeater::make('opening_hours')
                                    ->label('Opening Hours')
                                    ->helperText('Set your shop opening hours for each day.')
                                    ->schema([
                                        Forms\Components\Select::make('day')
                                            ->options([
                                                'monday' => 'Monday',
                                                'tuesday' => 'Tuesday',
                                                'wednesday' => 'Wednesday',
                                                'thursday' => 'Thursday',
                                                'friday' => 'Friday',
                                                'saturday' => 'Saturday',
                                                'sunday' => 'Sunday',
                                            ])
                                            ->required()
                                            ->dehydrated(), // Pastikan tersimpan
                                        Forms\Components\TimePicker::make('open')
                                            ->required()
                                            ->hidden(fn (Forms\Get $get) => $get('is_closed')),
                                        Forms\Components\TimePicker::make('close')
                                            ->required()
                                            ->hidden(fn (Forms\Get $get) => $get('is_closed')),
                                        Forms\Components\Toggle::make('is_closed')
                                            ->label('Closed')
                                            ->live(),
                                    ])
                                    ->columns(4)
                                    ->afterStateHydrated(function (Forms\Components\Repeater $component, $state) {
                                        if (blank($state)) {
                                            $component->state([
                                                ['day' => 'monday', 'open' => '08:00', 'close' => '22:00', 'is_closed' => false],
                                                ['day' => 'tuesday', 'open' => '08:00', 'close' => '22:00', 'is_closed' => false],
                                                ['day' => 'wednesday', 'open' => '08:00', 'close' => '22:00', 'is_closed' => false],
                                                ['day' => 'thursday', 'open' => '08:00', 'close' => '22:00', 'is_closed' => false],
                                                ['day' => 'friday', 'open' => '08:00', 'close' => '22:00', 'is_closed' => false],
                                                ['day' => 'saturday', 'open' => '08:00', 'close' => '22:00', 'is_closed' => false],
                                                ['day' => 'sunday', 'open' => '08:00', 'close' => '22:00', 'is_closed' => false],
                                            ]);
                                        }
                                    })
                                    ->addable(false)
                                    ->deletable(false)
                                    ->reorderable(false),
                            ]),

                        // Tab 3: Branding
                        Forms\Components\Tabs\Tab::make('Branding')
                            ->icon('heroicon-m-photo')
                            ->visible(fn () => auth()->user()?->hasFeature('Remove Branding') ?? false)
                            ->schema([
                                Forms\Components\FileUpload::make('logo')
                                    ->label('Logo Utama (Bebas/Horizontal)')
                                    ->image()
                                    ->imageEditor()
                                    ->imageResizeTargetWidth('600')
                                    ->maxSize(2048)
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->disk('public')
                                    ->directory('restaurants/logos')
                                    ->visibility('public')
                                    ->imagePreviewHeight('100'),
                                Forms\Components\FileUpload::make('logo_square')
                                    ->label('Logo Kotak (Opsional)')
                                    ->helperText('Logo kecil rasio 1:1 untuk ikon di halaman restoran. Jika tidak diisi, akan otomatis menampilkan inisial restoran.')
                                    ->image()
                                    ->imageEditor()
                                    ->imageResizeTargetWidth('300')
                                    ->imageResizeMode('cover')
                                    ->maxSize(2048)
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->disk('public')
                                    ->directory('restaurants/logos_square')
                                    ->visibility('public')
                                    ->imagePreviewHeight('100'),
                                Forms\Components\FileUpload::make('cover_image')
                                    ->image()
                                    ->imageEditor()
                                    ->imageResizeTargetWidth('1200')
                                    ->maxSize(2048)
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->disk('public')
                                    ->directory('restaurants/covers')
                                    ->visibility('public'),
                            ]),

                        // Tab 4: Loyalty System
                        Forms\Components\Tabs\Tab::make('Loyalty System')
                            ->icon('heroicon-m-gift')
                            ->visible(fn () => auth()->user()?->hasFeature('Membership & Loyalty') ?? false)
                            ->schema([
                                Forms\Components\TextInput::make('loyalty_point_rate')
                                    ->label('Nilai Poin (Rp)')
                                    ->prefix('Rp')
                                    ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                                    ->formatStateUsing(fn ($state) => $state ? number_format((float) $state, 0, ',', '.') : null)
                                    ->dehydrateStateUsing(fn ($state) => (float) str_replace('.', '', $state ?? 0))
                                    ->default(1000)
                                    ->suffix('per 1 Poin')
                                    ->helperText('Contoh: Isi 1000 agar belanja Rp 1.000 dapat 1 Poin.'),
                                
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('loyalty_silver_threshold')
                                            ->label('Ambang Batas Silver')
                                            ->prefix('Rp')
                                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                                            ->formatStateUsing(fn ($state) => $state ? number_format((float) $state, 0, ',', '.') : null)
                                            ->dehydrateStateUsing(fn ($state) => (float) str_replace('.', '', $state ?? 0))
                                            ->default(1000000)
                                            ->helperText('Total belanja minimal untuk jadi member Silver.'),
                                        
                                        Forms\Components\TextInput::make('loyalty_gold_threshold')
                                            ->label('Ambang Batas Gold')
                                            ->prefix('Rp')
                                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                                            ->formatStateUsing(fn ($state) => $state ? number_format((float) $state, 0, ',', '.') : null)
                                            ->dehydrateStateUsing(fn ($state) => (float) str_replace('.', '', $state ?? 0))
                                            ->default(5000000)
                                            ->helperText('Total belanja minimal untuk jadi member Gold.'),
                                    ]),

                                Forms\Components\Section::make('Point Redemption (Belanja Pakai Poin)')
                                    ->description('Atur apakah member bisa membayar menggunakan poin mereka.')
                                    ->schema([
                                        Forms\Components\Toggle::make('loyalty_redemption_enabled')
                                            ->label('Aktifkan Pembayaran Pakai Poin')
                                            ->helperText('Jika aktif, kasir bisa memotong tagihan menggunakan saldo poin member.')
                                            ->live(),
                                        
                                        Forms\Components\TextInput::make('loyalty_point_redemption_value')
                                            ->label('Nilai Tukar 1 Poin (Rp)')
                                            ->prefix('Rp')
                                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                                            ->formatStateUsing(fn ($state) => $state ? number_format((float) $state, 0, ',', '.') : null)
                                            ->dehydrateStateUsing(fn ($state) => (float) str_replace('.', '', $state ?? 0))
                                            ->default(100)
                                            ->visible(fn (Forms\Get $get) => $get('loyalty_redemption_enabled'))
                                            ->helperText('Contoh: Jika diisi 100, berarti 10 Poin bernilai Rp 1.000 potongan.'),
                                    ]),

                                Forms\Components\Section::make('Customer Feedback Reward')
                                    ->description('Berikan hadiah otomatis kepada pelanggan yang memberikan ulasan produk.')
                                    ->visible(fn () => auth()->user()?->hasFeature('Feedback Reward Automation') && 
                                        (auth()->user()?->can('manage_feedback_rewards') || auth()->user()?->hasRole('restaurant_owner')))
                                    ->schema([
                                        Forms\Components\Toggle::make('feedback_reward_enabled')
                                            ->label('Aktifkan Hadiah Ulasan')
                                            ->helperText('Jika aktif, pelanggan yang mengisi feedback akan mendapatkan hadiah otomatis.')
                                            ->live(),
                                        
                                        Forms\Components\Grid::make(2)
                                            ->visible(fn (Forms\Get $get) => $get('feedback_reward_enabled'))
                                            ->schema([
                                                Forms\Components\Select::make('feedback_reward_type')
                                                    ->label('Tipe Hadiah')
                                                    ->options([
                                                        'points' => 'Kasih Poin (Loyalty Points)',
                                                        'voucher' => 'Kasih Voucher Diskon',
                                                    ])
                                                    ->required()
                                                    ->live(),
                                                
                                                Forms\Components\Select::make('feedback_notification_channel')
                                                    ->label('Kirim Notifikasi Melalui')
                                                    ->options([
                                                        'whatsapp' => 'WhatsApp Only',
                                                        'email' => 'Email Only',
                                                        'both' => 'WhatsApp & Email',
                                                    ])
                                                    ->required(),

                                                Forms\Components\TextInput::make('feedback_reward_points')
                                                    ->label('Jumlah Poin')
                                                    ->numeric()
                                                    ->default(10)
                                                    ->required()
                                                    ->visible(fn (Forms\Get $get) => $get('feedback_reward_type') === 'points'),

                                                Forms\Components\Select::make('feedback_reward_discount_id')
                                                    ->label('Pilih Voucher')
                                                    ->options(fn () => \App\Models\Discount::where('restaurant_id', \Filament\Facades\Filament::getTenant()->id)
                                                        ->where('is_active', true)
                                                        ->pluck('name', 'id'))
                                                    ->required()
                                                    ->searchable()
                                                    ->visible(fn (Forms\Get $get) => $get('feedback_reward_type') === 'voucher'),
                                            ]),
                                    ]),
                            ]),
                        
                        // Tab 5: WhatsApp Gateway
                        Forms\Components\Tabs\Tab::make('WhatsApp Gateway')
                            ->icon('heroicon-m-chat-bubble-left-right')
                            ->visible(fn () => auth()->user()?->hasFeature('WhatsApp Marketing') && 
                                (auth()->user()?->can('manage_whatsapp_settings') || auth()->user()?->hasRole('restaurant_owner')))
                            ->schema([
                                Forms\Components\Toggle::make('wa_is_active')
                                    ->label('Enable WhatsApp Integration')
                                    ->helperText('Enable or disable sending notifications via WhatsApp.')
                                    ->default(false),
                                
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('wa_provider')
                                            ->label('WhatsApp Provider')
                                            ->options([
                                                'fonnte' => 'Fonnte',
                                                // 'wablas' => 'Wablas (Coming Soon)',
                                            ])
                                            ->default('fonnte')
                                            ->required(fn (Forms\Get $get) => $get('wa_is_active')),
                                        
                                        Forms\Components\TextInput::make('wa_number')
                                            ->label('Sender Phone Number')
                                            ->placeholder('e.g. 628123456789')
                                            ->helperText('Your registered sender number in vendor panel.'),
                                    ]),

                                Forms\Components\TextInput::make('wa_api_key')
                                    ->label('API Key / Token')
                                    ->password()
                                    ->revealable()
                                    ->required(fn (Forms\Get $get) => $get('wa_is_active'))
                                    ->helperText('Get this token from your WhatsApp vendor dashboard.'),
                            ]),

                        // Tab: Pajak & Biaya Tambahan
                        Forms\Components\Tabs\Tab::make('Pajak & Biaya Tambahan')
                            ->icon('heroicon-m-receipt-percent')
                            ->visible(fn () => auth()->user()?->can('manage_payment_settings') || auth()->user()?->hasRole('restaurant_owner'))
                            ->schema([
                                Forms\Components\Section::make('Pajak Restoran (PB1)')
                                    ->description('Aktifkan jika harga menu Anda belum termasuk pajak.')
                                    ->schema([
                                        Forms\Components\Toggle::make('tax_enabled')
                                            ->label('Terapkan Pajak pada Pesanan')
                                            ->live()
                                            ->default(false),
                                        
                                        Forms\Components\TextInput::make('tax_percentage')
                                            ->label('Persentase Pajak (%)')
                                            ->numeric()
                                            ->default(10)
                                            ->visible(fn (Forms\Get $get) => $get('tax_enabled'))
                                            ->required(fn (Forms\Get $get) => $get('tax_enabled'))
                                            ->maxValue(100)
                                            ->minValue(0.1)
                                            ->step(0.1),
                                    ]),

                                Forms\Components\Section::make('Biaya Tambahan Lainnya')
                                    ->description('Tambahkan service charge, platform fee, atau tarif kemasan/takeaway otomatis ke dalam tagihan akhir.')
                                    ->schema([
                                        Forms\Components\Repeater::make('additional_fees')
                                            ->label('')
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->label('Nama Biaya')
                                                    ->placeholder('Cth: Service Charge / Takeaway')
                                                    ->required(),
                                                
                                                Forms\Components\Select::make('type')
                                                    ->label('Tipe')
                                                    ->options([
                                                        'fixed'      => 'Nominal Tetap (Rp)',
                                                        'percentage' => 'Persentase (%)',
                                                    ])
                                                    ->default('percentage')
                                                    ->required()
                                                    ->live(),
                                                
                                                Forms\Components\TextInput::make('value')
                                                    ->label(fn (Forms\Get $get) => $get('type') === 'fixed' ? 'Nilai (Rp)' : 'Nilai (%)')
                                                    ->required()
                                                    ->placeholder(fn (Forms\Get $get) => $get('type') === 'fixed' ? 'Cth: 2.000' : 'Cth: 5')
                                                    ->prefix(fn (Forms\Get $get) => $get('type') === 'fixed' ? 'Rp' : null)
                                                    ->suffix(fn (Forms\Get $get) => $get('type') === 'percentage' ? '%' : null)
                                                    ->mask(fn (Forms\Get $get) => $get('type') === 'fixed'
                                                        ? RawJs::make('$money($input, \',\', \'.\', 0)')
                                                        : null
                                                    )
                                                    ->formatStateUsing(function ($state, Forms\Get $get) {
                                                        if ($get('type') === 'fixed' && $state) {
                                                            return number_format((float) $state, 0, ',', '.');
                                                        }
                                                        return $state;
                                                    })
                                                    ->dehydrateStateUsing(function ($state, Forms\Get $get) {
                                                        if ($get('type') === 'fixed') {
                                                            return (float) str_replace('.', '', $state ?? 0);
                                                        }
                                                        return (float) $state;
                                                    })
                                                    ->minValue(0),
                                            ])
                                            ->columns(3)
                                            ->addActionLabel('Tambah Biaya')
                                            ->defaultItems(0)
                                            ->reorderableWithButtons(),
                                    ]),
                            ]),

                        // Tab 6: Metode Pembayaran
                        Forms\Components\Tabs\Tab::make('Metode Pembayaran')
                            ->icon('heroicon-m-credit-card')
                            ->visible(fn () =>
                                auth()->user()?->can('manage_payment_settings') || auth()->user()?->hasRole('restaurant_owner')
                            )
                            ->schema([
                                Forms\Components\Section::make('Pilihan Metode Pembayaran')
                                    ->description('Pilih metode pembayaran yang tersedia untuk pelanggan Anda.')
                                    ->schema([
                                        Forms\Components\Select::make('payment_mode')
                                            ->label('Aktifkan Metode')
                                            ->options(function() {
                                                $options = ['kasir' => '💵 Kasir (Cash at Counter) saja'];

                                                // Only show digital options if has feature
                                                if (auth()->user()?->hasFeature('Payment Gateway')) {
                                                    $options['gateway'] = '📱 Payment Gateway (QRIS/E-Wallet) saja';
                                                    $options['both'] = '💵📱 Keduanya (Kasir + Payment Gateway)';
                                                }

                                                return $options;
                                            })
                                            ->default('kasir')
                                            ->required()
                                            ->live()
                                            ->helperText(function() {
                                                if (!auth()->user()?->hasFeature('Payment Gateway')) {
                                                    return '✨ Upgrade paket untuk mengaktifkan pembayaran QRIS & E-Wallet secara otomatis.';
                                                }
                                                return 'Tentukan cara pelanggan bisa membayar pesanan mereka.';
                                            }),
                                        
                                        
                                        Forms\Components\Toggle::make('is_online_order_enabled')
                                            ->label('Terima Pesanan Online/Publik (Tanpa Scan QR meja)')
                                            ->helperText('Jika diaktifkan, pengunjung yang membuka profil restoran Anda (via URL) bisa memesan menu tanpa berada di lokasi (Takeaway). Jika dinonaktifkan, halaman menu publik hanya bertindak sebagai katalog digital brosur berjalan.')
                                            ->default(false),

                                        Forms\Components\Section::make('Konfigurasi EDC')
                                            ->description('Tambahkan daftar bank dan persentase MDR untuk mesin EDC Anda.')
                                            ->icon('heroicon-m-credit-card')
                                            ->visible(fn () => auth()->user()?->hasFeature('EDC Integration'))
                                            ->schema([
                                                Forms\Components\Repeater::make('edc_config')
                                                    ->label('Daftar Bank EDC')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('bank_name')
                                                            ->label('Nama Bank')
                                                            ->placeholder('Cth: BCA / Mandiri')
                                                            ->required(),
                                                        Forms\Components\TextInput::make('mdr_percent')
                                                            ->label('MDR Fee (%)')
                                                            ->numeric()
                                                            ->default(0)
                                                            ->step(0.01)
                                                            ->suffix('%')
                                                            ->required()
                                                            ->helperText('Contoh: 0.15 untuk BCA'),
                                                    ])
                                                    ->columns(2)
                                                    ->addActionLabel('Tambah Bank EDC')
                                                    ->defaultItems(0)
                                                    ->reorderableWithButtons(),
                                            ]),
                                    ]),

                                // Sub-opsi Kasir
                                Forms\Components\Section::make('Pengaturan Kasir')
                                    ->description('Atur alur pembayaran di kasir.')
                                    ->icon('heroicon-m-banknotes')
                                    ->visible(fn (Forms\Get $get) => in_array($get('payment_mode'), ['kasir', 'both']))
                                    ->schema([
                                        Forms\Components\Toggle::make('kasir_direct_to_kds')
                                            ->label('Langsung ke Kitchen Display')
                                            ->helperText(
                                                'ON: Order langsung masuk Kitchen Display setelah pelanggan checkout. ' .
                                                'OFF: Order menunggu konfirmasi pembayaran dari kasir terlebih dahulu.'
                                            )
                                            ->default(true)
                                            ->live(),

                                        Forms\Components\Placeholder::make('kasir_info')
                                            ->label('')
                                            ->content(fn (Forms\Get $get) => $get('kasir_direct_to_kds')
                                                ? '✅ Mode aktif: Order langsung masuk ke dapur setelah checkout. Kasir konfirmasi pembayaran belakangan.'
                                                : '⏳ Mode aktif: Order menunggu kasir konfirmasi pembayaran dulu, baru masuk ke dapur.'
                                            ),

                                        Forms\Components\Toggle::make('auto_close_cashier')
                                            ->label('Tutup Kasir Otomatis')
                                            ->helperText('Jika diaktifkan, sesi kasir yang masih terbuka akan ditutup otomatis oleh sistem 1 Jam setelah jam operasional berakhir. Saldo aktual (Closing Cash) akan disamakan dengan ekspektasi sistem.')
                                            ->default(false),
                                    ]),

                                // Sub-opsi Payment Gateway
                                Forms\Components\Section::make('Pengaturan Payment Gateway')
                                    ->description('Atur integrasi pembayaran digital (QRIS, E-Wallet, dll).')
                                    ->icon('heroicon-m-qr-code')
                                    ->visible(fn (Forms\Get $get) => in_array($get('payment_mode'), ['gateway', 'both']))
                                    ->schema([
                                        Forms\Components\Select::make('gateway_mode')
                                            ->label('Sumber Akun Payment Gateway')
                                            ->options(function () {
                                                $options = ['own' => '🔑 Akun Sendiri (Midtrans)'];

                                                // Only allow Dineflo account if they have the Withdraw feature
                                                if (auth()->user()?->hasFeature('Payment Gateway Withdraw')) {
                                                    $options['dineflo'] = '🏦 Gunakan Akun Default ' . $this->getSiteName();
                                                }

                                                return $options;
                                            })
                                            ->required(fn (Forms\Get $get) => in_array($get('payment_mode'), ['gateway', 'both']))
                                            ->live()
                                            ->helperText(function () {
                                                if (! auth()->user()?->hasFeature('Payment Gateway Withdraw')) {
                                                    return "⚠️ Anda hanya bisa menggunakan akun Midtrans sendiri karena paket langganan Anda tidak mendukung sistem saldo/penarikan {$this->getSiteName()}.";
                                                }

                                                return "Pilih apakah Anda menggunakan akun Midtrans sendiri atau akun bawaan {$this->getSiteName()}. Dukungan Xendit akan hadir segera.";
                                            }),

                                        // Sub-opsi: Akun Sendiri
                                        Forms\Components\Fieldset::make('Kredensial Midtrans Anda')
                                            ->visible(fn (Forms\Get $get) => $get('gateway_mode') === 'own')
                                            ->schema([
                                                Forms\Components\TextInput::make('midtrans_client_key')
                                                    ->label('Client Key')
                                                    ->placeholder('SB-Mid-client-xxxx / Mid-client-xxxx')
                                                    ->helperText('Tersedia di Midtrans Dashboard → Pengaturan → Akses')
                                                    ->required(fn (Forms\Get $get) => $get('gateway_mode') === 'own'),

                                                Forms\Components\TextInput::make('midtrans_server_key')
                                                    ->label('Server Key')
                                                    ->password()
                                                    ->revealable()
                                                    ->placeholder('SB-Mid-server-xxxx / Mid-server-xxxx')
                                                    ->helperText('Jangan bagikan Server Key ke siapapun.')
                                                    ->required(fn (Forms\Get $get) => $get('gateway_mode') === 'own'),

                                                Forms\Components\Placeholder::make('midtrans_guide')
                                                    ->label('')
                                                    ->content('💡 Tip: Gunakan Sandbox key untuk testing, Production key untuk transaksi nyata. Anda bisa mengkustomisasi logo & warna QRIS pop-up melalui Midtrans Dashboard → Snap Preferences → Theme & Logo.'),
                                            ]),

                                        // Sub-opsi: Akun Default Dineflo
                                        Forms\Components\Fieldset::make(fn() => 'Informasi Akun Default ' . $this->getSiteName())
                                            ->visible(fn (Forms\Get $get) => $get('gateway_mode') === 'dineflo')
                                            ->schema([
                                                Forms\Components\Placeholder::make('dineflo_gateway_info')
                                                    ->label('')
                                                    ->content(fn() => "ℹ️ Transaksi pembayaran digital akan diproses melalui akun Midtrans resmi {$this->getSiteName()}. Dana hasil penjualan akan terakumulasi sebagai saldo di akun {$this->getSiteName()} Anda dan dapat dicairkan kapan saja melalui menu Penarikan Dana (Withdraw). Tim {$this->getSiteName()} akan memproses transfer manual ke rekening bank Anda dalam 1-3 hari kerja."),

                                                Forms\Components\Placeholder::make('dineflo_fee_info')
                                                    ->label('Ketentuan Pemotongan (Fee)')
                                                    ->content(function () {
                                                        $settings = app(\App\Settings\GeneralSettings::class);
                                                        return new \Illuminate\Support\HtmlString("
                                                            <ul class='list-disc list-inside text-sm text-gray-500 mt-2 space-y-1'>
                                                                <li><b>QRIS / GoPay / ShopeePay:</b> {$settings->midtrans_qris_fee_percentage}% per transaksi</li>
                                                                <li><b>Kartu Kredit:</b> {$settings->midtrans_cc_fee_percentage}% per transaksi</li>
                                                                <li><b>Virtual Account (VA):</b> Rp " . number_format($settings->midtrans_va_fee_flat, 0, ',', '.') . " per transaksi</li>
                                                                <li><b>Minimarket:</b> Rp " . number_format($settings->midtrans_cstore_fee_flat, 0, ',', '.') . " per transaksi</li>
                                                                <li><b>Tambahan Biaya Penarikan (Withdraw):</b> {$settings->dineflo_withdraw_admin_fee_percentage}% dari jumlah penarikan</li>
                                                            </ul>
                                                        ");
                                                    }),

                                                Forms\Components\Checkbox::make('agree_dineflo_fee')
                                                    ->label(fn() => "Saya menyetujui Ketentuan Pemotongan (Fee) di atas dan memahami bahwa biaya tersebut akan dipotong otomatis dari setiap transaksi.")
                                                    ->accepted()
                                                    ->validationMessages([
                                                        'accepted' => fn() => "Anda wajib menyetujui Ketentuan Pemotongan (Fee) untuk menggunakan akun Default {$this->getSiteName()}.",
                                                    ])
                                                    ->dehydrated(false)
                                                    ->required(),

                                                Forms\Components\Placeholder::make('dineflo_balance_info')
                                                    ->label('Saldo Tersedia')
                                                    ->content(fn ($record) => $record
                                                        ? '💰 Rp ' . number_format($record->balance, 0, ',', '.')
                                                        : 'Rp 0'
                                                    ),

                                                Forms\Components\Actions::make([
                                                    Forms\Components\Actions\Action::make('goToWithdraw')
                                                        ->label('Cairkan Dana (Withdraw)')
                                                        ->icon('heroicon-m-arrow-top-right-on-square')
                                                        ->color('success')
                                                        ->url(fn () => \App\Filament\Restaurant\Pages\WithdrawPage::getUrl())
                                                ]),
                                            ]),
                                    ]),
                            ]),

                        // Tab 7: Email Marketing
                        Forms\Components\Tabs\Tab::make('Email Marketing')
                            ->icon('heroicon-m-envelope')
                            ->visible(fn () => auth()->user()?->hasFeature('Email Marketing'))
                            ->schema([
                                Forms\Components\Textarea::make('google_map_embed')
                                    ->label('Google Maps Embed Code')
                                    ->placeholder('<iframe src="https://www.google.com/maps/embed?..." ...></iframe>')
                                    ->rows(3),
                                
                                Forms\Components\Section::make('SMTP Privat (Pemasaran Email)')
                                    ->description(fn() => "Gunakan server SMTP Anda sendiri untuk mengirimkan kampanye pemasaran email tanpa limit harian dari {$this->getSiteName()}.")
                                    ->collapsed()
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\Select::make('email_marketing_provider')
                                                    ->label('Provider')
                                                    ->options([
                                                        'smtp' => 'SMTP Server',
                                                        'mailgun' => 'Mailgun (Soon)',
                                                        'sendgrid' => 'Sendgrid (Soon)',
                                                    ])
                                                    ->default('smtp')
                                                    ->reactive(),
                                                
                                                Forms\Components\TextInput::make('email_marketing_smtp_host')
                                                    ->label('SMTP Host')
                                                    ->placeholder('smtp.googlemail.com'),
                                                
                                                Forms\Components\TextInput::make('email_marketing_smtp_port')
                                                    ->label('SMTP Port')
                                                    ->numeric()
                                                    ->placeholder('465 / 587'),
                                                
                                                Forms\Components\TextInput::make('email_marketing_smtp_username')
                                                    ->label('Username / User ID'),
                                                
                                                Forms\Components\TextInput::make('email_marketing_smtp_password')
                                                    ->label('Password / App Password')
                                                    ->password()
                                                    ->revealable(),
                                                
                                                Forms\Components\Select::make('email_marketing_smtp_encryption')
                                                    ->label('Encryption')
                                                    ->options([
                                                        'ssl' => 'SSL',
                                                        'tls' => 'TLS',
                                                        'none' => 'None',
                                                    ]),
                                                
                                                Forms\Components\TextInput::make('email_marketing_smtp_from_address')
                                                    ->label('From Email')
                                                    ->email()
                                                    ->placeholder('marketing@domain.com'),
                                            ]),

                                        Forms\Components\Group::make()
                                            ->schema([
                                                Forms\Components\Placeholder::make('test_smtp_info')
                                                    ->label('Tes Koneksi')
                                                    ->content('Pastikan data di atas sudah benar sebelum melakukan tes.'),
                                                
                                                Forms\Components\TextInput::make('test_recipient')
                                                    ->label('Alamat Email Penerima Tes')
                                                    ->email()
                                                    ->dehydrated(false)
                                                    ->placeholder('anda@email.com'),

                                                Forms\Components\Actions::make([
                                                    Forms\Components\Actions\Action::make('test_smtp_connection')
                                                        ->label('Coba Kirim Email Tes')
                                                        ->icon('heroicon-m-paper-airplane')
                                                        ->color('success')
                                                        ->action(function (Forms\Get $get, Forms\Set $set) {
                                                            $data = [
                                                                'host' => $get('email_marketing_smtp_host'),
                                                                'port' => $get('email_marketing_smtp_port'),
                                                                'username' => $get('email_marketing_smtp_username'),
                                                                'password' => $get('email_marketing_smtp_password'),
                                                                'encryption' => $get('email_marketing_smtp_encryption'),
                                                                'from_address' => $get('email_marketing_smtp_from_address'),
                                                                'test_recipient' => $get('test_recipient'),
                                                            ];

                                                            if (empty($data['host']) || empty($data['test_recipient'])) {
                                                                Notification::make()
                                                                    ->title('Data Tidak Lengkap')
                                                                    ->body('Harap isi minimal Host SMTP dan Alamat Penerima Tes.')
                                                                    ->warning()
                                                                    ->send();
                                                                return;
                                                            }

                                                            try {
                                                                $fromAddress = $data['from_address'] ?: $data['username'];
                                                                $fromName = \Filament\Facades\Filament::getTenant()->name;

                                                                Config::set('mail.mailers.smtp_test', [
                                                                    'transport' => 'smtp',
                                                                    'host' => $data['host'],
                                                                    'port' => $data['port'],
                                                                    'encryption' => $data['encryption'],
                                                                    'username' => $data['username'],
                                                                    'password' => $data['password'],
                                                                    'timeout' => 30,
                                                                ]);

                                                                Config::set('mail.from.address', $fromAddress);
                                                                Config::set('mail.from.name', $fromName);

                                                                Mail::purge('smtp_test');

                                                                Mail::mailer('smtp_test')->to($data['test_recipient'])->send(new \App\Mail\GenericTestMail(
                                                                    subject: "{$this->getSiteName()} - Tes Konfigurasi SMTP Privat",
                                                                    message: "Selamat! Konfigurasi SMTP Privat Anda untuk restoran " . \Filament\Facades\Filament::getTenant()->name . " telah berhasil terhubung. Anda sekarang bisa mulai mengirimkan kampanye pemasaran email menggunakan server sendiri."
                                                                ));

                                                                Notification::make()
                                                                    ->title('Berhasil!')
                                                                    ->body('Email uji coba berhasil dikirim ke ' . $data['test_recipient'] . '. Silakan cek kotak masuk (atau spam) Anda.')
                                                                    ->success()
                                                                    ->send();
                                                            } catch (\Exception $e) {
                                                                Notification::make()
                                                                    ->title('Koneksi Gagal')
                                                                    ->body('Gagal terhubung ke server SMTP: ' . $e->getMessage())
                                                                    ->danger()
                                                                    ->persistent()
                                                                    ->send();
                                                            }
                                                        }),
                                                ])->alignCenter(),
                                            ]),
                                    ]),
                            ]),
                        // Tab 8: WiFi & QR Info
                        Forms\Components\Tabs\Tab::make('WiFi & QR')
                            ->icon('heroicon-m-wifi')
                            ->schema([
                                Forms\Components\Section::make('Informasi WiFi Restoran')
                                    ->description('Informasi ini akan ditampilkan di bagian bawah kartu QR meja, sehingga pelanggan bisa langsung terhubung ke WiFi saat memesan.')
                                    ->icon('heroicon-m-wifi')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('wifi_name')
                                                    ->label('Nama Jaringan WiFi (SSID)')
                                                    ->placeholder('Contoh: DineCafe_Guest')
                                                    ->prefixIcon('heroicon-m-wifi')
                                                    ->helperText('Nama WiFi yang terlihat di daftar jaringan perangkat pelanggan.'),

                                                Forms\Components\TextInput::make('wifi_password')
                                                    ->label('Password WiFi')
                                                    ->placeholder('Contoh: welcome2024')
                                                    ->prefixIcon('heroicon-m-lock-closed')
                                                    ->password()
                                                    ->revealable()
                                                    ->helperText('Biarkan kosong jika jaringan WiFi tidak memerlukan password.')
                                                    ->dehydrateStateUsing(fn ($state) => $state),
                                            ]),

                                        Forms\Components\Placeholder::make('wifi_info_note')
                                            ->label('')
                                            ->content(new \Illuminate\Support\HtmlString('
                                                <div class="flex items-start gap-2 p-3 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
                                                    <svg class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <p class="text-sm text-blue-700 dark:text-blue-300">Informasi WiFi akan otomatis tampil di kartu QR meja Anda. Pelanggan bisa dengan mudah melihat nama dan password WiFi saat scan QR.</p>
                                                </div>
                                            ')),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}

