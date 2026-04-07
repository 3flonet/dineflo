<div class="min-h-screen bg-[#F8FAFC] dark:bg-[#020617] transition-colors duration-500 pb-20 font-sans selection:bg-indigo-500/30">
    {{-- Background Decorative Elements --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-indigo-500/5 blur-[120px] rounded-full animate-pulse"></div>
        <div class="absolute top-[20%] -right-[5%] w-[30%] h-[30%] bg-blue-500/5 blur-[100px] rounded-full animate-pulse-slow"></div>
    </div>

    {{-- Top Cover Section --}}
    <div class="relative w-full h-[45vh] lg:h-[55vh] bg-gray-900 overflow-hidden z-10">
        @if($restaurant->cover_image)
            <img src="{{ Storage::url($restaurant->cover_image) }}" class="w-full h-full object-cover opacity-70 transform hover:scale-105 transition-transform duration-10000" alt="{{ $restaurant->name }}">
        @else
            <div class="absolute inset-0 bg-gradient-to-br from-[#0F172A] via-[#1E293B] to-[#0F172A]"></div>
        @endif
        
        {{-- Overlay & Gradient --}}
        <div class="absolute inset-0 bg-gradient-to-t from-[#F8FAFC] dark:from-[#020617] via-transparent to-black/30"></div>
        
        {{-- Top Bar Navigation --}}
        <div class="absolute top-8 left-4 right-4 max-w-7xl mx-auto flex items-center justify-between z-30">
            <a href="{{ route('frontend.restaurants.show', $restaurant->slug) }}" class="group flex items-center gap-3 bg-white/10 dark:bg-black/20 backdrop-blur-xl px-4 py-2 rounded-2xl border border-white/20 hover:bg-white hover:text-indigo-600 dark:hover:bg-white transition-all duration-300 shadow-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
                <span class="text-xs font-black uppercase tracking-widest">Kembali</span>
            </a>

            <div class="flex gap-3">
                <button @click="theme = theme === 'light' ? 'dark' : 'light'; updateTheme()" 
                        class="p-3 rounded-2xl bg-white/10 dark:bg-black/20 backdrop-blur-xl border border-white/20 text-white hover:scale-110 transition-all shadow-xl">
                    <svg x-show="theme === 'light'" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <svg x-show="theme === 'dark' || theme === 'system'" x-cloak xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Hero Header Content --}}
        <div class="absolute bottom-20 left-0 right-0 text-center px-6 max-w-4xl mx-auto">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white text-[10px] font-black uppercase tracking-[0.3em] mb-6 shadow-2xl animate-bounce-slow">
                <span class="flex h-2 w-2 rounded-full bg-indigo-400"></span>
                Instant Table Reservation
            </div>
            <h1 class="text-4xl md:text-6xl font-black text-white px-2 mb-4 tracking-[-0.04em] drop-shadow-2xl">
                Amankan Kursi Utama <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-300 to-blue-200">Anda</span>
            </h1>
            <p class="text-indigo-950/70 dark:text-indigo-100/90 text-lg font-bold tracking-tight">{{ $restaurant->name }}</p>
        </div>
    </div>

    {{-- Content Area --}}
    <div class="max-w-6xl mx-auto px-4 lg:px-8 relative z-20">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 -mt-16">
            
            {{-- Left Side: Operational Info & Experience --}}
            <div class="lg:col-span-5 space-y-6 order-2 lg:order-1">
                {{-- Experience Card --}}
                <div class="hidden lg:block bg-white dark:bg-gray-900/50 backdrop-blur-xl rounded-[2.5rem] p-10 border border-gray-200 dark:border-white/5 shadow-2xl shadow-indigo-500/5">
                    <h3 class="text-xl font-black text-gray-900 dark:text-white mb-6">Nikmati Pengalaman Terbaik</h3>
                    <div class="space-y-8">
                        <div class="flex gap-5 group">
                            <div class="w-14 h-14 shrink-0 rounded-2xl bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                                ✨
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white mb-1">Meja Strategis</h4>
                                <p class="text-sm text-gray-500 leading-relaxed">Pilih meja favorit Anda atau biarkan kami merekomendasikan sudut terbaik.</p>
                            </div>
                        </div>
                        <div class="flex gap-5 group">
                            <div class="w-14 h-14 shrink-0 rounded-2xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                                📱
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white mb-1">Live Tracking</h4>
                                <p class="text-sm text-gray-500 leading-relaxed">Pantau status kesiapan meja Anda secara real-time melalui WhatsApp.</p>
                            </div>
                        </div>
                        <div class="flex gap-5 group">
                            <div class="w-14 h-14 shrink-0 rounded-2xl bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                                🍳
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white mb-1">Priority Service</h4>
                                <p class="text-sm text-gray-500 leading-relaxed">Pesanan Anda akan langsung diprioritaskan oleh tim dapur kami.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Operational Hours --}}
                <div class="bg-indigo-600 rounded-[2.5rem] p-10 text-white shadow-2xl shadow-indigo-500/20 relative overflow-hidden group">
                    <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="p-2 bg-white/20 rounded-xl backdrop-blur-md">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <h3 class="text-lg font-black uppercase tracking-widest">Jam Operasional</h3>
                        </div>
                        <div class="space-y-4">
                            @php
                                $daysMap = ['monday' => 'Senin', 'tuesday' => 'Selasa', 'wednesday' => 'Rabu', 'thursday' => 'Kamis', 'friday' => 'Jumat', 'saturday' => 'Sabtu', 'sunday' => 'Minggu'];
                            @endphp
                            @foreach($restaurant->opening_hours as $hours)
                                <div class="flex justify-between items-center pb-3 border-b border-white/10 group/row">
                                    <span class="text-xs font-bold uppercase tracking-wider text-white/60 group-hover/row:text-white transition-colors">{{ $daysMap[$hours['day']] ?? ucfirst($hours['day']) }}</span>
                                    @if($hours['is_closed'] ?? false)
                                        <span class="text-[10px] font-black px-2 py-1 bg-red-500/30 rounded-lg text-red-200">TUTUP</span>
                                    @else
                                        <span class="text-sm font-black">{{ \Carbon\Carbon::parse($hours['open'])->format('H:i') }} — {{ \Carbon\Carbon::parse($hours['close'])->format('H:i') }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Side: The Form --}}
            <div class="lg:col-span-7 order-1 lg:order-2">
                <div class="bg-white dark:bg-gray-900 rounded-[2.5rem] p-8 sm:p-12 border border-gray-200 dark:border-white/5 shadow-2xl transition-all duration-500">
                    
                    @if($success)
                        <div class="text-center py-6">
                            <div class="relative w-32 h-32 mx-auto mb-10">
                                <div class="absolute inset-0 bg-green-500/20 blur-2xl rounded-full animate-pulse"></div>
                                <div class="relative w-full h-full bg-green-500 text-white rounded-full flex items-center justify-center shadow-2xl border-[10px] border-green-50 dark:border-green-950">
                                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            </div>
                            
                            <h2 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white mb-4 tracking-tighter">Reservasi Terkirim!</h2>
                            <p class="text-gray-500 dark:text-gray-400 text-lg mb-10 max-w-md mx-auto leading-relaxed">
                                Yeay! Permintaan telah kami terima. Kami akan segera memproses dan memberitahu Anda lewat WhatsApp.
                            </p>

                            @if($lastReservation)
                            <div class="bg-gray-50 dark:bg-white/5 p-8 rounded-[2rem] border border-gray-100 dark:border-white/5 mb-10 text-left">
                                <label class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-4 block">Tracking Tautan Anda</label>
                                <div class="flex items-center gap-3 bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-200 dark:border-white/10 shadow-sm">
                                    <span class="text-indigo-600 dark:text-indigo-400 truncate text-[13px] font-bold flex-grow">
                                        {{ route('reservations.track', $lastReservation->tracking_hash) }}
                                    </span>
                                    <button type="button" @click="navigator.clipboard.writeText('{{ route('reservations.track', $lastReservation->tracking_hash) }}'); $dispatch('notify', {type:'success', message: 'Tautan disalin!'})" class="p-3 bg-gray-50 dark:bg-gray-700 rounded-xl hover:bg-black hover:text-white dark:hover:bg-white dark:hover:text-black transition-all shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                    </button>
                                </div>
                            </div>
                            @endif

                            <div class="flex flex-col sm:flex-row gap-4">
                                <a href="{{ route('reservations.track', $lastReservation?->tracking_hash ?? '#') }}" class="flex-grow py-5 px-8 rounded-2xl bg-black dark:bg-white text-white dark:text-black font-black text-sm uppercase tracking-widest shadow-2xl hover:scale-105 active:scale-95 transition-all">
                                    Track Sekarang
                                </a>
                                <a href="{{ route('frontend.restaurants.show', $restaurant->slug) }}" class="flex-grow py-5 px-8 rounded-2xl bg-gray-100 dark:bg-white/5 text-gray-700 dark:text-gray-300 font-black text-sm uppercase tracking-widest hover:bg-gray-200 dark:hover:bg-white/10 transition-all">
                                    Selesai
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="mb-10 text-center sm:text-left">
                            <h2 class="text-3xl font-black text-gray-900 dark:text-white mb-2 tracking-tight flex items-center gap-3">
                                <span class="text-indigo-600 truncate">Pesan Meja</span>
                            </h2>
                            <p class="text-gray-500 font-medium">Beri tahu kami detail kedatangan Anda.</p>
                        </div>

                        <form wire:submit.prevent="submit" class="space-y-8">
                            {{-- Field Group: Identity --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2 md:col-span-2">
                                    <label class="text-xs font-black uppercase tracking-widest text-gray-400">
                                        Nama Anda
                                    </label>
                                    <input wire:model="name" type="text" class="w-full bg-gray-50 dark:bg-gray-800/50 p-5 rounded-2xl border-2 border-transparent focus:bg-white dark:focus:bg-gray-800 focus:border-indigo-500 outline-none transition-all font-bold text-gray-900 dark:text-white placeholder:text-gray-300 dark:placeholder:text-gray-600" placeholder="Nama Lengkap">
                                    @error('name') <p class="text-[11px] text-red-500 font-bold italic mt-1 ml-2">{{ $message }}</p> @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="text-xs font-black uppercase tracking-widest text-gray-400">
                                        WhatsApp
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-5 top-1/2 -translate-y-1/2 font-black text-gray-400 border-r border-gray-200 dark:border-white/10 pr-4">+62</span>
                                        <input wire:model="phone" type="tel" class="w-full bg-gray-50 dark:bg-gray-800/50 py-5 pr-5 pl-20 rounded-2xl border-2 border-transparent focus:bg-white dark:focus:bg-gray-800 focus:border-indigo-500 outline-none transition-all font-bold text-gray-900 dark:text-white placeholder:text-gray-300 dark:placeholder:text-gray-600" placeholder="812xxx">
                                    </div>
                                    @error('phone') <p class="text-[11px] text-red-500 font-bold italic mt-1 ml-2">{{ $message }}</p> @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="text-xs font-black uppercase tracking-widest text-gray-400">
                                        Email (Opsional)
                                    </label>
                                    <input wire:model="email" type="email" class="w-full bg-gray-50 dark:bg-gray-800/50 p-5 rounded-2xl border-2 border-transparent focus:bg-white dark:focus:bg-gray-800 focus:border-indigo-500 outline-none transition-all font-bold text-gray-900 dark:text-white placeholder:text-gray-300 dark:placeholder:text-gray-600" placeholder="mail@example.com">
                                    @error('email') <p class="text-[11px] text-red-500 font-bold italic mt-1 ml-2">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            {{-- Field Group: Date & Time --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-xs font-black uppercase tracking-widest text-gray-400">
                                        Tanggal
                                    </label>
                                    <input wire:model.live="date" type="date" class="w-full bg-gray-50 dark:bg-gray-800/50 p-5 rounded-2xl border-2 border-transparent focus:bg-white dark:focus:bg-gray-800 focus:border-indigo-500 outline-none transition-all font-black text-gray-900 dark:text-white">
                                    @error('date') <p class="text-[11px] text-red-500 font-bold italic mt-1 ml-2">{{ $message }}</p> @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="text-xs font-black uppercase tracking-widest text-gray-400">Jumlah Tamu</label>
                                    <div class="flex items-center gap-4 bg-gray-50 dark:bg-gray-800/50 p-2 rounded-2xl border-2 border-transparent">
                                        <button type="button" @click="$wire.set('guest_count', Math.max(1, $wire.guest_count - 1))" class="w-12 h-12 rounded-xl bg-white dark:bg-gray-700 shadow-sm flex items-center justify-center hover:bg-gray-100 transition-all font-black text-xl text-gray-900 dark:text-white">−</button>
                                        <div class="flex-grow text-center">
                                            <span class="text-2xl font-black text-gray-900 dark:text-white">{{ $guest_count }}</span>
                                            <span class="text-[10px] font-black uppercase text-gray-400 ml-1">PPL</span>
                                        </div>
                                        <button type="button" @click="$wire.set('guest_count', parseInt($wire.guest_count) + 1)" class="w-12 h-12 rounded-xl bg-white dark:bg-gray-700 shadow-sm flex items-center justify-center hover:bg-gray-100 transition-all font-black text-xl text-gray-900 dark:text-white">+</button>
                                    </div>
                                </div>
                            </div>

                            {{-- Time Slots Selector --}}
                            <div class="space-y-4">
                                <label class="text-xs font-black uppercase tracking-widest text-gray-400">
                                    Jam Kedatangan
                                </label>

                                @if(count($timeSlots) > 0)
                                    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">
                                        @foreach($timeSlots as $slot)
                                            <button 
                                                type="button"
                                                wire:click="selectTime('{{ $slot }}')"
                                                class="group relative py-4 px-2 rounded-2xl border-2 transition-all duration-300 {{ $time === $slot ? 'bg-indigo-600 border-indigo-600 shadow-[0_10px_20px_-5px_rgba(79,70,229,0.5)]' : 'bg-gray-50 dark:bg-gray-800/30 border-transparent hover:border-gray-200 dark:hover:border-white/10' }}"
                                            >
                                                <span class="relative z-10 text-sm font-black {{ $time === $slot ? 'text-white' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white' }}">
                                                    {{ $slot }}
                                                </span>
                                            </button>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="bg-amber-500/10 rounded-[2rem] p-8 text-center border-2 border-dashed border-amber-500/20">
                                        <p class="text-sm font-black text-amber-600 dark:text-amber-400 mb-1">Maaf Restoran Tutup</p>
                                        <p class="text-xs text-amber-700/60 dark:text-amber-500/60 font-medium italic">Silakan pilih tanggal lain untuk ketersediaan jam.</p>
                                    </div>
                                @endif
                                @error('time') <p class="text-[11px] text-red-500 font-bold italic mt-2 ml-2">{{ $message }}</p> @enderror
                                <input type="hidden" wire:model="time">
                            </div>

                            {{-- Notes --}}
                            <div class="space-y-2">
                                <label class="text-xs font-black uppercase tracking-widest text-gray-400">
                                    Pesan Khusus (Opsional)
                                </label>
                                <textarea wire:model="notes" rows="3" class="w-full bg-gray-50 dark:bg-gray-800/50 p-5 rounded-2xl border-2 border-transparent focus:bg-white dark:focus:bg-gray-800 focus:border-indigo-500 outline-none transition-all font-bold text-gray-900 dark:text-white placeholder:text-gray-300 dark:placeholder:text-gray-600 resize-none" placeholder="Alergi makanan, kejutan ulang tahun, posisi meja..."></textarea>
                                @error('notes') <p class="text-[11px] text-red-500 font-bold italic mt-1 ml-2">{{ $message }}</p> @enderror
                            </div>

                            {{-- Submit Button --}}
                            <div class="pt-6">
                                <button type="submit" wire:loading.attr="disabled" class="w-full group relative overflow-hidden bg-black dark:bg-white text-white dark:text-black p-6 rounded-2xl font-black text-sm uppercase tracking-widest shadow-2xl hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50">
                                    <div class="absolute inset-0 bg-gradient-to-r from-indigo-500 to-blue-500 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                    <span class="relative z-10 flex items-center justify-center gap-3">
                                        <span wire:loading.remove>Amankan Meja Sekarang</span>
                                        <span wire:loading class="flex items-center gap-2">
                                            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Memproses Pesanan...
                                        </span>
                                    </span>
                                </button>
                                <p class="text-center text-[10px] uppercase font-black tracking-widest text-gray-400 mt-5">🔒 Secure & Encrypted by Dineflo Gateway</p>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes pulse-slow {
            0%, 100% { opacity: 0.05; transform: scale(1); }
            50% { opacity: 0.1; transform: scale(1.1); }
        }
        .animate-pulse-slow {
            animation: pulse-slow 8s infinite ease-in-out;
        }
        @keyframes bounce-slow {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }
        .animate-bounce-slow {
            animation: bounce-slow 4s infinite ease-in-out;
        }
        
        /* Fix Date Picker Icon Color in Dark Mode */
        .dark input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1) opacity(0.6);
            cursor: pointer;
        }
        .dark input[type="date"]::-webkit-calendar-picker-indicator:hover {
            opacity: 1;
        }
    </style>
</div>
