<?php

namespace App\Filament\Restaurant\Resources;

use App\Filament\Restaurant\Resources\GiftCardResource\Pages;
use App\Models\GiftCard;
use App\Settings\GeneralSettings;
use App\Jobs\SendWhitelabelMail;
use App\Mail\GiftCardSent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use App\Services\WhatsApp\WhatsAppService;

class GiftCardResource extends Resource
{
    protected static ?string $model = GiftCard::class;
    protected static ?string $navigationIcon = 'heroicon-o-gift';
    protected static ?string $navigationLabel = 'Gift Cards';
    protected static ?string $navigationGroup = 'Marketing';
    protected static ?int $navigationSort = 50;

    protected static function getSiteName(): string
    {
        try {
            return app(\App\Settings\GeneralSettings::class)->site_name;
        } catch (\Throwable $e) {
            return config('app.name', 'Dineflo');
        }
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        $restaurant = Filament::getTenant();
        return $user?->hasFeature('Gift Cards') && $user?->can('view_any_gift_card');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi Penerima')
                ->schema([
                    Forms\Components\TextInput::make('recipient_name')
                        ->label('Nama Penerima')
                        ->required()
                        ->maxLength(100)
                        ->placeholder('Contoh: Budi Santoso'),

                    Forms\Components\TextInput::make('recipient_phone')
                        ->label('No. WhatsApp Penerima')
                        ->tel()
                        ->placeholder('08123456789')
                        ->helperText('Kode Gift Card akan dikirim via WhatsApp ke nomor ini.'),

                    Forms\Components\TextInput::make('recipient_email')
                        ->label('Email Penerima (Opsional)')
                        ->email()
                        ->placeholder('penerima@email.com'),

                    Forms\Components\Textarea::make('personal_message')
                        ->label('Pesan Personal')
                        ->placeholder('Selamat ulang tahun! Semoga harimu menyenangkan 🎂')
                        ->rows(3)
                        ->columnSpanFull(),
                ])->columns(2),

            Forms\Components\Section::make('Nilai & Masa Berlaku')
                ->schema([
                    Forms\Components\TextInput::make('original_amount')
                        ->label('Nominal Gift Card')
                        ->required()
                        ->numeric()
                        ->prefix('Rp')
                        ->minValue(10000)
                        ->placeholder('100000')
                        ->helperText('Minimal Rp 10.000'),

                    Forms\Components\DatePicker::make('expires_at')
                        ->label('Berlaku Sampai')
                        ->nullable()
                        ->minDate(now()->addDay())
                        ->helperText('Kosongkan jika tidak ada batas waktu.'),
                ])->columns(2),

            Forms\Components\Section::make('Info Kode')
                ->schema([
                    Forms\Components\Placeholder::make('code_info')
                        ->label('Kode Gift Card')
                        ->content(fn ($record) => $record?->code ?? 'Akan di-generate otomatis saat disimpan')
                        ->visibleOn('edit'),
                    Forms\Components\Placeholder::make('balance_info')
                        ->label('Sisa Saldo')
                        ->content(fn ($record) => $record?->formatted_balance ?? '-')
                        ->visibleOn('edit'),
                ])->columns(2)->visibleOn('edit'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Kode disalin!')
                    ->fontFamily('mono')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('recipient_name')
                    ->label('Penerima')
                    ->searchable()
                    ->description(fn ($record) => $record->recipient_phone),

                Tables\Columns\TextColumn::make('original_amount')
                    ->label('Nominal')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('remaining_balance')
                    ->label('Sisa Saldo')
                    ->money('IDR')
                    ->sortable()
                    ->color(fn ($record) => $record->remaining_balance <= 0 ? 'danger' : 'success'),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'active',
                        'danger'  => 'used',
                        'warning' => 'expired',
                        'gray'    => 'cancelled',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'active'    => 'Aktif',
                        'used'      => 'Habis',
                        'expired'   => 'Kadaluarsa',
                        'cancelled' => 'Dibatalkan',
                        default     => $state,
                    }),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Berlaku s/d')
                    ->date('d M Y')
                    ->placeholder('Tidak ada batas')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active'    => 'Aktif',
                        'used'      => 'Habis',
                        'expired'   => 'Kadaluarsa',
                        'cancelled' => 'Dibatalkan',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('send_notification')
                    ->label('Kirim Notifikasi')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('primary')
                    ->visible(fn ($record) => $record->status === 'active' && ($record->recipient_phone || $record->recipient_email))
                    ->action(function ($record) {
                        $restaurant = Filament::getTenant();
                        $result = self::dispatchGiftCardNotifications($record, $restaurant);

                        if (empty($result['sent'])) {
                            Notification::make()
                                ->warning()
                                ->title('Notifikasi tidak terkirim')
                                ->body($result['reason'] ?? 'Tidak ada channel pengiriman yang aktif atau terkonfigurasi.')
                                ->send();
                        } else {
                            Notification::make()
                                ->success()
                                ->title('Notifikasi berhasil dikirim!')
                                ->body('Terkirim via: ' . implode(' & ', $result['sent']))
                                ->send();
                        }
                    }),

