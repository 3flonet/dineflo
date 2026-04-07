<?php

namespace App\Filament\Restaurant\Resources;

use App\Filament\Restaurant\Resources\EmailBroadcastResource\Pages;
use App\Models\EmailBroadcast;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User; // Added for policy context, though policy is not in this file
use Illuminate\Auth\Access\HandlesAuthorization; // Added for policy context, though policy is not in this file

class EmailBroadcastResource extends Resource
{
    protected static ?string $model = EmailBroadcast::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationGroup = 'LOYALitas & MARKETING';
    protected static ?string $navigationLabel = 'Email Broadcast';
    protected static ?string $modelLabel = 'Email Broadcast';
    protected static ?string $pluralModelLabel = 'Email Broadcasts';
    protected static ?int $navigationSort = 5;

    public static function canAccess(): bool
    {
        return parent::canAccess() && auth()->user()->hasFeature('Email Marketing');
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
                            ->placeholder('e.g. Pengumuman Menu Baru'),
                        
                        Forms\Components\TextInput::make('subject')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g. Hai {{member_name}}, Cobain Menu Baru Kami! 🍱'),
                        
                        Forms\Components\RichEditor::make('content')
                            ->required()
                            ->columnSpanFull()
                            ->helperText('Gunakan {{member_name}}, {{points_balance}}, {{tier}}, dan {{restaurant_name}} sebagai placeholder.')
                            ->placeholder('Halo {{member_name}}, kami punya pengumuman penting...'),
                    ])->columns(2),

                Forms\Components\Section::make('Delivery & Segmentation')
                    ->description('Tentukan kapan dan kepada siapa email ini dikirim.')
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
                    ->label('Stats (Sent/Open)')
                    ->description(fn (EmailBroadcast $record) => $record->total_recipients > 0 
                        ? round(($record->open_count / max($record->total_recipients, 1)) * 100, 1) . '% Open Rate' 
                        : 'Belum ada penerima')
                    ->getStateUsing(fn (EmailBroadcast $record) => "$record->sent_count / $record->open_count")
                    ->badge()
                    ->color('primary'),

                Tables\Columns\ViewColumn::make('progress')
                    ->label('Delivery Progress')
                    ->view('filament.tables.columns.campaign-progress'),

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
                    ->visible(fn (EmailBroadcast $record) => in_array($record->status, ['active', 'draft', 'scheduled', 'completed']))
                    ->requiresConfirmation()
                    ->action(function (EmailBroadcast $record): void {
                        \App\Jobs\ProcessManualCampaign::dispatch($record);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Proses Broadcast Dimulai')
                            ->body('Email sedang dikirim ke antrean massal.')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('sendTest')
                    ->label('Send Test')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('info')
                    ->form([
                        Forms\Components\TextInput::make('recipient_email')
                            ->label('Email Penerima')
                            ->email()
                            ->default(fn () => auth()->user()->email)
                            ->required(),
                    ])
                    ->action(function (EmailBroadcast $record, array $data): void {
                        try {
                            app(\App\Services\MarketingMailService::class)->sendTestEmail($record, $data['recipient_email']);
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Email Uji Coba Terkirim')
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
            'index' => Pages\ListEmailBroadcasts::route('/'),
            'create' => Pages\CreateEmailBroadcast::route('/create'),
            'edit' => Pages\EditEmailBroadcast::route('/{record}/edit'),
        ];
    }
}
