{{-- Premium QR Card Modal --}}
@php
    $templates = [
        'minimal' => ['label' => 'Fresh Minimal', 'icon' => '✨', 'desc' => 'Bersih & modern'],
        'bistro'  => ['label' => 'Warm Bistro',   'icon' => '☕', 'desc' => 'Hangat & nyaman'],
        'dark'    => ['label' => 'Modern Dark',   'icon' => '🌙', 'desc' => 'Elegan & premium'],
    ];
    $basePrintUrl = route('restaurant.tables.qr-print', [
        'restaurant' => $record->restaurant->slug,
        'table'      => $record->id,
    ]);
@endphp

<div
    x-data="{ tpl: 'minimal' }"
    class="qr-modal-root"
>
    <style>
        .qr-modal-root {
            font-family: 'Inter', 'Segoe UI', sans-serif;
        }
        .tpl-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 10px;
            border: 1.5px solid transparent;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.18s;
            background: transparent;
            color: inherit;
        }
        .tpl-btn:not(.tpl-active):hover {
            background: rgba(99,102,241,0.07);
            border-color: rgba(99,102,241,0.25);
        }
        .tpl-btn.tpl-active {
            background: rgba(99,102,241,0.12);
            border-color: #6366f1;
            color: #6366f1;
        }
        .dark .tpl-btn.tpl-active {
            background: rgba(99,102,241,0.2);
            color: #a5b4fc;
            border-color: #818cf8;
        }
        .qr-preview-wrap {
            display: flex;
            justify-content: center;
            overflow: auto;
            border-radius: 14px;
            border: 1px solid rgba(0,0,0,0.08);
            background: #f3f4f6;
            padding: 20px;
        }
        .dark .qr-preview-wrap {
            background: rgba(255,255,255,0.05);
            border-color: rgba(255,255,255,0.08);
        }
        .qr-preview-wrap .qr-card {
            transform: scale(0.75);
            transform-origin: top center;
            margin-bottom: calc((559px * 0.75) - 559px); /* compensate scale height */
        }
        .qr-action-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 24px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.18s;
            border: none;
        }
        .qr-action-primary {
            background: #6366f1;
            color: #fff;
            box-shadow: 0 4px 12px rgba(99,102,241,0.35);
        }
        .qr-action-primary:hover {
            background: #4f46e5;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(99,102,241,0.4);
        }
        .qr-action-secondary {
            background: transparent;
            color: inherit;
            border: 1.5px solid rgba(0,0,0,0.12);
        }
        .dark .qr-action-secondary {
            border-color: rgba(255,255,255,0.12);
        }
        .qr-action-secondary:hover {
            background: rgba(99,102,241,0.07);
            border-color: #6366f1;
        }
    </style>

    {{-- ── Header Info Meja ── --}}
    <div class="flex items-center gap-3 mb-5 p-3 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800">
        <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-800 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 6h18M3 14h18M3 18h18"/>
            </svg>
        </div>
        <div>
            <div class="font-bold text-gray-900 dark:text-white text-sm">{{ $record->name }}{{ $record->area ? ' — '.$record->area : '' }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 font-mono truncate max-w-xs">{{ $record->url }}</div>
        </div>
        <a href="{{ $record->url }}" target="_blank" class="ml-auto text-indigo-500 hover:text-indigo-700 transition" title="Buka URL meja">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
            </svg>
        </a>
    </div>

    {{-- ── Template Picker ── --}}
    <div class="mb-4">
        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">Pilih Template Kartu</p>
        <div class="flex gap-2 flex-wrap">
            @foreach($templates as $key => $info)
                <button
                    type="button"
                    @click.stop.prevent="tpl = '{{ $key }}'"
                    :class="tpl === '{{ $key }}' ? 'tpl-btn tpl-active' : 'tpl-btn'"
                >
                    <span>{{ $info['icon'] }}</span>
                    <span>{{ $info['label'] }}</span>
                    <span class="text-xs opacity-50 font-normal">{{ $info['desc'] }}</span>
                </button>
            @endforeach
        </div>
    </div>

    {{-- ── Live Preview ── --}}
    <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">Preview (A6)</p>

    <div class="qr-preview-wrap mb-5" style="min-height: 440px;">
        {{-- Minimal --}}
        <div x-show="tpl === 'minimal'" x-cloak>
            @include('restaurant.qr-card', ['record' => $record, 'template' => 'minimal'])
        </div>
        {{-- Bistro --}}
        <div x-show="tpl === 'bistro'" x-cloak>
            @include('restaurant.qr-card', ['record' => $record, 'template' => 'bistro'])
        </div>
        {{-- Dark --}}
        <div x-show="tpl === 'dark'" x-cloak>
            @include('restaurant.qr-card', ['record' => $record, 'template' => 'dark'])
        </div>
    </div>

    {{-- ── Actions ── --}}
    <div class="flex gap-3 justify-center flex-wrap">
        <a
            :href="`{{ $basePrintUrl }}?template=${tpl}`"
            target="_blank"
            class="qr-action-btn qr-action-primary"
        >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Print Kartu Ini
        </a>
        <a
            href="{{ route('restaurant.tables.qr-bulk-print', ['restaurant' => $record->restaurant->slug]) }}"
            target="_blank"
            class="qr-action-btn qr-action-secondary"
        >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
            </svg>
            Print Semua Meja
        </a>
    </div>
</div>
