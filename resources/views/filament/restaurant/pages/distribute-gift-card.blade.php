<x-filament-panels::page>
<style>
/* ══════════════════════════════════════════════
   DISTRIBUTE GIFT CARD — PREMIUM UI
══════════════════════════════════════════════ */

/* Hero Banner */
.dgc-hero {
    background: linear-gradient(135deg, #1e1b4b 0%, #312e81 40%, #4c1d95 100%);
    border-radius: 1.25rem;
    padding: 2rem 2.5rem;
    position: relative;
    overflow: hidden;
    margin-bottom: 2rem;
}
.dgc-hero::before {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 220px; height: 220px;
    background: rgba(167,139,250,.15);
    border-radius: 50%;
}
.dgc-hero::after {
    content: '';
    position: absolute;
    bottom: -40px; left: 30%;
    width: 160px; height: 160px;
    background: rgba(196,181,253,.08);
    border-radius: 50%;
}

/* Mode Card Selector */
.mode-card {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: .625rem;
    padding: 1.25rem .75rem;
    border-radius: 1rem;
    border: 2px solid;
    cursor: pointer;
    transition: all .2s cubic-bezier(.4,0,.2,1);
    text-align: center;
    background: white;
}
.dark .mode-card { background: rgb(17 24 39); }
.mode-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,.08); }
.mode-card.active {
    border-color: rgb(99 102 241);
    background: linear-gradient(135deg, rgb(238 242 255), rgb(245 243 255));
    box-shadow: 0 4px 20px rgba(99,102,241,.2);
}
.dark .mode-card.active {
    background: linear-gradient(135deg, rgba(99,102,241,.15), rgba(139,92,246,.1));
}
.mode-card:not(.active) { border-color: rgb(229 231 235); }
.dark .mode-card:not(.active) { border-color: rgb(55 65 81); }
.mode-icon-wrap {
    width: 2.75rem; height: 2.75rem;
    border-radius: .75rem;
    display: flex; align-items: center; justify-content: center;
    transition: all .2s;
}
.mode-card.active .mode-icon-wrap {
    background: linear-gradient(135deg, rgb(99 102 241), rgb(139 92 246));
    box-shadow: 0 4px 12px rgba(99,102,241,.35);
    color: white;
}
.mode-card:not(.active) .mode-icon-wrap {
    background: rgb(243 244 246);
    color: rgb(107 114 128);
}
.dark .mode-card:not(.active) .mode-icon-wrap {
    background: rgb(31 41 55);
    color: rgb(156 163 175);
}
.mode-badge {
    position: absolute; top: .6rem; right: .6rem;
    width: 1.1rem; height: 1.1rem;
    border-radius: 50%;
    background: rgb(99 102 241);
    display: flex; align-items: center; justify-content: center;
    opacity: 0; transform: scale(0);
    transition: all .2s cubic-bezier(.34,1.56,.64,1);
}
.mode-card.active .mode-badge { opacity: 1; transform: scale(1); }

/* Section Card */
.dgc-section {
    background: white;
    border-radius: 1rem;
    border: 1.5px solid rgb(219 222 235);
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0,0,0,.05), 0 8px 28px rgba(99,102,241,.06);
    position: relative;
}
.dgc-section::before {
    content: '';
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 4px;
    background: linear-gradient(to bottom, rgb(99 102 241), rgb(139 92 246));
    border-radius: 4px 0 0 4px;
}
.dark .dgc-section {
    background: rgb(15 20 35);
    border-color: rgb(37 44 60);
    box-shadow: 0 2px 8px rgba(0,0,0,.25), 0 8px 28px rgba(99,102,241,.08);
}
.dark .dgc-section::before {
    background: linear-gradient(to bottom, rgb(99 102 241), rgb(109 40 217));
}
.dgc-section-header {
    padding: 1.125rem 1.5rem 1.125rem 1.75rem;
    border-bottom: 1.5px solid rgb(237 239 248);
    display: flex; align-items: center; gap: .875rem;
    background: linear-gradient(to right, rgb(246 247 254), rgb(252 252 255));
}
.dark .dgc-section-header {
    border-color: rgb(30 36 55);
    background: linear-gradient(to right, rgba(99,102,241,.08), rgba(15,20,35,0));
}
.dgc-section-icon {
    width: 2.25rem; height: 2.25rem;
    border-radius: .625rem;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem;
    background: linear-gradient(135deg, rgb(238 242 255), rgb(245 243 255));
    flex-shrink: 0;
    box-shadow: 0 2px 6px rgba(99,102,241,.15);
}
.dark .dgc-section-icon {
    background: linear-gradient(135deg, rgba(99,102,241,.2), rgba(139,92,246,.15));
}
.dgc-section-body { padding: 1.625rem 1.5rem 1.625rem 1.75rem; }

/* Field Label */
.fl {
    display: block;
    font-size: .8125rem;
    font-weight: 600;
    color: rgb(55 65 81);
    margin-bottom: .375rem;
    letter-spacing: .01em;
}
.dark .fl { color: rgb(209 213 219); }
.fl .req { color: rgb(239 68 68); margin-left: 2px; }

