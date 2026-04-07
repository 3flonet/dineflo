<x-mail::message>
# 🎁 Halo {{ $giftCard->recipient_name }},

@if($giftCard->personal_message)
> *"{{ $giftCard->personal_message }}"*

@endif
Kamu mendapatkan **Gift Card** dari **{{ $giftCard->restaurant->name }}** senilai:

<x-mail::panel>
## {{ $giftCard->formatted_amount }}

**🔑 Kode Gift Card:**

# `{{ $giftCard->code }}`

| Detail | Info |
|---|---|
| 💰 Saldo Tersedia | **{{ $giftCard->formatted_balance }}** |
| 📅 Berlaku Sampai | **{{ $giftCard->expires_at ? $giftCard->expires_at->format('d M Y') : 'Tidak ada batas waktu' }}** |
</x-mail::panel>

**Cara Menggunakan:**
Tunjukkan atau ketikkan kode di atas saat checkout di kasir, kiosk, atau halaman pemesanan **{{ $giftCard->restaurant->name }}**.

@if($giftCard->restaurant->whatsapp_number ?? $giftCard->restaurant->phone)
Jika ada pertanyaan, hubungi kami di {{ $giftCard->restaurant->whatsapp_number ?? $giftCard->restaurant->phone }}.
@endif

Salam hangat,<br>
**{{ $giftCard->restaurant->name }}**
</x-mail::message>
