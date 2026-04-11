<?php

namespace App\Filament\Admin\Pages;

use App\Settings\GeneralSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Filament\Support\RawJs;

class ManageSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = GeneralSettings::class;

    protected static ?string $navigationGroup = 'Settings';
    
    protected static ?int $navigationSort = 100;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Settings')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('General')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Group::make()
                                            ->schema([
                                                Forms\Components\FileUpload::make('site_logo')
                                                    ->label('App Logo')
                                                    ->image()
                                                    ->imageEditor()
                                                    ->maxSize(2048)
                                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                                    ->disk('public')
                                                    ->directory('settings')
                                                    ->visibility('public')
                                                    ->imagePreviewHeight('80'),
                                                Forms\Components\FileUpload::make('site_favicon')
                                                    ->label('App Favicon')
                                                    ->image()
                                                    ->maxSize(1024)
                                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/x-icon', 'image/vnd.microsoft.icon'])
                                                    ->disk('public')
                                                    ->directory('settings')
                                                    ->visibility('public')
                                                    ->imagePreviewHeight('40'),
                                            ]),
                                        Forms\Components\FileUpload::make('site_og_image')
                                            ->label('Social Share Image (OG Cover)')
                                            ->helperText('Gambar cover saat link dibagikan (WhatsApp/FB/Twitter). Rekomendasi: 1200x630 px. Max 2MB.')
                                            ->image()
                                            ->imageEditor()
                                            ->maxSize(2048)
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->disk('public')
                                            ->directory('settings')
                                            ->visibility('public')
                                            ->imagePreviewHeight('150'),
                                    ]),
                                
                                Forms\Components\Section::make('PWA Configuration')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\FileUpload::make('pwa_icon_192')
                                                    ->label('PWA Icon (192x192)')
                                                    ->helperText('Required for PWA. PNG format recommended.')
                                                    ->image()
                                                    ->maxSize(1024)
                                                    ->acceptedFileTypes(['image/png', 'image/webp'])
                                                    ->disk('public')
                                                    ->directory('settings')
                                                    ->visibility('public')
                                                    ->imagePreviewHeight('100'),
                                                Forms\Components\FileUpload::make('pwa_icon_512')
                                                    ->label('PWA Icon (512x512)')
                                                    ->helperText('Required for PWA (Splash Screen). PNG format recommended.')
                                                    ->image()
                                                    ->maxSize(1024)
                                                    ->acceptedFileTypes(['image/png', 'image/webp'])
                                                    ->disk('public')
                                                    ->directory('settings')
                                                    ->visibility('public')
                                                    ->imagePreviewHeight('100'),
                                            ]),
                                    ])->collapsible(),

                                Forms\Components\TextInput::make('site_name')
                                    ->label('App Name')
                                    ->required(),
                                
                                Forms\Components\Section::make('SEO & Metadata')
                                    ->description('Optimalkan aplikasi untuk mesin pencari (Google, Bing, dll).')
                                    ->schema([
                                        Forms\Components\Textarea::make('site_description')
                                            ->label('Meta Description')
                                            ->rows(3)
                                            ->maxLength(160)
                                            ->helperText('Deskripsi singkat yang muncul di hasil pencarian Google. Maksimal 160 karakter.'),
                                        Forms\Components\TagsInput::make('site_keywords')
                                            ->label('Meta Keywords')
                                            ->placeholder('Add keywords...')
                                            ->separator(',')
                                            ->helperText('Kata kunci utama aplikasi Anda, pisahkan dengan koma.'),
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('site_author')
                                                    ->label('Meta Author')
                                                    ->placeholder('Your Company Name'),
                                            ]),
                                    ])->collapsible(),

                                Forms\Components\Section::make('Contact Information')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('support_email')
                                                    ->label('Support Email')
                                                    ->email()
                                                    ->required(),
                                                Forms\Components\TextInput::make('site_phone')
                                                    ->label('Phone Number')
                                                    ->tel(),
                                            ]),
                                        Forms\Components\Textarea::make('site_address')
                                            ->label('Office Address')
                                            ->rows(2),
                                        Forms\Components\Textarea::make('site_google_maps_embed')
                                            ->label('Google Maps Embed Code')
                                            ->helperText('Paste the iframe code from Google Maps Share -> Embed a map.')
                                            ->rows(3),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Social Media')
                            ->icon('heroicon-m-share')
                            ->schema([
                                Forms\Components\Section::make('Social Media Profiles')
                                    ->description('Masukkan URL lengkap profil media sosial Anda (misal: https://facebook.com/username).')
                                    ->schema([
                                        Forms\Components\TextInput::make('site_facebook_url')
                                            ->label('Facebook URL')
                                            ->url()
                                            ->prefix('https://')
                                            ->placeholder('facebook.com/yourbrand'),
                                        Forms\Components\TextInput::make('site_instagram_url')
                                            ->label('Instagram URL')
                                            ->url()
                                            ->prefix('https://')
                                            ->placeholder('instagram.com/yourbrand'),
                                        Forms\Components\TextInput::make('site_youtube_url')
                                            ->label('YouTube URL')
                                            ->url()
                                            ->prefix('https://')
                                            ->placeholder('youtube.com/@yourbrand'),
                                        Forms\Components\TextInput::make('site_linkedin_url')
                                            ->label('LinkedIn URL')
                                            ->url()
                                            ->prefix('https://')
                                            ->placeholder('linkedin.com/company/yourbrand'),
                                        Forms\Components\TextInput::make('site_github_url')
                                            ->label('GitHub URL')
                                            ->url()
                                            ->prefix('https://')
                                            ->placeholder('github.com/yourbrand'),
                                        Forms\Components\TextInput::make('site_twitter_url')
                                            ->label('Twitter (X) URL')
                                            ->url()
                                            ->prefix('https://')
                                            ->placeholder('x.com/yourbrand'),
                                    ]),
                            ]),
                        
                        Forms\Components\Tabs\Tab::make('Payment Gateway (Midtrans)')
                            ->schema([
                                Forms\Components\Toggle::make('midtrans_is_production')
                                    ->label('Production Mode')
                                    ->onColor('success')
                                    ->offColor('danger'),
                                Forms\Components\TextInput::make('midtrans_merchant_id')
                                    ->label('Merchant ID')
                                    ->required(),
                                Forms\Components\TextInput::make('midtrans_server_key')
                                    ->label('Server Key')
                                    ->password()
                                    ->revealable()
                                    ->required()
                                    ->formatStateUsing(function ($state) {
                                        if (!$state) return null;
                                        try { return \Illuminate\Support\Facades\Crypt::decryptString($state); } catch (\Exception $e) { return $state; }
                                    })
                                    ->dehydrateStateUsing(fn ($state) => $state ? \Illuminate\Support\Facades\Crypt::encryptString($state) : null),
                                Forms\Components\TextInput::make('midtrans_client_key')
                                    ->label('Client Key')
                                    ->password()
                                    ->revealable()
                                    ->required()
                                    ->formatStateUsing(function ($state) {
                                        if (!$state) return null;
                                        try { return \Illuminate\Support\Facades\Crypt::decryptString($state); } catch (\Exception $e) { return $state; }
                                    })
                                    ->dehydrateStateUsing(fn ($state) => $state ? \Illuminate\Support\Facades\Crypt::encryptString($state) : null),
                            ]),

                        Forms\Components\Tabs\Tab::make('Email (SMTP)')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('smtp_host')->label('Host')->required(),
                                        Forms\Components\TextInput::make('smtp_port')->label('Port')->numeric()->required(),
                                        Forms\Components\TextInput::make('smtp_username')->label('Username'),
                                        Forms\Components\TextInput::make('smtp_password')
                                            ->label('Password')
                                            ->password()
                                            ->revealable()
                                            ->formatStateUsing(function ($state) {
                                                if (!$state) return null;
                                                try { return \Illuminate\Support\Facades\Crypt::decryptString($state); } catch (\Exception $e) { return $state; }
                                            })
                                            ->dehydrateStateUsing(fn ($state) => $state ? \Illuminate\Support\Facades\Crypt::encryptString($state) : null),
                                        Forms\Components\TextInput::make('smtp_encryption')->label('Encryption')->placeholder('e.g. tls'),
                                        Forms\Components\TextInput::make('smtp_from_address')->label('From Address')->email()->required(),
                                        Forms\Components\TextInput::make('smtp_from_name')->label('From Name')->required(),
                                    ]),
                                
                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('sendTestEmail')
                                        ->label('Send Test Email')
                                        ->form([
                                            Forms\Components\TextInput::make('email')
                                                ->label('Recipient Email')
                                                ->email()
                                                ->required(),
                                        ])
                                        ->action(function (array $data, GeneralSettings $settings) {
                                            // Update config temporarily for testing
                                            config(['mail.default' => 'smtp']); // Force SMTP driver
                                            config([
                                                'mail.mailers.smtp.host' => $settings->smtp_host,
                                                'mail.mailers.smtp.port' => $settings->smtp_port,
                                                'mail.mailers.smtp.username' => $settings->smtp_username,
                                                'mail.mailers.smtp.password' => (function() use ($settings) {
                                                    $password = $settings->smtp_password;
                                                    try { return \Illuminate\Support\Facades\Crypt::decryptString($password); } catch (\Exception $e) { return $password; }
                                                })(),
                                                'mail.mailers.smtp.encryption' => $settings->smtp_encryption,
                                                'mail.from.address' => $settings->smtp_from_address,
                                                'mail.from.name' => $settings->smtp_from_name,
                                            ]);

                                            try {
                                                Mail::raw("This is a test email from your {$settings->site_name} system.", function ($message) use ($data, $settings) {
                                                    $message->to($data['email'])
                                                        ->subject("Test Email from {$settings->site_name}");
                                                });

                                                Notification::make()
                                                    ->title('Test email sent successfully to ' . $data['email'])
                                                    ->success()
                                                    ->send();
                                            } catch (\Exception $e) {
                                                Notification::make()
                                                    ->title('Failed to send email')
                                                    ->body($e->getMessage())
                                                    ->danger()
                                                    ->send();
                                            }
                                        })
                                        ->color('primary'),
                                ]),
                            ]),
                        
                        Forms\Components\Tabs\Tab::make('Real-time (Broadcasting)')
                            ->icon('heroicon-m-signal')
                            ->schema([
                                Forms\Components\Select::make('broadcast_driver')
                                    ->label('Broadcasting Driver')
                                    ->options([
                                        'reverb' => '📡 Internal Server (Laravel Reverb)',
                                        'pusher' => '☁️ Cloud Service (Pusher)',
                                    ])
                                    ->required()
                                    ->live()
                                    ->helperText(new \Illuminate\Support\HtmlString('
                                        <div class="space-y-2">
                                            <b>💡 Tips Panduan Server:</b><br>
                                            <ul class="list-disc ml-4 space-y-1">
                                                <li>
                                                    <b>Jika pilih Reverb (VPS):</b> Jalankan perintah 
                                                    <code class="bg-gray-100 p-1 rounded">php artisan reverb:start</code> 
                                                    <button type="button" onclick="const btn = this; navigator.clipboard.writeText(\'php artisan reverb:start\'); const oldText = btn.innerText; btn.innerText = \'Copied! ✅\'; setTimeout(() => btn.innerText = oldText, 2000)" class="text-xs text-blue-600 underline ml-1 font-bold">Copy</button>. 
                                                    Agar tetap menyala, gunakan 
                                                    <code class="bg-gray-100 p-1 rounded">nohup php artisan reverb:start > /dev/null 2>&1 &</code> 
                                                    <button type="button" onclick="const btn = this; navigator.clipboard.writeText(\'nohup php artisan reverb:start > /dev/null 2>&1 &\'); const oldText = btn.innerText; btn.innerText = \'Copied! ✅\'; setTimeout(() => btn.innerText = oldText, 2000)" class="text-xs text-blue-600 underline ml-1 font-bold">Copy</button>.
                                                </li>
                                                <li>
                                                    <b>Jika pilih Pusher (Shared Hosting):</b> Sistem otomatis mengirim data ke cloud Pusher. Anda <b>bebas ribet</b>, tidak perlu menjalankan perintah terminal apapun di server.
                                                </li>
                                            </ul>
                                        </div>
                                    ')),

                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('howToSetupPusher')
                                        ->label('📖 Cara Setup Pusher')
                                        ->icon('heroicon-m-question-mark-circle')
                                        ->color('info')
                                        ->visible(fn (Forms\Get $get) => $get('broadcast_driver') === 'pusher')
                                        ->modalHeading('Panduan Mendapatkan API Pusher')
                                        ->modalDescription('Ikuti langkah berikut untuk mengaktifkan fitur real-time via Pusher:')
                                        ->modalContent(view('components.pusher-tutorial-modal'))
                                        ->modalSubmitAction(false),
                                ]),

                                Forms\Components\Section::make('Laravel Reverb Configuration')
                                    ->visible(fn (Forms\Get $get) => $get('broadcast_driver') === 'reverb')
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('reverb_app_id')->label('App ID')->required(),
                                                Forms\Components\TextInput::make('reverb_app_key')->label('App Key')->required(),
                                                Forms\Components\TextInput::make('reverb_app_secret')
                                                    ->label('App Secret')
                                                    ->password()
                                                    ->revealable()
                                                    ->required()
                                                    ->formatStateUsing(function ($state) {
                                                        if (!$state) return null;
                                                        try { return \Illuminate\Support\Facades\Crypt::decryptString($state); } catch (\Exception $e) { return $state; }
                                                    })
                                                    ->dehydrateStateUsing(fn ($state) => $state ? \Illuminate\Support\Facades\Crypt::encryptString($state) : null),
                                            ]),
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('reverb_host')->label('Host')->placeholder('e.g. dineflo.test')->required(),
                                                Forms\Components\TextInput::make('reverb_port')->label('Port')->numeric()->default(8081)->required(),
                                                Forms\Components\Select::make('reverb_scheme')
                                                    ->label('Scheme')
                                                    ->options([
                                                        'http' => 'HTTP',
                                                        'https' => 'HTTPS',
                                                    ])
                                                    ->default('http')
                                                    ->required(),
                                            ]),
                                    ]),

                                Forms\Components\Section::make('Pusher Configuration')
                                    ->visible(fn (Forms\Get $get) => $get('broadcast_driver') === 'pusher')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('pusher_app_id')->label('App ID')->required(),
                                                Forms\Components\TextInput::make('pusher_app_key')->label('App Key')->required(),
                                                Forms\Components\TextInput::make('pusher_app_secret')
                                                    ->label('App Secret')
                                                    ->password()
                                                    ->revealable()
                                                    ->required()
                                                    ->formatStateUsing(function ($state) {
                                                        if (!$state) return null;
                                                        try { return \Illuminate\Support\Facades\Crypt::decryptString($state); } catch (\Exception $e) { return $state; }
                                                    })
                                                    ->dehydrateStateUsing(fn ($state) => $state ? \Illuminate\Support\Facades\Crypt::encryptString($state) : null),
                                                Forms\Components\TextInput::make('pusher_app_cluster')->label('Cluster')->placeholder('e.g. ap1')->required(),
                                            ]),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Finance & Fee')
                            ->icon('heroicon-m-currency-dollar')
                            ->schema([
                                Forms\Components\Section::make('Biaya Payment Gateway (Midtrans)')
                                    ->description('Fee ini digunakan untuk menghitung saldo bersih restoran setelah potongan. Ubah jika ada perubahan tarif dari Midtrans.')
                                    ->icon('heroicon-m-credit-card')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('midtrans_qris_fee_percentage')
                                                    ->label('QRIS / GoPay / ShopeePay Fee (%)')
                                                    ->numeric()
                                                    ->step(0.01)
                                                    ->suffix('%')
                                                    ->default(0.70)
                                                    ->helperText('Ditetapkan Bank Indonesia. Default: 0.70%'),

                                                Forms\Components\TextInput::make('midtrans_cc_fee_percentage')
                                                    ->label('Credit Card Fee (%)')
                                                    ->numeric()
                                                    ->step(0.01)
                                                    ->suffix('%')
                                                    ->default(2.00)
                                                    ->helperText('Rata-rata 2%. Sesuaikan dengan kontrak Midtrans Anda.'),

                                                Forms\Components\TextInput::make('midtrans_va_fee_flat')
                                                    ->label('Virtual Account Fee (Rp, flat)')
                                                    ->prefix('Rp')
                                                    ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                                                    ->formatStateUsing(fn ($state) => $state ? number_format((float) $state, 0, ',', '.') : null)
                                                    ->dehydrateStateUsing(fn ($state) => (float) str_replace('.', '', $state ?? 0))
                                                    ->default(4000)
                                                    ->helperText('Biaya flat per transaksi VA. Default: Rp 4.000'),

                                                Forms\Components\TextInput::make('midtrans_cstore_fee_flat')
                                                    ->label('Minimarket Fee (Rp, flat)')
                                                    ->prefix('Rp')
                                                    ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                                                    ->formatStateUsing(fn ($state) => $state ? number_format((float) $state, 0, ',', '.') : null)
                                                    ->dehydrateStateUsing(fn ($state) => (float) str_replace('.', '', $state ?? 0))
                                                    ->default(5000)
                                                    ->helperText('Indomaret / Alfamart. Default: Rp 5.000'),
                                            ]),
                                    ]),

                                Forms\Components\Section::make(fn (GeneralSettings $settings) => "Platform Fee {$settings->site_name} (Admin Fee on Withdraw)")
                                    ->description('Biaya layanan yang dikenakan kepada restoran setiap kali mereka melakukan penarikan dana (withdraw). Set 0 untuk gratis.')
                                    ->icon('heroicon-m-banknotes')
                                    ->schema([
                                        Forms\Components\TextInput::make('dineflo_withdraw_admin_fee_percentage')
                                            ->label('Admin Fee Withdraw (%)')
                                            ->numeric()
                                            ->step(0.01)
                                            ->suffix('%')
                                            ->default(0)
                                            ->minValue(0)
                                            ->maxValue(100)
                                            ->helperText('Set 0 untuk tidak mengenakan biaya admin. Contoh: 2 = potongan 2% dari jumlah withdraw.')
                                            ->live()
                                            ->afterStateUpdated(function () {}),

                                        Forms\Components\Placeholder::make('admin_fee_preview')
                                            ->label('Preview')
                                            ->content(fn (Forms\Get $get) =>
                                                (float) ($get('dineflo_withdraw_admin_fee_percentage') ?? 0) > 0
                                                    ? '✅ Contoh: Withdraw Rp 100.000 → Potongan admin ' .
                                                      $get('dineflo_withdraw_admin_fee_percentage') . '% = Rp ' .
                                                      number_format(100000 * ($get('dineflo_withdraw_admin_fee_percentage') / 100), 0, ',', '.') .
                                                      ' → Dana ditransfer: Rp ' .
                                                      number_format(100000 - (100000 * ($get('dineflo_withdraw_admin_fee_percentage') / 100)), 0, ',', '.')
                                                    : '💚 Admin fee tidak aktif — withdraw gratis untuk restoran.'
                                            ),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('WhatsApp')
                            ->icon('heroicon-m-chat-bubble-left-right')
                            ->schema([

                                // ── Global Toggle & Provider Selector ─────────────────────
                                Forms\Components\Section::make('Konfigurasi WhatsApp Platform')
                                    ->description(fn (GeneralSettings $settings) => "WhatsApp digunakan oleh {$settings->site_name} untuk mengirim System Broadcast ke seluruh pemilik restoran. Pilih provider yang Anda gunakan.")
                                    ->icon('heroicon-m-megaphone')
                                    ->schema([
                                        Forms\Components\Toggle::make('platform_wa_is_active')
                                            ->label('Aktifkan WhatsApp Broadcast Platform')
                                            ->helperText('Jika dimatikan, System Broadcast channel WhatsApp tidak akan terkirim meskipun dipilih di form broadcast.')
                                            ->onColor('success')
                                            ->offColor('gray')
                                            ->live(),

                                        Forms\Components\Select::make('platform_wa_provider')
                                            ->label('Provider WhatsApp')
                                            ->options([
                                                'fonnte' => '📱 Fonnte',
                                                'watzap' => '📱 Watzap.id',
                                                'watsap' => '📱 Watsap.id',
                                                // Tambahkan provider baru di sini:
                                                // 'zenziva' => '📱 Zenziva',
                                            ])
                                            ->default('fonnte')
                                            ->required()
                                            ->live()
                                            ->helperText('Pilih gateway WhatsApp yang akan digunakan untuk pengiriman broadcast platform.')
                                            ->visible(fn (Forms\Get $get) => (bool) $get('platform_wa_is_active')),
                                    ])->columns(1),

                                // ── Konfigurasi: Fonnte ───────────────────────────────────
                                Forms\Components\Section::make('🔑 Konfigurasi Fonnte')
                                    ->description('Dapatkan API Token di fonnte.com → Login → Dashboard → Device → Klik nama device → Token.')
                                    ->schema([
                                        Forms\Components\TextInput::make('platform_fonnte_api_key')
                                            ->label('Fonnte API Token')
                                            ->password()
                                            ->revealable()
                                            ->placeholder('Paste API Token dari dashboard Fonnte Anda')
                                            ->helperText('Token ini unik per device WhatsApp yang terdaftar di Fonnte.')
                                            ->columnSpanFull()
                                            ->formatStateUsing(function ($state) {
                                                if (!$state) return null;
                                                try { return \Illuminate\Support\Facades\Crypt::decryptString($state); } catch (\Exception $e) { return $state; }
                                            })
                                            ->dehydrateStateUsing(fn ($state) => $state ? \Illuminate\Support\Facades\Crypt::encryptString($state) : null),
                                    ])
                                    ->visible(fn (Forms\Get $get) => (bool) $get('platform_wa_is_active') && $get('platform_wa_provider') === 'fonnte'),

                                // ── Konfigurasi: Watzap.id ───────────────────────────────
                                Forms\Components\Section::make('🔑 Konfigurasi Watzap.id')
                                    ->description('Dapatkan API Key dan Number Key di watzap.id → Member Area → Integration → API Key & Apps.')
                                    ->schema([
                                        Forms\Components\TextInput::make('platform_watzap_api_key')
                                            ->label('Watzap API Key')
                                            ->password()
                                            ->revealable()
                                            ->placeholder('Paste API Key dari Member Area Watzap.id')
                                            ->helperText('Dari menu: Integration → API Key & Apps → API Key.')
                                            ->columnSpanFull()
                                            ->formatStateUsing(function ($state) {
                                                if (!$state) return null;
                                                try { return \Illuminate\Support\Facades\Crypt::decryptString($state); } catch (\Exception $e) { return $state; }
                                            })
                                            ->dehydrateStateUsing(fn ($state) => $state ? \Illuminate\Support\Facades\Crypt::encryptString($state) : null),
                                        Forms\Components\TextInput::make('platform_watzap_number_key')
                                            ->label('Number Key (Nomor Sender)')
                                            ->placeholder('Contoh: 628123456789')
                                            ->helperText('Dari menu: Integration → API Key & Apps → Assigned Numbers for API.')
                                            ->columnSpanFull(),
                                    ])
                                    ->visible(fn (Forms\Get $get) => (bool) $get('platform_wa_is_active') && $get('platform_wa_provider') === 'watzap'),

                                // ── Konfigurasi: Watsap.id ───────────────────────────────
                                Forms\Components\Section::make('🔑 Konfigurasi Watsap.id')
                                    ->description('Dapatkan API Key & ID Device di watsap.id → Login → Dashboard.')
                                    ->schema([
                                        Forms\Components\TextInput::make('platform_watsap_api_key')
                                            ->label('API Key')
                                            ->password()
                                            ->revealable()
                                            ->placeholder('Paste API Key dari dashboard Watsap.id')
                                            ->helperText('Contoh: 0e5bad843a4f3576bb133aa80ed15fd89e3206bf')
                                            ->columnSpanFull()
                                            ->formatStateUsing(function ($state) {
                                                if (!$state) return null;
                                                try { return \Illuminate\Support\Facades\Crypt::decryptString($state); } catch (\Exception $e) { return $state; }
                                            })
                                            ->dehydrateStateUsing(fn ($state) => $state ? \Illuminate\Support\Facades\Crypt::encryptString($state) : null),
                                        Forms\Components\TextInput::make('platform_watsap_id_device')
                                            ->label('ID Device (Nomor Pengirim)')
                                            ->placeholder('Contoh: 12345')
                                            ->helperText('ID Device yang sudah di-scan QR Code, berfungsi sebagai nomor pengirim.')
                                            ->columnSpanFull(),
                                    ])
                                    ->visible(fn (Forms\Get $get) => (bool) $get('platform_wa_is_active') && $get('platform_wa_provider') === 'watsap'),

                                // ── (Tambahkan section provider baru di sini) ─────────────
                                // ── Test Kirim ────────────────────────────────────────────
                                Forms\Components\Section::make('🧪 Uji Coba Pengiriman')
                                    ->schema([
                                        Forms\Components\Actions::make([
                                            Forms\Components\Actions\Action::make('testWaMessage')
                                                ->label('Kirim Test WhatsApp')
                                                ->icon('heroicon-m-paper-airplane')
                                                ->color('success')
                                                ->form([
                                                    Forms\Components\TextInput::make('target_phone')
                                                        ->label('Nomor WA Tujuan')
                                                        ->placeholder('628123456789')
                                                        ->helperText('Format: 628xxx (tanpa + atau spasi)')
                                                        ->required(),
                                                ])
                                                ->action(function (array $data, GeneralSettings $settings) {
                                                    if (!$settings->platform_wa_is_active) {
                                                        Notification::make()
                                                            ->title('WhatsApp Belum Diaktifkan')
                                                            ->body('Aktifkan toggle dan simpan pengaturan terlebih dahulu.')
                                                            ->warning()->send();
                                                        return;
                                                    }

                                                    $provider = $settings->platform_wa_provider ?? 'fonnte';
                                                    $phone    = $data['target_phone'];
                                                    $message  = "✅ *Test WhatsApp dari {$settings->site_name}* — Provider *" . strtoupper($provider) . "* berhasil dikonfigurasi!";

                                                    try {
                                                        match ($provider) {
                                                            'fonnte' => (function () use ($settings, $phone, $message) {
                                                                $apiKey = $settings->platform_fonnte_api_key;
                                                                try { $apiKey = \Illuminate\Support\Facades\Crypt::decryptString($apiKey); } catch (\Exception $e) {}

                                                                if (!$apiKey) {
                                                                    throw new \Exception('API Token Fonnte belum diisi.');
                                                                }
                                                                $res  = Http::withHeaders(['Authorization' => $apiKey])
                                                                    ->post('https://api.fonnte.com/send', ['target' => $phone, 'message' => $message]);
                                                                $body = $res->json();
                                                                if (!$res->successful() || ($body['status'] ?? true) === false) {
                                                                    throw new \Exception('Fonnte error: ' . ($body['reason'] ?? $res->body()));
                                                                }
                                                            })(),

                                                            'watzap' => (function () use ($settings, $phone, $message) {
                                                                $apiKey = $settings->platform_watzap_api_key;
                                                                try { $apiKey = \Illuminate\Support\Facades\Crypt::decryptString($apiKey); } catch (\Exception $e) {}

                                                                if (!$apiKey || !$settings->platform_watzap_number_key) {
                                                                    throw new \Exception('API Key atau Number Key Watzap.id belum diisi.');
                                                                }
                                                                $res  = Http::post('https://api.watzap.id/v1/send_message', [
                                                                    'api_key'    => $apiKey,
                                                                    'number_key' => $settings->platform_watzap_number_key,
                                                                    'phone_no'   => $phone,
                                                                    'message'    => $message,
                                                                ]);
                                                                $body = $res->json();
                                                                if (!$res->successful() || ($body['status'] ?? true) === false) {
                                                                    throw new \Exception('Watzap error: ' . ($body['message'] ?? $res->body()));
                                                                }
                                                            })(),

                                                            'watsap' => (function () use ($settings, $phone, $message) {
                                                                $apiKey = $settings->platform_watsap_api_key;
                                                                try { $apiKey = \Illuminate\Support\Facades\Crypt::decryptString($apiKey); } catch (\Exception $e) {}

                                                                if (!$apiKey || !$settings->platform_watsap_id_device) {
                                                                    throw new \Exception('API Key atau ID Device Watsap.id belum diisi.');
                                                                }
                                                                $res  = Http::withHeaders(['Content-Type' => 'application/json'])
                                                                    ->post('https://api.watsap.id/send-message', [
                                                                        'api-key'   => $apiKey,
                                                                        'id_device' => $settings->platform_watsap_id_device,
                                                                        'no_hp'     => $phone,
                                                                        'pesan'     => $message,
                                                                    ]);
                                                                $body = $res->json();
                                                                $code = $body['kode'] ?? $body['code'] ?? 0;
                                                                if (!$res->successful() || $code != 200) {
                                                                    $keterangan = match ((int) $code) {
                                                                        300 => 'Gagal kirim / tidak ada hasil',
                                                                        400 => 'ID Device tidak ditemukan',
                                                                        401 => 'API Key tidak ditemukan atau salah',
                                                                        402 => 'Nomor WA tidak terdaftar',
                                                                        403 => 'WhatsApp Anda Multi Device',
                                                                        404 => 'Harap SCAN QRCODE terlebih dahulu',
                                                                        500 => 'Gagal dikirim',
                                                                        default => $res->body(),
                                                                    };
                                                                    throw new \Exception('Watsap.id: ' . $keterangan);
                                                                }
                                                            })(),

                                                            default => throw new \Exception("Provider '{$provider}' belum dikonfigurasi."),
                                                        };

                                                        Notification::make()
                                                            ->title('Test WhatsApp Berhasil!')
                                                            ->body("Pesan dikirim ke {$phone} via " . strtoupper($provider))
                                                            ->success()->send();

                                                    } catch (\Exception $e) {
                                                        Notification::make()
                                                            ->title('Gagal Kirim WhatsApp')
                                                            ->body($e->getMessage())
                                                            ->danger()->send();
                                                    }
                                                }),
                                        ]),
                                    ])
                                    ->visible(fn (Forms\Get $get) => (bool) $get('platform_wa_is_active')),
                            ]),

                        Forms\Components\Tabs\Tab::make('Landing Page')
                            ->icon('heroicon-m-window')
                            ->schema([
                                Forms\Components\Section::make('Hero Section')
                                    ->description('Konfigurasi bagian atas landing page Anda.')
                                    ->schema([
                                        Forms\Components\TextInput::make('landing_hero_title')
                                            ->label('Hero Title')
                                            ->helperText('Gunakan HTML jika ingin menambahkan gradasi atau warna khusus.')
                                            ->columnSpanFull(),
                                        Forms\Components\Textarea::make('landing_hero_subtitle')
                                            ->label('Hero Subtitle')
                                            ->rows(3)
                                            ->columnSpanFull(),
                                        
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('landing_hero_cta_primary_text')
                                                    ->label('Primary CTA Text'),
                                                Forms\Components\TextInput::make('landing_hero_cta_primary_link')
                                                    ->label('Primary CTA URL'),
                                                Forms\Components\TextInput::make('landing_hero_cta_secondary_text')
                                                    ->label('Secondary CTA Text'),
                                                Forms\Components\TextInput::make('landing_hero_cta_secondary_link')
                                                    ->label('Secondary CTA URL'),
                                            ]),

                                        Forms\Components\Repeater::make('landing_hero_mockups')
                                            ->label('Dashboard Mockup Carousel')
                                            ->helperText('Tambahkan beberapa gambar untuk dijadikan slider di halaman depan.')
                                            ->schema([
                                                Forms\Components\TextInput::make('title')
                                                    ->label('Slide Title / Label')
                                                    ->placeholder('e.g. FILAMENT V3')
                                                    ->required(),
                                                Forms\Components\FileUpload::make('image')
                                                    ->label('Mockup Image')
                                                    ->image()
                                                    ->imageEditor()
                                                    ->maxSize(2048)
                                                    ->disk('public')
                                                    ->directory('settings')
                                                    ->visibility('public'),
                                            ])
                                            ->collapsible()
                                            ->collapsed(false)
                                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                                            ->grid(2)
                                            ->columnSpanFull(),

                                        Forms\Components\Section::make('Social Proof')
                                            ->description('Kelola logo partner dan testimoni pelanggan.')
                                            ->schema([
                                                Forms\Components\Repeater::make('landing_partner_logos')
                                                    ->label('Partner / Client Logos')
                                                    ->schema([
                                                        Forms\Components\FileUpload::make('image')
                                                            ->image()
                                                            ->directory('settings')
                                                            ->label('Logo Image')
                                                            ->required(),
                                                        Forms\Components\TextInput::make('name')
                                                            ->label('Restaurant Name')
                                                            ->required(),
                                                    ])
                                                    ->grid(4)
                                                    ->collapsible()
                                                    ->reorderable(),

                                                Forms\Components\Repeater::make('landing_testimonials')
                                                    ->label('Testimonials')
                                                    ->schema([
                                                        Forms\Components\FileUpload::make('avatar')
                                                            ->image()
                                                            ->avatar()
                                                            ->directory('settings')
                                                            ->label('Photo'),
                                                        Forms\Components\TextInput::make('name')
                                                            ->label('Customer Name')
                                                            ->required(),
                                                        Forms\Components\TextInput::make('role')
                                                            ->label('Role (e.g. Owner of Resto X)')
                                                            ->required(),
                                                        Forms\Components\Textarea::make('quote')
                                                            ->label('Testimonial Quote')
                                                            ->rows(3)
                                                            ->required(),
                                                        Forms\Components\Select::make('rating')
                                                            ->options([
                                                                5 => '5 Stars',
                                                                4 => '4 Stars',
                                                                3 => '3 Stars',
                                                            ])
                                                            ->default(5)
                                                            ->required(),
                                                    ])
                                                    ->grid(2)
                                                    ->collapsible()
                                                    ->reorderable(),
                                            ])
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Subscription')
                            ->icon('heroicon-m-calendar-days')
                            ->schema([
                                Forms\Components\Section::make('Subscription Thresholds')
                                    ->description('Atur ambang batas waktu untuk peringatan dan perpanjangan langganan.')
                                    ->schema([
                                        Forms\Components\TextInput::make('subscription_expiry_warning_days')
                                            ->label('Ambang Batas Peringatan & Perpanjangan (Hari)')
                                            ->helperText('Jumlah hari sebelum kedaluwarsa untuk memunculkan peringatan kuning di dasbor, mengirim email reminder, dan memperbolehkan user untuk memperpanjang paket yang sama.')
                                            ->numeric()
                                            ->required()
                                            ->minValue(1)
                                            ->maxValue(30)
                                            ->suffix('Hari'),
                                    ]),
                            ]),

                    ])
                    ->columnSpanFull(),
            ]);
    }
}
