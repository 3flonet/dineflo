{{--
    QR Card Template Partial
    Props:
      $record   — App\Models\Table
      $template — 'minimal' | 'bistro' | 'dark'
--}}
@php
    $restaurant = $record->restaurant;
    $hasLogo    = $restaurant?->logo;
    $logoUrl    = $hasLogo ? Storage::url($restaurant->logo) : null;
    $wifiName   = $restaurant?->wifi_name;
    $wifiPass   = $restaurant?->wifi_password;

    $styles = match($template) {
        'bistro' => [
            'card'      => 'background:#fdf6ed; border:2px solid #c9a96e; color:#3d2b1f;',
            'header'    => 'background:#8b5e30; color:#fff5e4;',
            'qrBg'      => 'background:#fff8f0; border:3px solid #c9a96e;',
            'tableBadge'=> 'background:#8b5e30; color:#fff5e4;',
            'separator' => 'border-color:#c9a96e;',
            'wifiBg'    => 'background:#fff8f0; border:1px solid #c9a96e;',
            'wifiText'  => 'color:#5c3d1e;',
            'scanText'  => 'color:#8b5e30;',
            'accentHex' => '#8b5e30',
        ],
        'dark' => [
            'card'      => 'background:#1a1a2e; border:1px solid #4f46e5; color:#e2e8f0;',
            'header'    => 'background:linear-gradient(135deg,#312e81,#1e1b4b); color:#c7d2fe;',
            'qrBg'      => 'background:#fff; border:3px solid #6366f1;',
            'tableBadge'=> 'background:#4f46e5; color:#e0e7ff;',
            'separator' => 'border-color:#4f46e5;',
            'wifiBg'    => 'background:#0f0f23; border:1px solid #4f46e5;',
            'wifiText'  => 'color:#a5b4fc;',
            'scanText'  => 'color:#818cf8;',
            'accentHex' => '#6366f1',
        ],
        default => [ // minimal
            'card'      => 'background:#fff; border:1.5px solid #e2e8f0; color:#1a202c;',
            'header'    => 'background:linear-gradient(135deg,#f0fdf4,#dcfce7); color:#166534;',
            'qrBg'      => 'background:#f9fafb; border:2px solid #d1d5db;',
            'tableBadge'=> 'background:#16a34a; color:#fff;',
            'separator' => 'border-color:#e2e8f0;',
            'wifiBg'    => 'background:#f9fafb; border:1px solid #e2e8f0;',
            'wifiText'  => 'color:#374151;',
            'scanText'  => 'color:#16a34a;',
            'accentHex' => '#16a34a',
        ],
    };

    // A6 = 105mm × 148mm → at 96dpi ≈ 397px × 559px
    $qrSize = 180;
@endphp

<div class="qr-card" style="
    width:397px;
    min-height:559px;
    border-radius:16px;
    overflow:hidden;
    font-family:'Inter','Segoe UI',sans-serif;
    display:flex;
    flex-direction:column;
    box-shadow:0 4px 24px rgba(0,0,0,0.12);
    page-break-inside:avoid;
    break-inside:avoid;
    {{ $styles['card'] }}
