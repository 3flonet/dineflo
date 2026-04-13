@php
    $siteName = app(\App\Settings\GeneralSettings::class)->site_name ?? config('app.name', 'Dineflo');
    $today = \Carbon\Carbon::now()->translatedFormat('l, d F Y');

    $guides = [
        [
            'color'  => '#7c3aed',
            'bg'     => '#ede9fe',
            'cat'    => 'SISTEM',
            'title'  => 'Manajemen Fitur & Paket',
            'desc'   => 'Atur fitur per paket langganan, hubungan induk-anak, dan limitasi bulanan tiap restoran.',
            'detail' => 'Gunakan Subscription Plan Resource untuk memilih fitur yang ingin diaktifkan. Fitur anak tidak akan muncul jika fitur induknya tidak aktif dalam paket yang sama. Setiap paket dapat dikonfigurasi dengan batasan jumlah outlet, transaksi, dan modul yang berbeda-beda.',
        ],
        [
            'color'  => '#059669',
            'bg'     => '#d1fae5',
            'cat'    => 'KEUANGAN',
            'title'  => 'Pencairan Dana (Withdraw)',
            'desc'   => 'Kelola dan verifikasi pengajuan tarik saldo dari setiap restoran mitra platform.',
            'detail' => 'Cek bukti transfer di rekening bank platform, lalu masuk ke menu Withdraw Requests. Klik Approve setelah transfer dikonfirmasi. Platform otomatis memotong saldo restoran yang bersangkutan.',
        ],
        [
            'color'  => '#0284c7',
            'bg'     => '#dbeafe',
            'cat'    => 'BRANDING',
            'title'  => 'Identitas & White Label',
            'desc'   => 'Ubah nama aplikasi, logo, dan favicon untuk kebutuhan branding ekosistem.',
            'detail' => 'Pergi ke General Settings. Ganti Site Name dan unggah Logo PNG transparan (512x512). Perubahan berlaku instan di seluruh ekosistem termasuk email notifikasi dan label panduan.',
        ],
        [
            'color'  => '#d97706',
            'bg'     => '#fef3c7',
            'cat'    => 'TEKNIS',
            'title'  => 'Monitor Real-time KDS',
            'desc'   => 'Pantau status pesanan di layar dapur dan pastikan Websocket berjalan stabil.',
            'detail' => 'Gunakan Laravel Reverb atau Pusher sebagai backend Websocket. Pastikan Queue Worker aktif dengan Supervisor. Jika pesanan tidak muncul otomatis di KDS, jalankan: php artisan queue:restart',
        ],
        [
            'color'  => '#4f46e5',
            'bg'     => '#e0e7ff',
            'cat'    => 'OPERASIONAL',
            'title'  => 'Onboarding Restoran Baru',
            'desc'   => 'Proses pendaftaran, verifikasi, dan aktivasi akun restoran mitra baru.',
            'detail' => 'Admin harus menyetujui pendaftaran di menu Users sebelum restoran bisa login. Setelah approved, owner bisa mengisi profil outlet, mengunggah foto menu, dan mulai menerima pesanan.',
        ],
        [
            'color'  => '#475569',
            'bg'     => '#f1f5f9',
            'cat'    => 'PEMBAYARAN',
            'title'  => 'Integrasi Payment Gateway',
            'desc'   => 'Konfigurasi Midtrans untuk menerima pembayaran QRIS, transfer bank, dan e-wallet.',
            'detail' => 'Dapatkan Server Key (Production) dari Dashboard Midtrans. Masukan ke menu Pengaturan > Pembayaran. Pastikan domain sudah di-whitelist di Midtrans untuk menghindari error CORS saat checkout.',
        ],
        [
            'color'  => '#0891b2',
            'bg'     => '#cffafe',
            'cat'    => 'DIGITAL',
            'title'  => 'QR Menu & Instalasi PWA',
            'desc'   => 'Aktifkan menu digital berbasis QR code dan PWA agar pelanggan bisa memesan tanpa aplikasi.',
            'detail' => 'Generate QR Code unik untuk setiap meja di menu Tables. Setelah scan, pelanggan langsung diarahkan ke menu digital. Aktifkan mode PWA di pengaturan untuk mengizinkan install ke homescreen HP.',
        ],
        [
            'color'  => '#dc2626',
            'bg'     => '#fee2e2',
            'cat'    => 'KEAMANAN',
            'title'  => 'Role & Izin Akses User',
            'desc'   => 'Konfigurasi hak akses karyawan berdasarkan departemen untuk menjaga keamanan data.',
            'detail' => 'Gunakan menu Roles & Permissions untuk membuat role Kasir, Manajer, atau Dapur. Pastikan akun Kasir tidak bisa melihat laporan keuangan dan akun Dapur hanya bisa melihat antrian pesanan.',
        ],
    ];

    $icons = [
        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z"/>',
        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/>',
        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.53 16.122a3 3 0 00-3.032 4.676h10.995a3 3 0 00-3.033-4.676m-4.93-3.602a3 3 0 113.862 0M18 10.5V6.75m0 0L15.75 9m2.25-2.25L20.25 9M3 12.75v2.625c0 .621.504 1.125 1.125 1.125H9M3 12.75V9m0 3.75L5.25 11m-2.25 1.75L3 12.75"/>',
        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 15V5.25m19.5 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V5.25"/>',
        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z"/>',
        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>',
        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75z"/>',
        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>',
    ];