/* Field Hint */
.fh {
    font-size: .6875rem;
    color: rgb(156 163 175);
    margin-top: .3125rem;
    line-height: 1.45;
}
.fh code {
    background: rgb(243 244 246);
    color: rgb(99 102 241);
    padding: 1px 5px;
    border-radius: 4px;
    font-family: 'Courier New', monospace;
    font-size: .65rem;
}
.dark .fh code {
    background: rgb(31 41 55);
    color: rgb(167 139 250);
}

/* Currency Input */
.cin-wrap {
    display: flex;
    align-items: stretch;
    border: 1.5px solid rgb(209 213 219);
    border-radius: .625rem;
    background: white;
    overflow: hidden;
    transition: border-color .15s, box-shadow .15s;
}
.dark .cin-wrap {
    border-color: rgb(55 65 81);
    background: rgb(17 24 39);
}
.cin-wrap:focus-within {
    border-color: rgb(99 102 241);
    box-shadow: 0 0 0 3px rgba(99,102,241,.12);
}
.cin-prefix {
    padding: 0 .875rem;
    font-size: .8125rem;
    font-weight: 700;
    color: rgb(99 102 241);
    background: rgb(238 242 255);
    border-right: 1.5px solid rgb(209 213 219);
    display: flex; align-items: center;
    white-space: nowrap;
    user-select: none;
    letter-spacing: .02em;
}
.dark .cin-prefix {
    background: rgba(99,102,241,.1);
    border-color: rgb(55 65 81);
    color: rgb(167 139 250);
}
.cin-input {
    flex: 1;
    padding: .625rem .875rem;
    font-size: .9375rem;
    font-weight: 600;
    color: rgb(17 24 39);
    background: transparent;
    border: none;
    outline: none;
    min-width: 0;
    letter-spacing: .01em;
}
.dark .cin-input { color: rgb(243 244 246); }
.cin-input::placeholder { color: rgb(203 213 225); font-weight: 400; }

/* Tier Row */
.tier-row {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.25rem;
    border-radius: .875rem;
    border: 2px solid;
    transition: all .2s;
    cursor: default;
}
.tier-row.tier-active {
    border-color: rgb(99 102 241);
    background: linear-gradient(to right, rgb(238 242 255), white);
}
.dark .tier-row.tier-active {
    background: linear-gradient(to right, rgba(99,102,241,.1), transparent);
}
.tier-row:not(.tier-active) {
    border-color: rgb(229 231 235);
    background: rgb(249 250 251);
}
.dark .tier-row:not(.tier-active) {
    border-color: rgb(31 41 55);
    background: rgba(31,41,55,.4);
}

/* Tag chip */
.member-chip {
    display: inline-flex; align-items: center; gap: .375rem;
    padding: .3125rem .75rem;
    border-radius: 9999px;
    background: rgb(238 242 255);
    color: rgb(67 56 202);
    font-size: .8125rem;
    font-weight: 500;
    border: 1.5px solid rgb(199 210 254);
    transition: all .15s;
}
.dark .member-chip {
    background: rgba(99,102,241,.15);
    color: rgb(167 139 250);
    border-color: rgba(99,102,241,.25);
}
.member-chip-del {
    display: flex; align-items: center;
    color: rgb(129 140 248);
    cursor: pointer;
    transition: color .15s;
    padding: 0;
    border: none; background: none;
}
.member-chip-del:hover { color: rgb(239 68 68); }

/* Preview Stat Card */
.stat-card {
    padding: 1.25rem;
    border-radius: 1rem;
    text-align: center;
    border: 1.5px solid;
}

/* Alert Banner */
.alert-info { background: rgb(239 246 255); border: 1px solid rgb(191 219 254); color: rgb(29 78 216); border-radius: .875rem; padding: .875rem 1rem; font-size: .8125rem; }
.alert-purple { background: rgb(245 243 255); border: 1px solid rgb(221 214 254); color: rgb(109 40 217); border-radius: .875rem; padding: .875rem 1rem; font-size: .8125rem; }
.alert-indigo { background: rgb(238 242 255); border: 1px solid rgb(199 210 254); color: rgb(67 56 202); border-radius: .875rem; padding: .875rem 1rem; font-size: .8125rem; }
.dark .alert-info { background: rgba(29,78,216,.1); border-color: rgba(59,130,246,.2); color: rgb(147 197 253); }
.dark .alert-purple { background: rgba(109,40,217,.1); border-color: rgba(139,92,246,.2); color: rgb(196 181 253); }
.dark .alert-indigo { background: rgba(79,70,229,.1); border-color: rgba(79,70,229,.2); color: rgb(165 180 252); }

/* Textarea */
.dgc-textarea {
    width: 100%;
    border-radius: .625rem;
    border: 1.5px solid rgb(209 213 219);
    background: white;
    padding: .75rem .875rem;
    font-size: .875rem;
    color: rgb(17 24 39);
    outline: none;
    resize: none;
    transition: border-color .15s, box-shadow .15s;
    line-height: 1.6;
    font-family: inherit;
}
.dark .dgc-textarea {
    border-color: rgb(55 65 81);
    background: rgb(17 24 39);
    color: rgb(243 244 246);
}
.dgc-textarea::placeholder { color: rgb(156 163 175); }
.dgc-textarea:focus {
    border-color: rgb(99 102 241);
    box-shadow: 0 0 0 3px rgba(99,102,241,.12);
}

