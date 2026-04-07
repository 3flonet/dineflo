<x-mail::message>
# Halo, {{ $customerName }}!

@if($isFullRefund)
Kami ingin memberitahukan bahwa **pengembalian dana penuh (Full Refund)** untuk pesanan Anda telah diproses.
@else
Kami ingin memberitahukan bahwa **pengembalian dana sebagian (Partial Refund)** untuk pesanan Anda telah diproses.
@endif

---

**Rincian Pengembalian Dana:**

- **Nomor Pesanan:** #{{ $orderNumber }}
- **Restoran:** {{ $restaurantName }}
- **Alasan Refund:** {{ $reason }}
- **Jumlah yang Dikembalikan:** **Rp {{ $refundAmount }}**
- **Total Pesanan Asal:** Rp {{ $totalAmount }}
- **Jenis Refund:** {{ $isFullRefund ? 'Pengembalian Penuh' : 'Pengembalian Sebagian' }}

---

Dana akan dikembalikan melalui metode pembayaran yang sama dengan transaksi awal Anda. Proses pengembalian dapat membutuhkan waktu tergantung pada kebijakan bank atau penyedia pembayaran Anda.

@if(!$isFullRefund)
> Pesanan Anda masih aktif untuk item-item yang tidak di-refund.
@endif

<x-mail::button :url="route('order.public_receipt', ['order' => $order->id])">
Lihat Nota Pesanan
</x-mail::button>

Jika Anda memiliki pertanyaan atau keberatan, silakan hubungi tim kami langsung di restoran.

Terima kasih atas pengertian Anda.<br>
{{ $restaurantName }}
</x-mail::message>
