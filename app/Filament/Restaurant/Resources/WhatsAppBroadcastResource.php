<?php

namespace App\Filament\Restaurant\Resources;

use App\Filament\Restaurant\Resources\WhatsAppBroadcastResource\Pages;
use App\Models\WhatsAppBroadcast;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WhatsAppBroadcastResource extends Resource
{
    protected static ?string $model = WhatsAppBroadcast::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationGroup = 'LOYALitas & MARKETING';
    protected static ?string $navigationLabel = 'WA Broadcast';
    protected static ?string $modelLabel = 'WA Broadcast';
    protected static ?string $pluralModelLabel = 'WA Broadcasts';
    protected static ?int $navigationSort = 7;

    public static function canAccess(): bool
    {
        return parent::canAccess() && auth()->user()->hasFeature('WhatsApp Marketing');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('trigger_type', 'manual');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Broadcast Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g. Pengumuman Menu Baru (WA)'),
                        
                        Forms\Components\Textarea::make('content')
                            ->required()
                            ->rows(5)
                            ->columnSpanFull()
                            ->helperText('Gunakan {{member_name}}, {{points_balance}}, {{tier}}, dan {{restaurant_name}} sebagai placeholder.')
                            ->placeholder("Halo {{member_name}},\n\nkami punya pengumuman penting..."),
                    ])->columns(1),

                Forms\Components\Section::make('Delivery & Segmentation')
                    ->description('Tentukan kapan dan kepada siapa pesan ini dikirim.')
                    ->schema([
                        Forms\Components\Hidden::make('trigger_type')->default('manual'),

                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\Select::make('segmentation_type')
                                    ->label('Target Segmentation')
                                    ->options([
                                        'all' => 'Semua Member',
                                        'tiers' => 'Berdasarkan Tier',
                                    ])
                                    ->required()
                                    ->default('all')
                                    ->live(),

                                Forms\Components\CheckboxList::make('target_tiers')
                                    ->label('Target Membership Tiers')
                                    ->options([
                                        'bronze' => 'Bronze Member',
                                        'silver' => 'Silver Member',
                                        'gold' => 'Gold Member',
                                    ])
                                    ->columns(3)
                                    ->visible(fn ($get) => $get('segmentation_type') === 'tiers')
                                    ->required(fn ($get) => $get('segmentation_type') === 'tiers'),
                            ])->columns(1),

                        Forms\Components\DateTimePicker::make('scheduled_at')
                            ->label('Jadwal Pengiriman')
                            ->helperText('Kosongkan untuk Kirim Langsung.'),

                        Forms\Components\Select::make('discount_id')
                            ->label('Attached Discount/Voucher')
                            ->relationship('discount', 'name', fn (Builder $query) => $query->where('is_active', true))
                            ->searchable()
                            ->preload()
                            ->helperText('Jika dipilih, tag [[voucher_code]] dapat digunakan di konten.'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Siap Kirim')
                            ->helperText('Matikan jika siaran ini masih draf.')
                            ->default(true)
                            ->onColor('success')
                            ->offColor('danger'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'scheduled' => 'warning',
                        'sending' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                Tables\Columns\TextColumn::make('engagement')
                    ->label('Stats (Sent)')
                    ->getStateUsing(fn (WhatsAppBroadcast $record) => "$record->sent_count")
                    ->badge()
                    ->color('primary'),

                Tables\Columns\ViewColumn::make('progress')
                    ->label('Delivery Progress')
                    ->view('filament.tables.columns.campaign-progress-wa'), // Need to create this view or reuse email one with mapping

                Tables\Columns\TextColumn::make('scheduled_at')
                    ->label('Jadwal')
                    ->dateTime()
                    ->placeholder('Kirim Langsung'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'scheduled' => 'Scheduled',
                        'sending' => 'Sending',
                        'completed' => 'Completed',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('broadcastNow')
                    ->label('Broadcast Now')
                    ->icon('heroicon-o-megaphone')
                    ->color('success')
                    ->visible(fn (WhatsAppBroadcast $record) => in_array($record->status, ['active', 'draft', 'scheduled', 'completed']))
                    ->requiresConfirmation()
                    ->action(function (WhatsAppBroadcast $record): void {
                        \App\Jobs\ProcessManualWhatsAppBroadcast::dispatch($record);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Proses Broadcast WA Dimulai')
                            ->body('Pesan sedang dikirim ke antrean massal.')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('sendTest')
                    ->label('Send Test')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('info')
                    ->form([
                        Forms\Components\TextInput::make('recipient_phone')
                            ->label('Nomor WhatsApp Penerima')
                            ->tel()
                            ->placeholder('e.g. 08123456789')
                            ->required(),
                    ])
                    ->action(function (WhatsAppBroadcast $record, array $data): void {
                        try {
                            app(\App\Services\MarketingWhatsAppService::class)->sendTestMessage($record, $data['recipient_phone']);
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Pesan Uji Coba Terkirim')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Gagal Mengirim')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
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
            'index' => Pages\ListWhatsAppBroadcasts::route('/'),
            'create' => Pages\CreateWhatsAppBroadcast::route('/create'),
            'edit' => Pages\EditWhatsAppBroadcast::route('/{record}/edit'),
        ];
    }
}