/* Divider */
.dgc-divider {
    height: 1px;
    background: linear-gradient(to right, transparent, rgb(229 231 235), transparent);
    margin: 1.5rem 0;
}
.dark .dgc-divider { background: linear-gradient(to right, transparent, rgb(31 41 55), transparent); }

/* ═════ MODAL CSS ═════ */
.dgc-modal-backdrop {
    position: fixed; inset: 0; z-index: 50;
    display: flex; align-items: center; justify-content: center;
    background-color: rgba(17, 24, 39, 0.7);
    backdrop-filter: blur(4px);
    padding: 1rem;
}
.dgc-modal-box {
    background: white; border-radius: 1.5rem;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
    padding: 1.75rem; max-width: 28rem; width: 100%;
    border: 1px solid rgb(229 231 235);
}
.dark .dgc-modal-box { background: rgb(17 24 39); border-color: rgb(31 41 55); }
.dgc-modal-icon-wrap {
    width: 3.5rem; height: 3.5rem; border-radius: 1rem;
    background: rgb(254 243 199); color: rgb(245 158 11);
    display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem;
}
.dark .dgc-modal-icon-wrap { background: rgba(245, 158, 11, 0.15); }
.dgc-dup-list {
    background: rgb(255 251 235); border: 1px solid rgb(253 230 138);
    border-radius: 0.75rem; margin-bottom: 1.25rem;
    max-height: 11rem; overflow-y: auto;
}
.dark .dgc-dup-list { background: rgba(245, 158, 11, 0.05); border-color: rgba(245, 158, 11, 0.2); }
.dgc-dup-item {
    display: flex; align-items: center; gap: 0.75rem;
    padding: 0.625rem 1rem; border-bottom: 1px solid rgb(253 230 138);
}
.dark .dgc-dup-item { border-color: rgba(245, 158, 11, 0.1); }
.dgc-dup-item:last-child { border-bottom: none; }
.dgc-dup-avatar {
    width: 2rem; height: 2rem; border-radius: 9999px;
    background: rgb(253 230 138); color: rgb(146 64 14);
    display: flex; align-items: center; justify-content: center;
    font-size: 0.75rem; font-weight: 700; flex-shrink: 0;
}
.dark .dgc-dup-avatar { background: rgba(245, 158, 11, 0.2); color: rgb(253 230 138); }

/* Modal Action Buttons */
.dgc-btn-prime {
    display: flex; align-items: center; gap: 0.75rem; width: 100%;
    padding: 0.875rem 1rem; border-radius: 0.75rem;
    background: rgb(79 70 229); color: white;
    text-align: left; transition: all 0.2s; border: none; cursor: pointer;
}
.dgc-btn-prime:hover { background: rgb(67 56 202); }
.dgc-btn-prime-icon {
    width: 2rem; height: 2rem; border-radius: 0.5rem; flex-shrink:0;
    background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center;
}
.dgc-btn-sec {
    display: flex; align-items: center; gap: 0.75rem; width: 100%;
    padding: 0.875rem 1rem; border-radius: 0.75rem; cursor: pointer;
    border: 2px solid rgb(229 231 235); background: transparent;
    text-align: left; transition: all 0.2s;
}
.dark .dgc-btn-sec { border-color: rgb(55 65 81); }
.dgc-btn-sec:hover { border-color: rgb(165 180 252); background: rgb(238 242 255); }
.dark .dgc-btn-sec:hover { border-color: rgb(79 70 229); background: rgba(79, 70, 229, 0.08); }
.dgc-btn-sec-icon {
    width: 2rem; height: 2rem; border-radius: 0.5rem; flex-shrink:0;
    background: rgb(243 244 246); color: rgb(107 114 128);
    display: flex; align-items: center; justify-content: center; transition: all 0.2s;
}
.dark .dgc-btn-sec-icon { background: rgb(31 41 55); color: rgb(156 163 175); }
.dgc-btn-sec:hover .dgc-btn-sec-icon { background: rgb(224 231 255); color: rgb(79 70 229); }
.dark .dgc-btn-sec:hover .dgc-btn-sec-icon { background: rgba(79, 70, 229, 0.2); color: rgb(165 180 252); }

/* STATS CARDS */
.stat-amber { background: rgb(255 251 235); border-color: rgb(253 230 138); color: rgb(217 119 6); }
.dark .stat-amber { background: rgba(245,158,11,.1); border-color: rgba(245,158,11,.2); color: rgb(251 191 36); }
.stat-blue { background: rgb(239 246 255); border-color: rgb(191 219 254); color: rgb(37 99 235); }
.dark .stat-blue { background: rgba(59,130,246,.1); border-color: rgba(59,130,246,.2); color: rgb(96 165 250); }
.stat-green { background: rgb(236 253 245); border-color: rgb(167 243 208); color: rgb(5 150 105); }
.dark .stat-green { background: rgba(16,185,129,.1); border-color: rgba(16,185,129,.2); color: rgb(52 211 153); }

