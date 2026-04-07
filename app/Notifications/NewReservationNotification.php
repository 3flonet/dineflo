<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;

class NewReservationNotification extends Notification
{
    use Queueable;

    public function __construct(public Reservation $reservation)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title('Reservasi Baru!')
            ->body("Ada reservasi baru atas nama **{$this->reservation->name}** untuk tanggal " . \Carbon\Carbon::parse($this->reservation->reservation_time)->format('d M Y, H:i') . ".")
            ->icon('heroicon-o-calendar-days')
            ->iconColor('success')
            ->actions([
                \Filament\Notifications\Actions\Action::make('view')
                    ->label('Lihat Detail')
                    ->url(\App\Filament\Restaurant\Resources\ReservationResource::getUrl('edit', ['record' => $this->reservation])),
            ])
            ->getDatabaseMessage();
    }
}