">
    {{-- ── Header ── --}}
    <div style="{{ $styles['header'] }} padding:20px 24px 16px; text-align:center;">
        @if($logoUrl)
            <img src="{{ $logoUrl }}" alt="{{ $restaurant->name }}"
                 style="max-height:52px; max-width:180px; object-fit:contain; display:inline-block; margin-bottom:8px;">
        @else
            <div style="font-size:22px; font-weight:900; letter-spacing:-0.5px; margin-bottom:2px;">
                {{ $restaurant?->name ?? 'Nama Restoran' }}
            </div>
        @endif
        @if($restaurant?->name && $logoUrl)
            <div style="font-size:15px; font-weight:700; margin-top:2px; opacity:0.9;">
                {{ $restaurant->name }}
            </div>
        @endif
        @if($restaurant?->description)
            <div style="font-size:11px; opacity:0.7; margin-top:3px; font-style:italic;">
                {{ Str::limit($restaurant->description, 60) }}
            </div>
        @endif
    </div>

    {{-- ── QR Code ── --}}
    <div style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; padding:22px 24px 16px;">
        <div style="font-size:11px; font-weight:600; letter-spacing:2px; text-transform:uppercase; opacity:0.5; margin-bottom:12px;">
            SCAN UNTUK MEMESAN
        </div>

        <div style="{{ $styles['qrBg'] }} border-radius:12px; padding:14px; display:inline-block; margin-bottom:16px;">
            {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size($qrSize)->errorCorrection('H')->generate($record->url) !!}
        </div>

        {{-- Table badge --}}
        <div style="{{ $styles['tableBadge'] }} border-radius:20px; padding:6px 20px; font-size:16px; font-weight:800; letter-spacing:0.3px; display:inline-flex; align-items:center; gap:8px; margin-bottom:6px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M3 4h18v2H3V4zm0 7h18v2H3v-2zm0 7h18v2H3v-2z"/></svg>
            {{ $record->name }}
            @if($record->area)
                <span style="font-size:12px; opacity:0.8; font-weight:600;">• {{ $record->area }}</span>
            @endif
        </div>

        @if($record->capacity)
            <div style="font-size:11px; opacity:0.5; margin-bottom:2px;">
                Kapasitas {{ $record->capacity }} orang
            </div>
        @endif
    </div>

    {{-- ── WiFi Section ── --}}
    @if($wifiName)
        <div style="padding:0 20px 16px;">
            <div style="border-top:1px dashed; {{ $styles['separator'] }} padding-top:14px;">
                <div style="{{ $styles['wifiBg'] }} border-radius:10px; padding:10px 14px;">
                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:6px;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="{{ $styles['accentHex'] }}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12.55a11 11 0 0 1 14.08 0"/><path d="M1.42 9a16 16 0 0 1 21.16 0"/><path d="M8.53 16.11a6 6 0 0 1 6.95 0"/><line x1="12" y1="20" x2="12.01" y2="20"/>
                        </svg>
                        <span style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px; {{ $styles['wifiText'] }}">
                            WiFi Restoran
                        </span>
                    </div>
                    <div style="display:flex; flex-direction:column; gap:3px;">
                        <div style="font-size:12px; {{ $styles['wifiText'] }}">
                            <span style="opacity:0.6;">Jaringan:</span>
                            <strong>{{ $wifiName }}</strong>
                        </div>
                        @if($wifiPass)
                            <div style="font-size:12px; {{ $styles['wifiText'] }}">
                                <span style="opacity:0.6;">Password:</span>
                                <strong style="font-family:monospace; letter-spacing:1px;">{{ $wifiPass }}</strong>
                            </div>
                        @else
                            <div style="font-size:11px; opacity:0.5; font-style:italic;">Jaringan terbuka (no password)</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Social Media ── --}}
    @php
        $socialLinks = is_array($restaurant->social_links) ? $restaurant->social_links : [];
    @endphp
    @if(count($socialLinks) > 0)
        <div style="padding:0 20px 14px; text-align:center;">
            <div style="display:flex; flex-wrap:wrap; gap:6px; justify-content:center; width:100%; min-height:30px">
                @foreach($socialLinks as $link)
                    @php
                        $icons = [
                            'instagram' => '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>',
                            'facebook'  => '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>',
                            'tiktok'    => '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"></path></svg>',
                            'twitter'   => '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4l11.733 16h4.267l-11.733 -16z"></path><path d="M4 20l6.768 -6.768m2.46 -2.46l6.772 -6.772"></path></svg>',
                            'youtube'   => '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"></path><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"></polygon></svg>',
                            'website'   => '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1 4-10z"></path></svg>'
                        ];
                        $platformRaw = $link['platform'] ?? '';
                        $platform = strtolower(trim($platformRaw));
                        $icon = $icons[$platform] ?? ($icons['website'] ?? '🔗');
                        
                        $url = trim($link['url'] ?? '');
                        if (!preg_match('~^(?:f|ht)tps?://~i', $url)) $url = "http://" . ltrim($url, '@');
                        $urlParts = parse_url($url);
                        
                        if ($platform === 'website' || !isset($icons[$platform])) {
                            $host = $urlParts['host'] ?? preg_replace('~^(?:f|ht)tps?://~i', '', $url);
                            $label = ltrim(preg_replace('/^www\./i', '', $host), '@');
                            if (empty($label)) $label = 'Website';
                        } else {
                            $path = trim($urlParts['path'] ?? '', '/');
                            $segment = explode('/', $path)[0];
                            $username = ltrim($segment, '@');
                            $label = $username ? '@' . $username : ucfirst($platform);
                        }
                    @endphp
                    <span style="font-size:9px; font-weight:700; padding:3px 8px; border-radius:20px; background:rgba(0,0,0,0.05); {{ $styles['wifiText'] }} display:inline-flex; align-items:center; gap:4px; opacity:0.85; white-space:nowrap">
                        {!! $icon !!} {{ $label }}
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ── Footer ── --}}
    <div style="padding:10px 24px 14px; text-align:center; font-size:10px; opacity:0.4; border-top:1px solid; {{ $styles['separator'] }}">
        {{ $record->url }}
    </div>
</div>
