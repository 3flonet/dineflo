<x-mail::message>
# Halo, {{ $user->name }}!

Terima kasih telah berlangganan Paket **{{ $planName }}** di {{ config('app.name', 'Dineflo') }}. Pembayaran Anda telah kami terima dan paket sudah diaktifkan.

Berikut adalah rincian pembayaran Anda:
- **Jumlah Pembayaran:** Rp {{ $amount }}
- **Tanggal:** {{ $paidAt }}
- **Status:** LUNAS (PAID)

Kami telah melampirkan invoice resmi dalam format PDF untuk keperluan administrasi dan perpajakan Anda.

<x-mail::button :url="config('app.url')">
Masuk ke Panel Restoran
</x-mail::button>

Semoga {{ config('app.name', 'Dineflo') }} dapat membantu operasional bisnis kuliner Anda menjadi lebih efisien dan menguntungkan. Jika ada pertanyaan, silakan hubungi tim support kami melalui balasan email ini.

Terima kasih,<br>
Tim {{ config('app.name', 'Dineflo') }}
</x-mail::message>
