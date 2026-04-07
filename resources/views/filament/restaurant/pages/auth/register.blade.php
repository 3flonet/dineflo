@php
    $settings = null;
    try {
        $settings = app(\App\Settings\GeneralSettings::class);
    } catch (\Throwable $e) {}

    $siteName   = $settings?->site_name    ?: 'Dineflo';
    $siteLogo   = $settings?->site_logo    ? \Illuminate\Support\Facades\Storage::url($settings->site_logo) : null;
@endphp

<x-filament-panels::page.simple>

<style>
    /* ===== GLOBAL RESET ===== */
    html, body { background: #020617 !important; margin: 0; padding: 0; }

    /* ===== RIGHT PANEL: fi-simple-main didorong ke kanan ===== */
    .fi-simple-layout {
        display: block !important;
        max-width: 100% !important;
        width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
        min-height: 100vh !important;
    }
    .fi-simple-main {
        margin-left: 50% !important;
        width: 50% !important;
        min-height: 100vh !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 5rem 4rem !important;
        background: #090e1d !important;
        box-shadow: none !important;
        max-width: none !important;
        overflow-y: auto !important;
        box-sizing: border-box !important;
    }
    .fi-simple-main > section {
        background: transparent !important;
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
        width: 100%;
        max-width: 26rem;
    }
    header.fi-simple-header { display: none !important; }
    footer.fi-simple-footer { display: none !important; }

    /* ===== INPUTS - Aggressive multi-selector override ===== */
    .fi-simple-main input[type="email"],
    .fi-simple-main input[type="password"],
    .fi-simple-main input[type="text"],
    .fi-simple-main input[type="number"],
    .fi-simple-main input,
    .fi-input,
    input.fi-input {
        background-color: #0f172a !important;
        background: #0f172a !important;
        color: #e2e8f0 !important;
        border: none !important;
        outline: none !important;
        padding: 0.75rem 1rem !important;
        font-size: 0.9375rem !important;
    }
    .fi-input::placeholder { color: rgba(148,163,184,0.45) !important; }

    .fi-input-wrapper,
    .fi-simple-main .fi-input-wrapper {
        background-color: #0f172a !important;
        background: #0f172a !important;
        border: 1px solid rgba(255,255,255,0.1) !important;
        border-radius: 0.75rem !important;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.3) !important;
        transition: all 0.2s ease !important;
        overflow: hidden;
    }
    .fi-input-wrapper:focus-within,
    .fi-simple-main .fi-input-wrapper:focus-within {
        border-color: #f59e0b !important;
        box-shadow: 0 0 0 2px rgba(245,158,11,0.2) !important;
        background-color: #1e293b !important;
        background: #1e293b !important;
    }

    /* ===== PASSWORD EYE TOGGLE ===== */
    .fi-input-wrapper button,
    .fi-simple-main .fi-input-suffix-action,
    [class*='fi-input'] button {
        background: transparent !important;
        background-color: transparent !important;
        border: none !important;
        box-shadow: none !important;
        color: #475569 !important;
        padding: 0 0.625rem !important;
        transition: color 0.2s ease !important;
        outline: none !important;
    }
    .fi-input-wrapper button:hover,
    .fi-simple-main .fi-input-suffix-action:hover,
    [class*='fi-input'] button:hover {
        background: transparent !important;
        color: #94a3b8 !important;
    }
    .fi-input-wrapper button svg,
    [class*='fi-input'] button svg {
        width: 1.125rem !important;
        height: 1.125rem !important;
        color: inherit !important;
    }

    /* ===== LABELS - cover semua kemungkinan class Filament ===== */
    .fi-fo-field-wrp-label,
    .fi-fo-field-wrp-label label,
    .fi-fo-field-wrp > label,
    .fi-simple-main label,
    .fi-simple-main span.fi-fo-field-wrp-label,
    [class*='fi-fo'] label,
    [class*='fi-fo'] span[class*='label'] {
        color: #f1f5f9 !important;
        font-weight: 600 !important;
        font-size: 0.875rem !important;
        letter-spacing: 0.01em !important;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    /* Required asterisk */
    .fi-fo-field-wrp-label span[class*='required'],
    .fi-fo-field-wrp-label .required {
        color: #f59e0b !important;
    }

    /* ===== CHECKBOX - Elegant custom ===== */
    .fi-checkbox-input,
    input[type='checkbox'] {
        -webkit-appearance: none !important;
        appearance: none !important;
        width: 1.125rem !important;
        height: 1.125rem !important;
        min-width: 1.125rem !important;
        background-color: #1e293b !important;
        background: #1e293b !important;
        border: 1.5px solid rgba(255,255,255,0.2) !important;
        border-radius: 0.3rem !important;
        cursor: pointer !important;
        position: relative !important;
        flex-shrink: 0 !important;
        transition: all 0.2s ease !important;
        vertical-align: middle !important;
    }
    .fi-checkbox-input:hover,
    input[type='checkbox']:hover {
        border-color: rgba(255,255,255,0.4) !important;
        background-color: #253448 !important;
    }
    .fi-checkbox-input:checked,
    input[type='checkbox']:checked {
        background-color: #1d3a5c !important;
        background: #1d3a5c !important;
        border-color: #60a5fa !important;
        box-shadow: 0 0 0 2px rgba(96,165,250,0.15) !important;
    }
    .fi-checkbox-input:checked::after,
    input[type='checkbox']:checked::after {
        content: '' !important;
        position: absolute !important;
        left: 50% !important;
        top: 45% !important;
        width: 7px !important;
        height: 12px !important;
        border: 2.5px solid #ffffff !important;
        border-top: none !important;
        border-left: none !important;
        transform: translate(-50%, -50%) rotate(45deg) !important;
        display: block !important;
    }
    /* Checkbox label text */
    .fi-simple-main .fi-checkbox-label,
    .fi-simple-main [class*='checkbox'] span,
    .fi-simple-main .fi-fo-field-wrp span,
    .fi-simple-main .fi-checkbox-wrapper label,
    .fi-simple-main .fi-checkbox-wrapper span {
        color: #94a3b8 !important;
        font-size: 0.875rem !important;
        font-weight: 500 !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    .fi-fo-field-wrp-error-message { color: #fca5a5 !important; font-size: 0.8rem !important; }
    .fi-simple-main .fi-fo-field-wrp-hint-text,
    .fi-simple-main [class*='hint'] { color: #64748b !important; font-size: 0.8125rem !important; }

    /* ===== BUTTON ===== */
    .fi-btn-primary {
        background: linear-gradient(135deg, #f59e0b 0%, #b45309 100%) !important;
        border: none !important;
        color: #fff !important;
        font-weight: 700 !important;
        font-size: 0.875rem !important;
        letter-spacing: 0.07em !important;
        text-transform: uppercase !important;
        border-radius: 0.75rem !important;
        padding: 0.75rem 1.5rem !important;
        width: 100% !important;
        box-shadow: 0 4px 20px rgba(245,158,11,0.35) !important;
        transition: all 0.25s ease !important;
    }
    .fi-btn-primary:hover { box-shadow: 0 6px 28px rgba(245,158,11,0.55) !important; transform: translateY(-1px) !important; }
    .fi-btn-primary:active { transform: translateY(1px) !important; }

    /* ===== LINKS ===== */
    a.text-primary-600, a.text-primary-500, .fi-link { color: #fbbf24 !important; font-weight: 600 !important; }
    a.text-primary-600:hover, a.text-primary-500:hover, .fi-link:hover { color: #fef3c7 !important; }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
        .fi-simple-main { margin-left: 0 !important; width: 100% !important; padding: 5rem 1.5rem 2rem !important; }
        #dineflo-left-panel { display: none !important; }
    }
</style>

{{-- ===== LEFT PANEL (Fixed, 50% lebar kiri) ===== --}}
<div id="dineflo-left-panel" style="position:fixed; inset:0; width:50%; height:100vh; display:flex; flex-direction:column; justify-content:space-between; padding:3rem; z-index:10; overflow:hidden; background:#020617;">
    {{-- Background decoration --}}
    <div style="position:absolute; inset:0; background-image: radial-gradient(ellipse 90% 90% at -5% -5%, rgba(245,158,11,0.22) 0%, transparent 55%), radial-gradient(ellipse 70% 70% at 110% 110%, rgba(99,102,241,0.12) 0%, transparent 55%); pointer-events:none;"></div>
    <div style="position:absolute; inset:0; background-image: url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2240%22 height=%2240%22%3E%3Ccircle cx=%222%22 cy=%222%22 r=%221%22 fill=%22rgba(255,255,255,0.04)%22/%3E%3C/svg%3E'); pointer-events:none;"></div>
    <div style="position:absolute; right:0; inset-y:0; width:1px; background: linear-gradient(to bottom, transparent, rgba(255,255,255,0.07) 20%, rgba(255,255,255,0.07) 80%, transparent);"></div>

    {{-- Logo --}}
    <div style="position:relative; display:flex; align-items:center; gap:0.75rem;">
        @if($siteLogo)
            <img src="{{ $siteLogo }}" alt="{{ $siteName }}" style="height:2.5rem; width:auto; object-fit:contain; border-radius:0.5rem;">
        @else
            <div style="width:2.5rem; height:2.5rem; border-radius:0.75rem; background:linear-gradient(135deg,#f59e0b,#d97706); display:flex; align-items:center; justify-content:center; box-shadow:0 0 24px rgba(245,158,11,0.4);">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" style="width:1.25rem; height:1.25rem;">
                    <path fill-rule="evenodd" d="M12.963 2.286a.75.75 0 0 0-1.071-.136 9.742 9.742 0 0 0-3.539 6.176 7.547 7.547 0 0 1-1.705-1.715.75.75 0 0 0-1.152-.082A9 9 0 1 0 15.68 4.534a7.46 7.46 0 0 1-2.717-2.248ZM15.75 14.25a3.75 3.75 0 1 1-7.313-1.172c.628.465 1.35.81 2.133 1a5.99 5.99 0 0 1 1.925-3.546 3.75 3.75 0 0 1 3.255 3.718Z" clip-rule="evenodd"/>
                </svg>
            </div>
        @endif
        <span style="font-size:1.25rem; font-weight:900; color:#fff; letter-spacing:-0.02em;">{{ $siteName }}</span>
    </div>

    {{-- Hero --}}
    <div style="position:relative;">
        <h1 style="font-size:clamp(2rem,3.5vw,3rem); font-weight:900; color:#fff; line-height:1.1; letter-spacing:-0.02em; margin-bottom:1.25rem;">
            Upgrade bisnis kuliner<br>
            <span style="background:linear-gradient(90deg,#f59e0b,#fde68a); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;">Anda hari ini.</span>
        </h1>
        <p style="font-size:1rem; color:#64748b; line-height:1.7; max-width:22rem; margin-bottom:1.5rem;">
            Lebih dari sekadar POS. {{ $siteName }} membantu Anda mengatur menu, stok, hingga antrean dapur secara real-time.
        </p>
        {{-- Feature list --}}
        @foreach(['Setup kurang dari 5 menit', 'Gratis paket Starter selamanya', 'Tanpa kontrak atau fee tersembunyi'] as $feat)
        <div style="display:flex; align-items:center; gap:0.625rem; margin-bottom:0.625rem;">
            <div style="width:1.25rem; height:1.25rem; border-radius:50%; background:rgba(16,185,129,0.12); border:1px solid rgba(16,185,129,0.35); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg style="width:0.625rem; height:0.625rem; color:#34d399;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <span style="font-size:0.875rem; font-weight:500; color:#cbd5e1;">{{ $feat }}</span>
        </div>
        @endforeach
    </div>

    {{-- Stats --}}
    <div style="position:relative; display:flex; align-items:center; gap:2rem;">
        <div>
            <div style="font-size:1.5rem; font-weight:900; color:#fff;">500+</div>
            <div style="font-size:0.75rem; color:#475569; margin-top:0.125rem;">Restoran aktif</div>
        </div>
        <div style="width:1px; height:2.5rem; background:rgba(255,255,255,0.08);"></div>
        <div>
            <div style="font-size:1.5rem; font-weight:900; color:#fff;">99.9%</div>
            <div style="font-size:0.75rem; color:#475569; margin-top:0.125rem;">Uptime SLA</div>
        </div>
        <div style="width:1px; height:2.5rem; background:rgba(255,255,255,0.08);"></div>
        <div>
            <div style="font-size:1.5rem; font-weight:900; color:#fff;">⭐ 4.9</div>
            <div style="font-size:0.75rem; color:#475569; margin-top:0.125rem;">Rating pengguna</div>
        </div>
    </div>
</div>

{{-- ===== PILL TOGGLE ===== --}}
<div style="position:fixed; top:1.5rem; right:1.75rem; z-index:50;">
    <div style="display:flex; align-items:center; padding:0.25rem; border-radius:9999px; background:rgba(9,14,29,0.95); border:1px solid rgba(255,255,255,0.08); backdrop-filter:blur(12px);">
        <a href="{{ route('home') }}" style="display:flex; align-items:center; gap:0.375rem; padding:0.5rem 1.125rem; border-radius:9999px; color:#64748b; font-weight:600; font-size:0.8125rem; text-decoration:none; transition:all 0.2s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#64748b'">
            <svg style="width:0.875rem; height:0.875rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Beranda
        </a>
        <div style="width:1px; height:1rem; background:rgba(255,255,255,0.1); margin:0 0.25rem;"></div>
        @if(filament()->hasLogin())
        <a href="{{ filament()->getLoginUrl() }}" style="padding:0.5rem 1.25rem; border-radius:9999px; color:#64748b; font-weight:600; font-size:0.8125rem; text-decoration:none; transition:color 0.2s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#64748b'">
            Login
        </a>
        @endif
        <a href="{{ filament()->getRegistrationUrl() }}" style="display:flex; align-items:center; gap:0.375rem; padding:0.5rem 1.25rem; border-radius:9999px; background:linear-gradient(135deg,#f59e0b,#d97706); color:#fff; font-weight:700; font-size:0.8125rem; text-decoration:none; box-shadow:0 2px 12px rgba(245,158,11,0.4);">
            <svg style="width:0.875rem; height:0.875rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Daftar
        </a>
    </div>
</div>

{{-- ===== FORM HEADER ===== --}}
<div style="margin-bottom:2rem;">
    <p style="font-size:0.7rem; font-weight:700; letter-spacing:0.1em; color:#f59e0b; text-transform:uppercase; margin-bottom:0.5rem;">Bergabung Gratis</p>
    <h2 style="font-size:1.875rem; font-weight:900; color:#fff; letter-spacing:-0.025em; margin-bottom:0.5rem;">Buat Akun Baru</h2>
    <p style="font-size:0.875rem; color:#64748b; line-height:1.6;">Isi formulir di bawah untuk mulai gunakan {{ $siteName }}.</p>
</div>

{{ \Filament\Support\Facades\FilamentView::renderHook('panels::auth.register.form.before') }}

<x-filament-panels::form wire:submit="register">
    {{ $this->form }}
    <x-filament-panels::form.actions
        :actions="$this->getCachedFormActions()"
        :full-width="$this->hasFullWidthFormActions()"
    />
</x-filament-panels::form>

{{ \Filament\Support\Facades\FilamentView::renderHook('panels::auth.register.form.after') }}

</x-filament-panels::page.simple>
