<div class="min-h-screen bg-[#FDFCFB] dark:bg-[#0F1115] pb-32">
    {{-- Ultra-Premium Hero Section --}}
    <div class="relative h-[85vh] w-full overflow-hidden group">
        <div class="absolute inset-0 transition-transform duration-[3000ms] scale-105 group-hover:scale-110">
            <img src="{{ Storage::url($package->cover_image) }}" class="w-full h-full object-cover">
        </div>
        
        {{-- Elegant Overlays --}}
        <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-transparent to-[#FDFCFB] dark:to-[#0F1115]"></div>
        <div class="absolute inset-0 bg-black/10"></div>
        
        {{-- Top Navigation --}}
        <div class="absolute top-0 left-0 right-0 p-8 z-30">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <a href="{{ route('frontend.restaurants.show', $restaurant->slug) }}" class="group/back flex items-center gap-3 bg-white/10 backdrop-blur-xl px-6 py-3 rounded-full text-white font-bold hover:bg-white/20 transition-all border border-white/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform group-hover/back:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" /></svg>
                    <span>Back to {{ $restaurant->name }}</span>
                </a>

                {{-- Theme Toggle --}}
                <button @click="theme = theme === 'light' ? 'dark' : 'light'; updateTheme()" 
                        class="p-3.5 rounded-full bg-white/10 backdrop-blur-xl text-white hover:bg-white/20 transition-all border border-white/20 flex items-center justify-center h-12 w-12 shadow-xl">
                    <svg x-show="theme === 'light'" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <svg x-show="theme === 'dark' || theme === 'system'" x-cloak xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Hero Text Center Content --}}
        <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-6 z-20">
            <div class="space-y-6 max-w-4xl" 
                 x-data x-intersect="$el.classList.add('animate-fade-in-up')"
                 class="opacity-0 transition-all duration-1000 transform translate-y-10">
                <div class="flex items-center justify-center gap-4 mb-2">
                    <div class="h-[1px] w-12 bg-amber-500/50"></div>
                    <span class="text-amber-500 text-xs font-black uppercase tracking-[0.4em]">Exclusive Collection</span>
                    <div class="h-[1px] w-12 bg-amber-500/50"></div>
                </div>
                <h1 class="text-5xl md:text-8xl font-black text-white leading-none tracking-tight">
                    {{ $package->name }}
                </h1>
                <p class="text-white/80 text-lg md:text-xl font-medium tracking-wide max-w-2xl mx-auto italic font-serif">
                    "Creating timeless memories in an atmosphere of refined elegance."
                </p>
            </div>
        </div>

        {{-- Scroll Indicator --}}
        <div class="absolute bottom-16 left-1/2 -translate-x-1/2 z-20 animate-bounce">
            <div class="w-6 h-10 rounded-full border-2 border-white/30 flex justify-center p-1">
                <div class="w-1 h-2 bg-white rounded-full"></div>
            </div>
        </div>
    </div>

    {{-- Overlapping Overview Card --}}
    <div class="max-w-6xl mx-auto px-6 -mt-32 relative z-30">
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl shadow-black/5 overflow-hidden border border-gray-100 dark:border-white/5 flex flex-col md:flex-row divide-y md:divide-y-0 md:divide-x divide-gray-100 dark:divide-white/5">
            <div class="flex-1 p-10 flex flex-col items-center justify-center text-center space-y-2">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Kapasitas Tamu</span>
                <p class="text-2xl font-black text-gray-900 dark:text-white">{{ $package->min_capacity }} - {{ $package->max_capacity }} <span class="text-sm text-gray-400 font-bold uppercase">Orang</span></p>
            </div>
            <div class="flex-1 p-10 flex flex-col items-center justify-center text-center space-y-2 bg-amber-50/30 dark:bg-amber-500/5">
                <span class="text-[10px] font-black text-amber-600/60 uppercase tracking-widest">Investasi Mulai Dari</span>
                <p class="text-3xl font-black text-amber-600 dark:text-amber-500">
                    @if($package->price)
                        Rp {{ number_format($package->price, 0, ',', '.') }}
                    @else
                        By Request
                    @endif
                </p>
            </div>
            <div class="flex-1 p-8 flex items-center justify-center">
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $restaurant->phone) }}?text={{ urlencode('Halo ' . $restaurant->name . ', saya tertarik dengan paket wedding ' . $package->name . '. Bisa dibantu rincian selengkapnya?') }}" target="_blank" class="w-full py-5 bg-gray-900 dark:bg-amber-500 text-white font-black rounded-2xl hover:scale-105 active:scale-95 transition-all shadow-xl shadow-black/10 text-center uppercase tracking-widest text-sm">
                    Book Consultation
                </a>
            </div>
        </div>
    </div>

    {{-- Detailed Info Section --}}
    <div class="max-w-7xl mx-auto px-6 mt-24">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-20">
            
            <div class="lg:col-span-12 lg:grid lg:grid-cols-12 gap-20">
                <div class="lg:col-span-7 space-y-20">
                    <div class="space-y-8">
                        <div class="flex items-center gap-4">
                            <span class="h-12 w-12 bg-amber-500/10 rounded-xl flex items-center justify-center text-amber-600 font-black text-xl">01</span>
                            <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Visi Layanan Kami</h2>
                        </div>
                        <div class="prose prose-xl prose-stone dark:prose-invert max-w-none text-gray-600 dark:text-gray-400 leading-relaxed font-medium">
                            {!! $package->description !!}
                        </div>
                    </div>

                    <div class="space-y-10">
                        <div class="flex items-center gap-4">
                            <span class="h-12 w-12 bg-amber-500/10 rounded-xl flex items-center justify-center text-amber-600 font-black text-xl">02</span>
                            <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Item Eksklusif Paket</h2>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($package->inclusions as $item)
                                <div class="group flex items-center gap-5 p-6 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-white/5 hover:border-amber-200 dark:hover:border-amber-500/30 transition-all duration-500">
                                    <div class="h-3 w-3 rounded-full bg-amber-500 shadow-[0_0_10px_rgba(245,158,11,0.5)]"></div>
                                    <span class="text-gray-700 dark:text-gray-300 font-bold group-hover:text-amber-600 dark:group-hover:text-amber-400 transition">{{ $item }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Gallery & Location Side --}}
                <div class="lg:col-span-5 space-y-16">
                    @if($package->gallery && count($package->gallery) > 0)
                        <div class="space-y-8">
                            <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.3em]">Documentation Gallery</h3>
                            <div class="grid grid-cols-2 gap-4" x-data="{ active: null }">
                                @foreach($package->gallery as $index => $img)
                                    <div class="relative group aspect-[4/5] overflow-hidden rounded-2xl cursor-pointer {{ $index === 0 ? 'col-span-2 aspect-[16/9]' : '' }}" @click="active = '{{ Storage::url($img['image']) }}'">
                                        <img src="{{ Storage::url($img['image']) }}" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110">
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center backdrop-blur-sm">
                                            <div class="p-4 bg-white/10 backdrop-blur-xl rounded-full border border-white/20">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                {{-- Immersive Lightbox --}}
                                <div x-show="active" 
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     class="fixed inset-0 z-[100] flex items-center justify-center p-8 bg-black/95 backdrop-blur-2xl"
                                     @click="active = null"
                                     x-cloak
                                >
                                    <img :src="active" class="max-w-full max-h-full rounded-2xl shadow-2xl border border-white/10">
                                    <button class="absolute top-10 right-10 text-white/50 hover:text-white transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Location Luxury Card --}}
                    <div class="relative overflow-hidden bg-gray-900 rounded-3xl p-10 text-white">
                        <div class="absolute -bottom-20 -right-20 h-64 w-64 bg-amber-500/20 rounded-full blur-[100px]"></div>
                        <div class="relative z-10 space-y-6">
                            <p class="text-xs font-black text-amber-500 uppercase tracking-widest">Location Venue</p>
                            <h4 class="text-2xl font-black leading-tight">{{ $restaurant->name }}</h4>
                            <div class="space-y-4">
                                <p class="text-white/60 font-medium leading-relaxed">{{ $restaurant->address }}</p>
                                <div class="h-[1px] w-full bg-white/10"></div>
                                <p class="text-white/60 font-medium">{{ $restaurant->phone }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes fade-in-up {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fade-in-up 1.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
    </style>
</div>