                Tables\Actions\Action::make('cancel')
                    ->label('Batalkan')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'active')
                    ->action(fn ($record) => $record->update(['status' => 'cancelled'])),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    /**
     * Smart dispatcher: kirim Gift Card via semua channel yang tersedia & valid.
     * Kondisi WA  : recipient_phone ada AND restaurant->wa_is_active
     * Kondisi Email: recipient_email ada AND SMTP valid (system atau custom)
     *
     * @return array ['sent' => ['WhatsApp', 'Email'], 'reason' => '...']
     */
    public static function dispatchGiftCardNotifications(GiftCard $card, $restaurant): array
    {
        $sent   = [];
        $reason = null;

        // ── Channel 1: WhatsApp ──────────────────────────────────────────────
        if ($card->recipient_phone && $restaurant->wa_is_active) {
            $waResult = WhatsAppService::sendMessage(
                $restaurant,
                $card->recipient_phone,
                self::buildWhatsAppMessage($card, $restaurant)
            );
            if ($waResult) {
                $sent[] = 'WhatsApp';
            }
        }

        // ── Channel 2: Email ─────────────────────────────────────────────────
        if ($card->recipient_email) {
            // Cek apakah SMTP tersedia: system SMTP atau custom SMTP restoran
            $settings     = app(GeneralSettings::class);
            $hasSystemSmtp = !empty($settings->smtp_host) && !empty($settings->smtp_username);
            $hasCustomSmtp = $restaurant->owner->hasFeature('Remove Branding')
                          && $restaurant->email_marketing_provider === 'custom'
                          && !empty($restaurant->email_marketing_smtp_host);

            if ($hasSystemSmtp || $hasCustomSmtp) {
                SendWhitelabelMail::dispatch($restaurant, $card->recipient_email, new GiftCardSent($card));
                $sent[] = 'Email';
            } else {
                $reason = 'Email tidak terkirim: SMTP belum dikonfigurasi.';
            }
        }

        // Jika tidak ada channel sama sekali
        if (empty($sent) && !$reason) {
            $reason = 'Tidak ada kontak (nomor WA / email) yang valid atau channel aktif.';
        }

        return compact('sent', 'reason');
    }

    public static function buildWhatsAppMessage(GiftCard $card, $restaurant): string
    {
        $amount   = 'Rp ' . number_format($card->original_amount, 0, ',', '.');
        $balance  = 'Rp ' . number_format($card->remaining_balance, 0, ',', '.');
        $expiry   = $card->expires_at ? $card->expires_at->format('d M Y') : 'Tidak ada batas waktu';
        $message  = $card->personal_message ? "\n\n💬 _\"{$card->personal_message}\"_" : '';

        return "🎁 *Halo {$card->recipient_name}!*{$message}\n\n"
            . "Kamu mendapatkan *Gift Card* dari *{$restaurant->name}* senilai *{$amount}*!\n\n"
            . "━━━━━━━━━━━━━━━━\n"
            . "🔑 *Kode:* `{$card->code}`\n"
            . "💰 *Saldo:* {$balance}\n"
            . "📅 *Berlaku s/d:* {$expiry}\n"
            . "━━━━━━━━━━━━━━━━\n\n"
            . "Gunakan kode ini saat checkout di meja, kiosk, atau kasir {$restaurant->name}.\n\n"
            . "_Powered by " . self::getSiteName() . "_ 🍽️";
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListGiftCards::route('/'),
            'create' => Pages\CreateGiftCard::route('/create'),
            'view'   => Pages\ViewGiftCard::route('/{record}'),
            'edit'   => Pages\EditGiftCard::route('/{record}/edit'),
        ];
    }
}
