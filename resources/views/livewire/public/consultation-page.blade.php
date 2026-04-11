<div class="bg-white dark:bg-[#0B0F19] text-gray-600 dark:text-gray-300 min-h-screen font-sans selection:bg-indigo-500 selection:text-white overflow-x-hidden transition-colors duration-300">
    <style>
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
    </style>

    <!-- Header / Navbar Space -->
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
                    <span class="font-bold text-2xl tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-amber-500 to-orange-600">{{ $settings->site_name }}</span>
                </a>
                <div class="hidden md:flex space-x-8 items-center">
                    <a href="{{ route('home') }}" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-white transition">Beranda</a>
                    <a href="{{ route('home') }}#fitur" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-white transition">Fitur</a>
                    <a href="{{ route('community') }}" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-white transition">Community</a>
                    <a href="{{ route('consultation') }}" class="text-sm font-medium text-primary-600 dark:text-white transition border-b-2 border-primary-500">Konsultasi</a>
                    
                    <div class="w-px h-5 bg-gray-200 dark:bg-gray-700"></div>
                    
                    <a href="{{ route('filament.restaurant.auth.login') }}" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-white transition">Login</a>
                    <a href="{{ route('filament.restaurant.auth.register') }}" class="px-5 py-2.5 rounded-full bg-primary-600 hover:bg-primary-500 text-white text-sm font-bold transition shadow-sm">
                        Mulai Gratis
                    </a>
                </div>

                {{-- Mobile Menu Button --}}
                <div class="md:hidden flex items-center gap-2">
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
                <a href="{{ route('home') }}" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Beranda</a>
                <a href="{{ route('community') }}" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Community</a>
                <a href="{{ route('consultation') }}" class="block text-sm font-medium text-primary-600 dark:text-white">Konsultasi</a>
                <div class="h-px bg-gray-200 dark:bg-gray-800"></div>
                <a href="{{ route('filament.restaurant.auth.login') }}" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Login Restoran</a>
                <a href="{{ route('filament.restaurant.auth.register') }}" class="block w-full py-3 text-center rounded-xl bg-primary-600 text-white font-bold text-sm shadow-lg">Daftar Sekarang</a>
            </div>
        </div>
    </nav>
    <div class="h-20"></div>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 relative">
        <!-- Background Glows -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[400px] bg-primary-500/10 dark:bg-indigo-600/20 blur-[100px] rounded-full pointer-events-none"></div>

        <div class="text-center mb-16 relative z-10">
            <h1 class="text-3xl md:text-5xl font-extrabold text-gray-900 dark:text-white tracking-tight mb-4">
                Konsultasi <span class="text-gradient">Gratis</span> Bersama Tim Kami
            </h1>
            <p class="text-gray-500 dark:text-gray-400 max-w-2xl mx-auto text-lg">
                Ingin tahu bagaimana {{ $settings->site_name }} bisa mempercepat pertumbuhan bisnis kuliner Anda? Tim kami siap membantu menjawab semua keraguan Anda.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start relative z-10">
            <!-- Left: Contact Details -->
            <div class="space-y-8">
                <div class="glass-panel p-8 rounded-3xl">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Informasi Kontak</h2>
                    
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 flex items-center justify-center shrink-0 border border-indigo-500/20">
                                <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white">Alamat Kami</h3>
                                <p class="text-gray-500 dark:text-gray-400 mt-1">{{ $settings->site_address ?: 'Jl. Modern Business Park No. 123, Jakarta' }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 flex items-center justify-center shrink-0 border border-emerald-500/20">
                                <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white">Telepon / WhatsApp</h3>
                                <p class="text-gray-500 dark:text-gray-400 mt-1">{{ $settings->site_phone ?: '+62 812 XXXX XXXX' }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-purple-500/10 flex items-center justify-center shrink-0 border border-purple-500/20">
                                <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white">Email Support</h3>
                                <p class="text-gray-500 dark:text-gray-400 mt-1">{{ $settings->support_email ?: 'support@dineflo.com' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if($settings->site_google_maps_embed)
                <div class="glass-panel overflow-hidden rounded-3xl h-64 border-none shadow-xl">
                    {!! str_replace('<iframe ', '<iframe class="w-full h-full border-0" ', $settings->site_google_maps_embed) !!}
                </div>
                @endif
            </div>

            <!-- Right: Contact Form -->
            <div class="glass-panel p-8 rounded-3xl relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 bg-indigo-500/10 rounded-bl-3xl">
                    <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </div>

                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8">Kirim Permintaan Konsultasi</h2>

                @if (session()->has('message'))
                    <div class="mb-8 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 animate-pulse">
                        {{ session('message') }}
                    </div>
                @endif

                <form wire:submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-700 dark:text-gray-300">Nama Lengkap</label>
                            <input type="text" wire:model="name" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all placeholder:text-gray-400" placeholder="Contoh: Budi Santoso">
                            @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-700 dark:text-gray-300">Email Kerja</label>
                            <input type="email" wire:model="email" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all placeholder:text-gray-400" placeholder="budi@example.com">
                            @error('email') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-700 dark:text-gray-300">Nomor Telepon / WA (Opsional)</label>
                            <input type="text" wire:model="phone" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all placeholder:text-gray-400" placeholder="0812XXXXXXXX">
                            @error('phone') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-gray-700 dark:text-gray-300">Topik Konsultasi</label>
                            <select wire:model="subject" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all">
                                <option value="Konsultasi Layanan">Konsultasi Layanan (Umum)</option>
                                <option value="Demo Platform">Request Demo Platform</option>
                                <option value="Partnership">Kerjasama (Partnership)</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                            @error('subject') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-bold text-gray-700 dark:text-gray-300">Ceritakan Bisnis & Kebutuhan Anda</label>
                        <textarea wire:model="message" rows="4" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all placeholder:text-gray-400" placeholder="Tuliskan tantangan yang Anda hadapi atau fitur apa yang paling Anda butuhkan..."></textarea>
                        @error('message') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="w-full py-4 rounded-xl bg-gradient-to-r from-primary-600 to-indigo-600 hover:from-primary-500 hover:to-indigo-500 text-white font-bold text-lg transition shadow-xl shadow-primary-500/20 transform hover:-translate-y-1 flex items-center justify-center gap-2 group">
                        <span wire:loading.remove>Kirim Sekarang</span>
                        <span wire:loading>Mengirim...</span>
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </main>

    <div class="h-24"></div>
</div>
