<x-mail::message>
# Halo, {{ $customerName }}!

Terima kasih telah memesan di **{{ $restaurantName }}**. Kami senang bisa melayani Anda.

Berikut adalah ringkasan pesanan Anda:
- **Nomor Pesanan:** #{{ $orderNumber }}
- **Total Pembayaran:** Rp {{ $totalAmount }}
- **Status Pembayaran:** {{ strtoupper($order->payment_status) }}

<x-mail::button :url="route('order.track', $order->tracking_hash)" color="success">
Lacak Pesanan Saya (Live)
</x-mail::button>

Kami telah melampirkan nota/invoice resmi dalam format PDF pada email ini sebagai bukti pembayaran yang sah.

<x-mail::button :url="route('order.summary', $order->id)">
Lihat Rincian Pesanan
</x-mail::button>

**Detail Pesanan:**
<x-mail::table>
| Menu | Jumlah | Harga | Total |
|:-----|:----:|:------:|:------:|
@foreach($order->items as $item)
| {{ $item->menuItem->name }} {{ $item->variant ? '('.$item->variant->name.')' : '' }} | {{ $item->quantity }} | {{ number_format($item->unit_price, 0, ',', '.') }} | {{ number_format($item->total_price, 0, ',', '.') }} |
@endforeach
| | | **Subtotal** | **{{ number_format($order->subtotal, 0, ',', '.') }}** |
@if($order->additional_fees_amount > 0)
| | | Biaya Tambahan | {{ number_format($order->additional_fees_amount, 0, ',', '.') }} |
@endif
@if($order->tax_amount > 0)
| | | Pajak | {{ number_format($order->tax_amount, 0, ',', '.') }} |
@endif
@if($order->voucher_discount_amount > 0)
| | | Voucher | -{{ number_format($order->voucher_discount_amount, 0, ',', '.') }} |
@endif
@if($order->points_discount_amount > 0)
| | | Poin | -{{ number_format($order->points_discount_amount, 0, ',', '.') }} |
@endif
@if($order->gift_card_discount_amount > 0)
| | | Gift Card | -{{ number_format($order->gift_card_discount_amount, 0, ',', '.') }} |
@endif
| | | **TOTAL** | **{{ number_format($order->total_amount, 0, ',', '.') }}** |
</x-mail::table>

Jika Anda membutuhkan bantuan atau informasi lebih lanjut terkait pesanan ini, silakan hubungi tim kami di restoran.

Terima kasih,<br>
{{ $restaurantName }}
</x-mail::message>
