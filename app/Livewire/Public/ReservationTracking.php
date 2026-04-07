<?php

namespace App\Livewire\Public;

use Livewire\Component;

use App\Models\Reservation;
use Livewire\Attributes\Layout;

class ReservationTracking extends Component
{
    public Reservation $reservation;

    public function mount($hash)
    {
        $this->reservation = Reservation::where('tracking_hash', $hash)->firstOrFail();
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        $this->reservation->refresh();
        
        return view('livewire.public.reservation-tracking')
            ->layoutData(['restaurant' => $this->reservation->restaurant]);
    }
}
