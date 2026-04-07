<x-filament-panels::page class="fi-dashboard-page">

    {{-- ─── Hero Header ─────────────────────────────────────────────────────── --}}
    <div style="position:relative;margin-bottom:32px;overflow:hidden;border-radius:24px;padding:32px;background:linear-gradient(135deg,#111827 0%,#1f2937 50%,#111827 100%);border:1px solid rgba(255,255,255,0.07);">
        
        {{-- Decorative blobs --}}
        <div style="position:absolute;top:-40px;right:-40px;width:280px;height:280px;border-radius:50%;background:rgba(59,130,246,0.08);filter:blur(60px);pointer-events:none;"></div>
        <div style="position:absolute;bottom:-20px;left:60px;width:180px;height:180px;border-radius:50%;background:rgba(16,185,129,0.06);filter:blur(50px);pointer-events:none;"></div>

        <div style="position:relative;display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:20px;">

            {{-- Left: Icon + Text --}}
            <div style="display:flex;align-items:center;gap:20px;">
                
                {{-- Store Icon --}}
                <div style="position:relative;flex-shrink:0;">
                    <div style="position:absolute;inset:0;border-radius:16px;background:linear-gradient(135deg,#3b82f6,#2563eb);filter:blur(12px);opacity:0.6;"></div>
                    <div style="position:relative;padding:16px;border-radius:16px;background:linear-gradient(135deg,#3b82f6,#2563eb);box-shadow:0 8px 32px rgba(59,130,246,0.4);">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="white" style="width:36px;height:36px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72L4.318 3.44A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72m-13.5 8.65h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .415.336.75.75.75Z" />
                        </svg>
                    </div>
                </div>

                {{-- Title + Desc --}}
                <div>
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;flex-wrap:wrap;">
                        <h1 style="margin:0;font-size:1.5rem;font-weight:900;color:white;letter-spacing:-0.025em;white-space:nowrap;">
                            Dashboard Restoran
                        </h1>
                        <span style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;padding:3px 10px;border-radius:999px;background:rgba(59,130,246,0.18);color:#93c5fd;border:1px solid rgba(59,130,246,0.3);white-space:nowrap;">
                            {{ Filament\Facades\Filament::getTenant()->name ?? 'Bisnis Anda' }}
                        </span>
                    </div>
                    <p style="margin:0;font-size:13px;color:#9ca3af;line-height:1.55;">
                        Ringkasan aktivitas, penjualan, dan performa restoran Anda hari ini.
                    </p>
                </div>
            </div>

            {{-- Right: Date/Time --}}
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

    @if (method_exists($this, 'filtersForm'))
        {{ $this->filtersForm }}
    @endif

    <x-filament-widgets::widgets
        :columns="$this->getColumns()"
        :data="
            [
                ...(property_exists($this, 'filters') ? ['filters' => $this->filters] : []),
                ...$this->getWidgetData(),
            ]
        "
        :widgets="$this->getVisibleWidgets()"
    />
</x-filament-panels::page>
