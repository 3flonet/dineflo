<x-filament-panels::page>
    @php
        $data = $this->getViewData();
    @endphp

    {{-- ─── Styles ─────────────────────────────────────────────────────────── --}}
    <style>
        /* ── Card Glass ── */
        .hq-card {
            background-color: #ffffff;
            border-color: rgba(0, 0, 0, 0.05);
        }
        .dark .hq-card {
            background-color: rgba(17, 24, 39, 0.45);
            border-color: rgba(255, 255, 255, 0.06);
        }

        /* ── Stat Number ── */
        .hq-stat-number {
            font-size: 2.1rem;
            font-weight: 900;
            letter-spacing: -0.03em;
            color: #111827;
        }
        .dark .hq-stat-number { color: #ffffff; }

        /* ── Table row hover ── */
        .hq-table tbody tr:hover { background: rgba(251,191,36,0.04); }
        .dark .hq-table tbody tr:hover { background: rgba(251,191,36,0.06); }

        /* ── Bar chart bar ── */
        .hq-bar {
            height: 6px;
            border-radius: 99px;
            background: linear-gradient(90deg, #f59e0b, #f97316);
            transition: width 0.9s cubic-bezier(0.22,1,0.36,1);
        }

        /* ── Enterprise badge pulse ── */
        @keyframes hq-pulse-ring {
            0%   { box-shadow: 0 0 0 0 rgba(16,185,129,0.5); }
            70%  { box-shadow: 0 0 0 8px rgba(16,185,129,0); }
            100% { box-shadow: 0 0 0 0 rgba(16,185,129,0); }
        }
        .hq-live-dot { animation: hq-pulse-ring 1.8s ease infinite; }
    </style>

    <div class="space-y-8">

        {{-- ─── Hero Header ────────────────────────────────────────────────── --}}
        <div style="position:relative;overflow:hidden;border-radius:24px;padding:32px;background:linear-gradient(135deg,#111827 0%,#1e2a3a 50%,#111827 100%);border:1px solid rgba(255,255,255,0.07);">

            {{-- Decorative blobs --}}
            <div style="position:absolute;top:-50px;right:-50px;width:300px;height:300px;border-radius:50%;background:rgba(245,158,11,0.07);filter:blur(70px);pointer-events:none;"></div>
            <div style="position:absolute;bottom:-30px;left:80px;width:200px;height:200px;border-radius:50%;background:rgba(99,102,241,0.06);filter:blur(55px);pointer-events:none;"></div>

            <div style="position:relative;display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:20px;">

                {{-- Left: Icon + Text --}}
                <div style="display:flex;align-items:center;gap:20px;">

                    {{-- Master Icon --}}
                    <div style="position:relative;flex-shrink:0;">
                        <div style="position:absolute;inset:0;border-radius:16px;background:linear-gradient(135deg,#f59e0b,#f97316);filter:blur(14px);opacity:0.55;"></div>
                        <div style="position:relative;padding:16px;border-radius:16px;background:linear-gradient(135deg,#f59e0b,#f97316);box-shadow:0 8px 32px rgba(245,158,11,0.4);">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="white" style="width:36px;height:36px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
                            </svg>
                        </div>
                    </div>

                    {{-- Title + Desc --}}
                    <div>
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;flex-wrap:wrap;">
                            <h1 style="margin:0;font-size:1.5rem;font-weight:900;color:white;letter-spacing:-0.025em;white-space:nowrap;">
                                Franchise HQ Dashboard
                            </h1>
                            <span style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;padding:3px 10px;border-radius:999px;background:rgba(245,158,11,0.18);color:#fcd34d;border:1px solid rgba(245,158,11,0.3);white-space:nowrap;">
                                Empire Plan
                            </span>
                        </div>
                        <p style="margin:0;font-size:13px;color:#9ca3af;line-height:1.55;">
                            Pantau seluruh kinerja jaringan outlet Anda dari satu pusat komando secara real-time.
                        </p>
                    </div>
                </div>

                {{-- Right: Live date + branch count --}}
                <div style="display:flex;flex-direction:column;align-items:flex-end;gap:8px;">
                    <div style="display:flex;align-items:center;gap:8px;padding:8px 16px;border-radius:999px;background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.12);">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#9ca3af" style="width:14px;height:14px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                        </svg>
                        <span style="font-size:11px;font-weight:700;color:#d1d5db;text-transform:uppercase;letter-spacing:0.08em;white-space:nowrap;">
                            {{ now()->translatedFormat('l, d F Y') }}
                        </span>
                    </div>
                    <div style="display:flex;align-items:center;gap:6px;padding:6px 14px;border-radius:999px;background:rgba(16,185,129,0.12);border:1px solid rgba(16,185,129,0.25);">
                        <span style="width:7px;height:7px;border-radius:50%;background:#10b981;display:inline-block;" class="hq-live-dot"></span>
                        <span style="font-size:11px;font-weight:800;color:#6ee7b7;letter-spacing:0.06em;white-space:nowrap;">
                            {{ $data['branchCount'] }} OUTLET AKTIF
                        </span>
                    </div>
                </div>

            </div>
        </div>

        {{-- ─── Main Global Statistics (Gated) ────────────────────────────────── --}}
        @if(auth()->user()->hasFeature('Dashboard HQ'))

            {{-- ─── Stat Cards ─────────────────────────────────────────────────── --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Revenue Card (Amber) --}}
                <div style="padding:28px;border-radius:24px;" class="hq-card relative flex flex-col justify-between border shadow-[0_8px_32px_rgba(245,158,11,0.06)] overflow-hidden transition-all duration-300 dark:shadow-none group hover:-translate-y-1 hover:shadow-xl hover:shadow-amber-500/10 dark:hover:border-amber-500/30">
                    <div style="position:absolute;top:-40px;right:-40px;width:150px;height:150px;border-radius:50%;background:rgba(245,158,11,0.07);filter:blur(40px);pointer-events:none;" class="group-hover:bg-amber-500/10 dark:group-hover:bg-amber-500/15 transition-all duration-500"></div>

                    <div style="position:relative;z-index:10;display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
                        <span style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:0.1em;" class="text-amber-600 dark:text-amber-400">Revenue Gabungan</span>
                        <div style="padding:10px;border-radius:14px;background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.1);" class="dark:bg-amber-500/20 dark:border-amber-500/10">
                            <x-heroicon-s-banknotes class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                        </div>
                    </div>

                    <div style="position:relative;z-index:10;">
                        <p style="font-size:11px;font-weight:600;margin-bottom:4px;text-transform:uppercase;letter-spacing:0.05em;" class="text-amber-500/70 dark:text-amber-400/60">Omzet Hari Ini</p>
                        <p class="hq-stat-number mb-4">
                            Rp {{ number_format($data['totalRevenueToday'], 0, ',', '.') }}
                        </p>
                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-amber-500/10 border border-amber-500/10 dark:bg-amber-500/10 dark:border-amber-500/10">
                            <div class="w-1.5 h-1.5 rounded-full bg-amber-500"></div>
                            <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 tracking-wider uppercase">Semua Outlet</span>
                        </div>
                    </div>
                </div>

                {{-- Orders Card (Indigo) --}}
                <div style="padding:28px;border-radius:24px;" class="hq-card relative flex flex-col justify-between border shadow-[0_8px_32px_rgba(99,102,241,0.05)] overflow-hidden transition-all duration-300 dark:shadow-none group hover:-translate-y-1 hover:shadow-xl hover:shadow-indigo-500/10 dark:hover:border-indigo-500/30">
                    <div style="position:absolute;top:-40px;right:-40px;width:150px;height:150px;border-radius:50%;background:rgba(99,102,241,0.07);filter:blur(40px);pointer-events:none;" class="group-hover:bg-indigo-500/10 transition-all duration-500"></div>

                    <div style="position:relative;z-index:10;display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
                        <span style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:0.1em;" class="text-indigo-600 dark:text-indigo-400">Total Pesanan</span>
                        <div style="padding:10px;border-radius:14px;background:rgba(99,102,241,0.1);border:1px solid rgba(99,102,241,0.1);" class="dark:bg-indigo-500/20 dark:border-indigo-500/10">
                            <x-heroicon-s-shopping-bag class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                        </div>
                    </div>

                    <div style="position:relative;z-index:10;">
                        <p style="font-size:11px;font-weight:600;margin-bottom:4px;text-transform:uppercase;letter-spacing:0.05em;" class="text-indigo-500/70 dark:text-indigo-400/60">Jaringan HQ</p>
                        <p class="hq-stat-number mb-4">
                            {{ number_format($data['totalOrdersToday'], 0, ',', '.') }}
                        </p>
                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-indigo-500/10 border border-indigo-500/10 dark:bg-indigo-500/10 dark:border-indigo-500/10">
                            <div class="w-1.5 h-1.5 rounded-full bg-indigo-500"></div>
                            <span class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 tracking-wider uppercase">Order Hari Ini</span>
                        </div>
                    </div>
                </div>

                {{-- Waiter Calls Card (Emerald) --}}
                <div style="padding:28px;border-radius:24px;" class="hq-card relative flex flex-col justify-between border shadow-[0_8px_32px_rgba(16,185,129,0.06)] overflow-hidden transition-all duration-300 dark:shadow-none group hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-500/10 dark:hover:border-emerald-500/30">
                    <div style="position:absolute;bottom:-20px;right:-20px;opacity:0.03;transform:rotate(-15deg);pointer-events:none;" class="dark:opacity-[0.05] group-hover:scale-110 group-hover:opacity-[0.05] dark:group-hover:opacity-[0.08] text-emerald-600 dark:text-emerald-400 transition-all duration-500">
                        <x-heroicon-s-bell-alert class="w-32 h-32" />
                    </div>
                    <div style="position:absolute;top:-40px;left:-40px;width:150px;height:150px;border-radius:50%;background:rgba(16,185,129,0.06);filter:blur(40px);pointer-events:none;" class="group-hover:bg-emerald-500/10 transition-all duration-500"></div>

                    <div style="position:relative;z-index:10;display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                        <span style="font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:0.1em;" class="text-emerald-600 dark:text-emerald-400">Panggilan Waiter</span>
                        <div style="padding:10px;border-radius:14px;background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.1);" class="dark:bg-emerald-500/20 dark:border-emerald-500/10">
                            <x-heroicon-s-bell-alert class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                        </div>
                    </div>

                    <div style="position:relative;z-index:10;">
                        <p style="font-size:11px;font-weight:600;margin-bottom:2px;text-transform:uppercase;letter-spacing:0.05em;" class="text-emerald-500/70 dark:text-emerald-400/60">Service Requests</p>
                        <p class="hq-stat-number mb-4">
                            {{ number_format($data['activeWaitersToday'], 0, ',', '.') }}
                        </p>
                        <div class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/15 dark:bg-emerald-500/15 dark:border-emerald-500/20">
                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)] animate-pulse"></div>
                            <span class="text-[10px] font-extrabold text-emerald-500 dark:text-emerald-400 tracking-wider uppercase">Real-time</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ─── Main Content ────────────────────────────────────────────────── --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- ── Branch Performance Table (2/3 width) ── --}}
                <div class="lg:col-span-2">
                    <div style="border-radius:24px;overflow:hidden;border:1px solid rgba(0,0,0,0.05);" class="hq-card shadow-[0_8px_32px_rgba(0,0,0,0.04)] dark:shadow-none">

                        {{-- Table header --}}
                        <div style="padding:20px 24px;border-bottom:1px solid rgba(0,0,0,0.04);" class="dark:border-white/5 flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <div style="padding:8px;border-radius:12px;background:rgba(245,158,11,0.1);">
                                    <x-heroicon-s-map-pin class="w-4 h-4 text-amber-500" />
                                </div>
                                <div>
                                    <h4 style="margin:0;font-size:14px;font-weight:800;letter-spacing:-0.01em;" class="text-gray-800 dark:text-white">Performansi Per Lokasi</h4>
                                    <p style="margin:0;font-size:10px;" class="text-gray-400 dark:text-gray-500">Ringkasan penjualan seluruh outlet hari ini</p>
                                </div>
                            </div>
                            <span style="font-size:10px;padding:4px 10px;border-radius:999px;background:rgba(245,158,11,0.1);font-weight:700;letter-spacing:0.05em;text-transform:uppercase;" class="text-amber-600 dark:text-amber-400">Live</span>
                        </div>

                        {{-- Table --}}
                        <div class="overflow-x-auto">
                            <table class="w-full text-left hq-table">
                                <thead>
                                    <tr style="border-bottom:1px solid rgba(0,0,0,0.04);" class="dark:border-white/5">
                                        <th style="padding:12px 24px;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:0.08em;" class="text-gray-400 dark:text-gray-500">Outlet / Cabang</th>
                                        <th style="padding:12px 24px;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:0.08em;" class="text-gray-400 dark:text-gray-500">Status</th>
                                        <th style="padding:12px 24px;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:0.08em;text-align:right;" class="text-gray-400 dark:text-gray-500">Penjualan</th>
                                        <th style="padding:12px 24px;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:0.08em;text-align:center;" class="text-gray-400 dark:text-gray-500">Orders</th>
                                        <th style="padding:12px 24px;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:0.08em;text-align:center;" class="text-gray-400 dark:text-gray-500">Queue</th>
                                        <th style="padding:12px 24px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($data['branchPerformance'] as $i => $branch)
                                    <tr style="border-bottom:1px solid rgba(0,0,0,0.03);transition:background 0.15s;" class="dark:border-white/5 group">
                                        <td style="padding:16px 24px;">
                                            <div style="display:flex;align-items:center;gap:12px;">
                                                {{-- Rank badge --}}
                                                <div style="width:28px;height:28px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:11px;font-weight:900;
                                                    {{ $i === 0 ? 'background:rgba(245,158,11,0.15);color:#d97706;' : ($i === 1 ? 'background:rgba(107,114,128,0.12);color:#6b7280;' : 'background:rgba(0,0,0,0.04);color:#9ca3af;') }}">
                                                    #{{ $i + 1 }}
                                                </div>
                                                <div>
                                                    <div style="font-size:13px;font-weight:800;" class="text-gray-800 dark:text-white">{{ $branch['name'] }}</div>
                                                    <div style="font-size:10px;margin-top:2px;font-family:monospace;letter-spacing:0.05em;" class="text-gray-400">{{ strtoupper($branch['slug']) }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="padding:16px 24px;">
                                            @if($branch['is_active'])
                                                <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:999px;font-size:10px;font-weight:800;letter-spacing:0.06em;text-transform:uppercase;background:rgba(16,185,129,0.12);color:#059669;border:1px solid rgba(16,185,129,0.2);" class="dark:bg-emerald-500/15 dark:text-emerald-400 dark:border-emerald-500/20">
                                                    <span style="width:5px;height:5px;border-radius:50%;background:#10b981;display:inline-block;" class="animate-pulse"></span>
                                                    Open
                                                </span>
                                            @else
                                                <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:999px;font-size:10px;font-weight:800;letter-spacing:0.06em;text-transform:uppercase;background:rgba(107,114,128,0.1);color:#6b7280;border:1px solid rgba(107,114,128,0.15);" class="dark:bg-gray-600/20 dark:text-gray-400 dark:border-gray-600/20">
                                                    Closed
                                                </span>
                                            @endif
                                        </td>
                                        <td style="padding:16px 24px;text-align:right;">
                                            <span style="font-size:14px;font-weight:900;letter-spacing:-0.02em;" class="text-gray-800 dark:text-white">
                                                Rp {{ number_format($branch['sales_today'], 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td style="padding:16px 24px;text-align:center;">
                                            <span style="font-size:14px;font-weight:700;" class="text-gray-700 dark:text-gray-300">
                                                {{ $branch['orders_today'] }}
                                            </span>
                                        </td>
                                        <td style="padding:16px 24px;text-align:center;">
                                            @if($branch['pending'] > 0)
                                                <span style="display:inline-flex;align-items:center;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:800;background:rgba(245,158,11,0.12);color:#d97706;" class="dark:bg-amber-500/15 dark:text-amber-400">
                                                    {{ $branch['pending'] }}
                                                </span>
                                            @else
                                                <span style="font-size:11px;" class="text-gray-300 dark:text-gray-600">—</span>
                                            @endif
                                        </td>
                                        <td style="padding:16px 24px;text-align:right;">
                                            <a href="/restaurants/{{ $branch['slug'] }}" style="display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:700;padding:5px 10px;border-radius:8px;transition:all 0.15s;text-decoration:none;" class="text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-500/10">
                                                Masuk
                                                <x-heroicon-m-arrow-right class="w-3 h-3" />
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" style="padding:48px;text-align:center;" class="text-gray-400 dark:text-gray-500">
                                            <x-heroicon-o-building-storefront class="w-12 h-12 mx-auto mb-3 opacity-30" />
                                            <p style="font-size:13px;font-weight:500;">Belum ada outlet terdaftar.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- ── Sidebar (1/3 width) ── --}}
                <div class="space-y-6">

                    {{-- Trend 7 Hari --}}
                    <div style="padding:24px;border-radius:24px;border:1px solid rgba(0,0,0,0.05);" class="hq-card shadow-[0_8px_32px_rgba(0,0,0,0.04)] dark:shadow-none">
                        <div class="flex items-center gap-3 mb-6">
                            <div style="padding:8px;border-radius:12px;background:rgba(99,102,241,0.1);">
                                <x-heroicon-s-chart-bar class="w-4 h-4 text-indigo-500" />
                            </div>
                            <div>
                                <h4 style="margin:0;font-size:13px;font-weight:800;" class="text-gray-800 dark:text-white">Tren 7 Hari</h4>
                                <p style="margin:0;font-size:10px;" class="text-gray-400">Gabungan seluruh outlet</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            @foreach($data['chartData']['labels'] as $index => $label)
                                @php
                                    $value = $data['chartData']['values'][$index];
                                    $maxV  = max($data['chartData']['values']) ?: 1;
                                    $pct   = round(($value / $maxV) * 100);
                                    $isToday = $index === 6;
                                @endphp
                                <div>
                                    <div style="display:flex;justify-content:space-between;align-items:baseline;margin-bottom:5px;">
                                        <span style="font-size:11px;font-weight:{{ $isToday ? '800' : '500' }};" class="{{ $isToday ? 'text-amber-600 dark:text-amber-400' : 'text-gray-500 dark:text-gray-400' }}">
                                            {{ $label }} {{ $isToday ? '(Hari Ini)' : '' }}
                                        </span>
                                        <span style="font-size:11px;font-weight:800;font-feature-settings:'tnum';" class="text-gray-700 dark:text-gray-300">
                                            Rp {{ number_format($value / 1000, 0, ',', '.') }}k
                                        </span>
                                    </div>
                                    <div style="height:6px;border-radius:99px;background:rgba(0,0,0,0.06);" class="dark:bg-white/5 overflow-hidden">
                                        <div class="hq-bar" style="width:{{ $pct }}%;{{ $isToday ? 'background:linear-gradient(90deg,#f59e0b,#ef4444);' : '' }}"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div style="margin-top:20px;padding-top:16px;border-top:1px dashed rgba(0,0,0,0.07);" class="dark:border-white/5 flex items-center justify-between">
                            <span style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;" class="text-gray-400 dark:text-gray-500">Total Periode</span>
                            <span style="font-size:15px;font-weight:900;letter-spacing:-0.02em;" class="text-gray-800 dark:text-white">
                                Rp {{ number_format(array_sum($data['chartData']['values']), 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    {{-- Enterprise Pro-Tip Card --}}
                    <div style="padding:24px;border-radius:24px;overflow:hidden;position:relative;background:linear-gradient(135deg,#1e1b4b 0%,#312e81 50%,#1e1b4b 100%);border:1px solid rgba(99,102,241,0.25);">
                        <div style="position:absolute;top:-30px;right:-30px;width:150px;height:150px;border-radius:50%;background:rgba(99,102,241,0.15);filter:blur(40px);pointer-events:none;"></div>
                        <div style="position:absolute;bottom:-20px;left:10px;width:100px;height:100px;border-radius:50%;background:rgba(245,158,11,0.08);filter:blur(30px);pointer-events:none;"></div>

                        <div style="position:relative;z-index:10;">
                            <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;">
                                <x-heroicon-s-sparkles class="w-5 h-5 text-yellow-400" />
                                <span style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:0.1em;color:rgba(255,255,255,0.6);">Enterprise Feature</span>
                            </div>
                            <h5 style="font-size:15px;font-weight:900;color:white;margin:0 0 8px;letter-spacing:-0.01em;line-height:1.3;">
                                Global Menu Sync
                            </h5>
                            <p style="font-size:12px;color:rgba(255,255,255,0.55);line-height:1.6;margin:0 0 16px;">
                                Seragamkan harga & item menu di seluruh outlet jaringan Anda dalam satu langkah.
                            </p>
                            <div style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:10px;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.12);font-size:11px;font-weight:700;color:#c7d2fe;letter-spacing:0.03em;cursor:pointer;transition:all 0.2s;backdrop-filter:blur(4px);">
                                <x-heroicon-m-arrow-right class="w-3 h-3" />
                                Segera Hadir
                            </div>
                        </div>
                    </div>

                    {{-- Quick Links --}}
                    <div style="padding:20px;border-radius:24px;border:1px solid rgba(0,0,0,0.05);" class="hq-card shadow-[0_4px_16px_rgba(0,0,0,0.03)] dark:shadow-none">
                        <h4 style="margin:0 0 14px;font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:0.08em;" class="text-gray-400 dark:text-gray-500">
                            Akses Cepat Outlet
                        </h4>
                        <div class="space-y-2">
                            @foreach($data['branchPerformance']->take(4) as $branch)
                            <a href="/restaurants/{{ $branch['slug'] }}" style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;border-radius:12px;border:1px solid rgba(0,0,0,0.04);transition:all 0.15s;text-decoration:none;" class="dark:border-white/5 hover:border-amber-500/30 hover:bg-amber-50/50 dark:hover:bg-amber-500/5 group">
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div style="width:8px;height:8px;border-radius:50%;flex-shrink:0;background:{{ $branch['is_active'] ? '#10b981' : '#6b7280' }};"></div>
                                    <span style="font-size:12px;font-weight:700;" class="text-gray-700 dark:text-gray-300 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">{{ $branch['name'] }}</span>
                                </div>
                                <x-heroicon-m-arrow-right class="w-3 h-3 text-gray-300 group-hover:text-amber-500 dark:text-gray-600 transition-colors" />
                            </a>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>

        @else
            {{-- ─── Upgrade CTA (Locked State) ────────────────────────────────── --}}
            <div style="position:relative;padding:80px 40px;border-radius:32px;background:linear-gradient(135deg, rgba(17,24,39,0.8), rgba(31,41,55,0.8));border:1px solid rgba(255,255,255,0.05);overflow:hidden;text-align:center;">
                {{-- Background Decorations --}}
                <div style="position:absolute;top:-20%;left:-10%;width:40%;height:60%;background:rgba(245,158,11,0.05);filter:blur(100px);pointer-events:none;"></div>
                <div style="position:absolute;bottom:-20%;right:-10%;width:40%;height:60%;background:rgba(99,102,241,0.05);filter:blur(100px);pointer-events:none;"></div>
                
                <div style="position:relative;z-index:10;max-width:600px;margin:0 auto;">
                    {{-- Premium Lock Icon --}}
                    <div style="width:80px;height:80px;margin:0 auto 24px;background:linear-gradient(135deg, #374151, #111827);border-radius:24px;display:flex;align-items:center;justify-content:center;box-shadow:0 20px 40px rgba(0,0,0,0.4);border:1px solid rgba(255,255,255,0.1);">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#f59e0b" style="width:40px;height:40px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                    </div>

                    <h2 style="font-size:2rem;font-weight:900;color:white;margin-bottom:16px;letter-spacing:-0.03em;">Buka Pusat Komando Empire</h2>
                    <p style="font-size:16px;color:#9ca3af;line-height:1.6;margin-bottom:32px;">
                        Pantau omzet global, tren seluruh cabang, dan performa outlet secara real-time. Fitur ini eksklusif bagi pemegang paket <strong>Empire</strong>.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-32 text-left">
                        <div class="flex items-start gap-3 p-4 rounded-2xl bg-white/5 border border-white/5">
                            <x-heroicon-o-check-circle class="w-5 h-5 text-amber-500 mt-0.5" />
                            <div>
                                <span class="block text-sm font-bold text-white uppercase tracking-wider mb-1">Global Analytics</span>
                                <span class="text-xs text-gray-400">Statistik pendapatan seluruh cabang dalam satu layar.</span>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-4 rounded-2xl bg-white/5 border border-white/5">
                            <x-heroicon-o-check-circle class="w-5 h-5 text-amber-500 mt-0.5" />
                            <div>
                                <span class="block text-sm font-bold text-white uppercase tracking-wider mb-1">Branch Ranking</span>
                                <span class="text-xs text-gray-400">Identifikasi cabang berkinerja terbaik & terburuk secara instan.</span>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('filament.hq.pages.my-subscription') }}" class="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-black rounded-2xl shadow-2xl shadow-amber-500/20 hover:shadow-amber-500/40 hover:-translate-y-1 transition-all duration-300">
                        Upgrade ke Empire Sekarang
                        <x-heroicon-m-arrow-trending-up class="w-5 h-5" />
                    </a>
                </div>
            </div>
        @endif

    </div>
</x-filament-panels::page>