</style>


    {{-- ══════════════════════════════════════════════════════════════
         MODAL KONFIRMASI DUPLIKAT
    ══════════════════════════════════════════════════════════════ --}}
    @if($showConfirmModal)
    <div class="dgc-modal-backdrop">
        <div class="dgc-modal-box">

            {{-- Header --}}
            <div class="text-center mb-5">
                <div class="dgc-modal-icon-wrap">
                    <x-heroicon-o-exclamation-triangle class="w-7 h-7"/>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Duplikat Terdeteksi</h3>
                <p class="text-sm text-gray-500">
                    <strong style="color:rgb(217,119,6);">{{ count($duplicateMembers) }} member</strong> sudah memiliki Gift Card aktif
                </p>
            </div>

            {{-- Daftar duplikat --}}
            <div class="dgc-dup-list">
                @foreach($duplicateMembers as $dup)
                <div class="dgc-dup-item">
                    <div class="dgc-dup-avatar">
                        {{ strtoupper(substr($dup['name'], 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate">{{ $dup['name'] }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ $dup['whatsapp'] ?? $dup['email'] ?? 'Tanpa kontak' }}</p>
                    </div>
                    <span class="text-xs px-2 py-0.5 rounded-full font-semibold capitalize bg-white/50 border border-gray-200 dark:bg-black/20 dark:border-gray-700/50 shrink-0" style="color:rgb(217,119,6);">{{ $dup['tier'] ?? 'bronze' }}</span>
                </div>
                @endforeach
            </div>

            {{-- Pilihan Tindakan --}}
            <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider mb-2">Pilih tindakan:</p>
            <div class="flex flex-col gap-2">

                {{-- Opsi 1: Lanjutkan Semua --}}
                <button wire:click="confirmAndProcess(false)" class="dgc-btn-prime">
                    <div class="dgc-btn-prime-icon">
                        <x-heroicon-o-paper-airplane class="w-4 h-4"/>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold leading-tight truncate">Lanjutkan untuk Semua</p>
                        <p class="text-xs mt-0.5 truncate" style="color:rgba(255,255,255,0.7);">Kirim ke semua, termasuk yang sudah punya</p>
                    </div>
                </button>

                {{-- Opsi 2: Skip Duplikat --}}
                <button wire:click="confirmAndProcess(true)" class="dgc-btn-sec">
                    <div class="dgc-btn-sec-icon">
                        <x-heroicon-o-forward class="w-4 h-4"/>
                    </div>
                    <div class="flex-1 min-w-0 text-gray-800 dark:text-gray-200">
                        <p class="text-sm font-bold leading-tight truncate">Lanjutkan, Skip Duplikat</p>
                        <p class="text-xs text-gray-400 mt-0.5 truncate">Hanya kirim ke member yang belum punya GC</p>
                    </div>
                </button>

                {{-- Opsi 3: Batal --}}
                <button wire:click="cancelConfirm"
                    class="w-full flex items-center justify-center gap-2 py-2 mt-1 text-sm text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800">
                    <x-heroicon-o-x-circle class="w-4 h-4"/>
                    Batal, kembali ke form
                </button>
            </div>
        </div>
    </div>
    @endif

    <form wire:submit.prevent="submit" class="space-y-7">

        {{-- ══════  HERO BANNER  ══════ --}}
        <div class="dgc-hero">
            <div class="relative z-10">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-white/15 backdrop-blur flex items-center justify-center text-2xl shrink-0">🎁</div>
                    <div>
                        <h2 class="text-xl font-bold text-white mb-1">Distribusi Gift Card</h2>
                        <p class="text-sm text-indigo-200 leading-relaxed max-w-xl">
                            Buat dan kirim Gift Card ke satu atau banyak pelanggan sekaligus — via WhatsApp &amp; Email secara otomatis sesuai data kontak member.
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3 mt-5">
                    @foreach([
                        ['🎯', 'Single Penerima'],
                        ['👥', 'Semua Member'],
                        ['🏆', 'By Tier'],
                        ['🔍', 'Pilih Manual'],
                    ] as [$ic, $lbl])
                    <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/10 border border-white/20 text-xs text-indigo-100 font-medium backdrop-blur-sm">
                        {{ $ic }} {{ $lbl }}
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ══════  STEP 1 — TARGET PENERIMA  ══════ --}}
        <div class="dgc-section">
            <div class="dgc-section-header">
                <div class="dgc-section-icon">🎯</div>
                <div>
                    <p class="text-sm font-bold text-gray-900 dark:text-white">Target Penerima</p>
                    <p class="text-xs text-gray-500 mt-0.5">Pilih mode distribusi Gift Card</p>
                </div>
            </div>
            <div class="dgc-section-body">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach([
                        ['single',         'heroicon-o-user',               'Single',       'Satu penerima tertentu'],
                        ['all_members',    'heroicon-o-users',               'Semua Member', 'Seluruh member restoran'],
                        ['by_tier',        'heroicon-o-trophy',              'By Tier',      'Pilih berdasarkan tier'],
                        ['select_members', 'heroicon-o-cursor-arrow-ripple', 'Pilih Member', 'Cari & pilih manual'],
                    ] as [$val, $icon, $label, $desc])
                    <button type="button" wire:click="$set('targetType', '{{ $val }}')"
                        class="mode-card {{ $targetType === $val ? 'active' : '' }}">
                        <div class="mode-icon-wrap">
                            <x-dynamic-component :component="$icon" class="w-5 h-5"/>
                        </div>
                        <span class="font-semibold text-sm {{ $targetType === $val ? 'text-indigo-700 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300' }}">{{ $label }}</span>
                        <span class="text-xs {{ $targetType === $val ? 'text-indigo-500' : 'text-gray-400' }}">{{ $desc }}</span>
                        <div class="mode-badge">
                            <x-heroicon-s-check class="w-2.5 h-2.5 text-white"/>
                        </div>
                    </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ══════  STEP 2a — SINGLE  ══════ --}}
        @if($targetType === 'single')
        <div class="dgc-section">
            <div class="dgc-section-header">
                <div class="dgc-section-icon">👤</div>
                <div>
                    <p class="text-sm font-bold text-gray-900 dark:text-white">Detail Penerima</p>
                    <p class="text-xs text-gray-500 mt-0.5">Minimal satu kontak agar notifikasi dapat dikirim</p>
                </div>
            </div>
            <div class="dgc-section-body">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">

                    <div class="sm:col-span-2 lg:col-span-3">
                        <label class="fl">Nama Penerima <span class="req">*</span></label>
                        <x-filament::input.wrapper prefix-icon="heroicon-o-user-circle">
                            <x-filament::input wire:model="singleName" type="text" placeholder="Contoh: Budi Santoso"/>
                        </x-filament::input.wrapper>
                        <p class="fh">Nama ini akan tampil di pesan WhatsApp dan template email yang dikirim.</p>
                        @error('singleName') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="fl">No. WhatsApp</label>
                        <x-filament::input.wrapper prefix-icon="heroicon-o-device-phone-mobile">
                            <x-filament::input wire:model="singlePhone" type="text" placeholder="08xxxxxxxxxx"/>
                        </x-filament::input.wrapper>
                        <p class="fh">Dikirim via WA jika WA Gateway aktif di pengaturan restoran.</p>
                    </div>

                    <div class="lg:col-span-2">
                        <label class="fl">Email</label>
                        <x-filament::input.wrapper prefix-icon="heroicon-o-envelope">
                            <x-filament::input wire:model="singleEmail" type="email" placeholder="email@contoh.com"/>
                        </x-filament::input.wrapper>
                        <p class="fh">Dikirim ke email jika SMTP sudah dikonfigurasi di pengaturan sistem.</p>
                    </div>

                    <div>
                        <label class="fl">Nilai Gift Card <span class="req">*</span></label>
                        <div class="cin-wrap w-full lg:max-w-[240px]" x-data="{
                            raw: @entangle('singleAmount'),
                            display: '',
                            init() { if (this.raw) this.display = Number(this.raw).toLocaleString('id-ID'); },
                            format(e) {
                                let d = e.target.value.replace(/\D/g,'');
                                this.raw = d ? Number(d) : 0;
                                this.display = d ? Number(d).toLocaleString('id-ID') : '';
                            }
                        }">
                            <span class="cin-prefix">Rp</span>
                            <input type="text" x-model="display" @input="format($event)"
                                placeholder="50.000" class="cin-input" inputmode="numeric" autocomplete="off"/>
                        </div>
                        <p class="fh">Minimal Rp 10.000. Ini adalah saldo awal yang bisa dipakai saat transaksi.</p>
                        @error('singleAmount') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="lg:col-span-2">
                        <label class="fl">Berlaku Sampai</label>
                        <x-filament::input.wrapper prefix-icon="heroicon-o-calendar-days">
                            <x-filament::input wire:model="expiresAt" type="date" min="{{ now()->addDay()->format('Y-m-d') }}"/>
                        </x-filament::input.wrapper>
                        <p class="fh">Kosongkan jika Gift Card tidak memiliki batas waktu kadaluarsa.</p>
                        @error('expiresAt') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2 lg:col-span-3">
                        <label class="fl">Pesan Personal</label>
                        <textarea wire:model="personalMessage" rows="3" class="dgc-textarea"
                            placeholder="Contoh: Halo {name}, selamat ulang tahun! Nikmati hadiah istimewa dari kami 🎁"></textarea>
                        <p class="fh">Opsional. Gunakan <code>{name}</code> — otomatis diganti nama penerima saat dikirim.</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- ══════  STEP 2b — ALL MEMBERS  ══════ --}}
        @if($targetType === 'all_members')
        <div class="dgc-section">
            <div class="dgc-section-header">
                <div class="dgc-section-icon">👥</div>
                <div>
                    <p class="text-sm font-bold text-gray-900 dark:text-white">Konfigurasi — Semua Member</p>
                    <p class="text-xs text-gray-500 mt-0.5">Nilai yang sama untuk seluruh member restoran</p>
                </div>
            </div>
            <div class="dgc-section-body">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                    <div>
                        <label class="fl">Nilai Gift Card <span class="req">*</span></label>
                        <div class="cin-wrap w-full lg:max-w-[240px]" x-data="{
                            raw: @entangle('flatAmount'),
                            display: '',
                            init() { if (this.raw) this.display = Number(this.raw).toLocaleString('id-ID'); },
                            format(e) {
                                let d = e.target.value.replace(/\D/g,'');
                                this.raw = d ? Number(d) : 0;
                                this.display = d ? Number(d).toLocaleString('id-ID') : '';
                            }
                        }">
                            <span class="cin-prefix">Rp</span>
                            <input type="text" x-model="display" @input="format($event)"
                                placeholder="50.000" class="cin-input" inputmode="numeric" autocomplete="off"/>
                        </div>
                        <p class="fh">Nilai yang sama diberikan ke semua member. Minimal Rp 10.000.</p>
                        @error('flatAmount') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="lg:col-span-2">
                        <label class="fl">Berlaku Sampai</label>
                        <x-filament::input.wrapper prefix-icon="heroicon-o-calendar-days">
                            <x-filament::input wire:model="expiresAt" type="date" min="{{ now()->addDay()->format('Y-m-d') }}"/>
                        </x-filament::input.wrapper>
                        <p class="fh">Sama untuk semua member. Kosongkan jika tidak ada batas waktu.</p>
                        @error('expiresAt') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="fl">Pesan Personal</label>
                        <textarea wire:model="personalMessage" rows="3" class="dgc-textarea"
                            placeholder="Contoh: Halo {name}, terima kasih sudah menjadi member setia kami! 🎁"></textarea>
                        <p class="fh">Opsional. Gunakan <code>{name}</code> — diganti nama masing-masing member secara otomatis.</p>
                    </div>
                </div>
                @if($flatAmount >= 10000) @php $this->buildPreview() @endphp @endif
            </div>
        </div>
        @endif

        {{-- ══════  STEP 2c — BY TIER  ══════ --}}
        @if($targetType === 'by_tier')
        <div class="dgc-section">
            <div class="dgc-section-header">
                <div class="dgc-section-icon">🏆</div>
                <div>
                    <p class="text-sm font-bold text-gray-900 dark:text-white">Konfigurasi per Tier</p>
                    <p class="text-xs text-gray-500 mt-0.5">Tentukan nilai Gift Card berbeda untuk setiap tier</p>
                </div>
            </div>
            <div class="dgc-section-body">
                <div class="alert-indigo mb-5">
                    💡 <strong>Tips:</strong> Tier member ditentukan otomatis berdasarkan total belanja — Bronze → Silver → Gold sesuai threshold di pengaturan restoran.
                </div>

                @error('selectedTiers') <p class="text-xs text-danger-600 mb-4">{{ $message }}</p> @enderror

                <div class="space-y-3 mb-6">
                    @foreach([
                        ['bronze', '🥉', 'Bronze', 'Total belanja terendah',   '#d97706', '#ffffff', 'bronzeAmount'],
                        ['silver', '🥈', 'Silver', 'Total belanja menengah',   '#64748b', '#ffffff', 'silverAmount'],
                        ['gold',   '🥇', 'Gold',   'Total belanja tertinggi',  '#f59e0b', '#422006', 'goldAmount'],
                    ] as [$tier, $emoji, $label, $tierDesc, $bgHex, $textHex, $model])
                    <div class="tier-row {{ in_array($tier, $selectedTiers) ? 'tier-active' : '' }}">
                        <input type="checkbox" wire:model.live="selectedTiers" value="{{ $tier }}"
                            id="tier_{{ $tier }}"
                            class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 shrink-0 cursor-pointer"/>
                        <label for="tier_{{ $tier }}" class="flex items-center gap-3 flex-1 min-w-0 cursor-pointer">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold shrink-0 shadow-sm" style="background-color: {{ $bgHex }}; color: {{ $textHex }};">
                                {{ $emoji }} {{ $label }}
                            </span>
                            <span class="text-sm text-gray-500 dark:text-gray-400 hidden sm:block">{{ $tierDesc }}</span>
                        </label>
                        @if(in_array($tier, $selectedTiers))
                        <div class="flex flex-col gap-1 shrink-0">
                            <div class="cin-wrap w-44" x-data="{
                                raw: @entangle($model),
                                display: '',
                                init() { if (this.raw) this.display = Number(this.raw).toLocaleString('id-ID'); },
                                format(e) {
                                    let d = e.target.value.replace(/\D/g,'');
                                    this.raw = d ? Number(d) : 0;
                                    this.display = d ? Number(d).toLocaleString('id-ID') : '';
                                }
                            }">
                                <span class="cin-prefix text-xs">Rp</span>
                                <input type="text" x-model="display" @input="format($event)"
                                    placeholder="50.000" class="cin-input text-sm" inputmode="numeric" autocomplete="off"/>
                            </div>
                            @error("{$tier}Amount") <p class="text-xs text-danger-600">{{ $message }}</p> @enderror
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>

                <div class="dgc-divider"></div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="fl">Berlaku Sampai</label>
                        <x-filament::input.wrapper prefix-icon="heroicon-o-calendar-days">
                            <x-filament::input wire:model="expiresAt" type="date" min="{{ now()->addDay()->format('Y-m-d') }}"/>
                        </x-filament::input.wrapper>
                        <p class="fh">Tanggal yang sama berlaku untuk semua tier. Kosongkan jika tidak ada batas waktu.</p>
                        @error('expiresAt') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="fl">Pesan Personal</label>
                        <textarea wire:model="personalMessage" rows="3" class="dgc-textarea"
                            placeholder="Contoh: Halo {name}, nikmati Gift Card eksklusif untuk member setia kami! 🎁"></textarea>
                        <p class="fh">Opsional. Gunakan <code>{name}</code> — diganti nama masing-masing member secara otomatis.</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- ══════  STEP 2d — SELECT MEMBERS  ══════ --}}
        @if($targetType === 'select_members')
        <div class="dgc-section">
            <div class="dgc-section-header">
                <div class="dgc-section-icon">🔍</div>
                <div>
                    <p class="text-sm font-bold text-gray-900 dark:text-white">Pilih Member</p>
                    <p class="text-xs text-gray-500 mt-0.5">Cari dan pilih member penerima Gift Card</p>
                </div>
            </div>
            <div class="dgc-section-body">

                <label class="fl">Cari Member</label>
                <div class="relative mb-1">
                    <x-filament::input.wrapper prefix-icon="heroicon-o-magnifying-glass">
                        <x-filament::input wire:model.live.debounce.300ms="memberSearch"
                            type="text" placeholder="Ketik min. 2 karakter — nama, WhatsApp, atau email..."/>
                    </x-filament::input.wrapper>
                    @if(strlen($memberSearch) >= 2)
                    <div class="absolute top-full left-0 right-0 mt-1.5 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 z-20 overflow-hidden">
                        @forelse($this->searchResults as $m)
                        <button type="button" wire:click="addMember({{ $m->id }})"
                            class="flex items-center gap-3 w-full px-4 py-3 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition text-left border-b border-gray-50 dark:border-gray-700 last:border-0 group">
                            <div class="w-9 h-9 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-sm font-bold text-indigo-600 dark:text-indigo-400 shrink-0 group-hover:scale-110 transition-transform">
                                {{ strtoupper(substr($m->name, 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $m->name }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ $m->whatsapp ?? $m->email ?? 'Tanpa kontak' }}</p>
                            </div>
                            <span class="text-xs px-2.5 py-1 rounded-full font-semibold shrink-0"
                                style="
                                    background-color: {{ $m->tier === 'gold' ? '#fef3c7' : ($m->tier === 'silver' ? '#f1f5f9' : '#fef3c7') }};
                                    color: {{ $m->tier === 'gold' ? '#b45309' : ($m->tier === 'silver' ? '#475569' : '#b45309') }};
                                    border: 1px solid {{ $m->tier === 'gold' ? '#fde68a' : ($m->tier === 'silver' ? '#e2e8f0' : '#fde68a') }};
                                ">
                                {{ $m->tier ?? 'bronze' }}
                            </span>
                        </button>
                        @empty
                        <div class="text-center py-5">
                            <x-heroicon-o-magnifying-glass class="w-8 h-8 text-gray-300 mx-auto mb-2"/>
                            <p class="text-sm text-gray-400">Tidak ada member ditemukan untuk "<strong>{{ $memberSearch }}</strong>"</p>
                        </div>
                        @endforelse
                    </div>
                    @endif
                </div>
                <p class="fh mb-5">Klik nama member di dropdown untuk menambahkan ke daftar penerima.</p>

                {{-- Selected Members --}}
                <label class="fl">
                    Daftar Penerima
                    @if(!empty($selectedMemberIds))
                    <span class="ml-2 text-xs font-semibold text-indigo-600 bg-indigo-50 dark:bg-indigo-900/30 dark:text-indigo-400 px-2.5 py-0.5 rounded-full">
                        {{ count($selectedMemberIds) }} dipilih
                    </span>
                    @endif
                </label>
                @if(!empty($selectedMemberIds))
                <div class="flex flex-wrap gap-2 p-4 rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 min-h-14 mb-1">
                    @foreach($this->selectedMembers as $m)
                    <div class="member-chip">
                        <span class="w-5 h-5 rounded-full bg-indigo-200 dark:bg-indigo-800 flex items-center justify-center text-[10px] font-bold text-indigo-700 dark:text-indigo-300 shrink-0">
                            {{ strtoupper(substr($m->name, 0, 1)) }}
                        </span>
                        {{ $m->name }}
                        <button type="button" wire:click="removeMember({{ $m->id }})" class="member-chip-del" title="Hapus">
                            <x-heroicon-s-x-mark class="w-3.5 h-3.5"/>
                        </button>
                    </div>
                    @endforeach
                </div>
                <p class="fh mb-5">Klik × pada nama untuk menghapus dari daftar.</p>
                @else
                <div class="flex flex-col items-center justify-center p-6 rounded-xl bg-gray-50 dark:bg-gray-800/60 border-2 border-dashed border-gray-200 dark:border-gray-700 text-center mb-5">
                    <x-heroicon-o-users class="w-8 h-8 text-gray-300 dark:text-gray-600 mb-2"/>
                    <p class="text-sm text-gray-400 dark:text-gray-500">Belum ada member dipilih</p>
                    <p class="text-xs text-gray-300 dark:text-gray-600 mt-0.5">Gunakan kotak pencarian di atas</p>
                </div>
                @endif

                <div class="dgc-divider"></div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                    <div>
                        <label class="fl">Nilai Gift Card <span class="req">*</span></label>
                        <div class="cin-wrap w-full lg:max-w-[240px]" x-data="{
                            raw: @entangle('flatAmount'),
                            display: '',
                            init() { if (this.raw) this.display = Number(this.raw).toLocaleString('id-ID'); },
                            format(e) {
                                let d = e.target.value.replace(/\D/g,'');
                                this.raw = d ? Number(d) : 0;
                                this.display = d ? Number(d).toLocaleString('id-ID') : '';
                            }
                        }">
                            <span class="cin-prefix">Rp</span>
                            <input type="text" x-model="display" @input="format($event)"
                                placeholder="50.000" class="cin-input" inputmode="numeric" autocomplete="off"/>
                        </div>
                        <p class="fh">Nilai sama untuk semua member dipilih. Min Rp 10.000.</p>
                        @error('flatAmount') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="lg:col-span-2">
                        <label class="fl">Berlaku Sampai</label>
                        <x-filament::input.wrapper prefix-icon="heroicon-o-calendar-days">
                            <x-filament::input wire:model="expiresAt" type="date" min="{{ now()->addDay()->format('Y-m-d') }}"/>
                        </x-filament::input.wrapper>
                        <p class="fh">Kosongkan jika tidak ada batas waktu kadaluarsa.</p>
                        @error('expiresAt') <p class="mt-1 text-xs text-danger-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="fl">Pesan Personal</label>
                        <textarea wire:model="personalMessage" rows="3" class="dgc-textarea"
                            placeholder="Contoh: Halo {name}, ini hadiah spesial khusus untuk Anda! 🎁"></textarea>
                        <p class="fh">Opsional. Gunakan <code>{name}</code> — diganti nama masing-masing member secara otomatis saat diproses.</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- ══════  PREVIEW DISTRIBUSI  ══════ --}}
        @if(!empty($previewData) && $targetType !== 'single')
        <div class="dgc-section">
            <div class="dgc-section-header">
                <div class="dgc-section-icon">📊</div>
                <div>
                    <p class="text-sm font-bold text-gray-900 dark:text-white">Preview Distribusi</p>
                    <p class="text-xs text-gray-500 mt-0.5">Ringkasan berdasarkan konfigurasi saat ini</p>
                </div>
            </div>
            <div class="dgc-section-body">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
                    <div class="stat-card border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/60">
                        <div class="text-3xl font-extrabold text-gray-900 dark:text-white">{{ $previewData['total'] }}</div>
                        <div class="text-xs font-semibold text-gray-500 mt-1.5">Total Member</div>
                        <div class="text-[11px] text-gray-400 mt-0.5">akan menerima GC</div>
                    </div>
                    @if($previewData['duplicates'] > 0)
                    <div class="stat-card stat-amber">
                        <div class="text-3xl font-extrabold">{{ $previewData['duplicates'] }}</div>
                        <div class="text-xs font-semibold mt-1.5">⚠️ Duplikat</div>
                        <div class="text-[11px] opacity-80 mt-0.5">sudah punya GC aktif</div>
                    </div>
                    @endif
                    @if($previewData['no_contact'] > 0)
                    <div class="stat-card stat-blue">
                        <div class="text-3xl font-extrabold">{{ $previewData['no_contact'] }}</div>
                        <div class="text-xs font-semibold mt-1.5">ℹ️ Tanpa Kontak</div>
                        <div class="text-[11px] opacity-80 mt-0.5">GC dibuat, notif skip</div>
                    </div>
                    @endif
                    @if(($previewData['total_value'] ?? 0) > 0)
                    <div class="stat-card stat-green">
                        <div class="text-xl font-extrabold">Rp {{ number_format($previewData['total_value'], 0, ',', '.') }}</div>
                        <div class="text-xs font-semibold mt-1.5">💰 Total Nilai</div>
                        <div class="text-[11px] opacity-80 mt-0.5">akan diterbitkan</div>
                    </div>
                    @endif
                </div>

                <div class="space-y-2.5">
                    @if($previewData['no_contact'] > 0)
                    <div class="alert-info">
                        ℹ️ <strong>{{ $previewData['no_contact'] }} member</strong> tidak punya WA atau email — Gift Card tetap dibuat, bisa dilihat di Member Dashboard.
                    </div>
                    @endif
                    @if(($previewData['total'] ?? 0) >= 50)
                    <div class="alert-purple">
                        ⚡ <strong>{{ $previewData['total'] }} member</strong> melebihi batas 50 — distribusi akan diproses di <strong>background (queue)</strong>. Notifikasi dikirim ke panel Anda ketika selesai.
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- ══════  SUBMIT BAR  ══════ --}}
        <div class="flex items-center justify-between gap-4 py-1">
            <a href="{{ \App\Filament\Restaurant\Resources\GiftCardResource::getUrl('index') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-xl transition">
                <x-heroicon-o-arrow-left class="w-4 h-4"/>
                Kembali ke Daftar
            </a>
            <x-filament::button type="submit" :disabled="$isProcessing" icon="heroicon-o-paper-airplane" size="lg"
                class="shadow-lg shadow-primary-500/20">
                @if($isProcessing)
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        Memproses...
                    </span>
                @else
                    Buat &amp; Kirim Gift Card
                @endif
            </x-filament::button>
        </div>

    </form>
</x-filament-panels::page>
