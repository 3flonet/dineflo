<?php

namespace App\Livewire\Restaurant;

use App\Models\Reservation;
use App\Models\Restaurant;
use Livewire\Component;
use Livewire\Attributes\Layout;

class ReservationForm extends Component
{
    use \App\Traits\NormalizesPhone;

    public Restaurant $restaurant;
    public $name;
    public $phone;
    public $email;
    public $date;
    public $time;
    public $guest_count = 2;
    public $notes;
    public $success = false;
    public $lastReservation;
    public $minTime;
    public $maxTime;
    public $isClosed = false;
    public $timeSlots = [];

    public function mount(Restaurant $restaurant)
    {
        $this->restaurant = $restaurant;
        // Default to today if possible
        $this->date = date('Y-m-d');
        $this->generateTimeSlots();
    }

    public function updatedDate()
    {
        $this->generateTimeSlots();
        $this->time = null; // Reset selected time when date changes
    }

    protected function generateTimeSlots()
    {
        if (!$this->date) {
            $this->timeSlots = [];
            $this->isClosed = false;
            return;
        }

        $reservationDate = \Carbon\Carbon::parse($this->date);
        $dayOfWeek = strtolower($reservationDate->format('l'));
        $isToday = $reservationDate->isToday();
        
        $openingHours = collect($this->restaurant->opening_hours)->firstWhere('day', $dayOfWeek);

        if (!$openingHours || ($openingHours['is_closed'] ?? false)) {
            $this->isClosed = true;
            $this->timeSlots = [];
            $this->addError('date', "Maaf, restoran kami tutup pada hari " . ucfirst($dayOfWeek) . ".");
            return;
        }

        $this->isClosed = false;
        $this->resetErrorBag('date');

        $openTime = \Carbon\Carbon::parse($openingHours['open']);
        $closeTime = \Carbon\Carbon::parse($openingHours['close']);
        
        $slots = [];
        $current = $openTime->copy();

        // Buffer 1 hour from now if today
        $now = now()->addHour();

        while ($current <= $closeTime) {
            // If today, only show slots at least 1 hour from now
            if (!$isToday || $current->greaterThan($now)) {
                $slots[] = $current->format('H:i');
            }
            $current->addMinutes(30);
        }

        $this->timeSlots = $slots;
        $this->minTime = $openTime->format('H:i');
        $this->maxTime = $closeTime->format('H:i');
    }

    public function selectTime($time)
    {
        $this->time = $time;
    }

    public function submit()
    {
        $this->validate([
            'name' => 'required|min:3|max:100', // added max
            'phone' => 'required|min:10|max:16', // added max
            'email' => 'nullable|email|max:100', // added max
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'guest_count' => 'required|integer|min:1|max:100', // added max
            'notes' => 'nullable|string|max:1000', // increased max, will sanitize
        ]);

        // Rate Limiting: Max 3 reservations per minute from one IP
        $key = 'reserve:' . request()->ip();
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($key);
            $this->addError('name', "Terlalu banyak permintaan. Silakan coba lagi dalam $seconds detik.");
            return;
        }

        // Validate Operational Hours
        $reservationDate = \Carbon\Carbon::parse($this->date);
        $dayOfWeek = strtolower($reservationDate->format('l')); // e.g., 'monday'
        
        $openingHours = collect($this->restaurant->opening_hours)->firstWhere('day', $dayOfWeek);

        if (!$openingHours || ($openingHours['is_closed'] ?? false)) {
            $this->addError('date', "Maaf, restoran kami tutup pada hari " . ucfirst($dayOfWeek) . ".");
            return;
        }

        $reservationTime = \Carbon\Carbon::parse($this->time)->format('H:i');
        $openTime = \Carbon\Carbon::parse($openingHours['open'])->format('H:i');
        $closeTime = \Carbon\Carbon::parse($openingHours['close'])->format('H:i');

        if ($reservationTime < $openTime || $reservationTime > $closeTime) {
            $this->addError('time', "Jam reservasi harus di antara jam operasional kami ($openTime - $closeTime).");
            return;
        }

        // Sanitization: Remove HTML tags from user inputs
        $cleanName = strip_tags($this->name);
        $cleanNotes = strip_tags($this->notes);

        $fullReservationTime = $this->date . ' ' . $this->time;

        $this->lastReservation = Reservation::create([
            'restaurant_id' => $this->restaurant->id,
            'name' => $cleanName,
            'phone' => $this->phone,
            'email' => $this->email,
            'reservation_time' => $fullReservationTime,
            'guest_count' => $this->guest_count,
            'notes' => $cleanNotes,
            'status' => 'pending',
        ]);

        \Illuminate\Support\Facades\RateLimiter::hit($key, 60);

        $this->success = true;
        $this->reset(['name', 'phone', 'email', 'date', 'time', 'guest_count', 'notes']);
        
        $message = $this->restaurant->wa_is_active 
            ? 'Reservasi Anda telah berhasil dikirim! Silakan tunggu konfirmasi dari kami via WhatsApp.'
            : 'Reservasi Anda telah berhasil dikirim! Kami akan segera menghubungi Anda.';

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $message
        ]);
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.restaurant.reservation-form')
            ->layoutData(['restaurant' => $this->restaurant]);
    }
}
