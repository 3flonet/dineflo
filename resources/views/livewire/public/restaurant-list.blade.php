@php $settings = $settings ?? app(\App\Settings\GeneralSettings::class); @endphp
<div class="min-h-screen bg-gray-50 dark:bg-[#0B0F19] flex flex-col pb-12 transition-colors duration-300 text-gray-900 dark:text-gray-100">
    <nav class="fixed top-0 w-full z-50 transition-all duration-300" 
         x-data="{ scrolled: false, mobileMenuOpen: false }" 
         @scroll.window="scrolled = (window.pageYOffset > 20)"
         :class="scrolled ? 'bg-white/80 dark:bg-[#111827]/80 backdrop-blur-xl border-b border-gray-200 dark:border-white/10 shadow-lg' : 'bg-transparent border-transparent'">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center transition-all duration-300" :class="scrolled ? 'h-16' : 'h-24'">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center gap-3 group">
                    @if($settings->site_logo)
                        <img src="{{ Storage::url($settings->site_logo) }}" alt="{{ $settings->site_name }}" class="h-9 w-auto object-contain transform group-hover:scale-105 transition-transform">
                    @else
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-500/30 transform group-hover:scale-105 transition-transform">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                    @endif
                    <span class="font-bold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-orange-500 transition-all" :class="scrolled ? 'text-xl' : 'text-2xl'">{{ $settings->site_name }}</span>
                </a>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex space-x-8 items-center">
                    <a href="{{ route('home') }}" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-white transition group flex items-center gap-1">
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-indigo-400 dark:group-hover:text-indigo-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Beranda
                    </a>
                    <a href="{{ route('home') }}#fitur" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-white transition group flex items-center gap-1">
                        Fitur
                    </a>
                    <a href="{{ route('home') }}#harga" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-white transition group flex items-center gap-1">
                        Harga
                    </a>
                    <div class="w-px h-5 bg-gray-200 dark:bg-white/20"></div>

                    {{-- Theme Toggle --}}
                    <button @click="theme = theme === 'light' ? 'dark' : 'light'; updateTheme()" 
                            class="p-2 rounded-xl bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-500 dark:text-gray-400 hover:bg-white dark:hover:bg-white/10 transition-all">
                        <svg x-show="theme === 'light'" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                        <svg x-show="theme === 'dark' || theme === 'system'" x-cloak xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                        </svg>
                    </button>

                    <a href="{{ route('filament.restaurant.auth.login') }}" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-white transition">
                        Login Restoran
                    </a>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-white focus:outline-none p-2 rounded-lg bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10">
                        <svg class="h-6 w-6" x-show="!mobileMenuOpen" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg class="h-6 w-6" x-show="mobileMenuOpen" style="display: none;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div x-show="mobileMenuOpen" 
             style="display: none;"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="md:hidden absolute top-full left-0 w-full bg-white/95 dark:bg-[#111827]/95 backdrop-blur-3xl border-b border-gray-200 dark:border-white/10 shadow-2xl py-4 flex flex-col space-y-4 px-6 z-50">
            <a href="{{ route('home') }}" class="text-base font-medium text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-white transition flex items-center gap-2">
                 <svg class="w-5 h-5 text-primary-500 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                 Beranda
            </a>
            <a href="{{ route('home') }}#fitur" class="text-base font-medium text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-white transition">Fitur</a>
            <a href="{{ route('home') }}#harga" class="text-base font-medium text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-white transition">Harga</a>
            <div class="h-px w-full bg-gray-200 dark:bg-white/10 my-2"></div>
            <a href="{{ route('filament.restaurant.auth.login') }}" class="flex justify-center w-full px-5 py-3 rounded-xl bg-primary-500/10 dark:bg-indigo-500/20 hover:bg-primary-500/20 dark:hover:bg-indigo-500/30 border border-primary-500/20 dark:border-indigo-500/30 text-primary-600 dark:text-indigo-300 text-sm font-bold transition">
                Masuk / Daftar Restoran
            </a>
        </div>
    </nav>

    {{-- Premium Hero Header --}}
    <div class="relative bg-white dark:bg-black text-gray-900 dark:text-white shrink-0 overflow-hidden pb-16 pt-32 border-b border-gray-100 dark:border-white/5 transition-colors duration-300">
        {{-- Background Effects --}}
        <div class="absolute inset-0 bg-gradient-to-b from-gray-50 to-white dark:from-[#0B0F19] dark:via-[#0B0F19] dark:to-[#111827]"></div>
        <div class="absolute inset-0 bg-[url('https://laravel.com/assets/img/welcome/background.svg')] bg-cover bg-top opacity-[0.03] dark:opacity-10 mix-blend-screen pointer-events-none"></div>
        <div class="absolute top-0 inset-x-0 h-40 bg-gradient-to-b from-primary-500/5 dark:from-white/5 to-transparent"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 relative z-10 flex flex-col items-center justify-center text-center mt-4">
            {{-- Little Badge --}}
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 backdrop-blur-md mb-6 shadow-sm dark:shadow-lg dark:shadow-black/50">
                <span class="flex h-2 w-2 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                </span>
                <span class="text-[11px] font-bold tracking-wider uppercase text-gray-500 dark:text-gray-300">Dineflo Network</span>
            </div>

            <h1 class="text-4xl sm:text-5xl md:text-6xl font-black mb-6 tracking-tight leading-tight text-gray-900 dark:text-white drop-shadow-sm">
                Temukan Pilihan <br class="hidden sm:block" />
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-600 via-amber-500 to-amber-600 dark:from-amber-200 dark:via-amber-400 dark:to-amber-200">Rasa Terbaik</span>
            </h1>
            
            <p class="text-gray-500 dark:text-gray-400 text-base sm:text-lg font-medium max-w-2xl mb-10 leading-relaxed">
                Jelajahi berbagai restoran eksklusif, lihat menu digital, dan pesan makanan langsung tanpa antre. Cepat, aman, dan memuaskan.
            </p>

            {{-- Floating Search Bar --}}
            <div class="w-full max-w-2xl relative group">
                <div class="absolute inset-0 bg-gradient-to-r from-primary-500/20 to-purple-500/20 rounded-2xl blur-xl group-hover:blur-2xl transition-all duration-300 opacity-70"></div>
                <div class="relative bg-white/90 dark:bg-[#111827]/80 backdrop-blur-xl border border-gray-200 dark:border-white/10 group-hover:border-primary-500/30 p-1.5 rounded-2xl flex items-center shadow-2xl transition-colors duration-300">
                    <div class="pl-4 pr-2 text-primary-600 dark:text-indigo-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Berdasarkan nama restoran atau kota..." 
                           class="w-full bg-transparent border-none text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 px-3 py-3 sm:py-4 focus:ring-0 text-[15px] sm:text-base font-medium">
                    <div class="pr-2 hidden sm:block opacity-0 group-hover:opacity-100 transition-opacity">
                         <span class="bg-primary-500/10 dark:bg-indigo-500/20 border border-primary-500/20 dark:border-indigo-500/20 text-xs font-bold px-2 py-1 rounded text-primary-600 dark:text-indigo-300 uppercase tracking-wider">Cari</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Content Area --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 relative z-20 w-full pt-12 flex-grow">
        
        {{-- Skeleton Loading --}}
        <div wire:loading wire:target="search" class="w-full">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 sm:gap-8">
                @for ($i = 0; $i < 8; $i++)
                    <div class="glass-panel rounded-[2rem] p-4 border border-gray-200 dark:border-white/5 animate-pulse flex flex-col h-80">
                        <div class="h-44 bg-gray-100 dark:bg-white/5 rounded-[1.5rem] mb-8 w-full relative">
                            <div class="absolute -bottom-6 left-6 h-14 w-14 bg-white/50 dark:bg-white/10 rounded-2xl border-4 border-gray-50 dark:border-[#111827]"></div>
                        </div>
                        <div class="px-2 mt-2">
                            <div class="h-5 bg-gray-200 dark:bg-white/10 rounded w-2/3 mb-3"></div>
                            <div class="h-4 bg-gray-100 dark:bg-white/5 rounded w-1/3 mb-4"></div>
                            <div class="mt-auto h-4 bg-gray-100 dark:bg-white/5 rounded w-full"></div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        {{-- Data List --}}
        <div wire:loading.remove wire:target="search">
            @if($restaurants->isEmpty())
                <div class="glass-panel rounded-[2rem] p-12 sm:p-20 border border-gray-200 dark:border-white/5 flex flex-col items-center justify-center text-center mt-2">
                    <div class="w-24 h-24 bg-gray-100 dark:bg-white/5 rounded-full flex items-center justify-center mb-6 border border-gray-200 dark:border-white/5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-3 tracking-tight">Tidak Ada Restoran Ditemukan</h3>
                    <p class="text-base text-gray-500 dark:text-gray-400 max-w-sm leading-relaxed">Pencarian Anda tidak membuahkan hasil. Coba gunakan istilah atau kota yang berbeda.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 sm:gap-8">
                    @foreach($restaurants as $resto)
                        <a href="{{ route('frontend.restaurants.show', $resto->slug) }}" class="group glass-panel rounded-[2rem] p-3 sm:p-4 border border-gray-200 dark:border-white/5 hover:border-primary-500/30 transition-all duration-500 flex flex-col transform hover:-translate-y-1.5 focus:outline-none focus:ring-2 focus:ring-primary-500 hover:shadow-xl dark:hover:shadow-[0_0_30px_rgba(99,102,241,0.15)] relative overflow-hidden bg-white/60 dark:bg-[#111827]/60">
                            <!-- Glow effect behind -->
                            <div class="absolute inset-0 bg-gradient-to-br from-primary-500/0 via-purple-500/0 to-amber-500/0 group-hover:from-primary-500/5 group-hover:via-purple-500/5 group-hover:to-amber-500/5 transition-all duration-500 rounded-[2rem]"></div>
                            
                            {{-- Image Cover & Logo Area --}}
                            <div class="relative w-full h-44 sm:h-48 mb-8 shrink-0">
                                
                                {{-- Cover Image Container (with overflow hidden) --}}
                                <div class="absolute inset-0 rounded-[1.5rem] overflow-hidden bg-gray-100 dark:bg-[#0A0E17] border border-gray-200 dark:border-white/5">
                                    @if($resto->cover_image)
                                        <img src="{{ Storage::url($resto->cover_image) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-out opacity-90 dark:opacity-80 group-hover:opacity-100" alt="{{ $resto->name }}">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 dark:from-[#111827] via-transparent to-transparent opacity-90"></div>
                                    @elseif($resto->logo)
                                        <img src="{{ Storage::url($resto->logo) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-out blur-md scale-125 opacity-20" alt="{{ $resto->name }}">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 dark:from-[#111827] via-black/40 to-transparent"></div>
                                    @else
                                        <div class="absolute inset-0 bg-gradient-to-br from-gray-50 to-white dark:from-[#0B0F19] dark:to-[#111827] group-hover:from-primary-50 dark:group-hover:from-indigo-900/40 group-hover:to-purple-50 dark:group-hover:to-purple-900/40 transition-colors duration-500">
                                            <!-- Decorative pattern -->
                                            <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(black 1px, transparent 1px); background-size: 16px 16px;"></div>
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-200 dark:text-white/5 group-hover:text-primary-400 dark:group-hover:text-indigo-400/30 transition-colors duration-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                {{-- Badges overlay --}}
                                <div class="absolute top-4 right-4 flex flex-col gap-2 items-end z-10">
                                    @if($resto->is_online_order_enabled)
                                        <span class="bg-white/80 dark:bg-black/40 backdrop-blur-md border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white px-3 py-1.5 rounded-full text-[10px] font-black tracking-widest uppercase shadow-lg flex items-center gap-1.5 transform group-hover:scale-105 transition-transform">
                                            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse shadow-[0_0_8px_rgba(34,197,94,0.6)]"></span>
                                            Takeaway Buka
                                        </span>
                                    @endif
                                </div>

                                {{-- Overlapping Logo --}}
                                <div class="absolute -bottom-6 left-4 sm:left-6 z-20">
                                    <div class="w-16 h-16 rounded-2xl bg-white dark:bg-[#0B0F19] p-1 shadow-xl dark:shadow-[0_8px_30px_rgba(0,0,0,0.5)] border border-gray-100 dark:border-white/10 transform group-hover:-translate-y-1 transition-transform duration-500 relative">
                                        @if($resto->logo_square)
                                            <img src="{{ Storage::url($resto->logo_square) }}" class="w-full h-full rounded-xl object-cover" alt="{{ $resto->name }}">
                                        @else
                                            <div class="w-full h-full rounded-xl bg-gradient-to-br from-primary-500/10 to-purple-500/10 dark:from-indigo-500/20 dark:to-purple-500/20 flex items-center justify-center text-xl font-black text-primary-600 dark:text-indigo-400">
                                                {{ substr($resto->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Info Content --}}
                            <div class="px-2 pb-2 sm:px-4 sm:pb-3 flex-grow flex flex-col text-left mt-2 relative z-10">
                                <h3 class="text-[19px] font-black text-gray-900 dark:text-white leading-tight mb-1.5 group-hover:text-primary-600 dark:group-hover:text-amber-400 transition-colors uppercase tracking-tight">{{ $resto->name }}</h3>
                                
                                <p class="text-[13px] font-bold text-gray-400 dark:text-gray-500 flex items-center gap-1.5 mb-3 uppercase tracking-wider">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-primary-500 dark:text-indigo-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $resto->city ?? 'Lokasi belum diset' }}
                                </p>
                                
                                <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 leading-relaxed flex-grow font-medium">
                                    {{ $resto->description ?: 'Buka direktori pemesanan digital dari '. $resto->name .', rasakan kemudahan bertransaksi dengan menu interaktif.' }}
                                </p>

                                {{-- Bottom Action Line --}}
                                <div class="mt-6 flex items-center justify-between border-t border-gray-100 dark:border-white/5 pt-4 group-hover:border-primary-500/20 dark:group-hover:border-indigo-500/20 transition-colors">
                                   <div class="flex items-center gap-1.5 text-xs font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest group-hover:text-primary-600 dark:group-hover:text-amber-400 transition-colors">
                                       Kunjungi Profil
                                   </div>
                                   <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/5 flex items-center justify-center text-gray-400 group-hover:bg-primary-600 dark:group-hover:bg-amber-400 text-gray-400 group-hover:text-white dark:group-hover:text-black group-hover:border-primary-600 dark:group-hover:border-amber-400 transition-all transform group-hover:scale-110 shadow-sm dark:shadow-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                        </svg>
                                   </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif

            {{-- Pagination --}}
            @if($restaurants->hasPages())
                <div class="mt-12 flex justify-center preview-dark-pagination">
                    {{ $restaurants->links() }}
                </div>
            @endif
        </div>

        {{-- Onboarding CTA Banner --}}
        <div class="mt-20">
            @livewire('onboarding-cta')
        </div>
    </div>

    <!-- Landing Footer -->
    <x-footer-premium />
</div>

