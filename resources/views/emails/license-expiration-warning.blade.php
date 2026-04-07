<x-mail::message>
    # Peringatan: Lisensi Dineflo Akan Kadaluarsa

    Halo {{ $customerName }},

    Kami ingin memberitahu bahwa lisensi Dineflo Anda ({{ $licenseKey }}) akan **kadaluarsa dalam {{ $daysRemaining }} hari**.

    ## Detail Lisensi

    - **Nomor Lisensi:** {{ $licenseKey }}
    - **Tanggal Kadaluarsa:** {{ $expiresAt }}
    @if($gracePeriodUntil)
    - **Grace Period Hingga:** {{ $gracePeriodUntil }}

    Anda masih memiliki waktu grace period untuk menggunakan sistem, namun fitur-fitur tertentu mungkin akan terbatas.
    @endif

    ## Tindakan yang Diperlukan

    Untuk memastikan layanan Anda tidak terganggu, silakan segera perbarui lisensi Anda melalui:

    <x-mail::button :url="$renewUrl" color="success">
        Perbarui Lisensi Sekarang
    </x-mail::button>

    ## Butuh Bantuan?

    Jika Anda memiliki pertanyaan atau membutuhkan bantuan, silakan hubungi tim support kami:

    <x-mail::button :url="$supportUrl" color="primary">
        Hubungi Support via WhatsApp
    </x-mail::button>

    Terima kasih telah menggunakan Dineflo!

    **Tim Dineflo**
</x-mail::message>