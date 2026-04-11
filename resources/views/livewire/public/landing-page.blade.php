@php $settings = $settings ?? app(\App\Settings\GeneralSettings::class); @endphp
<div class="bg-white dark:bg-[#0B0F19] text-gray-600 dark:text-gray-300 min-h-screen font-sans selection:bg-indigo-500 selection:text-white overflow-x-hidden transition-colors duration-300" x-data="{ mobileMenu: false }">
    {{-- Skip to Content for Keyboard Users --}}
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-[100] focus:px-6 focus:py-3 focus:bg-indigo-600 focus:text-white focus:rounded-full focus:font-bold focus:shadow-2xl">
        Skip to Content
    </a>
    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .glass-panel {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.05);
        }
        .dark .glass-panel {
            background: rgba(17, 24, 39, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.5);
        }
        .text-gradient {
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-image: linear-gradient(90deg, #6366f1, #a855f7);
        }
        .dark .text-gradient {
            background-image: linear-gradient(90deg, #818cf8, #c084fc);
        }
        .text-gradient-gold {
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-image: linear-gradient(90deg, #D97706, #EA580C);
        }
        .dark .text-gradient-gold {
            background-image: linear-gradient(90deg, #F59E0B, #F15A25);
        }
        [x-cloak] { display: none !important; }
    </style>

    <!-- 1. Navbar -->
    <nav class="fixed top-0 w-full z-50 glass-panel border-b-0 border-gray-200 dark:border-white/10 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex-shrink-0 flex items-center gap-3 cursor-pointer" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
                    @if($settings->site_logo)
                        <img src="{{ Storage::url($settings->site_logo) }}" alt="{{ $settings->site_name }}" class="h-10 w-auto object-contain">
                    @else
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                    @endif
                    <span class="font-bold text-lg sm:text-2xl tracking-tight text-gradient-gold">{{ $settings->site_name }}</span>
                </div>
                <div class="hidden md:flex space-x-8 items-center">
                    <a href="#fitur" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-white transition">Fitur</a>
                    <a href="#solusi" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-white transition">Solusi</a>
                    <a href="#komparasi" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-white transition">Komparasi</a>
                    <a href="#harga" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-white transition">Harga</a>
                    <a href="{{ route('consultation') }}" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-white transition">Konsultasi</a>
                    <div class="w-px h-5 bg-gray-200 dark:bg-gray-700"></div>
                    
                    {{-- Theme Toggle --}}
                    <button @click="theme = theme === 'light' ? 'dark' : 'light'; updateTheme()" 
                            :aria-label="theme === 'light' ? 'Ganti ke mode gelap' : 'Ganti ke mode terang'"
                            class="p-2 rounded-xl bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-500 dark:text-gray-400 hover:bg-white dark:hover:bg-white/10 transition-all focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                        <svg x-show="theme === 'light'" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                        <svg x-show="theme === 'dark' || theme === 'system'" x-cloak xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                        </svg>
                    </button>

                    <a href="{{ route('filament.restaurant.auth.login') }}" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-white transition">Login Restoran</a>
                    <a href="{{ route('frontend.restaurants.index') }}" class="px-5 py-2.5 rounded-full bg-primary-50 dark:bg-white/10 hover:bg-primary-100 dark:hover:bg-white/20 border border-primary-100 dark:border-white/10 text-primary-700 dark:text-white text-sm font-bold transition shadow-sm backdrop-blur-sm">
                        Cari Resto
                    </a>
                </div>

                {{-- Mobile Menu Button & Theme Toggle --}}
                <div class="flex items-center gap-2 md:hidden">
                    <button @click="theme = theme === 'light' ? 'dark' : 'light'; updateTheme()" 
                            class="p-2 rounded-xl bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-500 dark:text-gray-400">
                        <svg x-show="theme === 'light'" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                        <svg x-show="theme === 'dark' || theme === 'system'" x-cloak xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                        </svg>
                    </button>
                    <button @click="mobileMenu = !mobileMenu" class="p-2 rounded-xl bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-300">
                        <svg x-show="!mobileMenu" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
                        <svg x-show="mobileMenu" x-cloak class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile Menu Panel --}}
        <div x-show="mobileMenu" x-cloak x-collapse class="md:hidden border-t border-gray-200 dark:border-white/10 bg-white/95 dark:bg-[#0B0F19]/95 backdrop-blur-md">
            <div class="px-4 py-6 space-y-4">
                <a @click="mobileMenu = false" href="#fitur" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Fitur</a>
                <a @click="mobileMenu = false" href="#solusi" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Solusi</a>
                <a @click="mobileMenu = false" href="#komparasi" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Komparasi</a>
                <a @click="mobileMenu = false" href="#harga" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Harga</a>
                <a href="{{ route('consultation') }}" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Konsultasi</a>
                <div class="h-px bg-gray-200 dark:bg-gray-800"></div>
                <a href="{{ route('filament.restaurant.auth.login') }}" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Login Restoran</a>
                <a href="{{ route('frontend.restaurants.index') }}" class="block w-full py-3 text-center rounded-xl bg-primary-600 text-white font-bold text-sm shadow-lg shadow-primary-500/20">Cari Restoran</a>
            </div>
        </div>
    </nav>

    <!-- 2. Hero Section -->
    <main id="main-content" 
          class="relative pt-28 sm:pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden flex flex-col items-center md:justify-center md:min-h-[90vh]"
          x-data="{ 
            activeSlide: 0, 
            slides: {{ json_encode($settings->landing_hero_mockups) }},
            loop() {
                setInterval(() => {
                    this.activeSlide = (this.activeSlide + 1) % this.slides.length;
                }, 5000);
            }
          }" 
          x-init="loop()">
        <div class="absolute top-1/4 left-1/2 -translate-x-1/2 w-[600px] sm:w-[800px] h-[400px] sm:h-[500px] bg-primary-500/10 dark:bg-indigo-600/20 blur-[100px] sm:blur-[120px] rounded-full pointer-events-none"></div>
        <div class="absolute top-1/3 right-0 w-[300px] sm:w-[400px] h-[300px] sm:h-[400px] bg-purple-500/10 dark:bg-purple-600/20 blur-[80px] sm:blur-[100px] rounded-full pointer-events-none"></div>
        
        <div class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10 mt-4 sm:mt-0">
            <div class="inline-flex items-center gap-2 px-3 sm:px-4 py-1.5 sm:py-2 rounded-full glass-panel mb-6 sm:mb-8 border-indigo-500/30 text-indigo-600 dark:text-indigo-300 text-[10px] sm:text-sm font-bold shadow-[0_0_15px_rgba(99,102,241,0.15)] max-w-full">
                <span class="flex h-2 w-2 relative shrink-0">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
                </span>
                <span class="tracking-wide uppercase truncate">Platform SaaS Manajemen Restoran Modern</span>
            </div>
            
            <h1 class="text-[28px] sm:text-4xl md:text-5xl lg:text-7xl font-extrabold text-gray-900 dark:text-white tracking-tight mb-4 sm:mb-8 leading-[1.3] sm:leading-tight max-w-5xl mx-auto px-2 sm:px-1">
                {!! $settings->landing_hero_title !!}
            </h1>
            
            <p class="text-sm sm:text-lg md:text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto mb-8 sm:mb-12 leading-relaxed font-medium px-2 sm:px-0">
                {{ $settings->landing_hero_subtitle }}
            </p>
            
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center items-center w-full max-w-xs sm:max-w-none mx-auto px-2 sm:px-0">
                <a href="{{ $settings->landing_hero_cta_primary_link }}" class="w-full sm:w-auto px-6 sm:px-8 py-3.5 sm:py-4 rounded-full bg-primary-600 hover:bg-primary-500 text-white font-bold text-sm sm:text-lg transition shadow-xl shadow-primary-500/30 transform hover:-translate-y-1 text-center flex items-center justify-center gap-2">
                    {{ $settings->landing_hero_cta_primary_text }}
                </a>
                <a href="{{ $settings->landing_hero_cta_secondary_link }}" class="w-full sm:w-auto px-6 sm:px-8 py-3.5 sm:py-4 rounded-full bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-white font-bold text-sm sm:text-lg transition shadow-lg transform hover:-translate-y-1 text-center">
                    {{ $settings->landing_hero_cta_secondary_text }}
                </a>
            </div>

            <div class="mt-10 sm:mt-20 glass-panel p-1.5 sm:p-2 rounded-2xl mx-auto w-full max-w-5xl shadow-2xl ring-1 ring-white/10">
                <div class="relative aspect-[4/3] sm:aspect-[16/9] w-full rounded-xl overflow-hidden bg-gray-900 border border-white/5 shadow-inner flex flex-col">
                    <div class="w-full h-8 sm:h-12 border-b border-gray-800 flex items-center px-3 sm:px-4 gap-1.5 sm:gap-2 bg-[#1F2937] shrink-0 z-20">
                        <div class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full bg-red-500/80"></div>
                        <div class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full bg-yellow-500/80"></div>
                        <div class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full bg-green-500/80"></div>
                    </div>
                    
                    <div class="relative flex-1 w-full h-full text-gray-600 font-mono text-sm bg-[#111827]">
                        @foreach($settings->landing_hero_mockups as $index => $mockup)
                            <div class="absolute inset-0 w-full h-full flex" 
                                 x-show="activeSlide === {{ $index }}"
                                 x-transition:enter="transition ease-out duration-500"
                                 x-transition:enter-start="opacity-0 transform scale-95"
                                 x-transition:enter-end="opacity-100 transform scale-100"
                                 x-transition:leave="transition ease-in duration-300"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 x-cloak>
                                
                                @if(!empty($mockup['image']))
                                    <img src="{{ Storage::url($mockup['image']) }}" class="w-full h-full object-cover object-top" alt="{{ $mockup['title'] }}">
                                @else
                                    {{-- Fallback Simulation for Empty Image --}}
                                    @if($index % 2 === 0)
                                        <div class="w-48 lg:w-64 border-r border-gray-800 bg-[#1A2235] p-6 hidden md:block shrink-0">
                                            <div class="w-full h-8 bg-gray-800 rounded mb-6"></div>
                                            <div class="space-y-4"><div class="w-3/4 h-4 bg-indigo-500/20 rounded"></div><div class="w-1/2 h-4 bg-gray-800 rounded"></div><div class="w-2/3 h-4 bg-gray-800 rounded"></div></div>
                                        </div>
                                        <div class="flex-1 p-3 sm:p-8 grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-6 overflow-hidden">
                                            <div class="col-span-1 sm:col-span-3 h-16 sm:h-32 bg-gradient-to-r from-indigo-900/40 to-purple-900/40 rounded-xl sm:rounded-2xl border border-indigo-500/20"></div>
                                            <div class="col-span-1 sm:col-span-2 h-24 sm:h-64 bg-gray-800/50 rounded-xl sm:rounded-2xl"></div>
                                            <div class="col-span-1 h-24 sm:h-64 bg-gray-800/50 rounded-xl sm:rounded-2xl hidden sm:block"></div>
                                        </div>
                                    @else
                                        <div class="flex-1 p-3 sm:p-8 grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-4 bg-[#0F172A] overflow-hidden w-full">
                                            <div class="col-span-2 sm:col-span-4 h-12 sm:h-16 bg-gray-800/30 rounded-lg sm:rounded-xl mb-2 sm:mb-4"></div>
                                            <div class="h-20 sm:h-40 bg-gray-800/50 rounded-lg sm:rounded-xl"></div>
                                            <div class="h-20 sm:h-40 bg-gray-800/50 rounded-lg sm:rounded-xl"></div>
                                            <div class="h-20 sm:h-40 bg-gray-800/50 rounded-lg sm:rounded-xl hidden sm:block"></div>
                                            <div class="h-20 sm:h-40 bg-gray-800/50 rounded-lg sm:rounded-xl hidden sm:block"></div>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <div class="mt-8 sm:mt-12 flex justify-start sm:justify-center items-center gap-6 sm:gap-12 transition duration-500 overflow-x-auto pb-6 pt-2 no-scrollbar w-full px-2">
                @foreach($settings->landing_hero_mockups as $index => $mockup)
                    <button 
                        @click="activeSlide = {{ $index }}"
                        class="relative py-2 px-1 text-center font-bold tracking-widest whitespace-nowrap text-[10px] sm:text-sm shrink-0 transition-all duration-300 group outline-none"
                        :class="activeSlide === {{ $index }} ? 'text-primary-600 dark:text-indigo-400 opacity-100 scale-110' : 'text-gray-400 dark:text-white/40 opacity-40 grayscale hover:opacity-100 hover:grayscale-0'">
                        
                        <span class="relative z-10">{{ $mockup['title'] }}</span>
                        
                        {{-- Active Indicator Glow Line --}}
                        <div x-show="activeSlide === {{ $index }}" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="scale-x-0 opacity-0"
                             x-transition:enter-end="scale-x-100 opacity-100"
                             class="absolute -bottom-1 left-0 w-full h-0.5 bg-gradient-to-r from-primary-600 to-indigo-600 dark:from-indigo-500 dark:to-purple-500 rounded-full shadow-[0_0_8px_rgba(99,102,241,0.6)]"></div>
                    </button>
                @endforeach
            </div>
        </div>
    </main>

    <!-- 3. Problem vs Solution -->
    <div id="solusi" class="py-24 bg-gray-50 dark:bg-[#0a0e17] relative transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-sm font-bold text-primary-500 dark:text-indigo-400 tracking-widest uppercase mb-2">Mengapa {{ $settings->site_name }}?</h2>
                <h3 class="text-3xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4">Ubah Masalah Menjadi <span class="text-gradient">Efisiensi</span></h3>
                <p class="text-gray-500 dark:text-gray-400 max-w-2xl mx-auto">Masalah klasik restoran manual bikin pelanggan lari dan profit bocor. Ini cara {{ $settings->site_name }} menyelesaikannya.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Point 1: Wait Time -->
                <div class="glass-panel p-8 rounded-3xl hover:-translate-y-2 transition duration-500 border border-white/20 dark:border-white/5 shadow-xl hover:shadow-primary-500/10">
                    <div class="mb-5">
                        <span class="text-[10px] uppercase font-black tracking-widest text-red-600 dark:text-red-400 bg-red-100/50 dark:bg-red-400/10 px-3 py-1.5 rounded-lg border border-red-200/50">Cara Lama</span>
                        <h4 class="text-xl font-bold text-gray-800 dark:text-gray-200 mt-4 mb-2">Antrean & Tunggu Lama</h4>
                        <p class="text-sm text-gray-500 leading-relaxed">Pelanggan lelah melambaikan tangan, menunggu menu fisik, antre di kasir, membuang waktu 15 menit cuma untuk pesan.</p>
                    </div>
                    <div class="w-full h-px bg-gradient-to-r from-transparent via-gray-200 dark:via-gray-800 to-transparent my-6"></div>
                    <div>
                        <span class="text-[10px] uppercase font-black tracking-widest text-emerald-600 dark:text-emerald-400 bg-emerald-100/50 dark:bg-emerald-400/10 px-3 py-1.5 rounded-lg border border-emerald-200/50">Solusi {{ $settings->site_name }}</span>
                        <h4 class="text-xl font-bold text-gray-900 dark:text-white mt-4 mb-2">QR Order & Pay</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">Scan, Pesan, Bayar langsung dari meja. Pesanan masuk dapur dalam hitungan detik. Tanpa tunggu, tanpa ribet.</p>
                    </div>
                </div>

                <!-- Point 2: Kitchen -->
                <div class="glass-panel p-8 rounded-3xl hover:-translate-y-2 transition duration-500 border border-white/20 dark:border-white/5 shadow-xl hover:shadow-indigo-500/10">
                    <div class="mb-5">
                        <span class="text-[10px] uppercase font-black tracking-widest text-red-600 dark:text-red-400 bg-red-100/50 dark:bg-red-400/10 px-3 py-1.5 rounded-lg border border-red-200/50">Cara Lama</span>
                        <h4 class="text-xl font-bold text-gray-800 dark:text-gray-200 mt-4 mb-2">Order Hilang & Salah</h4>
                        <p class="text-sm text-gray-500 leading-relaxed">Tulisan tak terbaca, kertas hilang di dapur, pesanan salah buat. Profit terbuang sia-sia karena bahan baku mubazir.</p>
                    </div>
                    <div class="w-full h-px bg-gradient-to-r from-transparent via-gray-200 dark:via-gray-800 to-transparent my-6"></div>
                    <div>
                        <span class="text-[10px] uppercase font-black tracking-widest text-indigo-600 dark:text-indigo-400 bg-indigo-100/50 dark:bg-indigo-400/10 px-3 py-1.5 rounded-lg border border-indigo-200/50">Solusi {{ $settings->site_name }}</span>
                        <h4 class="text-xl font-bold text-gray-900 dark:text-white mt-4 mb-2">Kitchen Display</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">Layar digital cerdas di dapur. Pantau durasi masak (KPI), modifikasi menu, dan status pesanan secara real-time.</p>
                    </div>
                </div>

                <!-- Point 3: Finance -->
                <div class="glass-panel p-8 rounded-3xl hover:-translate-y-2 transition duration-500 border border-white/20 dark:border-white/5 shadow-xl hover:shadow-amber-500/10">
                    <div class="mb-5">
                        <span class="text-[10px] uppercase font-black tracking-widest text-red-600 dark:text-red-400 bg-red-100/50 dark:bg-red-400/10 px-3 py-1.5 rounded-lg border border-red-200/50">Cara Lama</span>
                        <h4 class="text-xl font-bold text-gray-800 dark:text-gray-200 mt-4 mb-2">Kebocoran Finance</h4>
                        <p class="text-sm text-gray-500 leading-relaxed">Uang laci hilang, fee QRIS tak terlacak, rekap data berjam-jam setiap malam secara manual. Lelah & tidak akurat.</p>
                    </div>
                    <div class="w-full h-px bg-gradient-to-r from-transparent via-gray-200 dark:via-gray-800 to-transparent my-6"></div>
                    <div>
                        <span class="text-[10px] uppercase font-black tracking-widest text-amber-600 dark:text-amber-400 bg-amber-100/50 dark:bg-amber-400/10 px-3 py-1.5 rounded-lg border border-amber-200/50">Solusi {{ $settings->site_name }}</span>
                        <h4 class="text-xl font-bold text-gray-900 dark:text-white mt-4 mb-2">Auto-Ledger & POS</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">Ledger otomatis memisahkan Net & Fee secara instan. Lacak mutasi kasir dan rekap harian dalam sekali klik.</p>
                    </div>
                </div>

                <!-- Point 4: Inventory -->
                <div class="glass-panel p-8 rounded-3xl hover:-translate-y-2 transition duration-500 border border-white/20 dark:border-white/5 shadow-xl hover:shadow-purple-500/10">
                    <div class="mb-5">
                        <span class="text-[10px] uppercase font-black tracking-widest text-red-600 dark:text-red-400 bg-red-100/50 dark:bg-red-400/10 px-3 py-1.5 rounded-lg border border-red-200/50">Cara Lama</span>
                        <h4 class="text-xl font-bold text-gray-800 dark:text-gray-200 mt-4 mb-2">Stok Bocor & Raib</h4>
                        <p class="text-sm text-gray-500 leading-relaxed">Bahan baku habis tiba-tiba saat ramai, opname manual yang rawan manipulasi, dan pemborosan tanpa jejak.</p>
                    </div>
                    <div class="w-full h-px bg-gradient-to-r from-transparent via-gray-200 dark:via-gray-800 to-transparent my-6"></div>
                    <div>
                        <span class="text-[10px] uppercase font-black tracking-widest text-purple-600 dark:text-purple-400 bg-purple-100/50 dark:bg-purple-400/10 px-3 py-1.5 rounded-lg border border-purple-200/50">Solusi {{ $settings->site_name }}</span>
                        <h4 class="text-xl font-bold text-gray-900 dark:text-white mt-4 mb-2">Smart Stock Guard</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">Setiap menu terjual otomatis memotong stok hingga gramasi terkecil. Berhenti rugi karena manajemen stok buruk.</p>
                    </div>
                </div>

                <!-- Point 5: Marketing & Loyalty -->
                <div class="glass-panel p-8 rounded-3xl hover:-translate-y-2 transition duration-500 border border-white/20 dark:border-white/5 shadow-xl hover:shadow-pink-500/10">
                    <div class="mb-5">
                        <span class="text-[10px] uppercase font-black tracking-widest text-red-600 dark:text-red-400 bg-red-100/50 dark:bg-red-400/10 px-3 py-1.5 rounded-lg border border-red-200/50">Cara Lama</span>
                        <h4 class="text-xl font-bold text-gray-800 dark:text-gray-200 mt-4 mb-2">Customer Datang & Pergi</h4>
                        <p class="text-sm text-gray-500 leading-relaxed">Tidak tahu siapa pelanggan setia, tidak punya database nomor WhatsApp, promosi hanya lewat brosur fisik yang dibuang.</p>
                    </div>
                    <div class="w-full h-px bg-gradient-to-r from-transparent via-gray-200 dark:via-gray-800 to-transparent my-6"></div>
                    <div>
                        <span class="text-[10px] uppercase font-black tracking-widest text-pink-600 dark:text-pink-400 bg-pink-100/50 dark:bg-pink-400/10 px-3 py-1.5 rounded-lg border border-pink-200/50">Solusi {{ $settings->site_name }}</span>
                        <h4 class="text-xl font-bold text-gray-900 dark:text-white mt-4 mb-2">WhatsApp CRM & Loyalty</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">Kumpulkan database pelanggan & poin reward otomatis. Kirim promo personal langsung ke WhatsApp untuk datangkan kembali pembeli.</p>
                    </div>
                </div>

                <!-- Point 6: Multi-Outlet HQ -->
                <div class="glass-panel p-8 rounded-3xl hover:-translate-y-2 transition duration-500 border border-white/20 dark:border-white/5 shadow-xl hover:shadow-emerald-500/10">
                    <div class="mb-5">
                        <span class="text-[10px] uppercase font-black tracking-widest text-red-600 dark:text-red-400 bg-red-100/50 dark:bg-red-400/10 px-3 py-1.5 rounded-lg border border-red-200/50">Cara Lama</span>
                        <h4 class="text-xl font-bold text-gray-800 dark:text-gray-200 mt-4 mb-2">Sulit Pantau Cabang</h4>
                        <p class="text-sm text-gray-500 leading-relaxed">Harus datang ke resto untuk cek laporan, owner pusing kalau punya banyak cabang karena data terpisah-pisah dan tidak transparan.</p>
                    </div>
                    <div class="w-full h-px bg-gradient-to-r from-transparent via-gray-200 dark:via-gray-800 to-transparent my-6"></div>
                    <div>
                        <span class="text-[10px] uppercase font-black tracking-widest text-emerald-600 dark:text-emerald-400 bg-emerald-100/50 dark:bg-emerald-400/10 px-3 py-1.5 rounded-lg border border-emerald-200/50">Solusi {{ $settings->site_name }}</span>
                        <h4 class="text-xl font-bold text-gray-900 dark:text-white mt-4 mb-2">Multi-Outlet Dashboard</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">Pantau omzet semua cabang dari HP secara real-time. Ambil keputusan bisnis berdasarkan data akurat, bukan lagi berdasarkan perasaan.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Key Metrics / Trust Stats -->
    <div class="py-20 bg-white dark:bg-[#0a0e17] transition-colors duration-300 relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-gray-200 dark:via-gray-800 to-transparent"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h3 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">Terbukti Meningkatkan <span class="text-gradient">Performa Bisnis</span></h3>
                <p class="text-gray-500 dark:text-gray-400 max-w-2xl mx-auto">{{ $settings->site_name }} bukan sekadar alat, tapi partner strategis untuk pertumbuhan restoran Anda.</p>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Stat 1 -->
                <div class="text-center p-6 rounded-2xl bg-gray-50/50 dark:bg-white/5 border border-gray-100 dark:border-white/5 transition duration-500 hover:border-primary-500/30">
                    <div class="text-4xl md:text-5xl font-black text-gradient mb-2 tracking-tight">+40%</div>
                    <div class="text-sm font-bold text-gray-800 dark:text-gray-200 mb-1">Efisiensi Pelayanan</div>
                    <p class="text-[10px] text-gray-500 leading-relaxed uppercase tracking-widest font-medium">Berdasarkan Kecepatan QR Order</p>
                </div>

                <!-- Stat 2 -->
                <div class="text-center p-6 rounded-2xl bg-gray-50/50 dark:bg-white/5 border border-gray-100 dark:border-white/5 transition duration-500 hover:border-primary-500/30">
                    <div class="text-4xl md:text-5xl font-black text-gradient-gold mb-2 tracking-tight">0%</div>
                    <div class="text-sm font-bold text-gray-800 dark:text-gray-200 mb-1">Order Error</div>
                    <p class="text-[10px] text-gray-500 leading-relaxed uppercase tracking-widest font-medium">Sinkronisasi Digital ke Dapur</p>
                </div>

                <!-- Stat 3 -->
                <div class="text-center p-6 rounded-2xl bg-gray-50/50 dark:bg-white/5 border border-gray-100 dark:border-white/5 transition duration-500 hover:border-primary-500/30">
                    <div class="text-4xl md:text-5xl font-black text-gradient mb-2 tracking-tight">100%</div>
                    <div class="text-sm font-bold text-gray-800 dark:text-gray-200 mb-1">Transparansi Keuangan</div>
                    <p class="text-[10px] text-gray-500 leading-relaxed uppercase tracking-widest font-medium">Laporan Ledger Real-time</p>
                </div>

                <!-- Stat 4 -->
                <div class="text-center p-6 rounded-2xl bg-gray-50/50 dark:bg-white/5 border border-gray-100 dark:border-white/5 transition duration-500 hover:border-primary-500/30">
                    <div class="text-4xl md:text-5xl font-black text-gradient-gold mb-2 tracking-tight">+25%</div>
                    <div class="text-sm font-bold text-gray-800 dark:text-gray-200 mb-1">Kenaikan Omzet</div>
                    <p class="text-[10px] text-gray-500 leading-relaxed uppercase tracking-widest font-medium">Via Smart Upsell & Loyalty</p>
                </div>
            </div>

            <div class="mt-16 text-center">
                <div class="inline-flex items-center gap-2 px-6 py-2 rounded-full border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900/50 text-sm font-medium text-gray-600 dark:text-gray-400">
                    <span class="flex h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Dipercaya oleh lebih dari <span class="font-bold text-gray-900 dark:text-white">500+ Restoran</span> di seluruh Indonesia
                </div>
            </div>
        </div>
    </div>


    <!-- 5. ROI Calculator -->
    <div class="py-24 bg-gray-50 dark:bg-[#0a0e17] transition-colors duration-300 relative overflow-hidden">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="glass-panel p-8 md:p-12 rounded-[2.5rem] shadow-2xl border border-white dark:border-white/5"
                 x-data="{ 
                    dailyOrders: 50, 
                    avgTicket: 50000,
                    get monthlyEfficiency() { return Math.round(this.dailyOrders * 30 * this.avgTicket * 0.15) },
                    get hoursSaved() { return Math.round((this.dailyOrders * 30 * 10) / 60) },
                    formatCurrency(val) {
                        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val);
                    }
                 }">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Hitung Potensi <span class="text-gradient">Keuntungan Anda</span></h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-8 leading-relaxed">Geser slider di samping untuk melihat seberapa besar {{ $settings->site_name }} membantu pertumbuhan bisnis Anda setiap bulannya.</p>
                        
                        <div class="space-y-8">
                            <!-- Input 1 -->
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <label class="text-sm font-bold text-gray-700 dark:text-gray-300">Jumlah Pesanan / Hari</label>
                                    <span class="px-3 py-1 bg-primary-500/10 text-primary-600 rounded-lg font-black text-lg" x-text="dailyOrders"></span>
                                </div>
                                <input type="range" min="10" max="500" step="5" x-model="dailyOrders" 
                                       class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-lg appearance-none cursor-pointer accent-primary-600">
                            </div>

                            <!-- Input 2 -->
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <label class="text-sm font-bold text-gray-700 dark:text-gray-300">Rata-rata Harga / Pesanan</label>
                                    <span class="px-3 py-1 bg-primary-500/10 text-primary-600 rounded-lg font-black text-lg" x-text="formatCurrency(avgTicket)"></span>
                                </div>
                                <input type="range" min="10000" max="500000" step="5000" x-model="avgTicket" 
                                       class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-lg appearance-none cursor-pointer accent-primary-600">
                            </div>
                        </div>
                    </div>

                    <div class="bg-primary-600 dark:bg-indigo-600 rounded-[2rem] p-8 text-white relative overflow-hidden shadow-2xl shadow-primary-500/20">
                        <div class="absolute top-0 right-0 -translate-y-12 translate-x-12 w-48 h-48 bg-white/10 blur-[60px] rounded-full"></div>
                        
                        <div class="relative z-10 space-y-8">
                            <div>
                                <p class="text-primary-100 text-xs uppercase tracking-widest font-black mb-2">Estimasi Tambahan Profit / Bulan</p>
                                <div class="text-3xl md:text-4xl font-black mb-1" x-text="formatCurrency(monthlyEfficiency)"></div>
                                <p class="text-primary-200 text-[10px] leading-relaxed italic">*Hasil dari peningkatan efisiensi & upselling otomatis.</p>
                            </div>

                            <div class="w-full h-px bg-white/20"></div>

                            <div>
                                <p class="text-primary-100 text-xs uppercase tracking-widest font-black mb-2">Waktu Yang Dihemat / Bulan</p>
                                <div class="text-3xl md:text-4xl font-black mb-1"><span x-text="hoursSaved"></span> Jam</div>
                                <p class="text-primary-200 text-[10px] leading-relaxed italic">*Asumsi hemat 10 menit per pesanan lewat QR Order & KDS.</p>
                            </div>

                            <div class="pt-4">
                                <a href="#harga" class="block w-full text-center bg-white text-primary-600 hover:bg-primary-50 text-sm font-black py-4 rounded-2xl shadow-xl transition transform hover:scale-105 active:scale-95">
                                    🚀 Ambil Keuntungan Ini Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 6. Social Proof: Partner Logos & Testimonials -->
    <div class="py-24 bg-white dark:bg-[#0b0f19] transition-colors duration-300 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            
            <!-- Logos -->
            @if(count($settings->landing_partner_logos) > 0)
                <div class="mb-24">
                    <p class="text-center text-xs uppercase font-black tracking-widest text-gray-400 mb-10">Dipercaya Oleh Berbagai Bisnis Kuliner Unggulan</p>
                    <div class="flex flex-wrap justify-center items-center gap-8 md:gap-16 opacity-50 grayscale hover:grayscale-0 transition-all duration-700">
                        @foreach($settings->landing_partner_logos as $partner)
                            <div class="flex flex-col items-center group">
                                @if($partner['image'])
                                    <img src="{{ Storage::url($partner['image']) }}" alt="{{ $partner['name'] }}" class="h-8 md:h-12 w-auto object-contain transition group-hover:scale-110">
                                @else
                                    <span class="text-lg md:text-2xl font-black text-gray-400 dark:text-gray-600 tracking-tighter group-hover:text-primary-500 transition">{{ $partner['name'] }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Testimonials -->
            <div class="text-center mb-16">
                <h3 class="text-3xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4">Apa Kata <span class="text-gradient">Mereka?</span></h3>
                <p class="text-gray-500 dark:text-gray-400 max-w-2xl mx-auto">Kepuasan pelanggan adalah prioritas kami. Inilah pengalaman nyata para pebisnis kuliner menggunakan {{ $settings->site_name }}.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-16">
                @foreach($settings->landing_testimonials as $testimonial)
                    <div class="glass-panel p-8 rounded-[2rem] relative border border-gray-100 dark:border-white/5 shadow-xl">
                        <div class="flex gap-4 items-center mb-6">
                            <div class="w-14 h-14 rounded-full bg-gray-200 dark:bg-gray-800 overflow-hidden shrink-0 border-2 border-primary-500/20">
                                @if($testimonial['avatar'])
                                    <img src="{{ Storage::url($testimonial['avatar']) }}" alt="{{ $testimonial['name'] }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-primary-500 font-bold text-xl uppercase">
                                        {{ substr($testimonial['name'], 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white leading-tight">{{ $testimonial['name'] }}</h4>
                                <p class="text-xs text-gray-500">{{ $testimonial['role'] }}</p>
                                <div class="flex gap-0.5 mt-1">
                                    @for($i = 0; $i < ($testimonial['rating'] ?? 5); $i++)
                                        <svg class="w-3 h-3 text-amber-500 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @endfor
                                </div>
                            </div>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 italic leading-relaxed">"{{ $testimonial['quote'] }}"</p>
                        
                        {{-- Quote Icon Decor --}}
                        <div class="absolute top-8 right-8 text-primary-500/10">
                            <svg class="w-12 h-12 fill-current" viewBox="0 0 32 32"><path d="M10 8v8H6v1h4v4H6a4 4 0 0 1-4-4V8a4 4 0 0 1 4-4h4zm16 0v8h-4v1h4v4h-4a4 4 0 0 1-4-4V8a4 4 0 0 1 4-4h4z"/></svg>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- CTA to /restaurants -->
            <div class="text-center">
                <a href="/restaurants" class="inline-flex items-center gap-3 px-8 py-4 rounded-2xl bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-bold hover:scale-105 active:scale-95 transition group">
                    <span>Lihat Daftar Restoran Berlangganan</span>
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <!-- 7. Fitur Utama -->
    <div id="fitur" class="py-24 relative overflow-hidden transition-colors duration-300">
        <div class="absolute top-1/2 left-0 w-full h-[600px] bg-primary-500/5 dark:bg-blue-600/10 blur-[150px] -skew-y-12"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4">Fitur Kelas <span class="bg-gradient-to-r from-primary-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-500 text-transparent bg-clip-text">Enterprise.</span> Harga UKM.</h2>
                <p class="text-gray-500 dark:text-gray-400 max-w-2xl mx-auto">Kami mendemokratisasi teknologi restoran mahal agar bisa dijangkau oleh semua pebisnis kuliner.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Fitur 1 -->
                <div class="glass-panel p-6 rounded-2xl group cursor-pointer hover:bg-white dark:hover:bg-white/5 transition-all">
                    <div class="w-12 h-12 bg-blue-500/10 dark:bg-blue-500/20 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform p-3">
                        @php $svg = public_path('vendor/uicons-regular-rounded/svg/fi-rr-credit-card.svg'); @endphp
                        <div class="w-full h-full fill-current text-blue-600 dark:text-blue-400">
                            {!! str_replace('<svg ', '<svg class="w-full h-full" ', file_get_contents($svg)) !!}
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-2">POS Internal Canggih</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Sistem kasir ringkas dengan Split Bill (by Amount & by Item), Multi-Payment, dan sinkronisasi real-time laci uang (ESC/POS).</p>
                </div>

                <!-- Fitur 2 -->
                <div class="glass-panel p-6 rounded-2xl group cursor-pointer hover:bg-white dark:hover:bg-white/5 transition-all">
                    <div class="w-12 h-12 bg-pink-500/10 dark:bg-pink-500/20 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform p-3">
                        @php $svg = public_path('vendor/uicons-regular-rounded/svg/fi-rr-smartphone.svg'); @endphp
                        <div class="w-full h-full fill-current text-pink-600 dark:text-pink-400">
                            {!! str_replace('<svg ', '<svg class="w-full h-full" ', file_get_contents($svg)) !!}
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-2">Self-Service Kiosk</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Ubah tablet jadi mesin kiosk berlayar penuh dengan animasi Glassmorphic. Pelanggan bisa memesan dan bayar sendiri layaknya restoran cepat saji global.</p>
                </div>

                <!-- Fitur 3 -->
                <div class="glass-panel p-6 rounded-2xl group cursor-pointer hover:bg-white dark:hover:bg-white/5 transition-all">
                    <div class="w-12 h-12 bg-purple-500/10 dark:bg-purple-500/20 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform p-3">
                        @php $svg = public_path('vendor/uicons-regular-rounded/svg/fi-rr-shopping-cart.svg'); @endphp
                        <div class="w-full h-full fill-current text-purple-600 dark:text-purple-400">
                            {!! str_replace('<svg ', '<svg class="w-full h-full" ', file_get_contents($svg)) !!}
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-2">Smart Upselling</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Algoritma rekomendasi "Pasangan Terbaik" di Keranjang. Fitur bundling interaktif di dalam Popup untuk otomatis tingkatkan nilai pesanan konsumen.</p>
                </div>

                <!-- Fitur 4 -->
                <div class="glass-panel p-6 rounded-2xl group cursor-pointer hover:bg-white dark:hover:bg-white/5 transition-all">
                    <div class="w-12 h-12 bg-emerald-500/10 dark:bg-emerald-500/20 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform p-3">
                        @php $svg = public_path('vendor/uicons-regular-rounded/svg/fi-rr-heart.svg'); @endphp
                        <div class="w-full h-full fill-current text-emerald-600 dark:text-emerald-400">
                            {!! str_replace('<svg ', '<svg class="w-full h-full" ', file_get_contents($svg)) !!}
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-2">Loyalty & Poin</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Kumpulkan database pelanggan (WhatsApp Marketing), berikan poin belanja, dan dorong retensi tinggi secara otomatis berbasis Tier (Bronze/Silver/Gold).</p>
                </div>
            </div>
            
            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                 <div class="glass-panel p-6 rounded-2xl flex items-center gap-4">
                     <div class="w-10 h-10 rounded-full bg-indigo-500/20 flex items-center justify-center shrink-0">
                         <svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                     </div>
                     <div>
                        <h4 class="font-bold text-gray-900 dark:text-gray-200">Manajemen Stok Resep</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Stok bahan baku otomatis berkurang saat menu dipesan.</p>
                     </div>
                 </div>
                 
                 <div class="glass-panel p-6 rounded-2xl flex items-center gap-4">
                     <div class="w-10 h-10 rounded-full bg-orange-500/20 flex items-center justify-center shrink-0">
                         <svg class="w-5 h-5 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                     </div>
                     <div>
                        <h4 class="font-bold text-gray-900 dark:text-gray-200">Happy Hour & Voucher</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Atur harga promo otomatis & voucher kode mandiri.</p>
                     </div>
                 </div>
                 
                 <div class="glass-panel p-6 rounded-2xl flex items-center gap-4">
                     <div class="w-10 h-10 rounded-full bg-teal-500/20 flex items-center justify-center shrink-0">
                         <svg class="w-5 h-5 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                     </div>
                      <div>
                        <h4 class="font-bold text-gray-900 dark:text-gray-200">WhatsApp Marketing</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Kirim otomatis resi & kampanye promo ke pelanggan.</p>
                      </div>
                 </div>

                 <div class="glass-panel p-6 rounded-2xl flex items-center gap-4 border-orange-500/20 shadow-[0_0_15px_rgba(249,115,22,0.1)]">
                     <div class="w-10 h-10 rounded-full bg-orange-500/20 flex items-center justify-center shrink-0">
                         <svg class="w-5 h-5 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                     </div>
                      <div>
                        <h4 class="font-bold text-gray-900 dark:text-gray-200">Queue & Display TV</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">Layar antrean real-time & ambil nomor di Kiosk.</p>
                      </div>
                 </div>
            </div>

            <!-- CTA Lihat Semua Fitur -->
            <div class="mt-12 text-center">
                <a href="{{ route('features') }}" class="inline-flex items-center gap-2 px-8 py-4 rounded-full border border-primary-500/40 bg-primary-500/10 dark:bg-indigo-500/10 hover:bg-primary-500/20 dark:hover:bg-indigo-500/20 text-primary-600 dark:text-indigo-300 hover:text-primary-700 dark:hover:text-white font-semibold transition-all duration-300 shadow-sm hover:shadow-primary-500/20 group">
                    <span>Lihat Semua Fitur Lengkap</span>
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>
    </div>

    <!-- 5. Komparasi -->
    <div id="komparasi" class="py-24 bg-gray-50 dark:bg-[#0a0e17] relative border-t border-b border-gray-200 dark:border-white/5 transition-colors duration-300">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Mengapa Pindah ke {{ $settings->site_name }}?</h2>
                <p class="text-gray-500 dark:text-gray-400">Bandingkan dengan aplikasi kasir konvensional atau sistem manual yang digunakan di pasaran saat ini.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead>
                        <tr>
                            <th class="p-4 font-semibold text-gray-600 dark:text-gray-400 bg-white dark:bg-[#0F172A] rounded-tl-xl border-b border-gray-200 dark:border-gray-800">Fitur & Kemampuan</th>
                            <th class="p-4 font-bold text-primary-600 dark:text-indigo-400 bg-primary-50 dark:bg-indigo-900/20 border-b border-primary-200 dark:border-indigo-500/20 text-center text-lg">{{ $settings->site_name }} (Super App)</th>
                            <th class="p-4 font-semibold text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 text-center">Aplikasi POS Biasa</th>
                            <th class="p-4 font-semibold text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-900 rounded-tr-xl border-b border-gray-200 dark:border-gray-800 text-center">Mencatat Manual</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        <tr>
                            <td class="p-4 bg-white dark:bg-[#0F172A] text-gray-700 dark:text-gray-300">Self-QR Ordering (Pelanggan Mandiri)</td>
                            <td class="p-4 bg-primary-50/30 dark:bg-indigo-900/10 text-center"><span class="text-emerald-600 dark:text-emerald-400 font-bold">Terintegrasi Bawaan</span></td>
                            <td class="p-4 bg-gray-50 dark:bg-gray-900 text-center text-gray-500">Bayar Plugin Mahal</td>
                            <td class="p-4 bg-gray-50 dark:bg-gray-900 text-center text-red-500 dark:text-red-400">Tidak Ada</td>
                        </tr>
                        <tr>
                            <td class="p-4 bg-white dark:bg-[#0F172A] text-gray-700 dark:text-gray-300">Kitchen Display System (KDS) Realtime</td>
                            <td class="p-4 bg-primary-50/30 dark:bg-indigo-900/10 text-center"><span class="text-emerald-600 dark:text-emerald-400 font-bold">Ya (WebSocket/Live)</span></td>
                            <td class="p-4 bg-gray-50 dark:bg-gray-900 text-center text-gray-500">Hanya Cetak Kertas/Thermal</td>
                            <td class="p-4 bg-gray-50 dark:bg-gray-900 text-center text-red-500 dark:text-red-400">Order Nyasar</td>
                        </tr>
                        <tr>
                            <td class="p-4 bg-white dark:bg-[#0F172A] text-gray-700 dark:text-gray-300">Sistem Loyalitas Poin Otomatis (WA Auth)</td>
                            <td class="p-4 bg-primary-50/30 dark:bg-indigo-900/10 text-center"><span class="text-emerald-600 dark:text-emerald-400 font-bold">Smart (Auto Tiering)</span></td>
                            <td class="p-4 bg-gray-50 dark:bg-gray-900 text-center text-gray-500">Pakai Kartu/Terpisah</td>
                            <td class="p-4 bg-gray-50 dark:bg-gray-900 text-center text-red-500 dark:text-red-400">Hanya Ingatan Kasir</td>
                        </tr>
                        <tr>
                            <td class="p-4 bg-white dark:bg-[#0F172A] text-gray-700 dark:text-gray-300">Smart Upselling (Bundle Recommendation)</td>
                            <td class="p-4 bg-primary-50/30 dark:bg-indigo-900/10 text-center"><span class="text-emerald-600 dark:text-emerald-400 font-bold">AI Based Relational</span></td>
                            <td class="p-4 bg-gray-50 dark:bg-gray-900 text-center text-red-500 dark:text-red-400">Tidak Ada</td>
                            <td class="p-4 bg-gray-50 dark:bg-gray-900 text-center text-red-500 dark:text-red-400">Lupa Tawarin</td>
                        </tr>
                        <tr>
                            <td class="p-4 bg-white dark:bg-[#0F172A] text-gray-700 dark:text-gray-300 rounded-bl-xl border-transparent">Penarikan Dana / Tarik Saldo Penjualan</td>
                            <td class="p-4 bg-primary-50/30 dark:bg-indigo-900/20 text-center border-transparent"><span class="text-emerald-600 dark:text-emerald-400 font-bold">Transparan (Gross/Net/Fee view)</span></td>
                            <td class="p-4 bg-gray-50 dark:bg-gray-900 text-center text-gray-500 border-transparent">Tergantung Provider Luar</td>
                            <td class="p-4 bg-gray-50 dark:bg-gray-900 rounded-br-xl text-center text-red-500 dark:text-red-400 border-transparent">Hitung Recahan Laci</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 6. Harga -->
    <div id="harga" class="py-24 relative overflow-hidden transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-5xl font-bold text-gray-900 dark:text-white mb-4">Investasi Berkelas, Berintegritas.</h2>
                <p class="text-gray-500 dark:text-gray-400 max-w-2xl mx-auto">Pilih paket yang sesuai untuk mengembangkan restoran Anda. Tidak ada komisi per pesanan tersembunyi.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($plans as $plan)
                    @php $isPopular = $plan->is_highlighted; @endphp
                    <div class="glass-panel p-8 rounded-2xl flex flex-col relative {{ $isPopular ? 'border-indigo-500/50 shadow-[0_0_30px_rgba(99,102,241,0.2)] transform lg:-translate-y-4' : '' }}">
                        @if($isPopular)
                            <div class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-gradient-to-r from-indigo-500 to-purple-500 text-white text-xs font-bold px-4 py-1 rounded-full shadow-lg">
                                PALING LARIS
                            </div>
                        @endif

                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $plan->name }}</h3>
                        @if($plan->description)
                            <p class="text-sm text-gray-600 dark:text-gray-400 italic border-l-2 border-primary-500/40 pl-3 mb-5 leading-snug">{{ $plan->description }}</p>
                        @else
                            <div class="mb-5"></div>
                        @endif
                        
                        <div class="mb-6">
                            @if($plan->price == 0)
                                <span class="text-4xl font-extrabold text-gray-900 dark:text-white">Gratis</span>
                            @else
                                <span class="text-2xl font-bold text-amber-600 dark:text-amber-400">Rp</span>
                                <span class="text-3xl font-extrabold text-amber-600 dark:text-amber-400">{{ number_format($plan->price, 0, ',', '.') }}</span>
                                <span class="text-gray-600 dark:text-gray-400 text-sm">/ {{ ($plan->billing_period ?? 'monthly') == 'monthly' ? 'bulan' : 'tahun' }}</span>
                            @endif
                        </div>

                        <div class="mb-8 flex-1 space-y-4">
                            @php 
                                $limits = is_string($plan->limits) ? json_decode($plan->limits, true) : $plan->limits;
                                $maxBranch = $limits['max_restaurants'] ?? 1;
                                $maxMenu = $limits['max_menus'] ?? 10;
                            @endphp
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-primary-600 dark:text-indigo-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span class="text-sm text-gray-600 dark:text-gray-300">
                                    {{ ($maxBranch == 9999 || $maxBranch == -1) ? 'Cabang Tidak Terbatas' : $maxBranch . ' Cabang Resto' }}
                                </span>
                            </div>
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-primary-600 dark:text-indigo-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span class="text-sm text-gray-600 dark:text-gray-300">
                                    {{ ($maxMenu == 9999 || $maxMenu == -1) ? 'Menu Tidak Terbatas' : $maxMenu . ' Menu Item' }}
                                </span>
                            </div>

                            @php $features = is_string($plan->features) ? json_decode($plan->features, true) : $plan->features; @endphp
                            @if(is_array($features))
                                @foreach(array_slice($features, 0, 10) as $feature)
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-primary-600 dark:text-indigo-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span class="text-sm text-gray-600 dark:text-gray-300">{{ $feature }}</span>
                                </div>
                                @endforeach
                            @endif
                        </div>

                        <a href="{{ route('filament.restaurant.auth.login') }}" class="w-full text-center py-3 px-4 rounded-xl font-bold transition {{ $isPopular ? 'bg-primary-600 hover:bg-primary-500 text-white shadow-lg' : 'bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 shadow-sm' }}">
                            Pilih Paket
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- 7. Testimonial (dari Database) -->
    @if($testimonials->count() > 0)
    <div class="py-24 bg-gray-50 dark:bg-[#0a0e17] relative transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Dipercaya oleh Restoran Terbaik</h2>
                <p class="text-gray-500 dark:text-gray-400">Apa kata mereka yang sudah merasakan langsung perbedaannya dengan {{ $settings->site_name }}.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($testimonials as $testi)
                <div class="glass-panel p-6 rounded-2xl flex flex-col justify-between">
                    <div>
                        <div class="flex gap-1 mb-4">
                            @for($i=1; $i<=5; $i++)
                                <svg class="w-4 h-4 {{ $i <= $testi->rating ? 'text-amber-400' : 'text-gray-300 dark:text-gray-700' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 text-sm italic mb-6">"{{ $testi->comment ?: 'Pelayanan luar biasa dan sistem sangat cepat.' }}"</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 flex items-center justify-center border border-gray-200 dark:border-gray-600">
                            <span class="text-gray-900 dark:text-white font-bold text-xs">{{ substr($testi->restaurant->name, 0, 1) }}</span>
                        </div>
                        <div>
                            <h5 class="text-gray-900 dark:text-white font-bold text-sm">{{ $testi->restaurant->name }}</h5>
                            <p class="text-xs text-gray-500">Order #{{ $testi->order->order_number ?? 'DNC-' }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- 8. FAQ -->
    <div class="py-24 relative max-w-3xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ open: null }">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Pertanyaan Umum (FAQ)</h2>
        </div>

        <div class="space-y-4">
            <!-- FAQ 1 -->
            <div class="glass-panel rounded-xl overflow-hidden border border-gray-200 dark:border-white/5">
                <button @click="open === 1 ? open = null : open = 1" 
                        :aria-expanded="open === 1"
                        aria-controls="faq-content-1"
                        class="w-full px-6 py-4 flex justify-between items-center bg-white dark:bg-[#111827] hover:bg-gray-50 dark:hover:bg-gray-900 transition focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                    <span class="font-bold text-gray-800 dark:text-gray-200 text-left">Bagaimana cara pelanggan memesan dan membayar?</span>
                    <svg class="w-5 h-5 text-gray-400 transform transition" :class="{'rotate-180': open === 1}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open === 1" id="faq-content-1" x-collapse x-cloak>
                    <div class="px-6 pb-4 pt-2 text-sm text-gray-600 dark:text-gray-400 bg-white dark:bg-[#111827]">
                        Cukup sediakan Standing QR atau Sticker QR {{ $settings->site_name }} di setiap meja. Pelanggan scan menggunakan HP, pilih menu, tambahkan catatan (misal: pedas), lalu bayar langsung via QRIS, ShopeePay, OVO, Dana, atau Virtual Account. Setelah bayar sukses, pesanan otomatis terkirim ke KDS (Kitchen Display System). Sangat cepat dan modern!
                    </div>
                </div>
            </div>

            <!-- FAQ 2 -->
            <div class="glass-panel rounded-xl overflow-hidden border border-gray-200 dark:border-white/5">
                <button @click="open === 2 ? open = null : open = 2" 
                        :aria-expanded="open === 2"
                        aria-controls="faq-content-2"
                        class="w-full px-6 py-4 flex justify-between items-center bg-white dark:bg-[#111827] hover:bg-gray-50 dark:hover:bg-gray-900 transition focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                    <span class="font-bold text-gray-800 dark:text-gray-200 text-left">Ke rekening mana uang hasil penjualan masuk?</span>
                    <svg class="w-5 h-5 text-gray-400 transform transition" :class="{'rotate-180': open === 2}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open === 2" id="faq-content-2" x-collapse x-cloak>
                    <div class="px-6 pb-4 pt-2 text-sm text-gray-600 dark:text-gray-400 bg-white dark:bg-[#111827]">
                        Jika menggunakan "Akun {{ $settings->site_name }} Default", dana transaksi online (QRIS/E-Wallet) akan masuk ke ledger virtual restoran Anda. Anda bisa request "Tarik Dana / Withdraw" kapan saja dari dashboard langsung ke rekening Bank Anda. Jika Anda ingin uang masuk langsung ke rekening setiap detik tanpa ditarik manual, Anda bisa mensetting kredensial API Midtrans pribadi Anda di menu Settings!
                    </div>
                </div>
            </div>
            
            <!-- FAQ 3 -->
            <div class="glass-panel rounded-xl overflow-hidden border border-gray-200 dark:border-white/5">
                <button @click="open === 3 ? open = null : open = 3" 
                        :aria-expanded="open === 3"
                        aria-controls="faq-content-3"
                        class="w-full px-6 py-4 flex justify-between items-center bg-white dark:bg-[#111827] hover:bg-gray-50 dark:hover:bg-gray-900 transition focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                    <span class="font-bold text-gray-800 dark:text-gray-200 text-left">Apakah butuh instalasi mahal seperti sistem restoran jadul?</span>
                    <svg class="w-5 h-5 text-gray-400 transform transition" :class="{'rotate-180': open === 3}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open === 3" id="faq-content-3" x-collapse x-cloak>
                    <div class="px-6 pb-4 pt-2 text-sm text-gray-600 dark:text-gray-400 bg-white dark:bg-[#111827]">
                        Tidak! {{ $settings->site_name }} 100% Cloud-based (SaaS). Anda daftar, atur menu, cetak QR code meja sendiri, dan langsung bisa terima pesanan di hari yang sama. Tanpa biaya instalasi teknisi yang mahal.
                    </div>
                </div>
            </div>

            <!-- FAQ 4 -->
            <div class="glass-panel rounded-xl overflow-hidden border border-gray-200 dark:border-white/5">
                <button @click="open === 4 ? open = null : open = 4" 
                        :aria-expanded="open === 4"
                        aria-controls="faq-content-4"
                        class="w-full px-6 py-4 flex justify-between items-center bg-white dark:bg-[#111827] hover:bg-gray-50 dark:hover:bg-gray-900 transition focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                    <span class="font-bold text-gray-800 dark:text-gray-200 text-left">Apa keunggulan {{ $settings->site_name }} dibandingkan solusi manajemen restoran lainnya?</span>
                    <svg class="w-5 h-5 text-gray-400 transform transition" :class="{'rotate-180': open === 4}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open === 4" id="faq-content-4" x-collapse x-cloak>
                    <div class="px-6 pb-5 pt-3 text-sm text-gray-600 dark:text-gray-400 bg-white dark:bg-[#111827] space-y-3">
                        <p>{{ $settings->site_name }} hadir dengan <span class="text-gray-900 dark:text-white font-medium">Investasi Berkelas, Berintegritas</span>. Berbeda dengan platform yang menerapkan biaya potongan besar, {{ $settings->site_name }} memberikan kebebasan penuh bagi Anda untuk memilih paket yang sesuai guna mengembangkan restoran. Tanpa komisi per pesanan, tanpa biaya tersembunyi. Fokus kami adalah mendukung pertumbuhan bisnis Anda secara transparan. Beberapa keunggulan Utama dibandingkan solusi manajemen restoran lainnya:</p>
                        <ul class="space-y-2.5 mt-2">
                            <li class="flex items-start gap-2.5">
                                <span class="text-primary-600 dark:text-indigo-400 mt-0.5 shrink-0">✦</span>
                                <span><span class="text-gray-900 dark:text-gray-200 font-medium">Tanpa komisi per transaksi.</span> Banyak platform POS mengenakan fee per order atau persentase dari penjualan. {{ $settings->site_name }} hanya biaya langganan flat — semakin ramai restoran Anda, semakin hemat.</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="text-primary-600 dark:text-indigo-400 mt-0.5 shrink-0">✦</span>
                                <span><span class="text-gray-900 dark:text-gray-200 font-medium">QR Order + KDS + Kiosk dalam satu platform.</span> Kebanyakan kompetitor memisahkan modul-modul ini ke paket berbeda atau aplikasi terpisah. Di {{ $settings->site_name }}, semuanya terintegrasi native.</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="text-primary-600 dark:text-indigo-400 mt-0.5 shrink-0">✦</span>
                                <span><span class="text-gray-900 dark:text-gray-200 font-medium">Loyalitas & Upselling built-in.</span> Fitur "Pasangan Terbaik" dan Dynamic Pricing (Happy Hour otomatis) jarang ditemukan di aplikasi kasir lokal lain pada harga yang sama.</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="text-primary-600 dark:text-indigo-400 mt-0.5 shrink-0">✦</span>
                                <span><span class="text-gray-900 dark:text-gray-200 font-medium">Bawa akun payment gateway sendiri.</span> Banyak platform agregator mengunci Anda ke ekosistem mereka. {{ $settings->site_name }} membebaskan Anda — gunakan QRIS Midtrans milik restoran sendiri, tanpa ketergantungan pihak ketiga.</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="text-primary-600 dark:text-indigo-400 mt-0.5 shrink-0">✦</span>
                                <span><span class="text-gray-900 dark:text-gray-200 font-medium">Dashboard HQ untuk franchise.</span> Memantau performa semua cabang dari satu layar — fitur yang biasanya hanya ada di solusi enterprise berharga puluhan juta rupiah per tahun.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 9. Consultation CTA -->
    <div class="py-24 relative overflow-hidden bg-white dark:bg-[#0B0F19] transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="glass-panel p-8 md:p-16 rounded-[2.5rem] relative overflow-hidden border-indigo-500/20 shadow-[0_20px_50px_rgba(99,102,241,0.1)] group">
                <!-- Background Decoration -->
                <div class="absolute -top-24 -right-24 w-64 h-64 bg-primary-500/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"></div>
                <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"></div>

                <div class="relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-indigo-500/10 text-indigo-500 text-xs font-bold uppercase tracking-widest mb-6 border border-indigo-500/20">
                            Professional Support
                        </div>
                        <h2 class="text-3xl md:text-5xl font-extrabold text-gray-900 dark:text-white mb-6 leading-tight">
                            Masih Ragu? Mari <span class="text-gradient">Konsultasi</span> Strategi Bisnis Anda.
                        </h2>
                        <p class="text-lg text-gray-500 dark:text-gray-400 mb-10 max-w-xl leading-relaxed">
                            Bukan sekadar aplikasi, kami adalah mitra pertumbuhan Anda. Diskusikan tantangan restoran Anda dan biarkan solusi teknologi kami menyelesaikannya.
                        </p>
                        <div class="flex flex-wrap gap-4">
                            <a href="{{ route('consultation') }}" class="px-10 py-5 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xl transition shadow-xl shadow-indigo-500/20 transform hover:-translate-y-1 flex items-center gap-3">
                                🤝 Jadwalkan Konsultasi Gratis
                            </a>
                        </div>
                    </div>
                    <div class="relative hidden lg:block">
                        <div class="aspect-video rounded-3xl bg-gray-900/5 dark:bg-white/5 border border-gray-200 dark:border-white/10 p-4 shadow-inner relative group-hover:rotate-1 transition-transform duration-500">
                             <div class="absolute inset-0 flex items-center justify-center">
                                 <div class="text-center">
                                     <div class="w-20 h-20 bg-indigo-500/20 rounded-full flex items-center justify-center mx-auto mb-4 border border-indigo-500/30">
                                         <svg class="w-10 h-10 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                     </div>
                                     <p class="text-indigo-500 font-bold">Video Meeting Ready</p>
                                     <p class="text-xs text-gray-400 mt-1">G-Meet / Zoom / WhatsApp</p>
                                 </div>
                             </div>
                             <!-- Decorative Floating Dots -->
                             <div class="absolute -top-4 -left-4 w-12 h-12 bg-white dark:bg-gray-800 rounded-2xl shadow-lg flex items-center justify-center animate-bounce">✨</div>
                             <div class="absolute -bottom-4 -right-4 w-12 h-12 bg-indigo-500 rounded-2xl shadow-lg flex items-center justify-center text-white animate-pulse">🚀</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 10. CTA Footer -->
    <div class="py-24 relative overflow-hidden bg-primary-50 dark:bg-indigo-900/20 border-t border-primary-100 dark:border-indigo-500/20 transition-colors duration-300">
        <div class="absolute inset-0 bg-gradient-to-r from-primary-600/5 to-purple-600/5 dark:from-indigo-600/10 dark:to-purple-600/10 mix-blend-overlay"></div>
        <div class="max-w-4xl mx-auto px-4 text-center relative z-10">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-6">Siap Mengubah Wajah Restoran Anda?</h2>
            <p class="text-lg text-primary-700 dark:text-indigo-200 mb-10 max-w-2xl mx-auto opacity-80">Bergabunglah dengan ratusan pengusaha kuliner cerdas yang telah bertransisi ke era digital bersama {{ $settings->site_name }}.</p>
            <a href="{{ route('filament.restaurant.auth.register') }}" class="inline-block px-10 py-5 rounded-full bg-primary-600 hover:bg-primary-500 text-white font-bold text-xl transition shadow-xl shadow-primary-500/20 transform hover:-translate-y-1">
                Daftar Sekarang (Mulai Gratis)
            </a>
        </div>
    </div>

    <!-- 11. Landing Footer -->
    <x-footer-premium />
</div>
