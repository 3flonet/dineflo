<x-filament-panels::page>
    @php
        $restaurant = \Filament\Facades\Filament::getTenant();
        $settings   = app(\App\Settings\GeneralSettings::class);

        $ledger     = \App\Models\RestaurantBalanceLedger::where('restaurant_id', $restaurant->id)->get();
        $grossTotal = $ledger->where('type', 'credit')->sum('gross_amount');
        $feeTotal   = $ledger->where('type', 'credit')->sum('fee_amount');
        $netBalance = $restaurant->balance; // selalu akurat dari increment/decrement

        $pendingRequests = \App\Models\WithdrawRequest::where('restaurant_id', $restaurant->id)
            ->whereIn('status', ['pending', 'approved'])
            ->latest()
            ->get();
    @endphp

    {{-- ─── Hero Header ─────────────────────────────────────────────────────── --}}
    <div style="position:relative;margin-bottom:32px;overflow:hidden;border-radius:24px;padding:32px;background:linear-gradient(135deg,#111827 0%,#1f2937 50%,#111827 100%);border:1px solid rgba(255,255,255,0.07);">
        
        {{-- Decorative blobs --}}
        <div style="position:absolute;top:-40px;right:-40px;width:280px;height:280px;border-radius:50%;background:rgba(16,185,129,0.08);filter:blur(60px);pointer-events:none;"></div>
        <div style="position:absolute;bottom:-20px;left:60px;width:180px;height:180px;border-radius:50%;background:rgba(59,130,246,0.06);filter:blur(50px);pointer-events:none;"></div>

        <div style="position:relative;display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:20px;">

            {{-- Left: Icon + Text --}}
            <div style="display:flex;align-items:center;gap:20px;">
                
                {{-- Master Icon --}}
                <div style="position:relative;flex-shrink:0;">
                    <div style="position:absolute;inset:0;border-radius:16px;background:linear-gradient(135deg,#10b981,#3b82f6);filter:blur(12px);opacity:0.6;"></div>
                    <div style="position:relative;padding:16px;border-radius:16px;background:linear-gradient(135deg,#10b981,#3b82f6);box-shadow:0 8px 32px rgba(16,185,129,0.4);">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="white" style="width:36px;height:36px;">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M22 11.08V12a10 10 0 1 1-5.93-9.14M22 4L12 14.01l-3-3" />
                        </svg>
                    </div>
                </div>

                {{-- Title + Desc --}}
                <div>
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;flex-wrap:wrap;">
                        <h1 style="margin:0;font-size:1.5rem;font-weight:900;color:white;letter-spacing:-0.025em;white-space:nowrap;">
                            Penarikan Dana (Withdraw)
                        </h1>
                        <span style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;padding:3px 10px;border-radius:999px;background:rgba(16,185,129,0.18);color:#6ee7b7;border:1px solid rgba(16,185,129,0.3);white-space:nowrap;">
                            Keuangan
                        </span>
                    </div>
                    <p style="margin:0;font-size:13px;color:#9ca3af;line-height:1.55;">
                        Cairkan pendapatan restoran Anda dengan aman dan transparan ke rekening bank terdaftar.
                    </p>
                </div>
            </div>

            {{-- Right: Status --}}
            <div style="display:flex;align-items:center;gap:8px;padding:8px 16px;border-radius:999px;background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.12);flex-shrink:0;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#9ca3af" style="width:16px;height:16px;">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                </svg>
                <span style="font-size:11px;font-weight:700;color:#d1d5db;text-transform:uppercase;letter-spacing:0.08em;white-space:nowrap;">
                    {{ now()->translatedFormat('l, d F Y') }}
                </span>
            </div>

        </div>
    </div>

    {{-- ─── Summary Cards (Premium) ─────────────────────────────────────── --}}
    <style>
        .wd-card-glass {
            background-color: #ffffff !important;
            border-color: rgba(0, 0, 0, 0.05) !important;
        }
        .dark .wd-card-glass {
            background-color: rgba(17, 24, 39, 0.4) !important;
            border-color: rgba(255, 255, 255, 0.05) !important;
        }
        .dark .wd-card-glass-darker {
            background-color: rgba(17, 24, 39, 0.6) !important;
        }
        .wd-text-primary { color: #111827 !important; }
        .dark .wd-text-primary { color: #ffffff !important; }
        .wd-badge-qris { background-color: rgba(244, 63, 94, 0.1) !important; color: #e11d48 !important; }
        .dark .wd-badge-qris { background-color: rgba(244, 63, 94, 0.2) !important; color: #fda4af !important; }
        .wd-badge-va { background-color: rgba(245, 158, 11, 0.15) !important; color: #d97706 !important; }
        .dark .wd-badge-va { background-color: rgba(245, 158, 11, 0.2) !important; color: #fbbf24 !important; }
    </style>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8" id="withdraw-summary-cards">

        {{-- Gross Total (Indigo) --}}
        <div style="padding:28px; border-radius: 24px;" class="wd-card-glass relative flex flex-col justify-between border shadow-[0_8px_32px_rgba(67,56,202,0.04)] overflow-hidden transition-all duration-300 dark:shadow-none group hover:-translate-y-1 hover:shadow-xl hover:shadow-indigo-500/10 dark:hover:border-indigo-500/30">
            
            {{-- Background Glow --}}
            <div style="position:absolute;top:-40px;right:-40px;width:140px;height:140px;border-radius:50%;background:rgba(99,102,241,0.06);filter:blur(40px);pointer-events:none;transition:all 0.5s ease;" class="group-hover:bg-indigo-500/10 dark:group-hover:bg-indigo-500/15"></div>

            <div style="position:relative;z-index:10;display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
                <span style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:0.1em;" class="text-indigo-600 dark:text-indigo-400">Total Penjualan</span>
                <div style="padding:10px;border-radius:14px;background:rgba(99,102,241,0.1);border:1px solid rgba(99,102,241,0.1);" class="dark:bg-indigo-500/20 dark:border-indigo-500/10">
                    <x-heroicon-s-banknotes class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                </div>
            </div>

            <div style="position:relative;z-index:10;">
                <p style="font-size:11px;font-weight:600;margin-bottom:4px;text-transform:uppercase;letter-spacing:0.05em;" class="text-indigo-500/70 dark:text-indigo-400/60">Gross Sales</p>
                <p style="font-size:2rem;font-weight:900;letter-spacing:-0.02em;margin-bottom:16px;" class="wd-text-primary tabular-nums">
                    Rp {{ number_format($grossTotal, 0, ',', '.') }}
                </p>
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-indigo-500/10 border border-indigo-500/10 dark:bg-indigo-500/10 dark:border-indigo-500/10">
                    <div class="w-1.5 h-1.5 rounded-full bg-indigo-500"></div>
                    <span class="text-[10px] font-bold text-indigo-500 dark:text-indigo-400 tracking-wider uppercase">*Sebelum Fee</span>
                </div>
            </div>
        </div>

        {{-- Fee Total (Rose) --}}
        <div style="padding:28px; border-radius: 24px;" class="wd-card-glass relative flex flex-col justify-between border shadow-[0_8px_32px_rgba(225,29,72,0.04)] overflow-hidden transition-all duration-300 dark:shadow-none group hover:-translate-y-1 hover:shadow-xl hover:shadow-rose-500/10 dark:hover:border-rose-500/30">
            
            {{-- Background Glow --}}
            <div style="position:absolute;top:-40px;right:-40px;width:140px;height:140px;border-radius:50%;background:rgba(244,63,94,0.06);filter:blur(40px);pointer-events:none;transition:all 0.5s ease;" class="group-hover:bg-rose-500/10 dark:group-hover:bg-rose-500/15"></div>

            <div style="position:relative;z-index:10;display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
                <span style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:0.1em;" class="text-rose-600 dark:text-rose-400">Total Potongan</span>
                <div style="padding:10px;border-radius:14px;background:rgba(244,63,94,0.1);border:1px solid rgba(244,63,94,0.1);" class="dark:bg-rose-500/20 dark:border-rose-500/10">
                    <x-heroicon-s-receipt-percent class="w-5 h-5 text-rose-600 dark:text-rose-400" />
                </div>
            </div>

            <div style="position:relative;z-index:10;">
                <p style="font-size:11px;font-weight:600;margin-bottom:4px;text-transform:uppercase;letter-spacing:0.05em;" class="text-rose-500/70 dark:text-rose-400/60">Platform Fees</p>
                <p style="font-size:2rem;font-weight:900;letter-spacing:-0.02em;margin-bottom:16px;" class="wd-text-primary tabular-nums">
                    - Rp {{ number_format($feeTotal, 0, ',', '.') }}
                </p>
                <div class="flex flex-wrap gap-2">
                    <span style="padding:4px 10px; border-radius:8px; font-size:9px; font-weight:800; letter-spacing:0.1em;" class="wd-badge-qris uppercase">QRIS {{ $settings->midtrans_qris_fee_percentage }}%</span>
                    <span style="padding:4px 10px; border-radius:8px; font-size:9px; font-weight:800; letter-spacing:0.1em;" class="wd-badge-va uppercase">VA Rp{{ number_format($settings->midtrans_va_fee_flat, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Net Balance (Emerald) --}}
        <div style="padding:28px; border-radius: 24px;" class="wd-card-glass wd-card-glass-darker relative flex flex-col justify-between border shadow-[0_8px_32px_rgba(16,185,129,0.06)] overflow-hidden transition-all duration-300 dark:shadow-none group hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-500/10 dark:hover:border-emerald-500/30">
            
            {{-- Big Watermark Icon --}}
            <div style="position:absolute;bottom:-20px;right:-20px;opacity:0.03;transform:rotate(-15deg);pointer-events:none;transition:all 0.5s ease;" class="dark:opacity-[0.05] group-hover:scale-110 group-hover:opacity-[0.05] dark:group-hover:opacity-[0.08] text-emerald-600 dark:text-emerald-400">
                <x-heroicon-s-wallet class="w-32 h-32" />
            </div>

            {{-- Background Glow --}}
            <div style="position:absolute;top:-40px;left:-40px;width:150px;height:150px;border-radius:50%;background:rgba(16,185,129,0.06);filter:blur(40px);pointer-events:none;transition:all 0.5s ease;" class="group-hover:bg-emerald-500/10 dark:group-hover:bg-emerald-500/15"></div>

            <div style="position:relative;z-index:10;display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                <span style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:0.1em;" class="text-emerald-600 dark:text-emerald-400">Saldo Tersedia</span>
                <div style="padding:10px;border-radius:14px;background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.1);" class="dark:bg-emerald-500/20 dark:border-emerald-500/10">
                    <x-heroicon-s-wallet class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                </div>
            </div>

            <div style="position:relative;z-index:10;">
                <p style="font-size:11px;font-weight:600;margin-bottom:2px;text-transform:uppercase;letter-spacing:0.05em;" class="text-emerald-500/70 dark:text-emerald-400/60">Net Balance</p>
                <p style="font-size:2.5rem;font-weight:900;letter-spacing:-0.03em;margin-bottom:16px;" class="wd-text-primary tabular-nums">
                    Rp {{ number_format($netBalance, 0, ',', '.') }}
                </p>
                <div class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/15 dark:bg-emerald-500/15 dark:border-emerald-500/20">
                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)] animate-pulse"></div>
                    <span class="text-[10px] font-extrabold text-emerald-500 dark:text-emerald-400 tracking-wider uppercase">Siap Dicairkan</span>
                </div>
            </div>
        </div>

    </div>




    {{-- ─── Info Fee ─────────────────────────────────────────────────────── --}}
    <div class="bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-2xl px-5 py-4 mb-8 flex gap-4 items-start">
        <div class="p-2 bg-amber-50 dark:bg-amber-500/10 rounded-lg text-amber-600 dark:text-amber-400">
            <x-heroicon-m-light-bulb class="w-5 h-5" />
        </div>
        <div class="text-[13px] leading-relaxed text-gray-600 dark:text-gray-400">
            <strong class="text-gray-900 dark:text-white">Transparansi Biaya:</strong> Saldo yang Anda terima adalah nilai bersih setelah otomatis dipotong biaya Payment Gateway. 
            Biaya QRIS saat ini adalah <span class="font-bold text-amber-600 dark:text-amber-400">{{ $settings->midtrans_qris_fee_percentage }}%</span> sesuai regulasi MDR Bank Indonesia.
        </div>
    </div>

    {{-- ─── Form Ajukan Withdraw ──────────────────────────────────────────── --}}
    <x-filament::section class="rounded-3xl shadow-sm border-gray-100 dark:border-gray-800 overflow-hidden ring-1 ring-gray-950/5 dark:ring-white/10">
        <x-slot name="heading">
            <span class="text-lg font-bold tracking-tight text-gray-900 dark:text-white">Formulir Penarikan Dana</span>
        </x-slot>
        <x-slot name="description">
            <span class="text-sm text-gray-500 dark:text-gray-400">Pastikan data rekening bank sudah benar untuk menghindari kegagalan transfer.</span>
        </x-slot>

        <form wire:submit="submit" class="mt-6">
            <div class="space-y-12">
                {{ $this->form }}
            </div>

            <div class="mt-8 pt-6 border-t border-dashed border-gray-100 dark:border-white/5 flex items-start">
                <x-filament::button type="submit" size="xl" color="success" icon="heroicon-m-paper-airplane" class="px-12 shadow-xl shadow-emerald-500/20 dark:shadow-none font-extrabold uppercase tracking-widest text-[12px] h-16">
                    Ajukan Withdraw Sekarang
                </x-filament::button>
            </div>
        </form>
    </x-filament::section>

    {{-- ─── Penarikan Sedang Berjalan (Pending/Approved) ─────────────────── --}}
    @if($pendingRequests->isNotEmpty())
        <x-filament::section class="mt-8 rounded-3xl shadow-sm border-amber-100 dark:border-amber-900/30 overflow-hidden ring-1 ring-amber-500/10 dark:ring-amber-500/20 bg-amber-50/30 dark:bg-amber-500/5">
            <x-slot name="heading">
                <div class="flex items-center gap-2 text-amber-600 dark:text-amber-500">
                    <x-heroicon-m-clock class="w-5 h-5" />
                    <span class="text-lg font-bold tracking-tight">Penarikan Sedang Diproses ({{ $pendingRequests->count() }})</span>
                </div>
            </x-slot>
            <x-slot name="description">
                <span class="text-sm text-gray-500 dark:text-gray-400">Pengajuan ini belum memotong saldo riwayat Anda hingga proses transfer selesai dikonfirmasi.</span>
            </x-slot>

            <div class="mt-4 flex flex-col gap-3">
                @foreach($pendingRequests as $req)
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-2xl bg-white dark:bg-white/5 border border-gray-100 dark:border-white/5">
                        <div class="flex items-start gap-4">
                            <div class="p-2.5 rounded-full bg-amber-100/50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                                <x-heroicon-o-banknotes class="w-5 h-5" />
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Kode Transaksi: <span class="font-bold text-gray-900 border-b border-dashed border-gray-300 dark:border-gray-600 dark:text-white pb-0.5">{{ $req->withdraw_code ?? '-' }}</span></p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white mt-1.5">{{ $req->bank_name }} - {{ $req->account_number }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">a/n {{ $req->account_name }}</p>
                            </div>
                        </div>
                        <div class="flex flex-col sm:items-end gap-1.5">
                            <span class="text-lg font-black text-gray-900 dark:text-white tracking-tight">Rp {{ number_format($req->amount, 0, ',', '.') }}</span>
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider
                                {{ $req->status === 'pending' ? 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400' : 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400' }}">
                                @if($req->status === 'pending')
                                    <x-heroicon-m-arrow-path class="w-3 h-3 animate-spin" /> Menunggu Persetujuan
                                @else
                                    <x-heroicon-m-check-badge class="w-3 h-3" /> Telah Disetujui (Proses Transfer)
                                @endif
                            </div>
                            <span class="text-[10px] text-gray-400">{{ $req->requested_at->format('d M Y, H:i') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>
    @endif

    {{-- ─── Riwayat Ledger ───────────────────────────────────────────────── --}}
    <x-filament::section class="mt-12 rounded-3xl shadow-sm border-gray-100 dark:border-gray-800 ring-1 ring-gray-950/5 dark:ring-white/10">
        <x-slot name="heading">
            <span class="text-lg font-bold tracking-tight text-gray-900 dark:text-white">Riwayat Mutasi Saldo</span>
        </x-slot>
        <x-slot name="description">
            <span class="text-sm text-gray-500 dark:text-gray-400">Daftar lengkap pergerakan saldo masuk dan keluar.</span>
        </x-slot>

        <div class="mt-6">
            {{ $this->table }}
        </div>
    </x-filament::section>

</x-filament-panels::page>
