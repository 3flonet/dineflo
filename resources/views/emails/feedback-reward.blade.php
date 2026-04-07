<x-mail::message>
# Terima kasih, {{ $customerName }}!

Kami sangat menghargai ulasan yang Anda berikan untuk **{{ $restaurant->name }}**. Masukan Anda sangat berarti bagi kami untuk terus meningkatkan pelayanan.

Sebagai bentuk apresiasi, kami memberikan hadiah spesial untuk Anda:

@if($rewardType === 'points')
**+{{ $rewardValue }} Poin Loyalitas**

Saldo poin Anda telah berhasil ditambahkan. Kumpulkan terus poinnya untuk ditukarkan dengan berbagai penawaran menarik di kunjungan berikutnya!
@else
**Voucher Diskon: {{ $rewardValue }}**

Gunakan kode voucher di atas pada pesanan Anda berikutnya untuk menikmati potongan harga spesial.
@endif

Terima kasih sekali lagi atas dukungannya!

Sampai jumpa kembali, <br>
**{{ $restaurant->name }}**
</x-mail::message>
