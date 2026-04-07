<?php

namespace App\Filament\Restaurant\Resources;

use App\Filament\Restaurant\Resources\ReservationResource\Pages;
use App\Filament\Restaurant\Resources\ReservationResource\RelationManagers;
use App\Models\Reservation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Reservasi & Antrean';
    protected static ?string $navigationGroup = 'OPERASIONAL';
    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        return parent::canAccess() && auth()->user()->hasFeature('Table Reservation');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Customer Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Pelanggan')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('No. Telepon / WhatsApp')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Alamat Email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('tracking_hash')
                            ->label('Tracking Hash')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn ($record) => $record !== null)
                            ->helperText(fn ($record) => $record ? route('reservations.track', $record->tracking_hash) : null),
                    ])->columns(2),

                Forms\Components\Section::make('Reservation Details')
                    ->schema([
                        Forms\Components\DateTimePicker::make('reservation_time')
                            ->label('Tanggal & Jam Reservasi')
                            ->required(),
                        Forms\Components\TextInput::make('guest_count')
                            ->label('Jumlah Tamu')
                            ->required()
                            ->numeric()
                            ->default(1),
                        Forms\Components\Select::make('table_id')
                            ->label('Assign Meja')
                            ->relationship(
                                'table',
                                'name',
                                fn (Builder $query) => $query
                                    ->where('is_active', true)
                                    ->where('status', '!=', 'occupied') // Meja yang sedang diduduki tidak bisa di-assign
                            )
                            ->getOptionLabelFromRecordUsing(fn (\App\Models\Table $record) => "Meja {$record->name} (" . strtoupper($record->status) . ")")
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->live()
                            ->hint(function ($state) {
                                if (!$state) return null;
                                $table = \App\Models\Table::find($state);
                                if (!$table) return null;

                                if ($table->status === 'dirty') return '🧹 Perlu dibersihkan sebelum digunakan';
                                if ($table->status === 'reserved') return '📅 Sudah dipesan di waktu lain';
                                return '✅ Meja tersedia';
                            })
                            ->hintColor(fn ($state) => match(\App\Models\Table::find($state)?->status) {
                                'available' => 'success',
                                'dirty' => 'warning',
                                'reserved' => 'info',
                                default => 'gray'
                            })
                            ->rules([
                                function () {
                                    return function (string $attribute, $value, \Closure $fail) {
                                        if (!$value) return;
                                        $table = \App\Models\Table::find($value);
                                        if ($table && $table->status === 'occupied') {
                                            $fail("Meja {$table->name} sedang diduduki dan tidak dapat di-assign ke reservasi.");
                                        }
                                    };
                                },
                            ])
                            ->helperText('Hanya meja aktif yang tidak sedang diduduki yang dapat dipilih.'),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending (Menunggu)',
                                'confirmed' => 'Confirmed (Sesuai)',
                                'completed' => 'Completed (Selesai)',
                                'cancelled' => 'Cancelled (Batal)',
                            ])
                            ->default('pending')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan Tambahan (Opsional)')
                            ->placeholder('Contoh: Minta kursi bayi, acara ulang tahun, alergi kacang...')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (Reservation $record) => $record->phone),
                Tables\Columns\TextColumn::make('reservation_time')
                    ->label('Jadwal Reservasi')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('guest_count')
                    ->label('Tamu')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state . ' Orang'),
                Tables\Columns\TextColumn::make('table.name')
                    ->label('Meja')
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn ($state) => $state ?? 'Belum Diatur'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'completed' => 'gray',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListReservations::route('/'),
            'create' => Pages\CreateReservation::route('/create'),
            'edit' => Pages\EditReservation::route('/{record}/edit'),
        ];
    }
}
