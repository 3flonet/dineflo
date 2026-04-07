<?php

namespace App\Observers;

use App\Models\Reservation;
use App\Notifications\NewReservationNotification;
use Illuminate\Support\Facades\Notification;

class ReservationObserver
{
    /**
     * Handle the Reservation "created" event.
     */
    public function created(Reservation $reservation): void
    {
        $restaurant = $reservation->restaurant;
        if (!$restaurant) return;

        // 1. Notif to Staff
        $users = $restaurant->users;
        if (!$users->isEmpty()) {
            Notification::send($users, new NewReservationNotification($reservation));
        }

        // 2. Notif to Customer (WhatsApp)
        if ($restaurant->wa_is_active && $reservation->phone) {
            \App\Jobs\SendReservationWhatsApp::dispatch($reservation);
        }

        // 3. Notif to Customer (Email)
        if ($reservation->email) {
            \App\Jobs\SendWhitelabelMail::dispatch(
                $restaurant,
                $reservation->email,
                new \App\Mail\ReservationReceived($reservation)
            );
        }
    }

    /**
     * Handle the Reservation "updated" event.
     */
    public function updated(Reservation $reservation): void
    {
        $restaurant = $reservation->restaurant;
        if (!$restaurant) return;

        // If status changed to confirmed
        if ($reservation->wasChanged('status') && $reservation->status === 'confirmed') {
            
            // 1. Notif to Customer (WhatsApp)
            if ($restaurant->wa_is_active && $reservation->phone) {
                \App\Jobs\SendReservationConfirmedWhatsApp::dispatch($reservation);
            }

            // 2. Notif to Customer (Email)
            if ($reservation->email) {
                \App\Jobs\SendWhitelabelMail::dispatch(
                    $restaurant,
                    $reservation->email,
                    new \App\Mail\ReservationConfirmed($reservation)
                );
            }
        }
    }
}
