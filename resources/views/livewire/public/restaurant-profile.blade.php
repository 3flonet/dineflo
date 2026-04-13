@php $settings = $settings ?? app(\App\Settings\GeneralSettings::class); @endphp
<div class="min-h-screen bg-white dark:bg-gray-900 pb-24 lg:pb-0">
    {{-- Top Cover Image --}}
    <div class="relative w-full h-[45vh] md:h-[60vh] bg-gray-900 overflow-hidden shrink-0">
        @if($restaurant->cover_image)
            <img src="{{ Storage::url($restaurant->cover_image) }}" class="w-full h-full object-cover scale-105 animate-pulse-slow" alt="Cover {{ $restaurant->name }}">
        @else
            <div class="absolute inset-0 bg-gradient-to-br from-amber-600 to-gray-900 opacity-90"></div>
        @endif
        <div class="absolute inset-0 bg-black/20 z-10"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-white dark:from-gray-900 via-transparent to-transparent z-10"></div>
        
        {{-- Top bar --}}
        <div class="absolute top-6 left-4 right-4 sm:left-8 sm:right-8 flex items-center justify-between z-20">
            {{-- Back Button --}}
            <a href="{{ route('frontend.restaurants.index') }}" class="bg-white/20 dark:bg-gray-800/40 backdrop-blur-md p-2 rounded-full text-white hover:bg-white hover:text-black dark:hover:bg-gray-700 transition flex items-center justify-center h-10 w-10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
            </a>

            {{-- Theme Toggle --}}
            <button @click="theme = theme === 'light' ? 'dark' : 'light'; updateTheme()" 
                    class="p-2.5 rounded-full bg-white/20 dark:bg-gray-800/40 backdrop-blur-md text-white hover:bg-white hover:text-black dark:hover:bg-gray-700 transition flex items-center justify-center h-10 w-10">
                <svg x-show="theme === 'light'" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
                <svg x-show="theme === 'dark' || theme === 'system'" x-cloak xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Main Content Space --}}
    <div class="max-w-4xl mx-auto px-4 sm:px-8 relative -mt-20 sm:-mt-24 z-10 flex flex-col">
        
        {{-- Profile Header Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 sm:p-8 shadow-[0_8px_30px_rgb(0,0,0,0.06)] dark:shadow-none border border-gray-100 dark:border-white/5 mb-8 w-full flex-shrink-0">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                
                {{-- Logo --}}
                <div class="relative shrink-0 -mt-16 sm:-mt-20">
                    <div class="h-28 sm:h-36 bg-white dark:bg-gray-700 p-2 shadow-xl ring-1 ring-gray-100 dark:ring-white/10 rounded-2xl flex items-center justify-center" style="max-width: 14rem; min-width: 7rem;">
                        @if($restaurant->logo)
                            <img src="{{ Storage::url($restaurant->logo) }}" class="h-full w-auto max-w-full object-contain" alt="Logo {{ $restaurant->name }}">
                        @else
                            <div class="w-full h-full rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-5xl font-black text-gray-300 dark:text-gray-600">
                                {{ substr($restaurant->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    @if($restaurant->is_online_order_enabled)
                        <div class="absolute -top-1 -right-1">
                            <span class="relative flex h-5 w-5">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-5 w-5 border-2 border-white bg-green-500"></span>
                            </span>
                        </div>
                    @endif
                </div>

                {{-- Text Info --}}
                <div class="flex-grow text-center sm:text-left flex flex-col justify-center">
                    <h1 class="text-3xl sm:text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight mb-2">{{ $restaurant->name }}</h1>
                    
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-3 sm:gap-4 text-sm text-gray-500 font-medium mb-4">
                        <span class="flex items-center gap-1.5 bg-gray-50 dark:bg-gray-700/50 px-3 py-1.5 rounded-full border border-gray-100 dark:border-white/5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-amber-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                            {{ $restaurant->city ?? 'Location not set' }}
                        </span>
                        
                        @if($restaurant->is_online_order_enabled)
                            <span class="flex items-center gap-1.5 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 px-3 py-1.5 rounded-full border border-green-100 dark:border-green-900/30">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                Takeaway Menerima Pesanan
                            </span>
                        @endif
                    </div>

                    <p class="text-gray-600 dark:text-gray-400 text-[15px] leading-relaxed max-w-2xl">
                        {{ $restaurant->description ?: 'Selamat datang di ' . $restaurant->name . '. Jelajahi profil dan menu digital kami.' }}
                    </p>
                </div>
            </div>
            
            {{-- Divider --}}
            <hr class="border-gray-100 dark:border-white/5 my-6">

            {{-- Action Buttons --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="{{ route('restaurant.index', $restaurant->slug) }}" class="flex items-center justify-center gap-2 bg-black text-white hover:bg-gray-800 transition py-4 rounded-xl font-bold shadow-lg shadow-black/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    Buka Buku Menu Digital
                </a>
                
                @if($restaurant->owner?->hasFeature('Table Reservation'))
                    <a href="{{ route('restaurant.reserve', $restaurant->slug) }}" class="flex items-center justify-center gap-2 bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-amber-500/20 transition border border-amber-200 dark:border-amber-500/20 py-4 rounded-xl font-bold">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Reservasi Tempat
                    </a>
                @endif

                @if($restaurant->owner?->hasFeature('Queue Management System'))
                    <button wire:click="takeQueue" class="flex items-center justify-center gap-2 bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-500/20 transition border border-blue-200 dark:border-blue-500/20 py-4 rounded-xl font-bold col-span-1 sm:col-span-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Ambil Antrean Online
                    </button>
                @endif

                @if($restaurant->owner?->hasFeature('Membership & Loyalty'))
                    <a href="{{ route('member.portal.login', $restaurant->slug) }}" class="flex items-center justify-center gap-2 bg-purple-50 dark:bg-purple-500/10 text-purple-700 dark:text-purple-400 hover:bg-purple-100 dark:hover:bg-purple-500/20 transition border border-purple-200 dark:border-purple-500/20 py-4 rounded-xl font-bold col-span-1 sm:col-span-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Portal Member — Cek Poin & Histori
                    </a>
                @endif

                {{-- Share Button --}}
                <button
                    id="btn-share-resto"
                    onclick="shareRestaurant()"
                    class="flex items-center justify-center gap-2 bg-gray-50 dark:bg-gray-800/10 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transformation border border-gray-200 dark:border-white/5 py-4 rounded-xl font-bold col-span-1 sm:col-span-2 group transition"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                    </svg>
                    <span id="share-btn-text">Bagikan Restoran Ini</span>
                </button>
            </div>

            {{-- Share Toast --}}
            <div id="share-toast" class="hidden mt-3 text-center text-sm font-semibold text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 py-2 px-4 rounded-xl animate-pulse">
                ✅ Link berhasil disalin!
            </div>

            <script>
                function shareRestaurant() {
                    const shareData = {
                        title: '{{ addslashes($restaurant->name) }}',
                        text: '{{ addslashes($restaurant->description ?: 'Kunjungi dan pesan di ' . $restaurant->name . '!') }}',
                        url: '{{ url()->current() }}'
                    };

                    const showSuccess = () => {
                        const toast = document.getElementById('share-toast');
                        const btn = document.getElementById('share-btn-text');
                        if(toast) toast.classList.remove('hidden');
                        if(btn) btn.textContent = 'Link Disalin!';
                        setTimeout(() => {
                            if(toast) toast.classList.add('hidden');
                            if(btn) btn.textContent = 'Bagikan Restoran Ini';
                        }, 3000);
                    };

                    if (navigator.share && navigator.canShare && navigator.canShare(shareData)) {
                        navigator.share(shareData).catch(() => {});
                    } else {
                        if (navigator.clipboard && navigator.clipboard.writeText) {
                            navigator.clipboard.writeText(shareData.url).then(showSuccess).catch(() => {
                                // Fallback
                                const textArea = document.createElement("textarea");
                                textArea.value = shareData.url;
                                document.body.appendChild(textArea);
                                textArea.select();
                                document.execCommand('copy');
                                document.body.removeChild(textArea);
                                showSuccess();
                            });
                        } else {
                            // Legacy fallback
                            const textArea = document.createElement("textarea");
                            textArea.value = shareData.url;
                            document.body.appendChild(textArea);
                            textArea.select();
                            try {
                                document.execCommand('copy');
                                showSuccess();
                            } catch (err) {}
                            document.body.removeChild(textArea);
                        }
                    }
                }
            </script>
        </div>

        {{-- Info Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 overflow-visible">
            
            {{-- Contact & Address --}}
            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-2xl p-6 border border-gray-100 dark:border-white/5 flex-shrink-0">
                 <h3 class="font-bold text-lg mb-5 flex items-center gap-2 dark:text-white">
                    <span class="bg-white dark:bg-gray-700 p-2 rounded-lg shadow-sm border border-gray-100 dark:border-white/5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 3l-6 6M21 3l-6-6M21 3v6M3 21l6-6M3 21v-6m6 6l-6-6m12-6l-6 6" />
                        </svg>
                    </span>
                    Informasi Kontak
                </h3>
                
                <div class="space-y-4">
                    <div class="flex gap-4 items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                         <div>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">Alamat</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 leading-relaxed">{{ $restaurant->address ?? 'Belum ada alamat' }}</p>
                        </div>
                    </div>
                    
                    @if($restaurant->phone)
                        <div class="flex gap-4 items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                             <div>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">Telepon</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $restaurant->phone }}</p>
                            </div>
                        </div>
                    @endif
                     @if($restaurant->email)
                        <div class="flex gap-4 items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                             <div>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">Email</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $restaurant->email }}</p>
                            </div>
                        </div>
                    @endif

                    @if($restaurant->social_links && is_array($restaurant->social_links) && count($restaurant->social_links) > 0)
                         <div class="pt-4 mt-2 border-t border-gray-200 dark:border-white/5">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Sosial Media</p>
                            <div class="flex flex-wrap items-center gap-3">
                                @foreach($restaurant->social_links as $social)
                                    @php
                                        $platform = $social['platform'] ?? 'website';
                                        $url = $social['url'] ?? '#';
                                        
                                        $iconSvg = match($platform) {
                                            'instagram' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>',
                                            'facebook' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>',
                                            'tiktok' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"></path></svg>',
                                            'twitter' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"></path></svg>',
                                            'youtube' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"></path><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"></polygon></svg>',
                                            'website' => '<img src="https://www.google.com/s2/favicons?domain=' . parse_url($url, PHP_URL_HOST) . '&sz=64" class="w-5 h-5 rounded-sm object-contain" alt="Website Favicon" />',
                                            default => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>'
                                        };
                                        
                                        $hoverClass = match($platform) {
                                            'instagram' => 'hover:text-pink-600 hover:border-pink-200 hover:bg-pink-50',
                                            'facebook' => 'hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50',
                                            'tiktok' => 'hover:text-black hover:border-gray-800 hover:bg-gray-100',
                                            'twitter' => 'hover:text-sky-500 hover:border-sky-200 hover:bg-sky-50',
                                            'youtube' => 'hover:text-red-600 hover:border-red-200 hover:bg-red-50',
                                            default => 'hover:text-amber-600 hover:border-amber-200 hover:bg-amber-50'
                                        };
                                    @endphp
                                     <a href="{{ $url }}" target="_blank" class="w-10 h-10 rounded-full bg-white dark:bg-gray-700 border border-gray-200 dark:border-white/5 flex items-center justify-center text-gray-600 dark:text-gray-300 transition-all shadow-sm {{ $hoverClass }}" title="{{ ucfirst($platform) }}">
                                        {!! $iconSvg !!}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Operational Hours --}}
            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-2xl p-6 border border-gray-100 dark:border-white/5 flex-shrink-0">
                 <h3 class="font-bold text-lg mb-4 flex items-center gap-2 dark:text-white">
                    <span class="bg-white dark:bg-gray-700 p-2 rounded-lg shadow-sm border border-gray-100 dark:border-white/5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                    Jam Operasional
                </h3>
                
                <div class="space-y-3">
                    @php
                        $daysMap = [
                            'monday' => 'Senin', 'tuesday' => 'Selasa', 'wednesday' => 'Rabu',
                            'thursday' => 'Kamis', 'friday' => 'Jumat', 'saturday' => 'Sabtu', 'sunday' => 'Minggu'
                        ];
                        $today = strtolower(now()->timezone('Asia/Jakarta')->englishDayOfWeek);
                    @endphp
                    
                    @if($restaurant->opening_hours && is_array($restaurant->opening_hours))
                        @foreach($restaurant->opening_hours as $idx => $hours)
                            @php 
                                $isToday = $hours['day'] === $today;
                                $dayName = $daysMap[$hours['day']] ?? ucfirst($hours['day']);
                            @endphp
                             <div class="flex items-center justify-between text-sm py-1 {{ $isToday ? 'bg-black dark:bg-primary-600 text-white px-3 py-2 -mx-3 rounded-xl font-bold font-black shadow-lg shadow-primary-500/20' : 'text-gray-600 dark:text-gray-400 font-medium' }}">
                                <span class="capitalize">{{ $dayName }}</span>
                                @if($hours['is_closed'] ?? false)
                                    <span class="{{ $isToday ? 'text-red-300 dark:text-red-200' : 'text-red-500 dark:text-red-400 font-bold' }}">Tutup</span>
                                @else
                                    <span>{{ $hours['open'] ?? '--:--' }} - {{ $hours['close'] ?? '--:--' }}</span>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <p class="text-sm text-gray-500 italic block">Jam buka tidak tersedia.</p>
                    @endif
                </div>
            </div>
            
        </div>

        {{-- Facilities & Gallery Section --}}
        @if($facilities->count() > 0)
            @php
                // Prepare all photos for the lightbox
                $allPhotos = [];
                foreach($facilities as $f) {
                    foreach($f->photos as $p) {
                        $allPhotos[] = [
                            'url' => Storage::url($p->image_path),
                            'facility' => $f->name,
                            'facility_id' => $f->id
                        ];
                    }
                }
            @endphp

            <div class="mb-12" 
                 x-data="{ 
                    activeFacility: 0,
                    lightboxOpen: false,
                    currentIndex: 0,
                    allPhotos: {{ json_encode($allPhotos) }},
                    get filteredPhotos() {
                        if (this.activeFacility === 0) return this.allPhotos;
                        return this.allPhotos.filter(p => p.facility_id === this.activeFacility);
                    },
                    openGallery(photoUrl) {
                        this.currentIndex = this.filteredPhotos.findIndex(p => p.url === photoUrl);
                        this.lightboxOpen = true;
                    },
                    next() {
                        this.currentIndex = (this.currentIndex + 1) % this.filteredPhotos.length;
                    },
                    prev() {
                        this.currentIndex = (this.currentIndex - 1 + this.filteredPhotos.length) % this.filteredPhotos.length;
                    }
                 }"
                 @keydown.window.escape="lightboxOpen = false"
                 @keydown.window.left="if(lightboxOpen) prev()"
                 @keydown.window.right="if(lightboxOpen) next()"
            >
                <div class="flex flex-col sm:flex-row items-center justify-between mb-8 gap-4">
                    <h3 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Fasilitas & Galeri</h3>
                    
                    {{-- Tab Switcher --}}
                    <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-2 w-full sm:w-auto">
                        <button 
                            @click="activeFacility = 0"
                            :class="activeFacility === 0 ? 'bg-black text-white dark:bg-amber-500 dark:text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-700'"
                            class="px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 transition-all shrink-0 shadow-sm"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                            Semua
                        </button>

                        @foreach($facilities as $facility)
                            <button 
                                @click="activeFacility = {{ $facility->id }}"
                                :class="activeFacility === {{ $facility->id }} ? 'bg-black text-white dark:bg-amber-500 dark:text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-700'"
                                class="px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 transition-all shrink-0 shadow-sm"
                            >
                                @if($facility->icon)
                                    @svg($facility->icon, 'w-4 h-4')
                                @endif
                                {{ $facility->name }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Gallery Grid --}}
                <div class="relative min-h-[300px]">
                    {{-- Tab: All Photos --}}
                    <div 
                        x-show="activeFacility === 0"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="grid grid-cols-2 md:grid-cols-3 gap-4"
                    >
                        @foreach($allPhotos as $photo)
                            <div class="group relative aspect-square rounded-[1.5rem] overflow-hidden bg-gray-100 dark:bg-gray-800 border border-gray-100 dark:border-white/5 cursor-pointer">
                                <img 
                                    src="{{ $photo['url'] }}" 
                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                    @click="openGallery('{{ $photo['url'] }}')"
                                >
                                <div class="absolute bottom-3 left-3 right-3 opacity-0 group-hover:opacity-100 transition-all transform translate-y-2 group-hover:translate-y-0 z-20">
                                    <div class="bg-black/60 backdrop-blur-md px-3 py-1.5 rounded-lg border border-white/10">
                                        <p class="text-[10px] text-white font-bold truncate">{{ $photo['facility'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if(empty($allPhotos))
                            <div class="col-span-full py-12 text-center bg-gray-50 dark:bg-gray-800/20 rounded-3xl border border-dashed border-gray-200 dark:border-white/10 text-gray-400 text-sm">
                                Belum ada foto galeri yang diunggah.
                            </div>
                        @endif
                    </div>

                    {{-- Tab: Specific Facility --}}
                    @foreach($facilities as $facility)
                        <div 
                            x-show="activeFacility === {{ $facility->id }}"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="space-y-6"
                            x-cloak
                        >
                            @if($facility->description)
                                <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed max-w-3xl">
                                    {{ $facility->description }}
                                </p>
                            @endif

                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @forelse($facility->photos as $photo)
                                    <div class="group relative aspect-square rounded-[1.5rem] overflow-hidden bg-gray-100 dark:bg-gray-800 border border-gray-100 dark:border-white/5 cursor-pointer">
                                        <img 
                                            src="{{ Storage::url($photo->image_path) }}" 
                                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                            @click="openGallery('{{ Storage::url($photo->image_path) }}')"
                                        >
                                    </div>
                                @empty
                                    <div class="col-span-full py-12 text-center bg-gray-50 dark:bg-gray-800/20 rounded-3xl border border-dashed border-gray-200 dark:border-white/10 text-gray-400 text-sm">
                                        Belum ada foto untuk fasilitas ini.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Interactive Lightbox --}}
                <div 
                    x-show="lightboxOpen" 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 z-[200] flex items-center justify-center p-4 bg-black/95 backdrop-blur-md"
                    @click="lightboxOpen = false"
                    x-cloak
                >
                    {{-- Close Button --}}
                    <button @click.stop="lightboxOpen = false" class="absolute top-6 right-6 text-white/50 hover:text-white transition z-[230] p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>

                    {{-- Navigation Buttons --}}
                    <button x-show="filteredPhotos.length > 1" @click.stop="prev()" class="absolute left-4 sm:left-10 text-white/50 hover:text-white hover:scale-110 active:scale-90 transition z-[230] p-4 bg-white/5 rounded-full backdrop-blur-md border border-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" /></svg>
                    </button>
                    
                    <button x-show="filteredPhotos.length > 1" @click.stop="next()" class="absolute right-4 sm:right-10 text-white/50 hover:text-white hover:scale-110 active:scale-90 transition z-[230] p-4 bg-white/5 rounded-full backdrop-blur-md border border-white/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" /></svg>
                    </button>

                    {{-- Image Display --}}
                    <div class="relative max-w-5xl w-full h-full flex flex-col items-center justify-center gap-6" @click.stop>
                        <img 
                            :src="filteredPhotos[currentIndex]?.url" 
                            class="max-w-full max-h-[75vh] object-contain rounded-2xl shadow-2xl transition-all duration-300 transform scale-100"
                        >
                        
                        <div class="text-center">
                            <p class="text-white font-black text-2xl mb-1" x-text="filteredPhotos[currentIndex]?.facility"></p>
                            <p class="text-white/40 text-sm font-bold tracking-widest uppercase" x-text="(currentIndex + 1) + ' / ' + filteredPhotos.length"></p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Wedding & Exclusive Events Section --}}
        @if($hasWeddingFeature && $weddingPackages->count() > 0)
            <div class="mb-12">
                <div class="mb-8">
                    <h3 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight flex items-center gap-3">
                        Wedding & Exclusive Events
                        <span class="bg-amber-100 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 text-[10px] font-black px-2 py-0.5 rounded-full uppercase tracking-widest border border-amber-200 dark:border-amber-500/20">Premium</span>
                    </h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Wujudkan momen tak terlupakan Anda bersama layanan terbaik kami.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    @foreach($weddingPackages as $package)
                        <div class="group bg-white dark:bg-gray-800 rounded-3xl overflow-hidden border border-gray-100 dark:border-white/5 shadow-sm hover:shadow-xl transition-all duration-500 flex flex-col">
                            {{-- Package Image --}}
                            <div class="relative h-64 overflow-hidden">
                                <img src="{{ Storage::url($package->cover_image) }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" alt="{{ $package->name }}">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                                <div class="absolute bottom-6 left-6 right-6">
                                    <h4 class="text-white text-xl font-black leading-tight">{{ $package->name }}</h4>
                                    <p class="text-white/70 text-sm font-bold mt-1">
                                        @if($package->min_capacity || $package->max_capacity)
                                            Kapasitas {{ $package->min_capacity }}{{ $package->max_capacity ? ' - ' . $package->max_capacity : '' }} Orang
                                        @endif
                                    </p>
                                </div>
                            </div>

                            {{-- Package Content --}}
                            <div class="p-8 flex flex-col flex-grow">
                                <div class="flex items-center justify-between mb-6">
                                    <div>
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Mulai Dari</p>
                                        <p class="text-2xl font-black text-gray-900 dark:text-amber-500">
                                            @if($package->price)
                                                Rp {{ number_format($package->price, 0, ',', '.') }}
                                            @else
                                                Hubungi Kami
                                            @endif
                                        </p>
                                    </div>
                                    <a href="{{ route('frontend.wedding.show', ['restaurant' => $restaurant->slug, 'package' => $package->slug]) }}" class="bg-gray-50 dark:bg-gray-700/50 p-3 rounded-2xl hover:bg-black dark:hover:bg-amber-500 hover:text-white transition group/btn">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    </a>
                                </div>

                                <div class="space-y-3 mb-8 flex-grow">
                                    @php $points = collect($package->inclusions)->take(3); @endphp
                                    @foreach($points as $item)
                                        <div class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-400">
                                            <div class="h-1.5 w-1.5 rounded-full bg-amber-500"></div>
                                            {{ $item }}
                                        </div>
                                    @endforeach
                                    @if(count($package->inclusions ?? []) > 3)
                                        <p class="text-[11px] text-amber-500 font-bold italic">+ {{ count($package->inclusions) - 3 }} Fasilitas Lainnya</p>
                                    @endif
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <a href="{{ route('frontend.wedding.show', ['restaurant' => $restaurant->slug, 'package' => $package->slug]) }}" class="py-3 px-4 bg-gray-900 dark:bg-gray-700 text-white text-xs font-black rounded-xl hover:bg-black transition text-center uppercase tracking-widest">
                                        Lihat Detail
                                    </a>
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $restaurant->phone) }}?text={{ urlencode('Halo ' . $restaurant->name . ', saya tertarik dengan paket wedding ' . $package->name . '. Bisa dibantu rincian selengkapnya?') }}" target="_blank" class="py-3 px-4 bg-green-500 text-white text-xs font-black rounded-xl hover:bg-green-600 transition text-center flex items-center justify-center gap-2 uppercase tracking-widest text-nowrap">
                                        WhatsApp
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Customer Reviews --}}
        @if($feedbacks && $feedbacks->count() > 0)
            <div class="mb-12">
                <div class="flex items-center justify-between mb-8">
                     <h3 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Apa Kata Pelanggan Kami?</h3>
                    <div class="flex items-center gap-2">
                        <span class="text-amber-500 font-black text-xl">{{ number_format($restaurant->orderFeedbacks()->where('is_public', true)->avg('rating'), 1) }}</span>
                                 <div class="flex text-amber-500">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 {{ $i <= round($restaurant->orderFeedbacks()->where('is_public', true)->avg('rating')) ? 'fill-current' : 'text-gray-200 dark:text-gray-700' }}" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @endfor
                                </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($feedbacks as $feedback)
                         <div class="bg-gray-50 dark:bg-gray-800/50 rounded-2xl p-6 border border-gray-100 dark:border-white/5 flex flex-col h-full hover:shadow-lg transition-shadow duration-300">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-amber-200 to-amber-400 flex items-center justify-center text-amber-800 font-bold">
                                        {{ substr($feedback->order->customer_name ?: 'P', 0, 1) }}
                                    </div>
                                     <div>
                                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $feedback->order->customer_name ?: 'Pelanggan' }}</p>
                                        <p class="text-[11px] text-gray-400 dark:text-gray-500 uppercase tracking-wider font-semibold">{{ $feedback->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                 <div class="flex text-amber-500">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 {{ $i <= $feedback->rating ? 'fill-current' : 'text-gray-200 dark:text-gray-700' }}" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @endfor
                                </div>
                            </div>
                                                        <p class="text-gray-600 dark:text-gray-400 text-sm italic leading-relaxed mb-4 flex-grow">
                                "{{ $feedback->comment ?: 'Pelanggan puas dengan hidangan dan pelayanan kami.' }}"
                            </p>

                            @if($feedback->reply_comment)
                                 <div class="mt-4 p-4 bg-white dark:bg-gray-700 rounded-2xl border border-gray-100 dark:border-white/5 relative shadow-sm">
                                    <div class="absolute -top-2 left-4 px-2 bg-amber-500 text-white text-[9px] font-bold rounded uppercase">Balasan Restoran</div>
                                    <p class="text-gray-800 dark:text-gray-200 text-xs leading-relaxed">
                                        {{ $feedback->reply_comment }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Google Map Embed --}}
        @if($restaurant->google_map_embed)
             <div class="bg-gray-50 dark:bg-gray-800/50 rounded-2xl p-3 sm:p-4 border border-gray-100 dark:border-white/5 flex-shrink-0 mb-20 w-full overflow-hidden">
                <div class="w-full h-64 sm:h-80 md:h-[400px] rounded-[1.5rem] overflow-hidden bg-gray-200">
                    {!! $restaurant->google_map_embed !!}
                </div>
            </div>
            {{-- Override iframe styles inside the map container to ensure responsiveness --}}
            <style>
                .overflow-hidden iframe {
                    width: 100% !important;
                    height: 100% !important;
                    border: 0;
                }
                @keyframes pulse-slow {
                    0%, 100% { opacity: 1; transform: scale(1.05); }
                    50% { opacity: 0.85; transform: scale(1.1); }
                }
                .animate-pulse-slow {
                    animation: pulse-slow 8s infinite alternate ease-in-out;
                }
            </style>
        @endif
    </div>
    {{-- QUEUE MODAL --}}
    @if($showQueueModal)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-md" wire:click="$set('showQueueModal', false)"></div>
                <div class="relative bg-white dark:bg-gray-800 w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden animate-[scaleUp_0.3s_ease-out]">
            <button wire:click="$set('showQueueModal', false)" class="absolute top-6 right-6 p-2 text-gray-400 hover:text-black transition z-10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>

            @if(!$createdQueue)
            <div class="p-8 sm:p-12">
                <div class="text-center mb-10">
                     <h3 class="text-3xl font-black text-gray-900 dark:text-white mb-2">Ambil Antrean Online</h3>
                    <p class="text-gray-500 dark:text-gray-400 font-medium">Berapa orang yang akan hadir?</p>
                </div>

                <div class="space-y-8">
                    {{-- Counter --}}
                    <div class="flex flex-col items-center">
                        <div class="flex items-center gap-8 mb-4">
                            <button wire:click="decrementGuestCount" class="w-14 h-14 bg-gray-50 dark:bg-gray-700/50 rounded-2xl flex items-center justify-center text-2xl font-black hover:bg-black hover:text-white dark:hover:bg-primary-500 transition active:scale-90 text-gray-900 dark:text-white">−</button>
                            <span class="text-6xl font-black tracking-tight w-12 text-center text-gray-900 dark:text-white">{{ $queueGuestCount }}</span>
                            <button wire:click="incrementGuestCount" class="w-14 h-14 bg-gray-50 dark:bg-gray-700/50 rounded-2xl flex items-center justify-center text-2xl font-black hover:bg-black hover:text-white dark:hover:bg-primary-500 transition active:scale-90 text-gray-900 dark:text-white">+</button>
                        </div>
                        <span class="px-4 py-1.5 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-full text-[10px] font-black uppercase tracking-widest border border-blue-100 dark:border-blue-900/30">
                            {{ \App\Models\Queue::getPrefixByGuestCount($queueGuestCount) === 'A' ? 'Meja Kecil (1-2)' : (\App\Models\Queue::getPrefixByGuestCount($queueGuestCount) === 'B' ? 'Meja Sedang (3-5)' : 'Meja Besar (6+)') }}
                        </span>
                    </div>

                    {{-- Form Inputs --}}
                    <div class="space-y-5">
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Nama Lengkap <span class="text-red-500 font-bold">*</span></label>
                            <input type="text" wire:model="queueCustomerName" placeholder="Contoh: Budi Sudarsono" class="w-full bg-gray-50 dark:bg-gray-900/50 border-2 border-transparent focus:border-black dark:focus:border-primary-500 rounded-2xl px-6 py-4 text-lg font-bold transition-all outline-none @error('queueCustomerName') border-red-500 @enderror text-gray-900 dark:text-white">
                            @error('queueCustomerName') <span class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Nomor WhatsApp <span class="text-red-500 font-bold">*</span></label>
                            <input type="tel" wire:model="queueCustomerPhone" placeholder="0812xxxxxx" class="w-full bg-gray-50 dark:bg-gray-900/50 border-2 border-transparent focus:border-black dark:focus:border-primary-500 rounded-2xl px-6 py-4 text-lg font-bold transition-all outline-none @error('queueCustomerPhone') border-red-500 @enderror text-gray-900 dark:text-white">
                             @error('queueCustomerPhone') <span class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</span> @enderror
                             <p class="text-[10px] text-gray-400 font-medium mt-2 italic">* Nomor ini diperlukan agar kami dapat memberitahu Anda saat meja siap dipanggil.</p>
                        </div>
                    </div>

                    <button wire:click="submitQueue" wire:loading.attr="disabled" class="w-full py-5 bg-blue-600 hover:bg-blue-700 dark:bg-primary-600 dark:hover:bg-primary-500 text-white rounded-2xl font-black text-xl shadow-xl shadow-blue-500/20 transition transform active:scale-[0.98] flex items-center justify-center relative overflow-hidden group">
                        <span wire:loading.remove class="relative z-10">DAPATKAN NOMOR ANTREAN</span>
                        <div wire:loading>
                            <svg class="animate-spin h-6 w-6 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </div>
                        <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity"></div>
                    </button>
                </div>
            </div>
            @else
            <div class="p-8 sm:p-12 text-center">
                <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-8 animate-[bounce_2s_infinite] transition-all duration-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                </div>
                
                <h3 class="text-3xl font-black text-gray-900 dark:text-white mb-2 uppercase tracking-tight">Antrean Berhasil!</h3>
                <p class="text-gray-500 dark:text-gray-400 font-medium mb-10">Nomor Anda telah terdaftar dalam sistem kami.</p>

                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-3xl p-10 relative overflow-hidden group mb-10 border border-gray-100 dark:border-white/5 shadow-sm text-center">
                    <p class="text-[10px] font-black uppercase tracking-[0.4em] text-gray-400 mb-2">Nomor Antrean Online</p>
                    <span class="text-[6rem] font-black block leading-none tracking-tighter text-black dark:text-white drop-shadow-[0_10px_30px_rgba(59,130,246,0.15)]">{{ $createdQueue->full_number }}</span>
                    <div class="mt-6 flex items-center justify-center gap-2">
                         <span class="px-3 py-1 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 text-[10px] font-black rounded-lg border border-gray-100 dark:border-white/5 uppercase tracking-widest">{{ $createdQueue->guest_count }} ORANG</span>
                         <span class="px-3 py-1 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 text-[10px] font-black rounded-lg border border-gray-100 dark:border-white/5 uppercase tracking-widest">{{ \Carbon\Carbon::now()->timezone('Asia/Jakarta')->format('H:i') }}</span>
                    </div>
                </div>

                <p class="text-sm text-gray-400 font-medium mb-8 leading-relaxed px-4">
                    Mohon datang tepat waktu. Kami akan memanggil nama Anda segera setelah meja tersedia.
                </p>

                <button wire:click="$set('showQueueModal', false)" class="w-full py-5 bg-black text-white rounded-2xl font-black text-xl transition active:scale-[0.98] shadow-lg">
                    OKE, SAYA MENGERTI
                </button>
            </div>
            @endif
        </div>
    </div>
    @endif
    @if(!isset($restaurant) || !$restaurant->owner?->hasFeature('Remove Branding'))
        <x-footer-premium />
    @endif
</div>
