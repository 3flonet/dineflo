<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SystemBroadcastResource\Pages;
use App\Models\SystemBroadcast;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Jobs\ProcessSystemBroadcast;
use Filament\Notifications\Notification;

class SystemBroadcastResource extends Resource
{
    protected static ?string $model = SystemBroadcast::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationGroup = 'Utility';
    protected static ?string $navigationLabel = 'System Broadcast';
    protected static ?string $pluralLabel = 'System Broadcasts';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Broadcast Details')
                    ->schema([
                        Forms\Components\TextInput::make('subject')
                            ->label('Judul / Subject')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('channel')
                            ->label('Kanal Pengiriman')
                            ->options([
                                'email'     => '📧 Email',
                                'whatsapp'  => '💬 WhatsApp',
                                'both'      => '📧 + 💬 Email & WhatsApp',
                            ])
                            ->default('email')
                            ->required()
                            ->live()
                            ->helperText('Pilih kanal yang akan digunakan untuk mengirim pesan ini.'),

                        Forms\Components\Select::make('status')
                            ->options([
                                'draft'     => 'Draft',
                                'scheduled' => 'Scheduled',
                                'sending'   => 'Sending',
                                'sent'      => 'Sent',
                            ])
                            ->default('draft')
                            ->required(),

                        Forms\Components\DateTimePicker::make('scheduled_at')
                            ->label('Jadwalkan Untuk')
                            ->placeholder('Kosongkan untuk kirim langsung saat klik Kirim Sekarang'),

                    ])->columns(2),

                // ── Konten Email ───────────────────────────────────────────
                Forms\Components\Section::make('📧 Konten Email')
                    ->description('Konten ini akan dikirimkan via email. Mendukung format HTML.')
                    ->schema([
                        Forms\Components\RichEditor::make('content')
                            ->label('Isi Pesan Email')
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Forms\Get $get) => in_array($get('channel'), ['email', 'both'])),

                // ── Konten WhatsApp ────────────────────────────────────────
                Forms\Components\Section::make('💬 Konten WhatsApp')
                    ->description('Pesan ini akan dikirim via WhatsApp. Gunakan format WA: *bold*, _italic_, ~strikethrough~.')
                    ->schema([
                        Forms\Components\Textarea::make('wa_message')
                            ->label('Isi Pesan WhatsApp')
                            ->placeholder("Contoh:\n*[DINEFLO]* Halo {nama}, ada pembaruan sistem terbaru!\n\nKunjungi panel Anda untuk informasi lebih lanjut.")
                            ->rows(6)
                            ->helperText('Gunakan *text* untuk bold. Nomor WA diambil dari profil akun owner restoran.')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Forms\Get $get) => in_array($get('channel'), ['whatsapp', 'both'])),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subject')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('channel')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'email'    => '📧 Email',
                        'whatsapp' => '💬 WhatsApp',
                        'both'     => '📧+💬 Keduanya',
                        default    => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'email'    => 'info',
                        'whatsapp' => 'success',
                        'both'     => 'warning',
                        default    => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft'     => 'secondary',
                        'scheduled' => 'warning',
                        'sending'   => 'info',
                        'sent'      => 'success',
                        default     => 'gray',
                    }),
                Tables\Columns\TextColumn::make('total_recipients')
                    ->label('Recipients')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('success_count')
                    ->label('Success')
                    ->color('success')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('failure_count')
                    ->label('Failed')
                    ->color('danger')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('scheduled_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sent_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('channel')
                    ->options([
                        'email'    => '📧 Email',
                        'whatsapp' => '💬 WhatsApp',
                        'both'     => '📧+💬 Keduanya',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft'     => 'Draft',
                        'scheduled' => 'Scheduled',
                        'sending'   => 'Sending',
                        'sent'      => 'Sent',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('sendNow')
                    ->label('Kirim Sekarang')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Kirim Broadcast Sekarang?')
                    ->modalDescription('Pesan akan segera dikirim ke seluruh pemilik restoran aktif. Tindakan ini tidak dapat dibatalkan.')
                    ->action(function (SystemBroadcast $record) {
                        if ($record->status === 'sent' || $record->status === 'sending') {
                            Notification::make()
                                ->title('Sudah dalam proses atau sudah terkirim')
                                ->warning()
                                ->send();
                            return;
                        }

                        ProcessSystemBroadcast::dispatch($record);

                        Notification::make()
                            ->title('Broadcast Sedang Diproses')
                            ->body('Pesan akan segera dikirim ke seluruh pemilik restoran.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (SystemBroadcast $record) => $record->status !== 'sent'),
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
            'index'  => Pages\ListSystemBroadcasts::route('/'),
            'create' => Pages\CreateSystemBroadcast::route('/create'),
            'edit'   => Pages\EditSystemBroadcast::route('/{record}/edit'),
        ];
    }
}