@endphp

<x-filament-panels::page>
<style>
.sg-hero { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px; }
.sg-hero-date { display:block; }
.sg-hero-title { display:flex; align-items:center; gap:10px; margin-bottom:6px; flex-wrap:wrap; }
.sg-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:24px; }
.sg-banner { display:flex; align-items:center; justify-content:space-between; gap:20px; flex-wrap:wrap; }
@media(max-width:1024px){ .sg-grid{ grid-template-columns:repeat(2,1fr); } }
@media(max-width:640px){
  .sg-hero{ flex-direction:column; align-items:flex-start; }
  .sg-hero-date{ display:none; }
  .sg-hero-title{ flex-direction:column; align-items:flex-start; gap:6px; }
  .sg-grid{ grid-template-columns:1fr; }
  .sg-banner{ flex-direction:column; align-items:flex-start; }
  .sg-banner a{ width:100%; text-align:center; }
}
</style>
<div x-data="{
    search: '',
    modal: false,
    modalTitle: '',
    modalDetail: '',
    openModal(title, detail) {
        this.modalTitle = title;
        this.modalDetail = detail;
        this.modal = true;
    }
}">

{{-- ═══════════════════════ HERO BANNER (persis gaya Admin Dashboard) ═══════════════════════ --}}
<div class="sg-hero" style="background:linear-gradient(135deg,#0f1117 0%,#1a1f2e 60%,#1e2a4a 100%);border-radius:16px;padding:28px 32px;margin-bottom:28px;position:relative;overflow:hidden;">
    {{-- Background glow --}}
    <div style="position:absolute;top:-40px;right:120px;width:200px;height:200px;background:radial-gradient(circle,rgba(139,92,246,0.15) 0%,transparent 70%);pointer-events:none;"></div>
    <div style="position:absolute;bottom:-40px;right:0;width:160px;height:160px;background:radial-gradient(circle,rgba(59,130,246,0.1) 0%,transparent 70%);pointer-events:none;"></div>

    <div style="display:flex;align-items:center;gap:20px;">
        {{-- Icon box --}}
        <div style="
            width:60px;height:60px;
            background:linear-gradient(135deg,#7c3aed,#4f46e5);
            border-radius:14px;
            display:flex;align-items:center;justify-content:center;
            flex-shrink:0;
        ">
            <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
            </svg>
        </div>
        <div>
            <div class="sg-hero-title">
                <span style="color:white;font-size:1.25rem;font-weight:700;letter-spacing:-0.02em;">Pusat Panduan Admin</span>
                <span style="background:rgba(255,255,255,0.12);color:rgba(255,255,255,0.8);font-size:10px;font-weight:700;letter-spacing:0.1em;padding:3px 10px;border-radius:999px;text-transform:uppercase;">Knowledge Base</span>
            </div>
            <p style="color:rgba(255,255,255,0.5);font-size:0.8rem;margin:0;">Panduan operasional dan teknis lengkap untuk administrator {{ $siteName }}.</p>
        </div>
    </div>

    {{-- Date badge --}}
    <div class="sg-hero-date" style="background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.1);border-radius:10px;padding:8px 16px;color:rgba(255,255,255,0.7);font-size:11px;font-weight:600;white-space:nowrap;letter-spacing:0.03em;flex-shrink:0;">
        📅 {{ strtoupper($today) }}
    </div>
</div>

{{-- ═══════════════════════ SEARCH BAR ═══════════════════════ --}}
<div style="margin-bottom:20px;position:relative;max-width:420px;">
    <div style="position:absolute;top:0;bottom:0;left:14px;display:flex;align-items:center;pointer-events:none;">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#9ca3af" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
    </div>
    <input
        type="text"
        x-model="search"
        placeholder="Cari topik panduan..."
        style="
            display:block;width:100%;
            padding:10px 16px 10px 42px;
            background:white;
            border:1px solid #e5e7eb;
            border-radius:10px;
            font-size:13px;font-weight:500;
            color:#111827;
            outline:none;
            box-shadow:0 1px 2px rgba(0,0,0,0.04);
            box-sizing:border-box;
        "
    >
</div>

{{-- ═══════════════════════ CARDS GRID ═══════════════════════ --}}
<div class="sg-grid">
    @foreach($guides as $i => $guide)
    <div
        x-show="search === '' || '{{ strtolower($guide['title']) }}'.includes(search.toLowerCase()) || '{{ strtolower($guide['cat']) }}'.includes(search.toLowerCase())"
        @click="openModal('{{ addslashes($guide['title']) }}', '{{ addslashes($guide['detail']) }}')"
        style="
            background:white;
            border:1px solid #e5e7eb;
            border-radius:12px;
            padding:20px;
            cursor:pointer;
            transition:box-shadow 0.2s, transform 0.2s, border-color 0.2s;
            overflow:hidden;
        "
        onmouseover="this.style.boxShadow='0 8px 24px rgba(0,0,0,0.08)';this.style.transform='translateY(-2px)';this.style.borderColor='{{ $guide['color'] }}';"
        onmouseout="this.style.boxShadow='none';this.style.transform='translateY(0)';this.style.borderColor='#e5e7eb';"
    >
        {{-- Icon --}}
        <div style="
            width:44px;height:44px;
            background:{{ $guide['bg'] }};
            border-radius:10px;
            display:flex;align-items:center;justify-content:center;
            margin-bottom:14px;
        ">
            <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="{{ $guide['color'] }}">
                {!! $icons[$i] !!}
            </svg>
        </div>

        {{-- Category badge --}}
        <div style="
            display:inline-block;
            font-size:9px;font-weight:800;
            color:{{ $guide['color'] }};
            background:{{ $guide['bg'] }};
            padding:2px 8px;
            border-radius:999px;
            letter-spacing:0.12em;
            margin-bottom:8px;
        ">{{ $guide['cat'] }}</div>

        {{-- Title --}}
        <h3 style="font-size:13px;font-weight:700;color:#111827;margin:0 0 6px 0;line-height:1.3;">{{ $guide['title'] }}</h3>

        {{-- Desc --}}
        <p style="font-size:11px;color:#6b7280;margin:0 0 14px 0;line-height:1.5;">{{ $guide['desc'] }}</p>

        {{-- CTA --}}
        <div style="display:flex;align-items:center;gap:4px;font-size:10px;font-weight:700;color:{{ $guide['color'] }};letter-spacing:0.08em;text-transform:uppercase;">
            Buka Panduan
            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="{{ $guide['color'] }}" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </div>
    </div>
    @endforeach
</div>

{{-- ═══════════════════════ SUPPORT BANNER ═══════════════════════ --}}
<div class="sg-banner" style="background:white;border:1px solid #e5e7eb;border-radius:12px;padding:24px 28px;">
    <div style="display:flex;align-items:center;gap:16px;">
        <div style="background:#ede9fe;border-radius:10px;padding:10px;flex-shrink:0;">
            <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="#7c3aed" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/>
            </svg>
        </div>
        <div>
            <p style="font-size:13px;font-weight:700;color:#111827;margin:0 0 3px 0;">Butuh bantuan teknis lebih lanjut?</p>
            <p style="font-size:11px;color:#6b7280;margin:0;">Tim ahli {{ $siteName }} siap membantu konfigurasi sistem dan merchant Anda.</p>
        </div>
    </div>
    <a href="#" style="
        background:linear-gradient(135deg,#7c3aed,#4f46e5);
        color:white;
        padding:10px 22px;
        border-radius:8px;
        font-size:11px;font-weight:700;
        text-decoration:none;
        white-space:nowrap;
        letter-spacing:0.08em;
        text-transform:uppercase;
        flex-shrink:0;
        transition:opacity 0.2s;
    " onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
        Hubungi Support
    </a>
</div>

{{-- ═══════════════════════ MODAL ═══════════════════════ --}}
<div
    x-show="modal"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:24px;background:rgba(0,0,0,0.45);backdrop-filter:blur(6px);"
    @click.self="modal = false"
>
    <div
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        style="background:white;border-radius:16px;width:100%;max-width:520px;overflow:hidden;box-shadow:0 25px 60px rgba(0,0,0,0.2);"
    >
        {{-- Modal header --}}
        <div style="background:linear-gradient(135deg,#0f1117,#1a1f2e);padding:24px 28px;display:flex;align-items:center;justify-content:space-between;">
            <div>
                <p style="font-size:9px;font-weight:800;color:rgba(255,255,255,0.4);letter-spacing:0.2em;text-transform:uppercase;margin:0 0 6px 0;">Detail Panduan</p>
                <h2 x-text="modalTitle" style="font-size:1.1rem;font-weight:700;color:white;margin:0;line-height:1.3;"></h2>
            </div>
            <button @click="modal = false" style="background:rgba(255,255,255,0.1);border:none;cursor:pointer;color:rgba(255,255,255,0.6);border-radius:8px;padding:6px;display:flex;align-items:center;justify-content:center;">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        {{-- Modal body --}}
        <div style="padding:28px;">
            <p x-text="modalDetail" style="font-size:13px;color:#374151;line-height:1.7;font-weight:500;margin:0 0 20px 0;"></p>
            <div style="background:#f8fafc;border-left:3px solid #7c3aed;border-radius:0 8px 8px 0;padding:12px 16px;">
                <p style="font-size:11px;color:#6b7280;margin:0;font-style:italic;">
                    💡 Jika ada kendala teknis, sampaikan melalui menu <strong>Support Tickets</strong> di panel administrasi.
                </p>
            </div>
            <div style="margin-top:24px;display:flex;justify-content:flex-end;">
                <button @click="modal = false" style="
                    background:linear-gradient(135deg,#7c3aed,#4f46e5);
                    color:white;
                    padding:10px 24px;
                    border-radius:8px;
                    border:none;
                    cursor:pointer;
                    font-size:11px;font-weight:700;
                    letter-spacing:0.1em;
                    text-transform:uppercase;
                ">Saya Mengerti</button>
            </div>
        </div>
    </div>
</div>

</div>
</x-filament-panels::page>
