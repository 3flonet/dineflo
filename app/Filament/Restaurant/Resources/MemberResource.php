<?php

namespace App\Filament\Restaurant\Resources;

use App\Filament\Restaurant\Resources\MemberResource\Pages;
use App\Filament\Restaurant\Resources\MemberResource\RelationManagers;
use App\Models\Member;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\RawJs;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Database Member';
    protected static ?string $navigationGroup = 'PELANGGAN';
    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return parent::canAccess() && auth()->user()->hasFeature('Membership & Loyalty');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->canAddMember(\Filament\Facades\Filament::getTenant());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Member')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('whatsapp')
                            ->label('WhatsApp Number')
                            ->required()
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('e.g. 08123456789'),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('birthday')
                            ->label('Tanggal Lahir'),
                    ])->columns(2),

                Forms\Components\Section::make('Status Loyalitas')
                    ->schema([
                        Forms\Components\Select::make('tier')
                            ->options([
                                'bronze' => 'Bronze',
                                'silver' => 'Silver',
                                'gold' => 'Gold',
                            ])
                            ->required()
                            ->default('bronze'),
                        Forms\Components\TextInput::make('points_balance')
                            ->label('Saldo Poin')
                            ->numeric()
                            ->default(0)
                            ->helperText(fn () => 'Poin biasanya bertambah otomatis dari transaksi (Rp ' . number_format(\Filament\Facades\Filament::getTenant()->loyalty_point_rate, 0, ',', '.') . ' = 1 Poin)'),
                        Forms\Components\TextInput::make('total_spent')
                            ->label('Total Belanja')
                            ->prefix('Rp')
                            ->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))
                            ->formatStateUsing(fn ($state) => $state ? number_format((float) $state, 0, ',', '.') : null)
                            ->dehydrateStateUsing(fn ($state) => (float) str_replace('.', '', $state ?? 0))
                            ->default(0)
                            ->helperText('Status Tier akan menyesuaikan ambang batas restoran secara otomatis saat di-simpan'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Member $record) => $record->whatsapp),
                Tables\Columns\TextColumn::make('tier')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'bronze' => 'gray',
                        'silver' => 'info',
                        'gold' => 'warning',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('points_balance')
                    ->label('Poin')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('total_spent')
                    ->label('Total Belanja')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('birthday')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tier')
                    ->options([
                        'bronze' => 'Bronze',
                        'silver' => 'Silver',
                        'gold' => 'Gold',
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
            ->striped()
            ->persistFiltersInSession()
            ->filtersFormColumns(1);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMember::route('/create'),
            'edit' => Pages\EditMember::route('/{record}/edit'),
        ];
    }
}
