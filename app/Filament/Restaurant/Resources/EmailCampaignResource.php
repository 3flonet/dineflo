<?php

namespace App\Filament\Restaurant\Resources;

use App\Filament\Restaurant\Resources\EmailCampaignResource\Pages;
use App\Models\EmailCampaign;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EmailCampaignResource extends Resource
{
    protected static ?string $model = EmailCampaign::class;

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';
    protected static ?string $navigationGroup = 'LOYALitas & MARKETING';
    protected static ?string $navigationLabel = 'Email Automation';
    protected static ?string $modelLabel = 'Email Automation';
    protected static ?string $pluralModelLabel = 'Email Automations';
    protected static ?int $navigationSort = 4;

    public static function canAccess(): bool
    {
        return parent::canAccess() && auth()->user()->hasFeature('Email Marketing');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('trigger_type', '!=', 'manual');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Campaign Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g. Birthday Special Promo'),
                        
                        Forms\Components\TextInput::make('subject')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g. Happy Birthday {{member_name}}! 🎂'),
                        
                        Forms\Components\RichEditor::make('content')
                            ->required()
                            ->columnSpanFull()
                            ->helperText('Gunakan {{member_name}}, {{points_balance}}, {{tier}}, dan {{restaurant_name}} sebagai placeholder.')
                            ->placeholder('Hello {{member_name}}, we have a special gift for you...'),
                    ])->columns(2),

                Forms\Components\Section::make('Automation Settings')
                    ->schema([
                        Forms\Components\Select::make('trigger_type')
                            ->label('Trigger Type')
                            ->options([
                                'birthday' => '🎂 Birthday Treat (3 days before)',
                                'win_back' => '🧟 Win-back Campaign (30 days inactivity)',
                                'tier_up' => '⭐ Tier-Up Appreciation',
                                'welcome' => '🎁 Welcome Member Enrollment',
                                'points_expiring' => '📉 Points Expiring Soon (14 days before)',
                            ])
                            ->required()
                            ->live(),

                        Forms\Components\TextInput::make('delay_days')
                            ->label('Trigger Delay (Days)')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->helperText('Hanya berlaku untuk tipe trigger tertentu.'),

                        Forms\Components\CheckboxList::make('target_tiers')
                            ->label('Target Membership Tiers')
                            ->options([
                                'bronze' => 'Bronze Member',
                                'silver' => 'Silver Member',
                                'gold' => 'Gold Member',
                            ])
                            ->columns(3)
                            ->required(),

                        Forms\Components\Select::make('discount_id')
                            ->label('Attached Discount/Voucher')
                            ->relationship('discount', 'name', fn (Builder $query) => $query->where('is_active', true))
                            ->searchable()
                            ->preload()
                            ->helperText('Jika dipilih, tag [[voucher_code]] dapat digunakan di konten.'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Kampanye Berjalan (Running)')
                            ->helperText('Matikan jika otomasi ini ingin dihentikan sementara.')
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
                
                Tables\Columns\TextColumn::make('trigger_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'birthday' => 'success',
                        'win_back' => 'danger',
                        'tier_up' => 'warning',
                        'welcome' => 'info',
                        'points_expiring' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->getStateUsing(fn (EmailCampaign $record) => $record->is_active ? 'running' : 'paused')
                    ->color(fn (string $state): string => match ($state) {
                        'running' => 'success',
                        'paused' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state)),

                Tables\Columns\TextColumn::make('engagement')
                    ->label('Stats (Sent/Open)')
                    ->getStateUsing(fn (EmailCampaign $record) => "$record->sent_count / $record->open_count")
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('last_run_at')
                    ->label('Terakhir Berjalan')
                    ->dateTime()
                    ->placeholder('Belum pernah'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('trigger_type')
                    ->options([
                        'birthday' => 'Birthday',
                        'win_back' => 'Win-back',
                        'tier_up' => 'Tier-up',
                        'welcome' => 'Welcome',
                    ]),
            ])
            ->actions([
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
                    ->action(function (EmailCampaign $record, array $data): void {
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
            'index' => Pages\ListEmailCampaigns::route('/'),
            'create' => Pages\CreateEmailCampaign::route('/create'),
            'edit' => Pages\EditEmailCampaign::route('/{record}/edit'),
        ];
    }
}
