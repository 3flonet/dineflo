<x-mail::message>
# Halo {{ $reservation->name }},

Terima kasih telah melakukan reservasi di **{{ $reservation->restaurant->name }}**.

Kami telah menerima permintaan reservasi Anda dengan detail sebagai berikut:

- **Tanggal:** {{ $reservation->reservation_time->format('d M Y') }}
- **Waktu:** {{ $reservation->reservation_time->format('H:i') }}
- **Jumlah Tamu:** {{ $reservation->guest_count }} Orang

Anda dapat memantau status reservasi Anda dan melihat nomor meja melalui tautan di bawah ini:

<x-mail::button :url="route('reservations.track', $reservation->tracking_hash)">
Pantau Status Reservasi
</x-mail::button>

Jika ada pertanyaan, silakan hubungi kami melalui WhatsApp di {{ $reservation->restaurant->whatsapp_number ?? $reservation->restaurant->phone }}.

Salam hangat,<br>
**{{ $reservation->restaurant->name }}**
</x-mail::message>
