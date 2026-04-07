@php
    $restaurant = $record->restaurant;
    $design = $restaurant->qr_card_design ?? [];

    // Fallbacks if no design saved yet
    $d = array_merge([
        'bgType' => 'color', 'bgImg' => null, 'cardBg' => '#ffffff',
        'showLogo' => true, 'showRName' => true,
        'hdrBg' => 'linear-gradient(135deg,#f0fdf4,#dcfce7)', 'hdrText' => '#166534',
        'qrX' => 0, 'qrY' => -20, 'qrSize' => 185, 'qrPad' => 12, 'qrR' => 10,
        'qrBg' => '#f9fafb', 'qrBorder' => '#d1d5db',
        'showArea' => true, 'scanTextValue' => 'SCAN UNTUK MEMESAN',
        'accent' => '#16a34a', 'bodyTxt' => '#1a202c', 'badgeTxt' => '#ffffff',
        'wifiBg' => '#f9fafb', 'wifiTxt' => '#374151',
        'socialBg' => '#f3f4f6', 'socialTxt' => '#374151',
        'els' => [
            'header'   => ['show'=>true, 'x'=>0, 'y'=>0],
            'scanText' => ['show'=>true, 'x'=>0, 'y'=>96],
            'badge'    => ['show'=>true, 'x'=>0, 'y'=>326],
            'wifi'     => ['show'=>true, 'x'=>20,'y'=>368],
            'social'   => ['show'=>true, 'x'=>0, 'y'=>448],
            'url'      => ['show'=>true, 'x'=>0, 'y'=>516],
        ]
    ], $design);

    $els = $d['els'];

    // Logo & QR
    $logoUrl = $restaurant->logo ? Storage::url($restaurant->logo) : null;
    $qrUrl = $record->url;
    $qrSvg = SimpleSoftwareIO\QrCode\Facades\QrCode::size(300)->errorCorrection('H')->generate($qrUrl);
    $qrSvg = preg_replace('/<\?xml.*?\?>/i', '', trim($qrSvg));

    // WiFi & Social
    $wifiName = $restaurant->wifi_name;
    $wifiPass = $restaurant->wifi_password;
    $socialLinks = is_array($restaurant->social_links) ? $restaurant->social_links : [];

    // Computations
    $qrLeft = (397 - $d['qrSize'])/2 + $d['qrX'];
    $qrTop  = (559 - $d['qrSize'])/2 + $d['qrY'];

    $bgStyle = $d['bgType'] === 'image' && $d['bgImg']
        ? "background-image:url('{$d['bgImg']}');background-size:cover;background-position:center;"
        : "background:{$d['cardBg']};";

    $elSt = function($name, $width, $forceX = null) use ($els) {
        $el = $els[$name] ?? ['show'=>true,'x'=>0,'y'=>0];
        $x = $forceX !== null ? $forceX : $el['x'];
        $st = "position:absolute;left:{$x}px;top:{$el['y']}px;width:{$width};z-index:5;";
        if(!$el['show']) $st .= 'display:none!important;';
        return $st;
    };
@endphp

