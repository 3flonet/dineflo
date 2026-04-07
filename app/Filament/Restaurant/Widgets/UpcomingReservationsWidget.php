<?php

namespace App\Filament\Restaurant\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Builder;

class UpcomingReservationsWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'half';

    public static function canView(): bool
    {
        return auth()->user()->hasFeature('Table Reservation')
            && auth()->user()->can('widget_UpcomingReservationsWidget');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Reservation::query()
                    ->where('status', 'pending')
                    ->orWhere('status', 'confirmed')
                    ->orderBy('reservation_time', 'asc')
                    ->limit(5)
            )
            ->heading('Reservasi Mendatang & Waitlist')
            ->description('Daftar pelanggan yang akan datang dalam waktu dekat.')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Pelanggan')
                    ->weight('bold')
                    ->description(fn (Reservation $record) => $record->phone),
                Tables\Columns\TextColumn::make('reservation_time')
                    ->label('Waktu')
                    ->dateTime('d M, H:i')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('guest_count')
                    ->label('Tamu')
                    ->numeric()
                    ->formatStateUsing(fn ($state) => $state . ' Org'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
            ])
            ->actions([
                Tables\Actions\Action::make('Process')
                    ->label('Kelola')
                    ->url(fn (Reservation $record): string => \App\Filament\Restaurant\Resources\ReservationResource::getUrl('edit', ['record' => $record]))
                    ->button()
                    ->outlined()
                    ->size('sm'),
            ])
            ->paginated(false);
    }
}
