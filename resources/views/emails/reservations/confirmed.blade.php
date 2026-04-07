<x-mail::message>
# Halo {{ $reservation->name }},

Kabar baik! Reservasi Anda di **{{ $reservation->restaurant->name }}** telah **DIKONFIRMASI**.

Berikut adalah detail meja Anda:

<x-mail::panel>
**Nomor Meja:** {{ $reservation->table?->table_number ?? 'Akan diinformasikan saat tiba' }}  
**Waktu:** {{ $reservation->reservation_time->format('d M Y, H:i') }}  
**Jumlah Tamu:** {{ $reservation->guest_count }} Orang
</x-mail::panel>

Harap tunjukkan link pelacakan atau email ini kepada staf kami saat Anda tiba di restoran.

@if($reservation->table)
Staf kami telah menyiapkan meja terbaik untuk Anda!
@endif

<x-mail::button :url="route('reservations.track', $reservation->tracking_hash)">
Lihat Detail Meja & Status
</x-mail::button>

Sampai jumpa di restoran!

Salam hangat,<br>
**{{ $reservation->restaurant->name }}**
</x-mail::message>
