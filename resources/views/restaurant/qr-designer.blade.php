<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>QR Designer – {{ $table->name }}</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html,body{height:100%;overflow:hidden}
body{font-family:'Inter',sans-serif;background:#0f1117;color:#e2e8f0;display:flex;flex-direction:column}
[x-cloak]{display:none!important}
.hidden-el{display:none!important}

/* TOOLBAR */
.tb{height:56px;background:#141820;border-bottom:1px solid rgba(255,255,255,.07);display:flex;align-items:center;gap:10px;padding:0 16px;flex-shrink:0;z-index:100}
.tb-back{display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:8px;background:rgba(255,255,255,.06);color:#94a3b8;font-size:12px;font-weight:600;text-decoration:none;border:1px solid rgba(255,255,255,.08);transition:all .15s}
.tb-back:hover{background:rgba(255,255,255,.1);color:#e2e8f0}
.tb-title{font-size:14px;font-weight:800;color:#f1f5f9}
.tb-sub{font-size:11px;color:#64748b}
.tb-sp{flex:1}
.btn-print{display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:9px;background:#4f46e5;color:#fff;font-size:12px;font-weight:700;border:none;cursor:pointer;transition:all .15s;box-shadow:0 4px 12px rgba(79,70,229,.4)}
.btn-print:hover{background:#4338ca;transform:translateY(-1px)}
.btn-save{display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:9px;background:#16a34a;color:#fff;font-size:12px;font-weight:700;border:none;cursor:pointer;transition:all .15s;box-shadow:0 4px 12px rgba(22,163,74,.4)}
.btn-save:hover{background:#15803d;transform:translateY(-1px)}

/* LAYOUT */
.layout{flex:1;display:flex;overflow:hidden}

/* LEFT PANEL */
.cp{width:290px;flex-shrink:0;background:#141820;border-right:1px solid rgba(255,255,255,.07);overflow-y:auto}
.cp::-webkit-scrollbar{width:3px}
.cp::-webkit-scrollbar-thumb{background:rgba(255,255,255,.1);border-radius:2px}
.sec{border-bottom:1px solid rgba(255,255,255,.06)}
.seh{display:flex;align-items:center;justify-content:space-between;padding:10px 14px;cursor:pointer;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#94a3b8;user-select:none;transition:color .15s}
.seh:hover,.seh.open{color:#a5b4fc}
.seh svg{transition:transform .2s}
.seh.open svg{transform:rotate(90deg)}
.seb{padding:0 14px 12px}
.cl{display:block;font-size:10px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.06em;margin-bottom:5px;margin-top:10px}
.ci{width:100%;padding:7px 9px;border-radius:7px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);color:#e2e8f0;font-size:12px;font-family:'Inter',sans-serif;outline:none;transition:border-color .15s}
.ci:focus{border-color:#6366f1;background:rgba(99,102,241,.08)}
.cr{width:100%;accent-color:#6366f1;cursor:pointer}
.cc{width:34px;height:34px;border-radius:7px;border:2px solid rgba(255,255,255,.15);cursor:pointer;padding:0;overflow:hidden}
.cc::-webkit-color-swatch-wrapper{padding:0}
.cc::-webkit-color-swatch{border:none}
.tr{display:flex;align-items:center;justify-content:space-between;padding:4px 0}
.tl{font-size:12px;color:#cbd5e1}
.tog{position:relative;display:inline-block;width:34px;height:18px;flex-shrink:0}
.tog input{opacity:0;width:0;height:0}
.ts{position:absolute;inset:0;background:rgba(255,255,255,.1);border-radius:20px;cursor:pointer;transition:background .2s}
.ts::before{content:'';position:absolute;height:12px;width:12px;left:3px;top:3px;background:#fff;border-radius:50%;transition:transform .2s}
.tog input:checked+.ts{background:#6366f1}
.tog input:checked+.ts::before{transform:translateX(16px)}
.pb{padding:6px 8px;border-radius:7px;border:1.5px solid rgba(255,255,255,.08);background:rgba(255,255,255,.04);cursor:pointer;font-size:11px;font-weight:600;color:#94a3b8;transition:all .15s;text-align:center}
.pb:hover,.pb.ac{background:rgba(99,102,241,.15);border-color:#6366f1;color:#a5b4fc}
.pg{display:grid;grid-template-columns:1fr 1fr;gap:5px}
.crow{display:flex;align-items:center;gap:8px;margin-top:6px}
.val{font-size:11px;font-weight:600;color:#6366f1;min-width:30px;text-align:right}
.upb{display:flex;align-items:center;gap:7px;padding:8px 12px;border-radius:7px;background:rgba(255,255,255,.04);border:1.5px dashed rgba(255,255,255,.14);color:#94a3b8;font-size:11px;font-weight:600;cursor:pointer;width:100%;transition:all .15s}
.upb:hover{background:rgba(99,102,241,.1);border-color:rgba(99,102,241,.4);color:#a5b4fc}
.esel{background:rgba(99,102,241,.1);border:1px solid rgba(99,102,241,.3);border-radius:8px;padding:8px 10px;margin-bottom:8px;font-size:11px;color:#a5b4fc}
.eel-row{display:flex;align-items:center;gap:6px;padding:5px 0;border-bottom:1px solid rgba(255,255,255,.05)}
.eel-lbl{font-size:12px;color:#cbd5e1;flex:1}
.eel-pos{font-size:10px;color:#6366f1;font-family:monospace}

/* PREVIEW AREA */
.pa{flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;background:#0f1117;overflow:auto;gap:14px;padding:20px}

/* A6 CARD CANVAS: 397×559px */
.qc{position:relative;width:397px;height:559px;overflow:hidden;box-shadow:0 24px 64px rgba(0,0,0,.6),0 0 0 1px rgba(255,255,255,.06);border-radius:14px;flex-shrink:0;cursor:default}

/* Card Background Layer */
.cbg{position:absolute;inset:0;z-index:0;background-size:cover;background-position:center}

/* Draggable card element */
.cel{position:absolute;z-index:5;cursor:move}
.cel:hover::after,.cel.sel::after{content:'';position:absolute;inset:-3px;border:2px dashed rgba(99,102,241,.7);border-radius:6px;pointer-events:none}
.cel.sel::after{border-color:#6366f1;box-shadow:0 0 0 3px rgba(99,102,241,.15)}
.cel-handle{position:absolute;top:-12px;left:50%;transform:translateX(-50%);background:#6366f1;color:#fff;border-radius:4px;padding:2px 6px;font-size:9px;font-weight:700;white-space:nowrap;opacity:0;pointer-events:none;transition:opacity .15s;letter-spacing:.04em;z-index:30}
.cel.sel .cel-handle,.cel:hover .cel-handle{opacity:1}

/* QR Drag Layer */
.qdl{position:absolute;z-index:20;cursor:grab;user-select:none;touch-action:none}
.qdl:active{cursor:grabbing}
.qdl .qi svg{width:100%;height:100%}
.qrc-corner{position:absolute;bottom:-7px;right:-7px;width:18px;height:18px;background:#6366f1;border-radius:4px;cursor:se-resize;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 6px rgba(0,0,0,.4);z-index:30}

/* Card inner elements */
.c-hdr{text-align:center;padding:20px 22px 16px;width:100%}
.c-logo{max-height:50px;max-width:170px;object-fit:contain;display:inline-block;margin-bottom:5px}
.c-rname{font-size:19px;font-weight:900;letter-spacing:-.3px}
.c-tagline{font-size:10px;opacity:.7;font-style:italic;margin-top:2px}
.c-scan{font-size:9px;font-weight:700;letter-spacing:2.5px;text-transform:uppercase;opacity:.45;text-align:center;padding:4px 0;width:100%}
.c-badge{display:inline-flex;align-items:center;gap:7px;padding:6px 20px;border-radius:20px;font-size:15px;font-weight:800}
.c-badge-wrap{text-align:center;width:100%}
.c-wifi{width:calc(100% - 40px);left:20px}
.c-wifi-inner{border-radius:9px;padding:9px 13px;font-size:11px}
.c-wifi-title{font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:1px;margin-bottom:4px;display:flex;align-items:center;gap:5px}
.c-social{text-align:center;width:100%;min-height:30px}
.c-soc-row{display:flex;gap:6px;justify-content:center;flex-wrap:wrap;padding:4px 12px;width:100%}
.c-soc-item{display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:20px;font-size:9.5px;font-weight:700;white-space:nowrap}
.c-url{text-align:center;font-size:9px;opacity:.35;font-family:monospace;word-break:break-all;padding:4px 16px;width:100%}

/* Hints */
.hints{font-size:11px;color:#475569;display:flex;gap:14px;flex-wrap:wrap;justify-content:center}

/* TOAST */
.tst{position:fixed;bottom:24px;left:50%;transform:translateX(-50%) translateY(100px);background:#fff;color:#0f1117;padding:12px 20px;border-radius:10px;font-size:13px;font-weight:700;display:flex;align-items:center;gap:10px;box-shadow:0 10px 30px rgba(0,0,0,0.5);opacity:0;transition:all 0.3s cubic-bezier(0.175,0.885,0.32,1.275);z-index:9999;pointer-events:none}
.tst.show{transform:translateX(-50%) translateY(0);opacity:1}
.tst-success{border-left:4px solid #10b981}
.tst-error{border-left:4px solid #ef4444}

@media print{
html,body{overflow:visible;height:auto}
.tb,.cp,.hints{display:none!important}
.layout{display:block}
.pa{padding:0;background:transparent!important;justify-content:flex-start;align-items:flex-start}
@page{size:A6 portrait;margin:0}
.qc{width:105mm!important;height:148mm!important;border-radius:0!important;box-shadow:none!important}
.cel::after,.cel-handle,.qrc-corner{display:none!important}
}
</style>
</head>

<body x-data="qrDesigner()" @mousemove.window="onMM($event)" @mouseup.window="onMU()" @touchmove.window.prevent="onTM($event)" @touchend.window="onTU()">

{{-- TOOLBAR --}}
<div class="tb">
    <a href="{{ route('filament.restaurant.resources.tables.index', ['tenant' => $restaurant->slug]) }}" class="tb-back">
        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali
    </a>
    <div>
        <div class="tb-title">🎨 QR Card Designer — {{ $table->name }}</div>
        <div class="tb-sub">{{ $restaurant->name }}{{ $table->area ? ' · '.$table->area : '' }}</div>
    </div>
    <div class="tb-sp"></div>
    <button type="button" class="btn-save" @click="save" :disabled="saving">
        <span x-show="!saving">💾 Simpan Desain</span>
        <span x-show="saving">⏳ Menyimpan...</span>
    </button>
    <button type="button" class="btn-print" onclick="window.print()">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Print / PDF
    </button>
</div>

{{-- LAYOUT --}}
<div class="layout">

{{-- ══ LEFT PANEL ══ --}}
<aside class="cp">

    {{-- Templates --}}
    <div class="sec">
        <div class="seh" :class="sec.tpl&&'open'" @click="sec.tpl=!sec.tpl">
            <span>🎨 Template</span>
            <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </div>
        <div class="seb" x-show="sec.tpl" x-cloak>
            <div class="pg">
                <button type="button" class="pb" :class="preset==='minimal'&&'ac'" @click="applyPreset('minimal')">✨ Minimal</button>
                <button type="button" class="pb" :class="preset==='bistro'&&'ac'" @click="applyPreset('bistro')">☕ Bistro</button>
                <button type="button" class="pb" :class="preset==='dark'&&'ac'" @click="applyPreset('dark')">🌙 Dark</button>
                <button type="button" class="pb" :class="preset==='custom'&&'ac'" @click="preset='custom'">🖼️ Custom</button>
            </div>
        </div>
    </div>

    {{-- Background --}}
    <div class="sec">
        <div class="seh" :class="sec.bg&&'open'" @click="sec.bg=!sec.bg">
            <span>🖼️ Background</span>
            <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </div>
        <div class="seb" x-show="sec.bg" x-cloak>
            <div style="display:flex;gap:5px;margin-bottom:7px">
                <button type="button" class="pb" style="flex:1" :class="bgType==='color'&&'ac'" @click="bgType='color';bgImg=null">Warna</button>
                <button type="button" class="pb" style="flex:1" :class="bgType==='image'&&'ac'" @click="$refs.bgf.click()">Upload</button>
            </div>
            <input type="file" x-ref="bgf" accept="image/*" style="display:none" @change="handleBg($event)">
            <div x-show="bgType==='color'">
                <span class="cl">Warna Kartu</span>
                <div class="crow"><input type="color" class="cc" x-model="cardBg"><span class="ci" style="flex:1" x-text="cardBg"></span></div>
            </div>
            <div x-show="bgType==='image'&&bgImg" style="margin-top:6px;background:rgba(99,102,241,.1);border:1px solid rgba(99,102,241,.3);border-radius:7px;padding:7px;font-size:10px;color:#a5b4fc">
                ✅ Foto terpasang <button type="button" @click="bgImg=null;bgType='color'" style="float:right;color:#f87171;background:none;border:none;cursor:pointer;font-weight:700">✕</button>
            </div>
        </div>
    </div>

    {{-- Header --}}
    <div class="sec">
        <div class="seh" :class="sec.hdr&&'open'" @click="sec.hdr=!sec.hdr">
            <span>🏪 Header</span>
            <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </div>
        <div class="seb" x-show="sec.hdr" x-cloak>
            <div class="tr"><span class="tl">Tampilkan Header</span><label class="tog"><input type="checkbox" x-model="els.header.show"><span class="ts"></span></label></div>
            <div x-show="els.header.show">
                <div class="tr"><span class="tl">Logo Restoran</span><label class="tog"><input type="checkbox" x-model="showLogo"><span class="ts"></span></label></div>
                <div class="tr"><span class="tl">Nama Restoran</span><label class="tog"><input type="checkbox" x-model="showRName"><span class="ts"></span></label></div>
                <span class="cl">Background Header</span>
                <div class="crow"><input type="color" class="cc" x-model="hdrBg"><span class="ci" style="flex:1" x-text="hdrBg"></span></div>
                <span class="cl">Warna Teks</span>
                <div class="crow"><input type="color" class="cc" x-model="hdrText"><span class="ci" style="flex:1" x-text="hdrText"></span></div>
                <span class="cl">Posisi Y: <span class="val" x-text="els.header.y+'px'"></span></span>
                <input type="range" class="cr" min="0" max="400" x-model.number="els.header.y">
            </div>
        </div>
    </div>

    {{-- QR Code --}}
    <div class="sec">
        <div class="seh" :class="sec.qr&&'open'" @click="sec.qr=!sec.qr">
            <span>⬛ QR Code</span>
            <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </div>
        <div class="seb" x-show="sec.qr" x-cloak>
            <span class="cl">Ukuran: <span class="val" x-text="qrSize+'px'"></span></span>
            <input type="range" class="cr" min="60" max="290" step="4" x-model.number="qrSize">
            <span class="cl">Padding</span>
            <input type="range" class="cr" min="4" max="28" step="2" x-model.number="qrPad">
            <span class="cl">Sudut: <span class="val" x-text="qrR+'px'"></span></span>
            <input type="range" class="cr" min="0" max="24" x-model.number="qrR">
            <span class="cl">Bg QR</span>
            <div class="crow"><input type="color" class="cc" x-model="qrBg"><span class="ci" style="flex:1" x-text="qrBg"></span></div>
            <span class="cl">Border QR</span>
            <div class="crow"><input type="color" class="cc" x-model="qrBorder"><span class="ci" style="flex:1" x-text="qrBorder"></span></div>
            <button type="button" @click="qrX=0;qrY=0" style="margin-top:8px;width:100%;padding:6px;border-radius:7px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);color:#94a3b8;font-size:11px;cursor:pointer">↺ Reset Posisi QR</button>
        </div>
    </div>

    {{-- Elemen & Posisi --}}
    <div class="sec">
        <div class="seh" :class="sec.els&&'open'" @click="sec.els=!sec.els">
            <span>☑️ Elemen & Posisi</span>
            <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </div>
        <div class="seb" x-show="sec.els" x-cloak>

            {{-- Scan Text --}}
            <div class="eel-row">
                <span class="eel-lbl">Teks Scan</span>
                <span class="eel-pos" x-text="'Y:'+els.scanText.y"></span>
                <button type="button" @click.stop="expanded=expanded==='scanText'?null:'scanText'" style="background:none;border:none;color:#6366f1;cursor:pointer;font-size:14px" title="Posisi">⚙</button>
                <label class="tog"><input type="checkbox" x-model="els.scanText.show"><span class="ts"></span></label>
            </div>
            <div x-show="expanded==='scanText'" style="padding:6px 0">
                <span class="cl" style="margin-top:0">Teks Label</span>
                <input type="text" class="ci" x-model="scanTextValue" style="margin-bottom:8px">
                <span class="cl">Posisi Y: <span class="val" x-text="els.scanText.y+'px'"></span></span>
                <input type="range" class="cr" min="0" max="520" x-model.number="els.scanText.y">
                <span class="cl">Posisi X: <span class="val" x-text="els.scanText.x+'px'"></span></span>
                <input type="range" class="cr" min="-100" max="200" x-model.number="els.scanText.x">
            </div>

            {{-- Table Badge --}}
            <div class="eel-row">
                <span class="eel-lbl">Badge Meja</span>
                <span class="eel-pos" x-text="'Y:'+els.badge.y"></span>
                <button type="button" @click.stop="expanded=expanded==='badge'?null:'badge'" style="background:none;border:none;color:#6366f1;cursor:pointer;font-size:14px">⚙</button>
                <label class="tog"><input type="checkbox" x-model="els.badge.show"><span class="ts"></span></label>
            </div>
            <div x-show="expanded==='badge'" style="padding:6px 0">
                <div class="tr"><span class="tl">Tampilkan Area</span><label class="tog"><input type="checkbox" x-model="showArea"><span class="ts"></span></label></div>
                <span class="cl">Posisi Y: <span class="val" x-text="els.badge.y+'px'"></span></span>
                <input type="range" class="cr" min="0" max="520" x-model.number="els.badge.y">
                <span class="cl">Posisi X: <span class="val" x-text="els.badge.x+'px'"></span></span>
                <input type="range" class="cr" min="-100" max="200" x-model.number="els.badge.x">
                <span class="cl">Warna Badge</span>
                <div class="crow"><input type="color" class="cc" x-model="accent"><input type="color" class="cc" x-model="badgeTxt"><span style="font-size:10px;color:#64748b;margin-left:4px">badge / teks</span></div>
            </div>

            {{-- WiFi --}}
            <div class="eel-row">
                <span class="eel-lbl">Info WiFi</span>
                <span class="eel-pos" x-text="'Y:'+els.wifi.y"></span>
                <button type="button" @click.stop="expanded=expanded==='wifi'?null:'wifi'" style="background:none;border:none;color:#6366f1;cursor:pointer;font-size:14px">⚙</button>
                <label class="tog"><input type="checkbox" x-model="els.wifi.show"><span class="ts"></span></label>
            </div>
            <div x-show="expanded==='wifi'" style="padding:6px 0">
                <span class="cl">Posisi Y: <span class="val" x-text="els.wifi.y+'px'"></span></span>
                <input type="range" class="cr" min="0" max="520" x-model.number="els.wifi.y">
                <span class="cl">Warna Bg WiFi</span>
                <div class="crow"><input type="color" class="cc" x-model="wifiBg"><span class="ci" style="flex:1" x-text="wifiBg"></span></div>
                <span class="cl">Warna Teks WiFi</span>
                <div class="crow"><input type="color" class="cc" x-model="wifiTxt"><span class="ci" style="flex:1" x-text="wifiTxt"></span></div>
            </div>

            {{-- Sosial Media --}}
            @if(count($socialLinks) > 0)
            <div class="eel-row">
                <span class="eel-lbl">Sosial Media</span>
                <span class="eel-pos" x-text="'Y:'+els.social.y"></span>
                <button type="button" @click.stop="expanded=expanded==='social'?null:'social'" style="background:none;border:none;color:#6366f1;cursor:pointer;font-size:14px">⚙</button>
                <label class="tog"><input type="checkbox" x-model="els.social.show"><span class="ts"></span></label>
            </div>
            <div x-show="expanded==='social'" style="padding:6px 0">
                <span class="cl">Posisi Y: <span class="val" x-text="els.social.y+'px'"></span></span>
                <input type="range" class="cr" min="0" max="530" x-model.number="els.social.y">
                <span class="cl">Warna Bg Item</span>
                <div class="crow"><input type="color" class="cc" x-model="socialBg"><input type="color" class="cc" x-model="socialTxt"><span style="font-size:10px;color:#64748b;margin-left:4px">bg / teks</span></div>
            </div>
            @endif

            {{-- URL --}}
            <div class="eel-row">
                <span class="eel-lbl">URL Footer</span>
                <span class="eel-pos" x-text="'Y:'+els.url.y"></span>
                <button type="button" @click.stop="expanded=expanded==='url'?null:'url'" style="background:none;border:none;color:#6366f1;cursor:pointer;font-size:14px">⚙</button>
                <label class="tog"><input type="checkbox" x-model="els.url.show"><span class="ts"></span></label>
            </div>
            <div x-show="expanded==='url'" style="padding:6px 0">
                <span class="cl">Posisi Y: <span class="val" x-text="els.url.y+'px'"></span></span>
                <input type="range" class="cr" min="0" max="545" x-model.number="els.url.y">
            </div>

        </div>
    </div>

    {{-- Warna --}}
    <div class="sec">
        <div class="seh" :class="sec.colors&&'open'" @click="sec.colors=!sec.colors">
            <span>🎨 Warna Teks</span>
            <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </div>
        <div class="seb" x-show="sec.colors" x-cloak>
            <span class="cl">Teks Utama</span>
            <div class="crow"><input type="color" class="cc" x-model="bodyTxt"><span class="ci" style="flex:1" x-text="bodyTxt"></span></div>
            <span class="cl">Aksen / Badge</span>
            <div class="crow"><input type="color" class="cc" x-model="accent"><span class="ci" style="flex:1" x-text="accent"></span></div>
        </div>
    </div>

</aside>

{{-- ══ RIGHT: PREVIEW ══ --}}
<main class="pa">

    <div class="qc" id="qc" @mousedown.self="selEl=null">

        {{-- Background --}}
        <div class="cbg" :style="bgStyle"></div>

        {{-- HEADER --}}
        <div class="cel" id="el-header"
             :class="selEl==='header'&&'sel'"
             :style="elSt('header','397px')"
             x-show="els.header.show"
             @mousedown.stop.prevent="startEl($event,'header')"
             @touchstart.stop.prevent="startElT($event,'header')">
            <div class="cel-handle">HEADER</div>
            <div class="c-hdr" :style="`background:${hdrBg};color:${hdrText};`">
                @if($logoUrl)
                <img src="{{ $logoUrl }}" class="c-logo" x-show="showLogo" alt="{{ $restaurant->name }}">
                @endif
                <div class="c-rname" x-show="showRName">{{ $restaurant->name }}</div>
                @if($restaurant->description)
                <div class="c-tagline">{{ Str::limit($restaurant->description, 55) }}</div>
                @endif
            </div>
        </div>

        {{-- SCAN TEXT --}}
        <div class="cel"
             :class="selEl==='scanText'&&'sel'"
             :style="elSt('scanText','397px')"
             x-show="els.scanText.show"
             @mousedown.stop.prevent="startEl($event,'scanText')"
             @touchstart.stop.prevent="startElT($event,'scanText')">
            <div class="cel-handle">SCAN TEXT</div>
            <div class="c-scan" :style="`color:${bodyTxt};`" x-text="scanTextValue"></div>
        </div>

        {{-- TABLE BADGE --}}
        <div class="cel"
             :class="selEl==='badge'&&'sel'"
             :style="elSt('badge','397px')"
             x-show="els.badge.show"
             @mousedown.stop.prevent="startEl($event,'badge')"
             @touchstart.stop.prevent="startElT($event,'badge')">
            <div class="cel-handle">BADGE MEJA</div>
            <div class="c-badge-wrap">
                <span class="c-badge" :style="`background:${accent};color:${badgeTxt};`">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M3 4h18v2H3V4zm0 7h18v2H3v-2zm0 7h18v2H3v-2z"/></svg>
                    {{ $table->name }}
                    <span x-show="showArea" style="font-size:12px;opacity:.8;font-weight:600">• {{ $table->area }}</span>
                </span>
            </div>
        </div>

        {{-- WIFI --}}
        <div class="cel c-wifi"
             :class="selEl==='wifi'&&'sel'"
             :style="elSt('wifi','calc(100% - 40px)',20)"
             x-show="els.wifi.show && wifiName"
             @mousedown.stop.prevent="startEl($event,'wifi')"
             @touchstart.stop.prevent="startElT($event,'wifi')">
            <div class="cel-handle">WIFI</div>
            <div class="c-wifi-inner" :style="`background:${wifiBg};color:${wifiTxt};border:1px solid ${accent}33;`">
                <div class="c-wifi-title" :style="`color:${accent};`">
                    <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.55a11 11 0 0 1 14.08 0"/><path d="M1.42 9a16 16 0 0 1 21.16 0"/><path d="M8.53 16.11a6 6 0 0 1 6.95 0"/><line x1="12" y1="20" x2="12.01" y2="20"/></svg>
                    WiFi Restoran
                </div>
                <div>Jaringan: <strong x-text="wifiName"></strong></div>
                <div x-show="wifiPass">Password: <strong style="font-family:monospace;letter-spacing:1px" x-text="wifiPass"></strong></div>
                <div x-show="!wifiPass" style="opacity:.5;font-style:italic;font-size:10px">Jaringan terbuka</div>
            </div>
        </div>

        {{-- SOCIAL MEDIA --}}
        @if(count($socialLinks) > 0)
        <div class="cel c-social"
             :class="selEl==='social'&&'sel'"
             :style="elSt('social','397px')"
             x-show="els.social.show"
             @mousedown.stop.prevent="startEl($event,'social')"
             @touchstart.stop.prevent="startElT($event,'social')">
            <div class="cel-handle">SOSIAL MEDIA</div>
            <div class="c-soc-row">
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
                    
                    // IF it is website OR the platform is unknown (fallback to website icon)
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
                <span class="c-soc-item" :style="`background:${socialBg};color:${socialTxt};`">
                    {!! $icon !!} {{ $label }}
                </span>
                @endforeach
            </div>
        </div>
        @endif

        {{-- URL FOOTER --}}
        <div class="cel"
             :class="selEl==='url'&&'sel'"
             :style="elSt('url','397px')"
             x-show="els.url.show"
             @mousedown.stop.prevent="startEl($event,'url')"
             @touchstart.stop.prevent="startElT($event,'url')">
            <div class="cel-handle">URL</div>
            <div class="c-url" :style="`color:${bodyTxt};`">{{ $qrUrl }}</div>
        </div>

        {{-- QR DRAGGABLE --}}
        <div class="qdl" id="qdl"
             :style="`left:${qrLeft}px;top:${qrTop}px;width:${qrSize}px;height:${qrSize}px;`"
             @mousedown.stop.prevent="startQr($event)"
             @touchstart.stop.prevent="startQrT($event)"
             @wheel.prevent="onWheel($event)">
            <div class="qi" :style="`background:${qrBg};border:2px solid ${qrBorder};border-radius:${qrR}px;padding:${qrPad}px;width:100%;height:100%;box-sizing:border-box;`">
                {!! $qrSvg !!}
            </div>
            <div class="qrc-corner"
                 @mousedown.stop.prevent="startRsz($event)"
                 @touchstart.stop.prevent="startRszT($event)">
                <svg width="9" height="9" fill="white" viewBox="0 0 8 8"><path d="M6 0v2H8V0H6zm0 6H0v2h8V6H6z"/></svg>
            </div>
        </div>

    </div>{{-- /qc --}}

    <div class="hints">
        <span>🖱 Drag elemen → pindah posisi</span>
        <span>⬛ Drag QR → posisi bebas</span>
        <span>🖱 Scroll di QR → resize</span>
        <span>↘ Pojok QR → resize</span>
    </div>

</main>
</div>

{{-- TOAST --}}
<div class="tst" :class="[toast.visible && 'show', 'tst-'+toast.type]">
    <span x-text="toast.type === 'success' ? '✅' : '❌'"></span>
    <span x-text="toast.msg"></span>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('qrDesigner', () => ({
        preset: 'minimal',
        restaurantName: @js($restaurant->name),
        wifiName:  @js($restaurant->wifi_name ?? ''),
        wifiPass:  @js($restaurant->wifi_password ?? ''),

        // BG
        bgType:'color', bgImg:null, cardBg:'#ffffff',

        // Header
        showLogo:true, showRName:true,
        hdrBg:'linear-gradient(135deg,#f0fdf4,#dcfce7)', hdrText:'#166534',

        // QR
        qrX:0, qrY:-20, qrSize:185, qrPad:12, qrR:10,
        qrBg:'#f9fafb', qrBorder:'#d1d5db',

        // Element visibility + positions
        showArea:true,
        scanTextValue: 'SCAN UNTUK MEMESAN',
        els:{
            header:   {show:true, x:0, y:0},
            scanText: {show:true, x:0, y:96},
            badge:    {show:true, x:0, y:326},
            wifi:     {show:true, x:20,y:368},
            social:   {show:true, x:0, y:448},
            url:      {show:true, x:0, y:516},
        },

        // Colors
        accent:'#16a34a', bodyTxt:'#1a202c', badgeTxt:'#ffffff',
        wifiBg:'#f9fafb', wifiTxt:'#374151',
        socialBg:'#f3f4f6', socialTxt:'#374151',

        // Section open states & expanded element
        sec:{tpl:true,bg:true,hdr:true,qr:true,els:true,colors:false},
        expanded:null,

        // Drag state
        isDragQr:false, isDragEl:false, isRsz:false,
        selEl:null,
        dSX:0, dSY:0,     // drag start mouse
        dQX:0, dQY:0,     // drag start qr pos
        dEX:0, dEY:0,     // drag start el pos
        rSX:0, rSZ:0,     // resize start

        // Computed
        get qrLeft(){ return (397-this.qrSize)/2 + this.qrX },
        get qrTop(){  return (559-this.qrSize)/2 + this.qrY },
        get bgStyle(){
            if(this.bgType==='image'&&this.bgImg) return `background-image:url('${this.bgImg}');background-size:cover;background-position:center;`;
            return `background:${this.cardBg};`;
        },

        elSt(name, width, forceX){
            const el = this.els[name];
            const x  = forceX !== undefined ? forceX : el.x;
            const isSel = this.selEl === name;
            let st = `left:${x}px;top:${el.y}px;width:${width};z-index:${isSel?25:5};`;
            if(!el.show) st += 'display:none!important;';
            return st;
        },

        // Presets
        applyPreset(n){
            this.preset=n; this.bgType='color'; this.bgImg=null;
            const P={
                minimal:{cardBg:'#ffffff',hdrBg:'linear-gradient(135deg,#f0fdf4,#dcfce7)',hdrText:'#166534',accent:'#16a34a',bodyTxt:'#1a202c',badgeTxt:'#ffffff',qrBg:'#f9fafb',qrBorder:'#d1d5db',wifiBg:'#f9fafb',wifiTxt:'#374151',socialBg:'#f3f4f6',socialTxt:'#374151'},
                bistro: {cardBg:'#fdf6ed',hdrBg:'#8b5e30',hdrText:'#fff5e4',accent:'#8b5e30',bodyTxt:'#3d2b1f',badgeTxt:'#fff5e4',qrBg:'#fff8f0',qrBorder:'#c9a96e',wifiBg:'#fff8f0',wifiTxt:'#5c3d1e',socialBg:'#f5e6d3',socialTxt:'#5c3d1e'},
                dark:   {cardBg:'#1a1a2e',hdrBg:'linear-gradient(135deg,#312e81,#1e1b4b)',hdrText:'#c7d2fe',accent:'#6366f1',bodyTxt:'#e2e8f0',badgeTxt:'#e0e7ff',qrBg:'#ffffff',qrBorder:'#6366f1',wifiBg:'#0f0f23',wifiTxt:'#a5b4fc',socialBg:'#1e1b4b',socialTxt:'#a5b4fc'},
            };
            if(P[n]) Object.assign(this, P[n]);
        },

        handleBg(e){
            const f=e.target.files[0]; if(!f) return;
            const r=new FileReader();
            r.onload=ev=>{this.bgImg=ev.target.result;this.bgType='image';this.preset='custom'};
            r.readAsDataURL(f);
        },

        // QR Drag
        startQr(e){this.isDragQr=true;this.selEl=null;this.dSX=e.clientX;this.dSY=e.clientY;this.dQX=this.qrX;this.dQY=this.qrY},
        startQrT(e){const t=e.touches[0];this.isDragQr=true;this.selEl=null;this.dSX=t.clientX;this.dSY=t.clientY;this.dQX=this.qrX;this.dQY=this.qrY},

        // Element Drag
        startEl(e,name){this.isDragEl=true;this.selEl=name;this.dSX=e.clientX;this.dSY=e.clientY;this.dEX=this.els[name].x;this.dEY=this.els[name].y},
        startElT(e,name){const t=e.touches[0];this.isDragEl=true;this.selEl=name;this.dSX=t.clientX;this.dSY=t.clientY;this.dEX=this.els[name].x;this.dEY=this.els[name].y},

        // Resize
        startRsz(e){this.isRsz=true;this.rSX=e.clientX;this.rSZ=this.qrSize},
        startRszT(e){const t=e.touches[0];this.isRsz=true;this.rSX=t.clientX;this.rSZ=this.qrSize},

        // Mouse/Touch Move
        onMM(e){
            if(this.isDragQr){
                const mx=(397-this.qrSize)/2+20;
                const my=(559-this.qrSize)/2+20;
                this.qrX=Math.max(-mx,Math.min(mx,this.dQX+(e.clientX-this.dSX)));
                this.qrY=Math.max(-my,Math.min(my,this.dQY+(e.clientY-this.dSY)));
            }
            if(this.isDragEl&&this.selEl){
                this.els[this.selEl].x=Math.max(-100,Math.min(300,this.dEX+(e.clientX-this.dSX)));
                this.els[this.selEl].y=Math.max(-20,Math.min(545,this.dEY+(e.clientY-this.dSY)));
            }
            if(this.isRsz){
                this.qrSize=Math.max(60,Math.min(290,this.rSZ+(e.clientX-this.rSX)*2));
            }
        },
        onTM(e){
            const t=e.touches[0];
            if(this.isDragQr){
                const mx=(397-this.qrSize)/2+20;
                const my=(559-this.qrSize)/2+20;
                this.qrX=Math.max(-mx,Math.min(mx,this.dQX+(t.clientX-this.dSX)));
                this.qrY=Math.max(-my,Math.min(my,this.dQY+(t.clientY-this.dSY)));
            }
            if(this.isDragEl&&this.selEl){
                this.els[this.selEl].x=Math.max(-100,Math.min(300,this.dEX+(t.clientX-this.dSX)));
                this.els[this.selEl].y=Math.max(-20,Math.min(545,this.dEY+(t.clientY-this.dSY)));
            }
        },
        onMU(){ this.isDragQr=false;this.isDragEl=false;this.isRsz=false },
        onTU(){ this.isDragQr=false;this.isDragEl=false;this.isRsz=false },

        // Wheel Resize (only when over QR)
        onWheel(e){
            const d=e.deltaY>0?-8:8;
            this.qrSize=Math.max(60,Math.min(290,this.qrSize+d));
        },

        toast: { visible: false, msg: '', type: 'success', timeout: null,
            show(msg, type='success') {
                this.msg = msg; this.type = type; this.visible = true;
                if(this.timeout) clearTimeout(this.timeout);
                this.timeout = setTimeout(() => this.visible = false, 3000);
            }
        },

        saving: false,
        save() {
            this.saving = true;
            const payload = {
                preset: this.preset, bgType: this.bgType, bgImg: this.bgImg, cardBg: this.cardBg,
                showLogo: this.showLogo, showRName: this.showRName, hdrBg: this.hdrBg, hdrText: this.hdrText,
                qrX: this.qrX, qrY: this.qrY, qrSize: this.qrSize, qrPad: this.qrPad, qrR: this.qrR,
                qrBg: this.qrBg, qrBorder: this.qrBorder, showArea: this.showArea, els: this.els,
                scanTextValue: this.scanTextValue,
                accent: this.accent, bodyTxt: this.bodyTxt, badgeTxt: this.badgeTxt,
                wifiBg: this.wifiBg, wifiTxt: this.wifiTxt, socialBg: this.socialBg, socialTxt: this.socialTxt
            };

            fetch('{{ route("restaurant.tables.qr-save", $restaurant->slug) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ design: payload })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    this.toast.show('Desain Kartu QR berhasil disimpan!', 'success');
                }
            })
            .catch(err => this.toast.show('Gagal menyimpan desain!', 'error'))
            .finally(() => this.saving = false);
        },

        init() {
            const initial = @js($restaurant->qr_card_design);
            if(initial && typeof initial === 'object') {
                Object.keys(initial).forEach(key => {
                    if(this[key] !== undefined) this[key] = initial[key];
                });
            }
        }
    }));
});
</script>
<script defer src="https://unpkg.com/alpinejs@3/dist/cdn.min.js"></script>
</body>
</html>
