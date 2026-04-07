<div class="min-h-screen bg-gray-50 dark:bg-[#0B0F19] transition-colors duration-300 pb-20" wire:poll.5s x-data>
    {{-- Header --}}
    <div class="sticky top-0 z-50 bg-white/80 dark:bg-[#0B0F19]/80 backdrop-blur-md shadow-sm border-b dark:border-white/5 transition-colors">
        <div class="max-w-xl mx-auto px-4 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                @if($reservation->restaurant->logo)
                    <img src="{{ Storage::url($reservation->restaurant->logo) }}" alt="{{ $reservation->restaurant->name }}" class="w-8 h-8 rounded-lg object-cover">
                @endif
                <h1 class="font-bold text-gray-900 dark:text-white">{{ $reservation->restaurant->name }}</h1>
            </div>
            
            <div class="flex items-center gap-2">
                {{-- Theme Toggle --}}
                <button @click="theme = theme === 'light' ? 'dark' : 'light'; updateTheme()" 
                        class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/10 transition-all flex items-center justify-center">
                    <svg x-show="theme === 'light'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <svg x-show="theme === 'dark' || theme === 'system'" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Hero Section --}}
    <div class="relative w-full h-48 bg-black overflow-hidden shrink-0">
        @if($reservation->restaurant->cover_image)
            <img src="{{ Storage::url($reservation->restaurant->cover_image) }}" class="w-full h-full object-cover opacity-40" alt="Cover">
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-gray-50 dark:from-[#0B0F19] to-transparent"></div>
        
        <div class="absolute bottom-10 left-0 right-0 text-center px-4">
            <h1 class="text-2xl font-black text-white tracking-tight">Status Reservasi</h1>
            <p class="text-gray-300 text-sm mt-1">#{{ strtoupper(substr($reservation->tracking_hash, 0, 8)) }}</p>
        </div>
    </div>

    <div class="max-w-xl mx-auto px-4 -mt-6 relative z-10">
        {{-- Status Card --}}
        <div class="bg-white dark:bg-gray-900 rounded-[2rem] p-8 shadow-xl dark:shadow-none border border-gray-100 dark:border-white/5 text-center mb-6 transition-colors">
            @php
                $statusColors = [
                    'pending' => 'bg-amber-100 text-amber-600 border-amber-200 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-800/50',
                    'confirmed' => 'bg-green-100 text-green-600 border-green-200 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800/50',
                    'cancelled' => 'bg-red-100 text-red-600 border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800/50',
                ];
                $statusLabels = [
                    'pending' => 'Menunggu Konfirmasi',
                    'confirmed' => 'Berhasil Dikonfirmasi',
                    'cancelled' => 'Dibatalkan',
                ];
            @endphp

            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border {{ $statusColors[$reservation->status] ?? 'bg-gray-100 dark:bg-gray-800 text-gray-500' }} text-[11px] font-black uppercase tracking-wider mb-6">
                {{ $statusLabels[$reservation->status] ?? $reservation->status }}
            </div>

            <h2 class="text-3xl font-black text-gray-900 dark:text-white mb-2">{{ $reservation->name }}</h2>
            <p class="text-gray-500 dark:text-gray-400 font-medium mb-8">{{ $reservation->guest_count }} Tamu • {{ $reservation->reservation_time->format('d M Y, H:i') }}</p>

            @if($reservation->status === 'confirmed')
                <div class="bg-green-50 dark:bg-green-900/10 rounded-3xl p-6 border border-green-100 dark:border-green-900/20 mb-6">
                    <p class="text-xs font-bold text-green-800 dark:text-green-400 uppercase tracking-widest mb-3">Nomor Meja Anda</p>
                    <div class="text-5xl font-black text-green-600 dark:text-green-500 tracking-tighter">
                        {{ $reservation->table?->name ?? '--' }}
                    </div>
                    <p class="text-xs text-green-700 dark:text-green-600/80 mt-3 font-medium mb-4">Silakan tunjukkan halaman ini kepada staf kami saat Anda tiba.</p>
                    
                    <button
                        id="btn-share-track"
                        onclick="shareTracking()"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-white dark:bg-gray-800 text-green-700 dark:text-green-400 rounded-xl border border-green-200 dark:border-green-800 text-xs font-black uppercase tracking-wider hover:bg-green-50 dark:hover:bg-gray-700 transition shadow-sm group"
                    >
                        <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
                        <span id="share-btn-text">Bagikan Status Ini</span>
                    </button>

                    <div id="share-toast" class="hidden mt-3 text-center text-[11px] font-bold text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 py-2 px-4 rounded-xl animate-pulse">
                        ✅ Link berhasil disalin!
                    </div>
                </div>
            @elseif($reservation->status === 'pending')
                <div class="bg-amber-50 dark:bg-amber-900/10 rounded-3xl p-6 border border-amber-100 dark:border-amber-900/20 mb-6">
                    <p class="text-xs font-bold text-amber-800 dark:text-amber-400 uppercase tracking-widest mb-1">Permintaan Diproses</p>
                    <p class="text-sm text-amber-700 dark:text-amber-600 font-medium leading-relaxed">Tim kami sedang mengecek ketersediaan meja. Kami akan segera mengonfirmasi pesanan Anda.</p>
                </div>
            @endif

            <div class="grid grid-cols-2 gap-4 text-left p-4 bg-gray-50 dark:bg-gray-800 rounded-2xl">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Waktu</p>
                    <p class="text-sm font-black text-gray-800 dark:text-gray-200">{{ $reservation->reservation_time->format('H:i') }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Tanggal</p>
                    <p class="text-sm font-black text-gray-800 dark:text-gray-200">{{ $reservation->reservation_time->format('d M Y') }}</p>
                </div>
            </div>
        </div>

        {{-- Contact Info --}}
        <div class="bg-white dark:bg-gray-900 rounded-[2rem] p-6 shadow-md dark:shadow-none border border-gray-100 dark:border-white/5 flex items-center gap-4 transition-colors">
            <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-400 dark:text-gray-500 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
            </div>
            <div class="flex-grow">
                <p class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Butuh bantuan?</p>
                <p class="text-sm font-black text-gray-900 dark:text-white">Hubungi {{ $reservation->restaurant->name }}</p>
            </div>
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $reservation->restaurant->whatsapp_number ?? $reservation->restaurant->phone) }}" class="w-10 h-10 rounded-full bg-green-500 text-white flex items-center justify-center hover:scale-110 transition-transform">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.652zm11.837-21.502c-5.308 0-9.623 4.316-9.626 9.625-.001 1.956.591 3.844 1.708 5.426l-.92 3.361 3.45-.905c1.523.832 3.242 1.27 5.004 1.272h.004c5.307 0 9.623-4.314 9.626-9.625.002-2.571-1-4.987-2.817-6.806-1.818-1.819-4.234-2.822-6.809-2.822zm5.277 12.753c-.289-.144-1.713-.846-1.979-.942-.265-.096-.458-.144-.65.144-.193.289-.748.942-.916 1.135-.169.193-.337.217-.627.072-.29-.144-1.224-.451-2.332-1.439-.861-.768-1.443-1.716-1.611-2.005-.169-.289-.018-.445.127-.589.13-.13.29-.337.434-.506.144-.169.193-.289.289-.482.096-.193.048-.361-.024-.506-.072-.144-.65-1.566-.89-2.144-.233-.563-.473-.487-.65-.496-.169-.008-.361-.01-.554-.01s-.506.072-.771.361c-.265.289-1.012.988-1.012 2.41s1.036 2.795 1.181 2.988c.144.193 2.035 3.108 4.93 4.358.689.297 1.227.474 1.646.608.692.219 1.32.188 1.816.114.554-.082 1.713-.7 1.954-1.373.241-.673.241-1.253.169-1.373-.072-.12-.265-.193-.554-.337z"/></svg>
            </a>
        </div>

        <div class="mt-8 text-center text-gray-400 dark:text-gray-500 text-xs font-medium">
            <p>&copy; {{ date('Y') }} {{ $reservation->restaurant->name }} — Powered by Dineflo</p>
        </div>
    </div>
    
    @if($reservation->status === 'confirmed')
    <script>
        function shareTracking() {
            const shareData = {
                title: 'Status Reservasi {{ addslashes($reservation->name) }}',
                text: 'Cek status reservasi saya di {{ addslashes($reservation->restaurant->name) }} - Meja: {{ $reservation->table?->name ?? "--" }}',
                url: window.location.href
            };
            
            const showSuccess = () => {
                const toast = document.getElementById('share-toast');
                const btnText = document.getElementById('share-btn-text');
                if(toast) toast.classList.remove('hidden');
                if(btnText) btnText.textContent = 'Link Disalin!';
                setTimeout(() => {
                    if(toast) toast.classList.add('hidden');
                    if(btnText) btnText.textContent = 'Bagikan Status Ini';
                }, 3000);
            };

            if (navigator.share && navigator.canShare && navigator.canShare(shareData)) {
                navigator.share(shareData).catch(() => {});
            } else {
                navigator.clipboard.writeText(shareData.url).then(showSuccess);
            }
        }
    </script>
    @endif
</div>
