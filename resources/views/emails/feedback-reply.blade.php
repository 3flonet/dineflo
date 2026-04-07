<x-mail::message>
# Halo, {{ $customerName }}!

Terima kasih atas ulasan yang Anda berikan untuk **{{ $restaurant->name }}**. Kami telah membaca tanggapan Anda dan berikut adalah balasan dari tim kami:

<x-mail::panel>
**Balasan Kami:**  
{{ $replyMessage }}
</x-mail::panel>

@if($feedback->rating >= 4)
Kami senang Anda memiliki pengalaman yang menyenangkan di tempat kami. Semoga kami bisa terus memberikan pelayanan yang terbaik untuk Anda.
@else
Kami memohon maaf jika ada hal yang kurang berkenan selama kunjungan Anda. Masukan Anda akan kami jadikan evaluasi tim untuk menjadi lebih baik lagi di masa depan.
@endif

Terima kasih sekali lagi atas kunjungan dan dukungannya!

Sampai jumpa kembali, <br>
**{{ $restaurant->name }}**
</x-mail::message>
