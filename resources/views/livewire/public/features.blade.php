@php $settings = $settings ?? app(\App\Settings\GeneralSettings::class); @endphp
<div class="bg-gray-50 dark:bg-[#0B0F19] text-gray-400 dark:text-gray-300 min-h-screen font-sans overflow-x-hidden transition-colors duration-300" x-data="featuresPage">
    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('featuresPage', () => ({
            activeTab: 'all',
            search: '',
            mobileMenu: false,
            get searchActive() {
                return this.search.trim() !== '';
            },
            // sectionText = gabungan semua teks card dalam section (dihitung di Blade)
            sectionVisible(tab, sectionText) {
                if (this.search.trim() !== '') {
                    // Tampilkan section HANYA jika minimal 1 card-nya cocok
                    return sectionText.toLowerCase().includes(this.search.toLowerCase().trim());
                }
                return this.activeTab === 'all' || this.activeTab === tab;
            },
            cardVisible(cardText, sectionTitleText) {
                if (this.search.trim() === '') return true;
                const q = this.search.toLowerCase().trim();
                // Jika judul/tab section cocok → tampilkan SEMUA card di section ini
                if (sectionTitleText.toLowerCase().includes(q)) return true;
                // Jika tidak → hanya tampilkan card yang teks-nya sendiri cocok
                return cardText.toLowerCase().includes(q);
            }
        }));
    });
    </script>

    {{-- Skip to Content --}}
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-[100] focus:px-6 focus:py-3 focus:bg-indigo-600 focus:text-white focus:rounded-full focus:font-bold focus:shadow-2xl">
        Skip to Content
    </a>
