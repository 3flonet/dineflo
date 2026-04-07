<x-filament-panels::page>
    @php
        $cards = $this->getLauncherCards();
    @endphp

    {{-- ─── Hero Header ─────────────────────────────────────────────────────── --}}
    <div style="position:relative;margin-bottom:32px;overflow:hidden;border-radius:24px;padding:32px;background:linear-gradient(135deg,#111827 0%,#1f2937 50%,#111827 100%);border:1px solid rgba(255,255,255,0.07);">

        {{-- Decorative blobs --}}
        <div style="position:absolute;top:-40px;right:-40px;width:280px;height:280px;border-radius:50%;background:rgba(251,146,60,0.08);filter:blur(60px);pointer-events:none;"></div>
        <div style="position:absolute;bottom:-20px;left:60px;width:180px;height:180px;border-radius:50%;background:rgba(139,92,246,0.06);filter:blur(50px);pointer-events:none;"></div>

        <div style="position:relative;display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:20px;">

            {{-- Left: Icon + Text --}}
            <div style="display:flex;align-items:center;gap:20px;">

                {{-- Rocket Icon --}}
                <div style="position:relative;flex-shrink:0;">
                    <div style="position:absolute;inset:0;border-radius:16px;background:linear-gradient(135deg,#f59e0b,#f97316);filter:blur(12px);opacity:0.6;"></div>
                    <div style="position:relative;padding:16px;border-radius:16px;background:linear-gradient(135deg,#f59e0b,#f97316);box-shadow:0 8px 32px rgba(249,115,22,0.4);">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="white" style="width:36px;height:36px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 0 1-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 0 0 6.16-12.12A14.98 14.98 0 0 0 9.631 8.41m5.96 5.96a14.926 14.926 0 0 1-5.841 2.58m-.119-8.54a6 6 0 0 0-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 0 0-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 0 1-2.448-2.448 14.9 14.9 0 0 1 .06-.312m-2.24 2.39a4.493 4.493 0 0 0-1.757 4.306 4.493 4.493 0 0 0 4.306-1.758M16.5 9a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z" />
                        </svg>
                    </div>
                </div>

                {{-- Title + Badge + Desc --}}
                <div>
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;flex-wrap:wrap;">
                        <h1 style="margin:0;font-size:1.5rem;font-weight:900;color:white;letter-spacing:-0.025em;white-space:nowrap;">Quick Launch</h1>
                        <span style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;padding:3px 10px;border-radius:999px;background:rgba(251,146,60,0.18);color:#fcd34d;border:1px solid rgba(251,146,60,0.3);white-space:nowrap;">
                            {{ count($cards) }} Tersedia
                        </span>
                    </div>
                    <p style="margin:0;font-size:13px;color:#9ca3af;line-height:1.55;">
                        Akses cepat ke layar operasional restoran. Klik card untuk membuka di tab baru.
                    </p>
                </div>
            </div>

            {{-- Right: Status Badge --}}
            <div style="display:flex;align-items:center;gap:8px;padding:8px 16px;border-radius:999px;background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.12);flex-shrink:0;">
                <div style="width:8px;height:8px;border-radius:50%;background:#10b981;box-shadow:0 0 8px rgba(16,185,129,0.7);"></div>
                <span style="font-size:11px;font-weight:700;color:#d1d5db;text-transform:uppercase;letter-spacing:0.08em;white-space:nowrap;">Sistem Aktif</span>
            </div>

        </div>
    </div>

    @if(count($cards) === 0)
        {{-- Empty State --}}
        <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:80px 24px;text-align:center;">
            <div style="padding:20px;border-radius:24px;margin-bottom:20px;" class="bg-gray-100 dark:bg-gray-800">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:40px;height:40px;" class="text-gray-400">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                </svg>
            </div>
            <h3 style="font-size:1.125rem;font-weight:800;margin-bottom:8px;" class="text-gray-700 dark:text-gray-200">Tidak Ada Akses</h3>
            <p style="font-size:13px;max-width:320px;" class="text-gray-500 dark:text-gray-400">Hubungi pemilik restoran untuk mendapatkan izin ke layar operasional.</p>
        </div>
    @else
        {{-- ─── Cards Grid ────────────────────────────────────────────────────── --}}
        <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(340px, 1fr));gap:20px;">
            @foreach($cards as $card)
                @php
                    $styles = [
                        'orange'  => ['grad'=>'linear-gradient(135deg,#f97316,#f59e0b)','bg'=>'rgba(249,115,22,0.04)','border'=>'rgba(249,115,22,0.2)','iconBg'=>'rgba(249,115,22,0.1)','iconColor'=>'#f97316','badge'=>'rgba(249,115,22,0.1)','badgeText'=>'#c2410c','btnGrad'=>'linear-gradient(135deg,#f97316,#f59e0b)','btnGlow'=>'rgba(249,115,22,0.4)','dot'=>'#f97316'],
                        'blue'    => ['grad'=>'linear-gradient(135deg,#3b82f6,#6366f1)','bg'=>'rgba(59,130,246,0.04)','border'=>'rgba(59,130,246,0.2)','iconBg'=>'rgba(59,130,246,0.1)','iconColor'=>'#3b82f6','badge'=>'rgba(59,130,246,0.1)','badgeText'=>'#1d4ed8','btnGrad'=>'linear-gradient(135deg,#3b82f6,#6366f1)','btnGlow'=>'rgba(59,130,246,0.4)','dot'=>'#3b82f6'],
                        'violet'  => ['grad'=>'linear-gradient(135deg,#8b5cf6,#a855f7)','bg'=>'rgba(139,92,246,0.04)','border'=>'rgba(139,92,246,0.2)','iconBg'=>'rgba(139,92,246,0.1)','iconColor'=>'#8b5cf6','badge'=>'rgba(139,92,246,0.1)','badgeText'=>'#6d28d9','btnGrad'=>'linear-gradient(135deg,#8b5cf6,#a855f7)','btnGlow'=>'rgba(139,92,246,0.4)','dot'=>'#8b5cf6'],
                        'emerald' => ['grad'=>'linear-gradient(135deg,#10b981,#14b8a6)','bg'=>'rgba(16,185,129,0.04)','border'=>'rgba(16,185,129,0.2)','iconBg'=>'rgba(16,185,129,0.1)','iconColor'=>'#10b981','badge'=>'rgba(16,185,129,0.1)','badgeText'=>'#065f46','btnGrad'=>'linear-gradient(135deg,#10b981,#14b8a6)','btnGlow'=>'rgba(16,185,129,0.4)','dot'=>'#10b981'],
                        'sky'     => ['grad'=>'linear-gradient(135deg,#0ea5e9,#38bdf8)','bg'=>'rgba(14,165,233,0.04)','border'=>'rgba(14,165,233,0.2)','iconBg'=>'rgba(14,165,233,0.1)','iconColor'=>'#0ea5e9','badge'=>'rgba(14,165,233,0.1)','badgeText'=>'#0369a1','btnGrad'=>'linear-gradient(135deg,#0ea5e9,#38bdf8)','btnGlow'=>'rgba(14,165,233,0.4)','dot'=>'#0ea5e9'],
                    ];
                    $s = $styles[$card['color']];

                    $icons = [
                        'kitchen' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0 1 12 21 8.25 8.25 0 0 1 6.038 7.047 8.287 8.287 0 0 0 9 9.601a8.983 8.983 0 0 1 3.361-6.867 8.21 8.21 0 0 0 3 2.48Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 0 0 .495-7.468 5.99 5.99 0 0 0-1.925 3.547 5.975 5.975 0 0 1-2.133-1.001A3.75 3.75 0 0 0 12 18Z" />',
                        'service' => '<path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />',
                        'pos'     => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0H3" />',
                        'kiosk'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18h3" />',
                        'queue-display' => '<path stroke-linecap="round" stroke-linejoin="round" d="M6 20.25h12m-7.5-3v3m3-3v3m-10.125-3h17.25c.621 0 1.125-.504 1.125-1.125V4.875c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125Z" />',
                        'profile' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />',
                    ];
                @endphp

                <a
                    href="{{ $card['url'] }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    id="quick-launch-{{ $card['id'] }}"
                    style="display:flex;flex-direction:column;border-radius:24px;background:{{ $s['bg'] }};border:1px solid {{ $s['border'] }};overflow:hidden;text-decoration:none;color:inherit;transition:all 0.25s ease;position:relative;"
                    onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 20px 40px {{ $s['btnGlow'] }}';"
                    onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none';"
                >
                    {{-- Top gradient bar --}}
                    <div style="height:3px;width:100%;background:{{ $s['grad'] }};flex-shrink:0;"></div>

                    {{-- Background orb --}}
                    <div style="position:absolute;right:-30px;top:-30px;width:160px;height:160px;border-radius:50%;background:{{ $s['grad'] }};opacity:0.07;filter:blur(30px);pointer-events:none;"></div>

                    <div style="padding:28px;display:flex;flex-direction:column;flex:1;position:relative;">
                        {{-- Header: Icon + Badge + External --}}
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px;">
                            <div style="padding:12px;border-radius:16px;background:{{ $s['iconBg'] }};border:1px solid rgba(255,255,255,0.1);">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="{{ $s['iconColor'] }}" style="width:24px;height:24px;">
                                    {!! $icons[$card['id']] !!}
                                </svg>
                            </div>
                            <div style="display:flex;align-items:center;gap:8px;padding-top:2px;">
                                <span style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:0.1em;padding:4px 10px;border-radius:999px;background:{{ $s['badge'] }};color:{{ $s['badgeText'] }};">
                                    {{ $card['badge'] }}
                                </span>
                                <div style="padding:6px;border-radius:8px;background:rgba(0,0,0,0.05);">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="{{ $s['iconColor'] }}" style="width:13px;height:13px;opacity:0.7;">
                                        <path fill-rule="evenodd" d="M4.25 5.5a.75.75 0 0 0-.75.75v8.5c0 .414.336.75.75.75h8.5a.75.75 0 0 0 .75-.75v-4a.75.75 0 0 1 1.5 0v4A2.25 2.25 0 0 1 12.75 17h-8.5A2.25 2.25 0 0 1 2 14.75v-8.5A2.25 2.25 0 0 1 4.25 4h5a.75.75 0 0 1 0 1.5h-5Z" clip-rule="evenodd" />
                                        <path fill-rule="evenodd" d="M6.194 12.753a.75.75 0 0 0 1.06.053L16.5 4.44v2.81a.75.75 0 0 0 1.5 0v-4.5a.75.75 0 0 0-.75-.75h-4.5a.75.75 0 0 0 0 1.5h2.553l-9.056 8.194a.75.75 0 0 0-.053 1.06Z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Title & Desc --}}
                        <div style="flex:1;margin-bottom:20px;">
                            <h3 style="font-size:1.2rem;font-weight:900;letter-spacing:-0.01em;margin-bottom:8px;" class="text-gray-900 dark:text-white">
                                {{ $card['title'] }}
                            </h3>
                            <p style="font-size:13px;line-height:1.65;" class="text-gray-500 dark:text-gray-400">
                                {{ $card['description'] }}
                            </p>
                        </div>

                        {{-- Divider --}}
                        <div style="border-top:1px solid rgba(0,0,0,0.07);margin-bottom:20px;" class="dark:!border-white/5"></div>

                        {{-- Footer CTA --}}
                        <div style="display:flex;align-items:center;justify-content:space-between;">
                            <div style="display:flex;align-items:center;gap:7px;">
                                <div style="width:7px;height:7px;border-radius:50%;background:{{ $s['dot'] }};box-shadow:0 0 6px {{ $s['dot'] }};"></div>
                                <span style="font-size:11px;color:#9ca3af;font-weight:500;">Buka di tab baru</span>
                            </div>
                            <div style="display:inline-flex;align-items:center;gap:8px;padding:8px 18px;border-radius:12px;background:{{ $s['btnGrad'] }};color:white;font-size:13px;font-weight:700;box-shadow:0 4px 16px {{ $s['btnGlow'] }};">
                                Launch
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:14px;height:14px;">
                                    <path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.638L10.23 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.158-3.96H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- ─── Info Bar ───────────────────────────────────────────────────────── --}}
        <div style="margin-top:24px;display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;">
            @foreach([
                ['icon'=>'M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5','text'=>'Buka beberapa layar sekaligus untuk multi-display'],
                ['icon'=>'M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z','text'=>'Card tampil sesuai hak akses akun Anda'],
                ['icon'=>'M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99','text'=>'Update real-time via WebSocket Reverb'],
            ] as $info)
                <div style="display:flex;align-items:center;gap:10px;padding:12px 16px;background:rgba(0,0,0,0.03);border:1px solid rgba(0,0,0,0.06);border-radius:14px;" class="dark:!bg-white/[0.03] dark:!border-white/8">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#9ca3af" style="width:16px;height:16px;flex-shrink:0;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $info['icon'] }}" />
                    </svg>
                    <span style="font-size:11.5px;line-height:1.4;" class="text-gray-500 dark:text-gray-400">{{ $info['text'] }}</span>
                </div>
            @endforeach
        </div>
    @endif
</x-filament-panels::page>
