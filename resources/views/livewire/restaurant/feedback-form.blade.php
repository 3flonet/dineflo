<div class="min-h-screen bg-cover bg-center bg-no-repeat transition-colors duration-500 py-12 px-4" style="background-image: url('{{ $order->restaurant->cover_image ? Storage::url($order->restaurant->cover_image) : 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80' }}')">
    <div class="absolute inset-0 bg-black/60 dark:bg-black/80 backdrop-blur-sm transition-colors duration-500"></div>

    {{-- Theme Toggle --}}
    <div class="absolute top-6 right-6 z-50">
        <button @click="theme = theme === 'light' ? 'dark' : 'light'; updateTheme()" 
                class="p-2.5 rounded-full bg-white/20 dark:bg-gray-800/40 backdrop-blur-md text-white hover:bg-white hover:text-black dark:hover:bg-gray-700 transition flex items-center justify-center h-10 w-10 border border-white/20 dark:border-white/10 shadow-sm">
            <svg x-show="theme === 'light'" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
            <svg x-show="theme === 'dark' || theme === 'system'" x-cloak xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
            </svg>
        </button>
    </div>

    <div class="relative max-w-lg mx-auto">
        <!-- Logo & Branding -->
        <div class="text-center mb-10 animate-fade-in-down">
            @if($order->restaurant->logo)
                <img src="{{ Storage::url($order->restaurant->logo) }}" alt="{{ $order->restaurant->name }}" class="w-24 h-24 mx-auto rounded-full border-4 border-white/20 shadow-2xl mb-4 object-cover">
            @endif
            <h1 class="text-3xl font-bold text-white tracking-tight">{{ $order->restaurant->name }}</h1>
            <p class="text-white/70 text-sm mt-1">Beri tahu kami pengalaman Anda di meja #{{ $order->table->name ?? 'N/A' }}</p>
        </div>

        @if($isSubmitted)
            <!-- Success Message -->
            <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl p-10 text-center shadow-2xl animate-scale-up">
                <div class="w-20 h-20 bg-emerald-500/20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-white mb-2">Terima Kasih!</h2>
                <p class="text-white/70">Tanggapan Anda sangat berharga bagi kami untuk terus meningkatkan pelayanan.</p>
                <div class="mt-8">
                    <a href="{{ route('restaurant.index', $order->restaurant->slug) }}" class="inline-block px-8 py-3 bg-white text-gray-900 font-bold rounded-full transition-all hover:bg-emerald-400 hover:text-white hover:scale-105 active:scale-95">
                        Kembali ke Menu
                    </a>
                </div>
            </div>
        @elseif($alreadyReviewed)
            <!-- Already Reviewed -->
            <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl p-10 text-center shadow-2xl">
                <div class="w-20 h-20 bg-amber-500/20 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-white mb-2">Sudah Dinilai</h2>
                <p class="text-white/70">Pesanan ini sudah pernah Anda beri penilaian sebelumnya. Terima kasih atas partisipasinya!</p>
                <div class="mt-8">
                    <a href="{{ route('restaurant.index', $order->restaurant->slug) }}" class="inline-block px-8 py-3 bg-white text-gray-900 font-bold rounded-full transition-all">
                        Kembali ke Menu
                    </a>
                </div>
            </div>
        @else
            <!-- Feedback Form -->
            <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl overflow-hidden shadow-2xl">
                <div class="p-8">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-bold text-white mb-2">Bagaimana Kunjungan Anda?</h2>
                        <div class="flex justify-center gap-2 mt-4" x-data="{ currentRating: @entangle('rating') }">
                            @foreach(range(1, 5) as $i)
                                <button type="button" 
                                    wire:click="setRating({{ $i }})"
                                    class="transition-all duration-300 transform"
                                    :class="currentRating >= {{ $i }} ? 'text-amber-400 scale-125' : 'text-white/20 hover:text-amber-200'"
                                >
                                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </button>
                            @endforeach
                        </div>
                        <p class="text-white/60 text-sm mt-3" x-show="rating">
                            @php
                                $labels = [1 => 'Mengecewakan', 2 => 'Kurang Puas', 3 => 'Biasa Saja', 4 => 'Sangat Baik', 5 => 'Luar Biasa!'];
                            @endphp
                            {{ $labels[$rating] ?? '' }}
                        </p>
                    </div>

                    <div class="space-y-8 mt-10">
                        <!-- Individual Category Ratings -->
                        <div class="grid grid-cols-1 gap-6">
                            <!-- Food -->
                            <div class="space-y-4">
                                <div class="flex justify-between items-center text-white">
                                    <span class="font-medium">Rasa Makanan</span>
                                    <span class="text-amber-400 font-bold" x-text="$wire.food_rating + ' / 5'"></span>
                                </div>
                                <input type="range" wire:model.live="food_rating" min="1" max="5" class="w-full h-2 bg-white/20 rounded-lg appearance-none cursor-pointer accent-emerald-500">
                            </div>

                            <!-- Service -->
                            <div class="space-y-4">
                                <div class="flex justify-between items-center text-white">
                                    <span class="font-medium">Kualitas Pelayanan</span>
                                    <span class="text-amber-400 font-bold" x-text="$wire.service_rating + ' / 5'"></span>
                                </div>
                                <input type="range" wire:model.live="service_rating" min="1" max="5" class="w-full h-2 bg-white/20 rounded-lg appearance-none cursor-pointer accent-emerald-500">
                            </div>

                            <!-- Ambience -->
                            <div class="space-y-4">
                                <div class="flex justify-between items-center text-white">
                                    <span class="font-medium">Kebersihan & Suasana</span>
                                    <span class="text-amber-400 font-bold" x-text="$wire.ambience_rating + ' / 5'"></span>
                                </div>
                                <input type="range" wire:model.live="ambience_rating" min="1" max="5" class="w-full h-2 bg-white/20 rounded-lg appearance-none cursor-pointer accent-emerald-500">
                            </div>
                        </div>

                        <!-- Comment Area -->
                        <div class="space-y-3">
                            <label class="text-white font-medium block">Tulis komentar Anda (opsional)</label>
                            <textarea 
                                wire:model="comment" 
                                class="w-full bg-white/10 border border-white/20 rounded-2xl p-4 text-white placeholder-white/30 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all outline-none"
                                placeholder="Apa yang Anda suka atau perbaiki dari kunjungan ini?"
                                rows="4"
                            ></textarea>
                            @error('comment') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <button 
                            wire:click="submit"
                            wire:loading.attr="disabled"
                            class="w-full py-4 bg-emerald-500 hover:bg-emerald-600 disabled:opacity-50 text-white font-bold rounded-2xl shadow-xl shadow-emerald-900/20 transform transition-all active:scale-95 flex items-center justify-center gap-3"
                        >
                            <span wire:loading.remove>Kirim Penilaian</span>
                            <span wire:loading class="flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Mengirim...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <footer class="mt-12 text-center text-white/40 text-xs">
            &copy; {{ date('Y') }} Powered by Dineflo
        </footer>
        <style>
            @keyframes fade-in-down {
                0% { opacity: 0; transform: translateY(-20px); }
                100% { opacity: 1; transform: translateY(0); }
            }
            @keyframes scale-up {
                0% { opacity: 0; transform: scale(0.9); }
                100% { opacity: 1; transform: scale(1); }
            }
            .animate-fade-in-down { animation: fade-in-down 0.6s ease-out; }
            .animate-scale-up { animation: scale-up 0.4s ease-out; }

            /* Custom scrollbar for range input if needed */
            input[type=range]::-webkit-slider-thumb {
                border: 4px solid white;
                height: 24px;
                width: 24px;
                border-radius: 50%;
                background: #10b981;
                cursor: pointer;
                -webkit-appearance: none;
                margin-top: -8px; 
            }
        </style>
    </div>
</div>
