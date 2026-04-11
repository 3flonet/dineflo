@php $settings = $settings ?? app(\App\Settings\GeneralSettings::class); @endphp
<nav class="fixed top-0 w-full z-50 glass-panel border-b-0 border-gray-200 dark:border-white/10 transition-colors duration-300" x-data="{ mobileMenu: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center gap-3 cursor-pointer">
                @if($settings->site_logo)
                    <img src="{{ Storage::url($settings->site_logo) }}" alt="{{ $settings->site_name }}" class="h-10 w-auto object-contain">
                @else
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                @endif
                <span class="font-bold text-lg sm:text-2xl tracking-tight text-gradient-gold">{{ $settings->site_name }}</span>
            </a>
            
            <div class="hidden md:flex space-x-8 items-center">
                <a href="{{ route('home') }}#fitur" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-white transition">Fitur</a>
                <a href="{{ route('home') }}#solusi" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-white transition">Solusi</a>
                <a href="{{ route('home') }}#harga" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-white transition">Harga</a>
                <a href="{{ route('community') }}" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-white transition {{ Request::routeIs('community') ? 'text-primary-600 dark:text-white' : '' }}">Community</a>
                <a href="{{ route('news.index') }}" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-white transition {{ Request::routeIs('news.*') ? 'text-primary-600 dark:text-white' : '' }}">News</a>
                <a href="{{ route('consultation') }}" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-white transition {{ Request::routeIs('consultation') ? 'text-primary-600 dark:text-white' : '' }}">Konsultasi</a>
                
                <div class="w-px h-5 bg-gray-200 dark:bg-gray-700"></div>
                
                {{-- Theme Toggle --}}
                <button @click="theme = theme === 'light' ? 'dark' : 'light'; updateTheme()" 
                        class="p-2 rounded-xl bg-gray-100 dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-500 dark:text-gray-400">
                    <svg x-show="theme === 'light'" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <svg x-show="theme === 'dark' || theme === 'system'" x-cloak xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                    </svg>
                </button>

                <a href="{{ route('filament.restaurant.auth.login') }}" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-white transition">Login</a>
                <a href="{{ route('frontend.restaurants.index') }}" class="px-5 py-2.5 rounded-full bg-primary-50 dark:bg-white/10 hover:bg-primary-100 dark:hover:bg-white/20 border border-primary-100 dark:border-white/10 text-primary-700 dark:text-white text-sm font-bold transition shadow-sm backdrop-blur-sm">
                    Cari Resto
                </a>
            </div>

            {{-- Mobile Menu Button --}}
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
            <a href="{{ route('home') }}" @click="mobileMenu = false" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Beranda</a>
            <a href="{{ route('community') }}" @click="mobileMenu = false" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Community</a>
            <a href="{{ route('news.index') }}" @click="mobileMenu = false" class="block text-sm font-medium text-gray-600 dark:text-gray-300">News</a>
            <a href="{{ route('consultation') }}" @click="mobileMenu = false" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Konsultasi</a>
            <div class="h-px bg-gray-200 dark:bg-gray-800"></div>
            <a href="{{ route('filament.restaurant.auth.login') }}" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Login Restoran</a>
            <a href="{{ route('frontend.restaurants.index') }}" class="block w-full py-3 text-center rounded-xl bg-primary-600 text-white font-bold text-sm shadow-lg">Cari Restoran</a>
        </div>
    </div>
</nav>