<div class="qc-wrapper" style="position:relative;width:397px;height:559px;overflow:hidden;border-radius:14px;box-shadow:0 10px 25px rgba(0,0,0,0.1);border:1px solid #e5e7eb;font-family:'Inter',sans-serif;page-break-inside:avoid;break-inside:avoid;background:#fff; {{$bgStyle}}">

    {{-- HEADER --}}
    <div style="{{ $elSt('header', '397px') }}">
        <div style="text-align:center;padding:20px 22px 16px;width:100%;background:{{$d['hdrBg']}};color:{{$d['hdrText']}};">
            @if($logoUrl && $d['showLogo'])
                <img src="{{ $logoUrl }}" style="max-height:50px;max-width:170px;object-fit:contain;display:inline-block;margin-bottom:5px">
            @endif
            @if($d['showRName'])
                <div style="font-size:19px;font-weight:900;letter-spacing:-.3px">{{ $restaurant->name }}</div>
            @endif
            @if($restaurant->description)
                <div style="font-size:10px;opacity:.7;font-style:italic;margin-top:2px">{{ Str::limit($restaurant->description, 55) }}</div>
            @endif
        </div>
    </div>

    {{-- SCAN TEXT --}}
    <div style="{{ $elSt('scanText', '397px') }}">
        <div style="font-size:9px;font-weight:700;letter-spacing:2.5px;text-transform:uppercase;opacity:.45;text-align:center;padding:4px 0;width:100%;color:{{$d['bodyTxt']}};">
            {{ $d['scanTextValue'] }}
        </div>
    </div>

    {{-- TABLE BADGE --}}
    <div style="{{ $elSt('badge', '397px') }}">
        <div style="text-align:center;width:100%;">
            <span style="display:inline-flex;align-items:center;gap:7px;padding:6px 20px;border-radius:20px;font-size:15px;font-weight:800;background:{{$d['accent']}};color:{{$d['badgeTxt']}};">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M3 4h18v2H3V4zm0 7h18v2H3v-2zm0 7h18v2H3v-2z"/></svg>
                {{ $record->name }}
                @if($d['showArea'] && $record->area)
                    <span style="font-size:12px;opacity:.8;font-weight:600">• {{ $record->area }}</span>
                @endif
            </span>
        </div>
    </div>

    {{-- WIFI --}}
    @if($wifiName && ($els['wifi']['show'] ?? true))
    <div style="{{ $elSt('wifi', 'calc(100% - 40px)', 20) }}">
        <div style="border-radius:9px;padding:9px 13px;font-size:11px;background:{{$d['wifiBg']}};color:{{$d['wifiTxt']}};border:1px solid {{$d['accent']}}33;">
            <div style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:1px;margin-bottom:4px;display:flex;align-items:center;gap:5px;color:{{$d['accent']}};">
                <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.55a11 11 0 0 1 14.08 0"/><path d="M1.42 9a16 16 0 0 1 21.16 0"/><path d="M8.53 16.11a6 6 0 0 1 6.95 0"/><line x1="12" y1="20" x2="12.01" y2="20"/></svg>
                WiFi Restoran
            </div>
            <div>Jaringan: <strong>{{$wifiName}}</strong></div>
            @if($wifiPass)
                <div>Password: <strong style="font-family:monospace;letter-spacing:1px">{{$wifiPass}}</strong></div>
            @else
                <div style="opacity:.5;font-style:italic;font-size:10px">Jaringan terbuka</div>
            @endif
        </div>
    </div>
    @endif

    {{-- SOCIAL MEDIA --}}
    @if(count($socialLinks) > 0 && ($els['social']['show'] ?? true))
    <div style="{{ $elSt('social', '397px') }}">
        <div style="display:flex;gap:6px;justify-content:center;flex-wrap:wrap;padding:4px 12px;text-align:center;width:100%;min-height:30px">
            @foreach($socialLinks as $link)
                @php
                    $icons = [
                        'instagram' => '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>',
                        'facebook'  => '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>',
                        'tiktok'    => '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"></path></svg>',
                        'twitter'   => '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4l11.733 16h4.267l-11.733 -16z"></path><path d="M4 20l6.768 -6.768m2.46 -2.46l6.772 -6.772"></path></svg>',
                        'youtube'   => '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"></path><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"></polygon></svg>',
                        'website'   => '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>'
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
                <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:20px;font-size:9.5px;font-weight:700;background:{{$d['socialBg']}};color:{{$d['socialTxt']}};white-space:nowrap">
                    {!! $icon !!} {{ $label }}
                </span>
            @endforeach
        </div>
    </div>
    @endif

    {{-- URL FOOTER --}}
    <div style="{{ $elSt('url', '397px') }}">
        <div style="text-align:center;font-size:9px;opacity:.35;font-family:monospace;word-break:break-all;padding:4px 16px;width:100%;color:{{$d['bodyTxt']}};">
            {{ $record->url }}
        </div>
    </div>

    {{-- QR CODE --}}
    <div style="position:absolute;z-index:20;left:{{$qrLeft}}px;top:{{$qrTop}}px;width:{{$d['qrSize']}}px;height:{{$d['qrSize']}}px;">
        <div style="width:100%;height:100%;box-sizing:border-box;background:{{$d['qrBg']}};border:2px solid {{$d['qrBorder']}};border-radius:{{$d['qrR']}}px;padding:{{$d['qrPad']}}px;display:flex;align-items:center;justify-content:center;">
            {!! $qrSvg !!}
        </div>
    </div>
</div>

<style>
@media print {
    .qc-wrapper { border-radius: 0 !important; box-shadow: none !important; border:none !important; width:105mm!important; height:148mm!important; }
}
</style>