<style>
    .gp{background:rgba(255,255,255,.6);backdrop-filter:blur(20px);border:1px solid rgba(0,0,0,.05);box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);}
    .dark .gp{background:rgba(17,24,39,.6);backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,.05);box-shadow: 0 4px 6px -1px rgba(0,0,0,0.2), 0 2px 4px -1px rgba(0,0,0,0.1);}
    .bp{background:linear-gradient(135deg,#f59e0b11,#b4530911);border:1px solid #f59e0b33;color:#b45309}
    .dark .bp{background:linear-gradient(135deg,#f59e0b15,#b4530915);border:1px solid #f59e0b33;color:#fbbf24}
    .bs{background:rgba(99,102,241,.05);border:1px solid rgba(99,102,241,.15);color:#4f46e5}
    .dark .bs{background:rgba(99,102,241,.1);border:1px solid rgba(99,102,241,.2);color:#a5b4fc}
    .fc{transition:all .4s cubic-bezier(0.4, 0, 0.2, 1)}.fc:hover{transform:translateY(-8px);border-color:rgba(99,102,241,.3)!important;box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);}
    .dark .fc:hover{border-color:rgba(99,102,241,.4)!important;box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);}
    .search-input{transition: all 0.3s ease; border-radius: 20px !important;}
    .search-input::placeholder{color:#9ca3af}
    .dark .search-input::placeholder{color:#4b5563}
    .search-input:focus{outline:none;border-color:#6366f1;box-shadow:0 0 30px rgba(99,102,241,.15)}
    .dark .search-input:focus{box-shadow:0 0 30px rgba(99,102,241,.25)}
    .tab-scroll::-webkit-scrollbar {
        height: 4px;
    }
    .tab-scroll::-webkit-scrollbar-track {
        background: transparent;
    }
    .tab-scroll::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }
    .dark .tab-scroll::-webkit-scrollbar-thumb {
        background: #1e293b;
    }
    .tab-scroll {
        scrollbar-width: thin;
        scrollbar-color: #e2e8f0 transparent;
    }
    .dark .tab-scroll {
        scrollbar-color: #1e293b transparent;
    }
    .mesh-gradient {
        background-color: transparent;
        background-image: radial-gradient(at 100% 0%, rgba(99,102,241, 0.15) 0px, transparent 50%),
                          radial-gradient(at 0% 100%, rgba(168, 85, 247, 0.15) 0px, transparent 50%);
    }
    [x-cloak] { display: none !important; }
</style>

@include('components.public.navbar')

{{-- Hero --}}
<main id="main-content" class="relative pt-32 pb-10 text-center overflow-hidden">
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[700px] h-[300px] bg-primary-500/10 dark:bg-indigo-600/15 blur-[120px] rounded-full pointer-events-none"></div>
    <div class="relative max-w-4xl mx-auto px-4 z-10">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-primary-600 dark:hover:text-gray-300 transition mb-6">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg> Kembali ke Beranda
        </a>
        <h1 class="text-4xl md:text-6xl font-extrabold text-gray-900 dark:text-white mb-4 tracking-tight">
            Semua Fitur <span class="bg-gradient-to-r from-primary-600 to-indigo-600 dark:from-indigo-400 dark:to-purple-400 bg-clip-text text-transparent">{{ $settings->site_name }}</span>
        </h1>
        <p class="text-lg text-gray-500 dark:text-gray-400 max-w-2xl mx-auto mb-8">Platform all-in-one manajemen restoran modern. Dari kasir hingga dapur, loyalitas pelanggan hingga laporan keuangan.</p>

        {{-- Search Bar --}}
        <div class="relative max-w-xl mx-auto">
            <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input
                type="text"
                x-model="search"
                aria-label="Cari fitur"
                placeholder="Cari fitur... (contoh: KDS, split bill, QRIS, loyalty)"
                class="search-input w-full pl-12 pr-12 py-4 rounded-2xl bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white text-sm transition shadow-sm"
            >
            <button
                x-show="searchActive"
                @click="search = ''"
                aria-label="Hapus pencarian"
                class="absolute inset-y-0 right-4 flex items-center text-gray-400 dark:text-gray-500 hover:text-primary-600 dark:hover:text-white transition focus:outline-none"
                x-transition
            >
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Search hint --}}
        <div class="mt-4 flex justify-center gap-3 flex-wrap text-xs text-gray-600">
            <span class="bp px-3 py-1 rounded-full font-semibold">⭐ Premium</span>
            <span class="bs px-3 py-1 rounded-full font-semibold">🔹 Standar</span>
            <span class="px-3 py-1 rounded-full bg-white/5 border border-white/10">{{ $totalFeatures }} + Fitur Tersedia</span>
        </div>
    </main>

{{-- Tab Filter (hidden when searching) --}}
<div class="sticky top-16 z-40 bg-white/95 dark:bg-[#0B0F19]/95 backdrop-blur border-b border-gray-200 dark:border-white/5 transition-colors duration-300" x-show="!searchActive" x-transition>
    @php $settings = $settings ?? app(\App\Settings\GeneralSettings::class); @endphp
    <div class="transition-colors duration-300 font-sans selection:bg-primary-500/30">
    <div class="max-w-7xl mx-auto px-4">
        <div 
            class="flex gap-2 overflow-x-auto pb-4 pt-3 tab-scroll snap-x snap-mandatory scroll-smooth" 
            role="tablist" 
            aria-label="Kategori Fitur">
            @foreach([
                ['all','Semua Fitur'],['order','🛒 Pemesanan'],['kitchen','🍳 Dapur & KDS'],['pos','💳 POS & Kasir'],
                ['kiosk','🖥️ Kiosk'],['loyalty','🎁 Loyalitas'],['engagement','⭐ Microsite'],['finance','💰 Keuangan'],['analytics','📊 Analitik'],
                ['notif','🔔 Notifikasi'],['pwa','📱 PWA'],['support','🎫 Bantuan'],['admin','⚙️ Admin']
            ] as [$tab,$label])
            <button @click="activeTab='{{ $tab }}'" 
                role="tab"
                :aria-selected="activeTab === '{{ $tab }}'"
                :tabindex="activeTab === '{{ $tab }}' ? 0 : -1"
                :class="activeTab==='{{ $tab }}' ? 'bg-primary-600 dark:bg-indigo-600 text-white shadow-md' : 'text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-white/5'"
                class="flex-none px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap transition snap-start focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ $label }}</button>
            @endforeach
            {{-- Extra space on right --}}
            <div class="flex-none w-8"></div>
        </div>
    </div>
    </div>
</div>

{{-- Search status bar --}}
<div x-show="searchActive" x-transition class="sticky top-16 z-40 bg-primary-50 dark:bg-indigo-600/10 border-b border-primary-100 dark:border-indigo-500/20 py-2.5 px-4 text-center transition-colors duration-300">
    <p class="text-sm text-primary-700 dark:text-indigo-300" aria-live="polite">
        Menampilkan hasil untuk: <span class="font-bold text-primary-900 dark:text-white" x-text="'&quot;' + search + '&quot;'"></span>
        <button @click="search = ''" class="ml-3 text-xs text-primary-600 dark:text-indigo-400 hover:text-primary-800 dark:hover:text-white underline focus:outline-none focus:ring-1 focus:ring-indigo-500">✕ Hapus pencarian</button>
    </p>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 flex flex-col gap-16">

@foreach($sections as $section)
@php
    $sectionTitleText   = strtolower($section['title'] . ' ' . $section['tab']);
    $sectionSearchText  = $sectionTitleText . ' ' .
        collect($section['cards'])
            ->map(fn($c) => strtolower($c['title'] . ' ' . $c['desc'] . ' ' . 
                collect($c['bullets'])->pluck('text')->implode(' ')
            ))
            ->implode(' ');
@endphp
<section x-show="sectionVisible('{{ $section['tab'] }}', {{ Js::from($sectionSearchText) }})">
    <div class="flex items-center gap-3 mb-8">
        @php $svgPath = public_path("vendor/uicons-regular-rounded/svg/fi-rr-{$section['icon']}.svg"); @endphp
        <div class="w-10 h-10 rounded-xl bg-white dark:bg-white/5 border border-gray-100 dark:border-white/5 shadow-sm flex items-center justify-center text-xl overflow-hidden p-2">
            @if(file_exists($svgPath))
                <div class="w-full h-full fill-current text-indigo-500">
                    @php $svgContent = preg_replace('/<\?xml.*?\?>/i', '', file_get_contents($svgPath)); @endphp
                    {!! str_replace('<svg ', '<svg class="w-full h-full" ', $svgContent) !!}
                </div>
            @else
                {{ $section['icon'] }}
            @endif
        </div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white transition-colors">{{ $section['title'] }}</h2>
        <span class="{{ $section['badge']==='Premium' ? 'bp' : 'bs' }} px-3 py-0.5 rounded-full text-xs font-semibold">{{ $section['badge'] }}</span>
    </div>
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($section['cards'] as $card)
        @php 
            $bulletText = collect($card['bullets'])->pluck('text')->implode(' ');
            $searchText = strtolower($card['title'].' '.$card['desc'].' '.$bulletText); 
        @endphp
        <a href="{{ route('features.show', $card['slug']) }}" class="fc gp p-6 rounded-2xl block hover:no-underline" data-ft-card x-show="cardVisible({{ Js::from($searchText) }}, {{ Js::from($sectionTitleText) }})">
            <div>
                <div class="{{ $card['badge']==='Premium'?'bp':'bs' }} inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold mb-3">{{ $card['badge'] }}</div>
                <h3 class="font-bold text-gray-900 dark:text-white text-lg mb-1.5 transition-colors">{{ $card['title'] }}</h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm mb-4 leading-relaxed transition-colors">{{ $card['desc'] }}</p>
                <ul class="space-y-1.5 text-xs text-gray-400 dark:text-gray-500">
                    @foreach($card['bullets'] as $b)
                    <li class="flex items-center gap-2">
                        @php 
                            $rawIcon = $b['icon'] ?? 'star';
                            $cleanIcon = str_replace(['fi ', 'fi-rr-', 'fi-rs-', 'fi-br-', 'fi-sr-'], '', $rawIcon);
                            $bulletSvg = public_path("vendor/uicons-regular-rounded/svg/fi-rr-{$cleanIcon}.svg");
                        @endphp
                        <span class="text-primary-500 shrink-0 w-3 h-3 block">
                            @if(file_exists($bulletSvg))
                                <div class="w-full h-full fill-current">
                                    @php $bSvgContent = preg_replace('/<\?xml.*?\?>/i', '', file_get_contents($bulletSvg)); @endphp
                                    {!! str_replace('<svg ', '<svg class="w-full h-full" ', $bSvgContent) !!}
                                </div>
                            @else
                                ✓
                            @endif
                        </span>
                        {{ $b['text'] }}
                    </li>
                    @endforeach
                </ul>
                <div class="mt-6 flex items-center gap-2 text-xs font-semibold text-primary-600 dark:text-indigo-400 group-hover:gap-3 transition-all">
                    Selengkapnya 
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </div>
            </div>
        </a>
        @endforeach
    </div>
</section>
@endforeach

    {{-- No results message --}}
    <div x-show="searchActive && $el.previousElementSibling && document.querySelectorAll('[data-ft-card]').length > 0 &&
         [...document.querySelectorAll('[data-ft-card]')].every(el => el.style.display === 'none')"
         class="text-center py-20" style="display:none;">
        <div class="text-5xl mb-4">🔍</div>
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Fitur tidak ditemukan</h3>
        <p class="text-gray-500 dark:text-gray-400 mb-6 transition-colors">Tidak ada fitur yang cocok dengan "<span class="text-primary-600 dark:text-white" x-text="search"></span>"</p>
        <button @click="search = ''" class="px-6 py-3 rounded-full bg-primary-600 hover:bg-primary-500 dark:bg-indigo-600 dark:hover:bg-indigo-500 text-white font-semibold transition shadow-lg shadow-primary-500/20">Hapus Pencarian</button>
    </div>

    </div>
    {{-- CTA --}}
    <section class="gp rounded-3xl p-12 text-center relative overflow-hidden transition-all duration-300">
        <div class="absolute inset-0 bg-gradient-to-br from-primary-600/5 to-purple-600/5 dark:from-indigo-600/10 dark:to-purple-600/5 pointer-events-none"></div>
        <div class="relative z-10">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4 transition-colors">Siap Mencoba {{ $settings->site_name }}?</h2>
            <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-xl mx-auto transition-colors">Daftar gratis dan rasakan sendiri bagaimana platform ini mengubah cara kerja restoran Anda.</p>
            <div class="flex justify-center gap-4 flex-wrap">
                <a href="{{ route('filament.restaurant.auth.register') }}" class="px-8 py-4 rounded-full bg-primary-600 hover:bg-primary-500 dark:bg-indigo-600 dark:hover:bg-indigo-500 text-white font-bold text-lg transition shadow-xl shadow-primary-500/20 hover:-translate-y-1 transform">🚀 Daftar Gratis Sekarang</a>
                <a href="{{ route('home') }}#harga" class="px-8 py-4 rounded-full border border-gray-200 dark:border-white/10 bg-white/50 dark:bg-white/5 hover:bg-gray-100 dark:hover:bg-white/10 text-gray-900 dark:text-white font-bold text-lg transition hover:-translate-y-1 transform">Lihat Paket Harga</a>
            </div>
        </div>
    </section>
</div>

    {{-- Footer --}}
    <x-footer-premium />
</div>
