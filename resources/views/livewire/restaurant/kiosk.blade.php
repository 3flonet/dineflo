<div class="flex-1 flex flex-col h-full relative" x-data="{ 
    showCart: false,
    handleSnap(token) {
        console.log('Initiating Snap with token:', token);
        if (token.includes('MOCK_TOKEN')) {
            alert('MOCK MODE: Mensimulasikan pembayaran sukses (karena menggunakan server key sandbox placeholder).');
            $wire.markAsPaidAndDone();
            return;
        }
        if (!window.snap) {
            alert('Layanan pembayaran sedang dimuat, harap tunggu sebentar...');
            return;
        }
        window.snap.pay(token, {
            onSuccess: (res) => { $wire.markAsPaidAndDone() },
            onPending: (res) => { $wire.markAsPaidAndDone() },
            onError: (res) => { alert('Kesalahan Pembayaran!') },
            onClose: () => { console.log('Pembayaran dibatalkan') }
        });
    }
}">

    {{-- VIEW 1: WELCOME SCREEN --}}
    @if($currentView === 'welcome')
        <div class="h-full w-full relative overflow-hidden text-white flex flex-col">
            {{-- Background Image --}}
            <div class="absolute inset-0 bg-cover bg-center scale-105 transition-transform duration-[20s] ease-linear"
                 style="background-image: url('{{ $restaurant->cover_image ? Storage::url($restaurant->cover_image) : 'https://images.unsplash.com/photo-1514933651103-005eab06c04d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80' }}')"></div>

            {{-- Multi-layer premium dark overlay --}}
            <div class="absolute inset-0 bg-gradient-to-br from-black/80 via-black/60 to-black/80"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-black/30"></div>

            {{-- Subtle dot-grid pattern overlay --}}
            <div class="absolute inset-0 opacity-[0.08]"
                 style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 40px 40px;"></div>

            {{-- Top bar --}}
            <div class="relative z-10 flex items-center justify-between px-10 pt-8">
                <div class="flex items-center gap-3">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                    </span>
                    <span class="text-xs font-bold uppercase tracking-[0.3em] text-white/50">Kiosk Aktif</span>
                </div>
                <span class="text-xs font-bold uppercase tracking-[0.25em] text-white/30">{{ $restaurant->city ?? 'Self Order' }}</span>
            </div>

            {{-- Main content --}}
            <div class="relative z-10 flex-1 flex flex-col items-center justify-center px-8 text-center">

                {{-- Logo / Initial Avatar --}}
                @if($restaurant->logo)
                    <div class="mb-10 flex items-center justify-center">
                        <div class="h-32 bg-white/10 backdrop-blur-xl border border-white/20 rounded-[1.75rem] flex items-center justify-center px-6 shadow-[0_20px_60px_rgba(0,0,0,0.4)] ring-1 ring-white/10" style="max-width: 18rem; min-width: 8rem;">
                            <img src="{{ Storage::url($restaurant->logo) }}" class="h-24 w-auto max-w-full object-contain drop-shadow-xl" alt="{{ $restaurant->name }}">
                        </div>
                    </div>
                @else
                    <div class="w-36 h-36 rounded-[2rem] bg-white/10 backdrop-blur-xl flex items-center justify-center text-6xl font-black mb-10 border border-white/20 shadow-[0_20px_60px_rgba(0,0,0,0.4)] ring-1 ring-white/10">
                        {{ substr($restaurant->name, 0, 1) }}
                    </div>
                @endif

                {{-- Restaurant name --}}
                <h1 class="text-5xl md:text-7xl lg:text-8xl font-black tracking-tight leading-none mb-4 drop-shadow-2xl"
                    style="text-shadow: 0 4px 30px rgba(0,0,0,0.5);">
                    {{ $restaurant->name }}
                </h1>

                {{-- Subtitle --}}
                <p class="text-base md:text-lg text-white/50 font-medium tracking-[0.15em] uppercase max-w-md mx-auto mb-16">
                    {{ $restaurant->description ? \Illuminate\Support\Str::limit($restaurant->description, 80) : 'Sentuh layar untuk mulai memesan' }}
                </p>

                {{-- CTA Buttons --}}
                <div class="flex flex-col md:flex-row gap-6 w-full max-w-2xl px-4">
                    <button wire:click="startOrder"
                            class="flex-1 group relative overflow-hidden bg-white text-black font-black py-8 px-12 rounded-[2.5rem] text-2xl shadow-[0_20px_60px_rgba(255,255,255,0.15)] hover:shadow-[0_20px_80px_rgba(255,255,255,0.3)] transition-all duration-500 transform hover:scale-105 active:scale-95">
                        <div class="absolute inset-0 bg-gradient-to-r from-orange-400 to-rose-500 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        <span class="relative z-10 flex flex-col items-center gap-2 group-hover:text-white transition-colors duration-300">
                            <span class="text-4xl mb-1">🍔</span>
                            <span>Mulai Pesan</span>
                        </span>
                    </button>

                    @if($restaurant->owner->hasFeature('Queue Management System'))
                        <button wire:click="takeQueue"
                                class="flex-1 group relative overflow-hidden bg-white/10 backdrop-blur-xl text-white font-black py-8 px-12 rounded-[2.5rem] text-2xl border-2 border-white/20 shadow-2xl hover:bg-white/20 hover:border-white transition-all duration-500 transform hover:scale-105 active:scale-95">
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-indigo-600 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                            <span class="relative z-10 flex flex-col items-center gap-2 group-hover:text-white transition-colors duration-300">
                                <span class="text-4xl mb-1">🔊</span>
                                <span>Ambil Antrean</span>
                            </span>
                        </button>
                    @endif
                </div>

                {{-- Tap hint --}}
                <p class="mt-12 text-xs font-bold uppercase tracking-[0.3em] text-white/25 animate-pulse">
                    SENTUH TOMBOL DI ATAS UNTUK MEMULAI
                </p>
            </div>

            {{-- Bottom strip --}}
            <div class="relative z-10 px-10 py-6 flex items-center justify-between border-t border-white/5">
                <span class="text-xs font-bold uppercase tracking-[0.3em] text-white/20">Self Order Kiosk</span>
                <div class="flex items-center gap-2">
                    <div class="w-1.5 h-1.5 rounded-full bg-white/20"></div>
                    <div class="w-1.5 h-1.5 rounded-full bg-white/20"></div>
                    <div class="w-1.5 h-1.5 rounded-full bg-white/20"></div>
                </div>
                <span class="text-xs font-bold uppercase tracking-[0.3em] text-white/20">Powered by Dineflo</span>
            </div>
        </div>
    @endif
    
    {{-- VIEW: QUEUE FORM --}}
    @if($currentView === 'queue-form')
        <div class="h-full w-full relative overflow-y-auto hide-scrollbar flex flex-col p-4 md:p-12 animate-[slideUp_0.3s_ease-out] text-white overscroll-contain">
            {{-- Background Image --}}
            <div class="absolute inset-0 bg-cover bg-center fixed"
                 style="background-image: url('{{ $restaurant->cover_image ? Storage::url($restaurant->cover_image) : 'https://images.unsplash.com/photo-1514933651103-005eab06c04d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80' }}')"></div>
            <div class="absolute inset-0 bg-black/80 backdrop-blur-md fixed"></div>

            <div class="relative z-10 max-w-2xl mx-auto w-full flex-1 flex flex-col justify-center py-12">
                <button wire:click="resetKiosk" class="absolute top-0 left-0 flex items-center text-white/70 hover:text-white font-bold transition-colors text-xl bg-white/10 backdrop-blur-md px-8 py-4 rounded-full border border-white/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Batal
                </button>

                <div class="text-center mb-12">
                    <h2 class="text-5xl md:text-7xl font-black mb-4 tracking-tight">Ambil Nomor Antrean</h2>
                    <p class="text-xl text-white/50 font-medium">Berapa orang yang akan makan bersama Anda?</p>
                </div>

                <div class="bg-white rounded-[3rem] p-10 md:p-16 shadow-2xl text-black">
                    <div class="space-y-8">
                        {{-- Guest Count Selector --}}
                        <div class="flex flex-col items-center">
                            <label class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6">Jumlah Tamu</label>
                            <div class="flex items-center gap-8">
                                <button wire:click="decrementQueueGuestCount" class="w-20 h-20 bg-gray-100 rounded-3xl flex items-center justify-center text-4xl font-black hover:bg-black hover:text-white transition-all active:scale-90">−</button>
                                <span class="text-8xl font-black tracking-tight min-w-[3rem] text-center">{{ $queueGuestCount }}</span>
                                <button wire:click="incrementQueueGuestCount" class="w-20 h-20 bg-gray-100 rounded-3xl flex items-center justify-center text-4xl font-black hover:bg-black hover:text-white transition-all active:scale-90">+</button>
                            </div>
                            <p class="mt-6 text-sm font-bold text-blue-600 uppercase tracking-widest bg-blue-50 px-6 py-2 rounded-full">
                                Kategori: {{ \App\Models\Queue::getPrefixByGuestCount($queueGuestCount) === 'A' ? 'Meja Kecil (1-2)' : (\App\Models\Queue::getPrefixByGuestCount($queueGuestCount) === 'B' ? 'Meja Sedang (3-5)' : 'Meja Besar (6+)') }}
                            </p>
                        </div>

                        <div class="space-y-6 pt-6 border-t border-gray-100">
                            <div>
                                <label class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3 block">Nama Anda (Opsional)</label>
                                <input type="text" wire:model="queueCustomerName" placeholder="Masukkan nama panggilan..." class="w-full bg-gray-50 border-4 border-transparent focus:border-black rounded-[1.5rem] px-8 py-6 text-2xl font-black transition-all outline-none">
                            </div>
                            <div>
                                <label class="text-xs font-black text-gray-400 uppercase tracking-widest mb-3 block">Nomor WhatsApp (Opsional)</label>
                                <input type="tel" wire:model="queueCustomerPhone" placeholder="0812xxxxxx" class="w-full bg-gray-50 border-4 border-transparent focus:border-black rounded-[1.5rem] px-8 py-6 text-2xl font-black transition-all outline-none">
                                <p class="text-[10px] text-gray-400 font-bold mt-2 italic">* Kami akan mengirim notifikasi saat meja Anda siap.</p>
                            </div>
                        </div>

                        <button wire:click="submitQueue" 
                                class="w-full bg-black hover:bg-blue-600 text-white font-black py-8 rounded-[2rem] text-3xl shadow-2xl transition-all transform active:scale-[0.98] flex items-center justify-center group overflow-hidden relative mt-4">
                            <span class="relative z-10 flex items-center gap-3">
                                AMBIL NOMOR SEKARANG
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- VIEW: QUEUE SUCCESS --}}
    @if($currentView === 'queue-success')
        <div class="h-full w-full relative overflow-hidden flex flex-col items-center justify-center text-white p-8">
            {{-- Background Image --}}
            <div class="absolute inset-0 bg-cover bg-center"
                 style="background-image: url('{{ $restaurant->cover_image ? Storage::url($restaurant->cover_image) : 'https://images.unsplash.com/photo-1514933651103-005eab06c04d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80' }}')"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600/95 via-indigo-600/95 to-blue-500/95 backdrop-blur-md"></div>

            <div class="relative z-10 flex flex-col items-center justify-center text-center max-w-2xl">
                <div class="w-40 h-40 bg-white/20 backdrop-blur-xl rounded-[3rem] flex items-center justify-center mb-10 shadow-[0_20px_60px_rgba(0,0,0,0.3)] border border-white/30 animate-bounce duration-[2000ms]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 text-white drop-shadow-lg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
                    </svg>
                </div>
                
                <h1 class="text-6xl font-black mb-4 tracking-tight leading-none uppercase">Antrean Berhasil!</h1>
                <p class="text-2xl font-bold text-white/80 mb-12">Silakan tunggu sebentar, kami akan segera memanggil nomor Anda.</p>
                
                <div class="bg-white text-black rounded-[4rem] px-20 py-16 shadow-[0_40px_100px_rgba(0,0,0,0.4)] border border-white/20 relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-br from-gray-50 to-white"></div>
                    <div class="relative z-10 flex flex-col items-center">
                        <p class="text-[12px] font-black uppercase tracking-[0.4em] text-gray-400 mb-4">Nomor Antrean Anda</p>
                        <span class="text-[11rem] font-black block leading-none tracking-tighter text-black">{{ $createdQueue?->full_number }}</span>
                        <p class="mt-4 text-xs font-bold text-blue-600 uppercase tracking-widest bg-blue-50 px-6 py-2 rounded-full ring-2 ring-blue-100">
                            {{ $createdQueue?->guest_count }} Orang • {{ \Carbon\Carbon::now()->format('H:i') }}
                        </p>
                    </div>
                </div>

                <div class="mt-20 flex flex-col items-center">
                    <div class="w-64 h-2 bg-white/10 rounded-full overflow-hidden mb-4">
                        <div class="h-full bg-white/50 animate-[process_8s_linear_forwards]"></div>
                    </div>
                    <p class="text-white/40 font-bold uppercase tracking-widest text-[10px]" wire:poll.8s="resetKiosk">
                        Selesai otomatis dalam 8 detik...
                    </p>
                </div>
            </div>
        </div>
    @endif


    {{-- VIEW 1.5: ORDER TYPE SELECTION --}}
    @if($currentView === 'order-type-selection')
        <div class="h-full w-full relative overflow-hidden flex flex-col p-4 md:p-8 animate-[slideUp_0.3s_ease-out]">
            {{-- Background Image --}}
            <div class="absolute inset-0 bg-cover bg-center"
                 style="background-image: url('{{ $restaurant->cover_image ? Storage::url($restaurant->cover_image) : 'https://images.unsplash.com/photo-1514933651103-005eab06c04d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80' }}')"></div>
            <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>

            <div class="relative z-10 max-w-4xl mx-auto w-full flex-1 flex flex-col items-center justify-center">
                <button wire:click="resetKiosk" class="absolute top-0 left-0 flex items-center text-white/70 hover:text-white font-bold transition-colors text-xl bg-white/10 backdrop-blur-md px-6 py-3 rounded-full border border-white/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Batal
                </button>

                <h2 class="text-4xl md:text-5xl font-black text-center mb-3 text-white">Makan di mana?</h2>
                <p class="text-xl text-white/50 text-center mb-12 font-medium">Silakan pilih cara Anda menikmati hidangan kami.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 w-full max-w-3xl">
                    {{-- Dine In Button --}}
                    <button wire:click="selectOrderType('dine_in')" class="group relative bg-white/10 backdrop-blur-xl border-2 border-white/20 hover:border-orange-400 hover:bg-white/20 rounded-[2rem] p-12 shadow-xl hover:shadow-2xl hover:shadow-orange-500/20 transition-all duration-300 flex flex-col items-center justify-center text-center transform hover:-translate-y-2">
                        <div class="w-32 h-32 bg-orange-500/20 text-orange-400 rounded-full flex items-center justify-center mb-8 group-hover:scale-110 transition-transform duration-300 border border-orange-400/20">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-3a2 2 0 00-2-2H5a2 2 0 00-2 2v3h18z" />
                            </svg>
                        </div>
                        <h3 class="text-3xl font-black text-white mb-2">Makan di Sini</h3>
                        <p class="text-white/50 font-medium">(Dine In)</p>
                    </button>

                    {{-- Takeaway Button --}}
                    <button wire:click="selectOrderType('takeaway')" class="group relative bg-white/10 backdrop-blur-xl border-2 border-white/20 hover:border-white hover:bg-white/20 rounded-[2rem] p-12 shadow-xl hover:shadow-2xl transition-all duration-300 flex flex-col items-center justify-center text-center transform hover:-translate-y-2">
                        <div class="w-32 h-32 bg-white/10 text-white rounded-full flex items-center justify-center mb-8 group-hover:scale-110 transition-transform duration-300 border border-white/20">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <h3 class="text-3xl font-black text-white mb-2">Bawa Pulang</h3>
                        <p class="text-white/50 font-medium">(Takeaway)</p>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- VIEW 1.8: TABLE SELECTION (If Dine In) --}}
    @if($currentView === 'table-selection')
        <div class="h-full w-full relative overflow-hidden flex flex-col p-4 md:p-8 animate-[slideUp_0.3s_ease-out]">
            {{-- Background Image --}}
            <div class="absolute inset-0 bg-cover bg-center"
                 style="background-image: url('{{ $restaurant->cover_image ? Storage::url($restaurant->cover_image) : 'https://images.unsplash.com/photo-1514933651103-005eab06c04d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80' }}')"></div>
            <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
            <div class="relative z-10 max-w-5xl mx-auto w-full flex-1 flex flex-col">
                {{-- Navigation --}}
                <div class="flex items-center justify-between mb-8">
                    <button wire:click="$set('currentView', 'order-type-selection')" class="flex items-center text-white/70 hover:text-white font-bold transition-colors text-xl bg-white/10 backdrop-blur-md px-6 py-3 rounded-full border border-white/20">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                        Kembali
                    </button>
                    <button wire:click="selectTable(null)" class="flex items-center text-orange-400 hover:text-orange-300 font-bold transition-colors text-lg bg-orange-500/20 backdrop-blur-md px-6 py-3 rounded-full border border-orange-400/30">
                        Lewati (Pilih Nanti)
                    </button>
                </div>

                <div class="text-center mb-8">
                    <h2 class="text-4xl md:text-5xl font-black mb-4 text-white">Pilih Nomor Meja</h2>
                    <p class="text-xl text-white/50">Silakan pilih meja yang akan atau sedang Anda tempati.</p>
                </div>

                @if(!$this->hasAvailableTables && $restaurant->owner->hasFeature('Queue Management System'))
                    <div class="mb-8 p-10 bg-gradient-to-br from-blue-600/30 to-indigo-600/30 backdrop-blur-xl border-2 border-white/20 rounded-[2.5rem] text-center animate-fadeIn shadow-2xl">
                        <div class="w-20 h-20 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl shadow-lg ring-4 ring-blue-500/30">🔊</div>
                        <h3 class="text-3xl font-black text-white mb-3">Maaf, Area Makan Sedang Penuh</h3>
                        <p class="text-xl text-white/60 mb-8 max-w-lg mx-auto font-medium">Jangan lewatkan kelezatan hari ini. Ambil nomor antrean dan kami akan memberitahu Anda segera setelah meja tersedia!</p>
                        <button wire:click="takeQueue" class="bg-white text-blue-600 hover:bg-blue-50 font-black py-5 px-12 rounded-2xl text-2xl shadow-xl transition-all transform hover:scale-105 active:scale-95 flex items-center gap-3 mx-auto">
                            AMBIL NOMOR ANTREAN
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                        </button>
                    </div>
                @endif

                <div class="flex-1 overflow-y-auto hide-scrollbar pb-12 pt-2 px-2">
                    @if($this->tables->isEmpty())
                        <div class="text-center py-20 text-white/40">
                            <div class="text-6xl mb-4">🪑</div>
                            <p class="text-xl font-bold">Belum ada meja yang terdaftar.</p>
                            <button wire:click="selectTable(null)" class="mt-8 bg-white/10 backdrop-blur-md text-white px-8 py-3 rounded-full font-bold border border-white/20">Lanjutkan Tanpa Meja</button>
                        </div>
                    @else
                        <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 md:gap-6">
                            @foreach($this->tables as $table)
                                @php $isAvailable = $table->status === 'available'; @endphp
                                <button @if($isAvailable) wire:click="selectTable({{ $table->id }})" @endif 
                                    class="group relative bg-white/10 backdrop-blur-xl border-2 {{ $isAvailable ? 'border-white/20 hover:border-orange-400 hover:bg-white/20' : 'border-red-500/30 opacity-60' }} rounded-3xl p-6 shadow-sm {{ $isAvailable ? 'hover:shadow-[0_12px_40px_rgba(251,146,60,0.25)] hover:-translate-y-2' : 'cursor-not-allowed grayscale-[0.5]' }} transition-all duration-300 flex flex-col items-center justify-center aspect-square overflow-hidden">
                                    
                                    @if(!$isAvailable)
                                        <div class="absolute inset-0 bg-red-900/10 backdrop-blur-[2px]"></div>
                                        <div class="absolute top-2 right-2">
                                            <span class="text-[8px] font-black uppercase bg-red-500 text-white px-2 py-1 rounded-lg shadow-lg">TERISI</span>
                                        </div>
                                    @endif

                                    <div class="text-4xl md:text-5xl mb-2 group-hover:scale-110 transition-transform duration-300">🪑</div>
                                    <h3 class="text-xl md:text-2xl font-black text-white leading-tight {{ !$isAvailable ? 'text-white/60' : '' }}">{{ $table->name }}</h3>
                                    
                                    @if($table->capacity)
                                        <div class="flex items-center gap-1 mt-1.5 text-white/50 group-hover:text-orange-300 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <span class="text-[11px] font-bold">{{ $table->capacity }} orang</span>
                                        </div>
                                    @endif
                                    
                                    @if($table->area)
                                        <span class="mt-1 text-[10px] font-bold uppercase tracking-widest text-white/30 group-hover:text-white/50 transition-colors">{{ $table->area }}</span>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- VIEW 2: MENU & CART --}}
    @if($currentView === 'menu' || $currentView === 'cart')
        <div class="flex-1 flex flex-col md:flex-row h-full overflow-hidden bg-gray-50">
            
            {{-- Left Side: Menu Grid (Full width on mobile if cart hidden) --}}
            <div class="flex-1 flex flex-col h-full overflow-hidden relative">
                {{-- Header / Categories --}}
                <div class="bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-white/5 transition-colors">
                    <div class="px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-6">
                            <button wire:click="$set('currentView', 'welcome')" class="p-3 bg-gray-100 dark:bg-gray-800 rounded-2xl text-gray-400 dark:text-gray-500 hover:text-black dark:hover:text-white transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                            </button>
                            <div>
                                <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight transition-colors">Pilih Menu</h2>
                                <p class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mt-0.5 transition-colors">{{ $restaurant->name }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 flex-1 justify-end">

                            <div class="relative w-full max-w-xs group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 group-focus-within:text-black dark:group-focus-within:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari makanan atau minuman..." 
                                       class="w-full bg-gray-100 dark:bg-gray-800 border-2 border-transparent focus:border-black dark:focus:border-primary-500 focus:bg-white dark:focus:bg-gray-900 rounded-2xl pl-10 pr-4 py-3 text-sm font-bold focus:ring-0 transition-all placeholder:text-gray-400 dark:placeholder:text-gray-500 text-gray-900 dark:text-white">
                            </div>
                        </div>
                    </div>
                </div>
                    
                {{-- Category Scroll (Premium Flex Card Style) --}}
                <div class="bg-gray-50/50 dark:bg-gray-800/20 backdrop-blur-sm transition-colors overflow-hidden">
                    <div class="overflow-x-auto whitespace-nowrap px-6 py-4 flex space-x-4 hide-scrollbar items-stretch">
                        <button wire:click="selectCategory(null)" 
                            class="flex-shrink-0 flex flex-col items-center justify-center px-8 py-2 rounded-2xl border-2 transition-all {{ is_null($activeCategory) ? 'border-orange-500 bg-orange-500 text-white shadow-lg shadow-orange-500/30' : 'border-gray-100 dark:border-white/5 bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:border-gray-200 dark:hover:border-white/10' }} min-w-[6rem]">
                            <span class="text-sm font-black uppercase tracking-wider">Semua</span>
                        </button>
                        @foreach($categories as $category)
                            <button wire:click="selectCategory({{ $category->id }})"
                                class="group flex-shrink-0 flex items-center p-2.5 rounded-2xl border-2 transition-all {{ $activeCategory === $category->id ? 'border-black dark:border-primary-500 bg-white dark:bg-gray-900 shadow-xl shadow-black/10 dark:shadow-primary-500/5' : 'border-gray-100 dark:border-white/5 bg-gray-100/50 dark:bg-gray-800/40 hover:border-gray-300 dark:hover:border-white/10 hover:shadow-lg' }} w-64 md:w-72 text-left gap-4 overflow-hidden">
                                
                                <div class="w-14 h-14 rounded-xl {{ $category->image ? 'bg-gray-100 dark:bg-gray-700' : 'bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800' }} overflow-hidden flex-shrink-0 flex items-center justify-center border border-black/5 dark:border-white/5 relative group-hover:scale-105 transition-transform duration-300">
                                    @if($category->image)
                                        <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}" class="absolute inset-0 w-full h-full object-cover">
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                    @endif
                                </div>

                                <div class="flex flex-col overflow-hidden py-1 pr-4 min-w-0">
                                    <span class="text-sm font-black truncate tracking-tight {{ $activeCategory === $category->id ? 'text-black dark:text-white' : 'text-gray-900 dark:text-gray-100 group-hover:text-black dark:group-hover:text-white' }} transition-colors">{{ $category->name }}</span>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Products Grid --}}
                <div class="flex-1 overflow-y-auto p-4 pb-32 md:pb-4 hide-scrollbar">
                    @php
                        $activeCategoryModel = $activeCategory ? $categories->firstWhere('id', $activeCategory) : null;
                    @endphp

                    @if($activeCategoryModel)
                        <div class="mb-6 bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm border border-gray-100 dark:border-white/5 flex items-center gap-6 animate-fadeIn">
                            <div class="w-20 h-20 rounded-2xl bg-orange-500/10 flex items-center justify-center text-4xl flex-shrink-0 overflow-hidden">
                                @if($activeCategoryModel->image)
                                    <img src="{{ Storage::url($activeCategoryModel->image) }}" class="w-full h-full object-cover">
                                @else
                                    🍚
                                @endif
                            </div>
                            <div class="overflow-hidden min-w-0 flex-1">
                                <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">{{ $activeCategoryModel->name }}</h2>
                            </div>
                        </div>
                    @endif

                    @if($this->menuItems->isEmpty())
                        <div class="h-full flex flex-col items-center justify-center text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                            </svg>
                            <p class="text-xl font-medium">No items found</p>
                        </div>
                    @else
                        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            @foreach($this->menuItems as $item)
                                @php
                                    $isOutOfStock = $item->manage_stock && $item->stock_quantity <= 0;
                                @endphp
                                <div @if(!$isOutOfStock) wire:click="openItemModal({{ $item->id }})" @endif class="bg-white dark:bg-gray-800 rounded-2xl p-3 shadow-sm border border-gray-100 dark:border-white/5 {{ $isOutOfStock ? 'opacity-60 cursor-not-allowed' : 'hover:border-black dark:hover:border-primary-500 cursor-pointer group' }} flex flex-col relative overflow-hidden transition-all duration-300">
                                    <div class="aspect-square w-full bg-gray-100 dark:bg-gray-700/50 rounded-xl overflow-hidden mb-3 relative">
                                        @if($item->image)
                                            <img src="{{ Storage::url($item->image) }}" class="w-full h-full object-cover {{ $isOutOfStock ? 'grayscale opacity-50' : 'group-hover:scale-110' }} transition-transform duration-500" alt="{{ $item->name }}">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-4xl">🍽️</div>
                                        @endif
                                        
                                        @if($isOutOfStock)
                                            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center">
                                                <span class="bg-red-500 text-white font-black px-3 py-1.5 text-sm rounded-lg transform -rotate-12 shadow-xl border-2 border-white">HABIS</span>
                                            </div>
                                        @else
                                            {{-- Plus Button overlay --}}
                                            <div class="absolute bottom-2 right-2 bg-black dark:bg-primary-600 text-white w-10 h-10 flex items-center justify-center rounded-full font-bold text-xl shadow-lg opacity-90 group-hover:opacity-100 group-hover:bg-orange-500 dark:group-hover:bg-primary-500 transition-colors">
                                                +
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 flex flex-col justify-between">
                                        <div>
                                            <h3 class="font-bold text-gray-900 dark:text-white text-lg leading-tight mb-1 line-clamp-2 transition-colors">{{ $item->name }}</h3>
                                            @if($item->variants->count() > 0)
                                                <p class="text-[10px] text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider mb-2 border border-gray-200 dark:border-white/10 inline-block px-1 rounded bg-gray-50 dark:bg-gray-700/50 transition-colors">Varian Tersedia</p>
                                            @endif
                                        </div>
                                        @if($item->has_active_discount)
                                            <div class="flex flex-col items-end leading-tight">
                                                <div class="flex items-center gap-1.5 mb-1">
                                                    <span class="text-[12px] text-gray-400 line-through">Rp {{ number_format($item->original_price, 0, ',', '.') }}</span>
                                                    @if($item->getActiveDiscount())
                                                        <span class="text-[9px] bg-red-100 text-red-600 px-1.5 py-0.5 rounded-md font-bold uppercase tracking-wider">{{ $item->getActiveDiscount()->name }}</span>
                                                    @endif
                                                </div>
                                                <span class="font-black text-red-600 text-lg">{{ $item->formatted_price }}</span>
                                            </div>
                                        @else
                                            <span class="font-black text-black text-lg">{{ $item->formatted_price }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Mobile Cart Toast/Bottom Bar --}}
                @if(count($cart) > 0 && $currentView === 'menu')
                    <div class="absolute bottom-6 left-4 right-4 md:hidden pointer-events-auto z-40">
                        <button wire:click="viewCart" x-on:click="showCart = true" class="w-full bg-black text-white p-4 rounded-2xl shadow-2xl flex items-center justify-between hover:bg-gray-900 active:scale-[0.98] transition-all">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-white text-black rounded-full flex items-center justify-center font-black text-lg">
                                    {{ $this->cartCount }}
                                </div>
                                <div class="text-left">
                                    <p class="text-xs text-gray-300 uppercase font-bold tracking-wider">Total Pesanan</p>
                                    <p class="text-lg font-black">Rp {{ number_format($this->total, 0, ',', '.') }}</p>
                                </div>
                            </div>
                            <div class="font-bold flex items-center space-x-1">
                                <span>Lihat</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                            </div>
                        </button>
                    </div>
                @endif
            </div>

            {{-- Right Side: Cart Panel (Hidden on Mobile unless requested) --}}
            <div class="{{ $currentView === 'cart' ? 'flex absolute inset-0 z-50' : 'hidden md:flex' }} bg-white w-full md:relative md:w-[400px] lg:w-[450px] shadow-2xl flex-col h-full border-l border-gray-200 transition-all">
                
                {{-- Cart Header --}}
                <div class="p-5 border-b flex items-center justify-between bg-gray-50/50">
                    <h2 class="text-2xl font-black flex items-center">
                        <span class="mr-2">🛒</span> Pesanan Saya
                    </h2>
                    @if($currentView === 'cart')
                        <button wire:click="viewMenu" class="md:hidden text-gray-500 hover:text-black font-bold text-sm bg-gray-200 px-3 py-1.5 rounded-full">Tutup</button>
                    @endif
                </div>

                {{-- Cart Items --}}
                <div class="flex-1 overflow-y-auto p-4 hide-scrollbar bg-gray-50/30">
                    @if(empty($cart))
                        <div class="h-full flex flex-col items-center justify-center text-center opacity-50 pt-20">
                            <div class="text-6xl mb-4">🍽️</div>
                            <p class="text-xl font-bold text-gray-600">Keranjang Masih Kosong</p>
                            <p class="text-sm text-gray-400 mt-2">Silakan pilih menu makanan untuk mulai memesan.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($cart as $item)
                                <div class="bg-white border hover:border-gray-300 rounded-xl p-3 flex relative shadow-sm group transition-colors">
                                    {{-- Delete btn (absolute top right of item) --}}
                                    <button wire:click="removeItem('{{ $item['id'] }}')" class="absolute top-2 right-2 p-1.5 text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                    
                                    {{-- Image --}}
                                    <div class="w-16 h-16 rounded-lg bg-gray-100 mr-3 flex-shrink-0 overflow-hidden">
                                        @if(isset($item['image']) && $item['image'])
                                            <img src="{{ Storage::url($item['image']) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-2xl">🍽️</div>
                                        @endif
                                    </div>

                                    {{-- Info --}}
                                    <div class="flex-1 pr-6 flex flex-col justify-between">
                                        <div>
                                            <h4 class="font-bold text-gray-900 leading-tight">{{ $item['name'] }}</h4>
                                            @if($item['variant'])
                                                <p class="text-xs text-blue-600 font-medium mt-0.5">• {{ $item['variant']['name'] }}</p>
                                            @endif
                                            @if(!empty($item['addons']))
                                                @foreach($item['addons'] as $addon)
                                                    <p class="text-xs text-orange-600 font-medium">• {{ $addon['name'] }}</p>
                                                @endforeach
                                            @endif
                                            @if(!empty($item['note']))
                                                <p class="text-[10px] text-amber-600 font-bold mt-0.5 line-clamp-1 italic">• Note: {{ $item['note'] }}</p>
                                            @endif
                                        </div>
                                        <div class="flex items-end justify-between mt-2">
                                            <div class="flex flex-col">
                                                @if(isset($item['original_price']) && $item['original_price'] > $item['price'])
                                                    <div class="flex items-center gap-2 mb-0.5">
                                                        <span class="text-[10px] text-gray-400 line-through">Rp {{ number_format($item['original_price'] * $item['quantity'], 0, ',', '.') }}</span>
                                                        @if(isset($item['discount_name']))
                                                            <span class="text-[9px] bg-red-50 text-red-600 px-1.5 py-0.5 rounded-md font-bold uppercase tracking-wider">{{ $item['discount_name'] }}</span>
                                                        @endif
                                                    </div>
                                                @endif
                                                <span class="font-black text-sm">Rp {{ number_format($item['total_price'], 0, ',', '.') }}</span>
                                            </div>
                                            
                                            {{-- Qty Controls --}}
                                            <div class="flex items-center space-x-3 bg-gray-50 border rounded-lg p-0.5">
                                                <button wire:click="updateQuantity('{{ $item['id'] }}', -1)" class="w-6 h-6 flex items-center justify-center hover:bg-white hover:text-red-500 rounded text-gray-500 font-bold">−</button>
                                                <span class="text-sm font-black w-4 text-center">{{ $item['quantity'] }}</span>
                                                <button wire:click="updateQuantity('{{ $item['id'] }}', 1)" class="w-6 h-6 flex items-center justify-center hover:bg-white hover:text-green-500 rounded text-gray-500 font-bold">+</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Action / Checkout Bar (Floating Island Style) --}}
                @if(count($cart) > 0)
                    <div class="px-6 pb-8 pt-2">
                        <div class="p-6 bg-white rounded-[2.5rem] shadow-[0_15px_60px_rgba(0,0,0,0.15)] border border-gray-100 flex flex-col">
                            <div class="mb-5 px-2">
                                @if($this->tax > 0 || $this->additionalFees > 0)
                                    <div class="flex justify-between items-center mb-1.5 text-xs font-bold text-gray-400 uppercase tracking-widest">
                                        <span>Subtotal</span>
                                        <span>Rp {{ number_format($this->subtotal, 0, ',', '.') }}</span>
                                    </div>
                                @endif

                                @if($restaurant->additional_fees)
                                    @foreach($restaurant->additional_fees as $fee)
                                        @php
                                            $feeAmount = ($fee['type'] ?? '') === 'fixed' ? $fee['value'] : ($this->subtotal * ($fee['value'] / 100));
                                        @endphp
                                        @if($feeAmount > 0)
                                            <div class="flex justify-between items-center mb-1.5 text-xs font-bold text-gray-400 uppercase tracking-widest">
                                                <span>{{ $fee['name'] }}</span>
                                                <span>Rp {{ number_format($feeAmount, 0, ',', '.') }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif

                                @if($this->tax > 0)
                                    <div class="flex justify-between items-center mb-3 text-xs font-bold text-gray-400 uppercase tracking-widest">
                                        <span>Pajak ({{ $restaurant->tax_percentage }}%)</span>
                                        <span>Rp {{ number_format($this->tax, 0, ',', '.') }}</span>
                                    </div>
                                @endif

                                @if($this->pointDiscount > 0)
                                    <div class="flex justify-between items-center mb-3 text-xs font-bold text-orange-500 uppercase tracking-widest">
                                        <span>Potongan Poin</span>
                                        <span>- Rp {{ number_format($this->pointDiscount, 0, ',', '.') }}</span>
                                    </div>
                                @endif

                                @if($this->giftCardDiscount > 0)
                                    <div class="flex justify-between items-center mb-3 text-xs font-bold text-blue-500 uppercase tracking-widest">
                                        <span>Gift Card Dipakai</span>
                                        <span>- Rp {{ number_format($this->giftCardDiscount, 0, ',', '.') }}</span>
                                    </div>
                                @endif

                                <div class="flex justify-between items-center border-t border-gray-50 pt-3">
                                    <span class="text-gray-900 font-black text-lg">Total Tagihan</span>
                                    <span class="text-4xl font-black text-black tracking-tighter">Rp {{ number_format($this->total, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            
                            <button wire:click="$set('currentView', 'checkout')" class="w-full bg-black hover:bg-gray-800 text-white font-black py-7 px-8 rounded-[2rem] text-2xl shadow-2xl shadow-black/20 transform transition-all active:scale-[0.98] flex items-center justify-center group overflow-hidden relative">
                                <div class="relative z-10 flex items-center gap-4">
                                    Checkout Sekarang
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                                </div>
                                <div class="absolute inset-0 bg-gradient-to-r from-gray-900 to-black opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- VIEW 3: CHECKOUT (Payment Info) --}}
    @if($currentView === 'checkout')
        <div class="h-full w-full relative overflow-hidden flex flex-col p-4 md:p-12 animate-[slideUp_0.3s_ease-out]">
            {{-- Background Image --}}
            <div class="absolute inset-0 bg-cover bg-center"
                 style="background-image: url('{{ $restaurant->cover_image ? Storage::url($restaurant->cover_image) : 'https://images.unsplash.com/photo-1514933651103-005eab06c04d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80' }}')"></div>
            <div class="absolute inset-0 bg-black/75 backdrop-blur-sm"></div>

            <div class="relative z-10 max-w-2xl mx-auto w-full flex-1 flex flex-col min-h-0">
                <button wire:click="viewCart" class="flex items-center text-white/70 hover:text-white font-bold mb-6 transition-colors bg-white/10 backdrop-blur-md px-8 py-4 rounded-full border border-white/20 w-fit text-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali
                </button>
                
                <div class="bg-white rounded-[3rem] shadow-[0_40px_100px_rgba(0,0,0,0.6)] w-full flex-1 flex flex-col overflow-hidden relative border border-white/10 min-h-0">
                    <div class="p-10 md:p-16 flex-1 overflow-y-auto scroll-smooth overscroll-contain" style="touch-action: pan-y; -webkit-overflow-scrolling: touch;">
                        @if($checkoutStep === 1)
                            <h2 class="text-4xl font-black text-center mb-2 tracking-tight">Data Pesanan</h2>
                            <p class="text-gray-500 text-center mb-10 font-medium">Lengkapi data Anda untuk kemudahan pelayanan.</p>
                            
                            <div class="space-y-8 mb-10">
                                {{-- Name Input --}}
                                <div>
                                    <label class="block text-sm font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Nama Lengkap <span class="text-red-500">*</span></label>
                                    <input type="text" wire:model.live="customerName" 
                                        class="w-full bg-gray-50 border-4 border-gray-100 focus:border-black focus:bg-white rounded-[1.5rem] px-6 py-5 text-2xl font-black transition-all outline-none @error('customerName') border-red-500 @enderror"
                                        placeholder="Masukkan Nama Anda">
                                    @error('customerName') <p class="text-red-500 text-sm font-bold mt-2 ml-2">{{ $message }}</p> @enderror
                                </div>

                                {{-- Phone Input --}}
                                <div>
                                    <label class="block text-sm font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Nomor WhatsApp <span class="text-gray-400 font-bold">(Opsional)</span></label>
                                    <div class="relative">
                                        <input type="tel" wire:model.live="customerPhone" 
                                            class="w-full bg-gray-50 border-4 border-gray-100 focus:border-black focus:bg-white rounded-[1.5rem] px-6 py-5 text-2xl font-black transition-all outline-none @error('customerPhone') border-red-500 @enderror"
                                            placeholder="0812xxxxxx">
                                        
                                        @if($isMember && $member)
                                            <div class="absolute right-4 top-1/2 -translate-y-1/2 bg-green-500 text-white px-4 py-2 rounded-xl text-sm font-black flex items-center gap-2 animate-bounce-once">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                                MEMBER
                                            </div>
                                        @endif
                                    </div>
                                    @error('customerPhone') <p class="text-red-500 text-sm font-bold mt-2 ml-2">{{ $message }}</p> @enderror

                                    {{-- Point Redemption Section (Kiosk) --}}
                                    @if($isMember && $member && $restaurant->loyalty_redemption_enabled && $member->points_balance > 0)
                                        <div class="mt-8 p-8 bg-orange-50 border-4 border-orange-100 rounded-[2rem] shadow-xl overflow-hidden relative animate-fade-in">
                                            <div class="flex items-center justify-between mb-4">
                                                <div class="flex items-center gap-4">
                                                    <div class="p-3 bg-orange-500 rounded-2xl shadow-lg shadow-orange-500/20">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M5 5a3 3 0 015-2.236A3 3 0 0114.83 6H16a2 2 0 110 4h-5V9a1 1 0 10-2 0v1H4a2 2 0 110-4h1.17C5.06 5.687 5 5.35 5 5zm4 1V5a1 1 0 10-1.223.979L9 6.223zM11 5v1.223l1.223-.244A1 1 0 1011 5zM4 12a1 1 0 011-1h5.25c.15 0 .3.05.4.15l2.5 2.5a.5.5 0 010 .7l-2.5 2.5a.5.5 0 01-.4.15H5a1 1 0 01-1-1v-4z" clip-rule="evenodd" />
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <span class="block text-xl font-black text-orange-900 uppercase tracking-tight">Tukar Poin Jadi Diskon?</span>
                                                        <span class="block text-sm text-orange-700 font-bold uppercase tracking-wider">Saldo: {{ number_format($member->points_balance) }} Poin (Nilai: Rp{{ number_format($restaurant->loyalty_point_redemption_value, 0, ',', '.') }}/Poin)</span>
                                                    </div>
                                                </div>
                                                <label class="relative inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" wire:model.live="usePoints" class="sr-only peer">
                                                    <div class="w-14 h-8 bg-orange-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-orange-600"></div>
                                                </label>
                                            </div>
                                            
                                            @if($usePoints)
                                                <div class="mt-6 pt-6 border-t-4 border-dashed border-orange-200 animate-slide-up">
                                                    <div class="flex items-center bg-white border-4 border-orange-200 rounded-[1.5rem] p-2 focus-within:border-orange-500 transition-colors">
                                                        <span class="bg-orange-100 px-6 py-4 rounded-2xl text-orange-700 font-black text-lg uppercase tracking-widest leading-none">Poin Digunakan</span>
                                                        <input type="number" wire:model.live.debounce.500ms="pointsToUse" class="w-full bg-transparent text-right font-black text-4xl outline-none px-4 text-orange-900" placeholder="0">
                                                    </div>
                                                    @if($this->pointDiscount > 0)
                                                        <div class="mt-6 p-6 bg-white/60 rounded-[1.5rem] border-4 border-orange-200 flex justify-between items-center">
                                                            <span class="text-orange-900 font-black text-xl uppercase">Potongan Harga</span>
                                                            <span class="text-orange-600 font-black text-4xl">- Rp{{ number_format($this->pointDiscount, 0, ',', '.') }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    {{-- Registration Checkbox --}}
                                    @if(!$isMember && strlen($customerPhone) >= 10)
                                        <div class="mt-4 p-6 bg-orange-50 rounded-[1.5rem] border-4 border-dashed border-orange-100 animate-pulse-once">
                                            <label class="flex items-center cursor-pointer group">
                                                <div class="relative flex items-center">
                                                    <input type="checkbox" wire:model="wantsToRegister" class="w-8 h-8 text-orange-600 border-4 border-orange-200 rounded-xl focus:ring-0 transition-all cursor-pointer">
                                                </div>
                                                <div class="ml-4">
                                                    <span class="block text-lg font-black text-gray-900 group-hover:text-orange-600 transition-colors">Gabung Member & Kumpulkan Poin? ✨</span>
                                                    <span class="block text-sm text-orange-400 font-bold uppercase tracking-wider">Dapatkan diskon eksklusif di kunjungan berikutnya</span>
                                                </div>
                                            </label>
                                        </div>
                                    @endif
                                </div>

                                {{-- Gift Card Section (Kiosk) --}}
                                @if($restaurant->owner->hasFeature('Gift Cards'))
                                    <div class="mt-8 p-8 bg-blue-50 border-4 border-blue-100 rounded-[2rem] shadow-xl overflow-hidden relative animate-fade-in">
                                        <div class="flex items-center justify-between mb-4">
                                            <div class="flex items-center gap-4">
                                                <div class="p-3 bg-blue-500 rounded-2xl shadow-lg shadow-blue-500/20">
                                                    <x-heroicon-o-gift class="w-6 h-6 text-white" />
                                                </div>
                                                <div>
                                                    <span class="block text-xl font-black text-blue-900 uppercase tracking-tight">Punya Saldo Gift Card?</span>
                                                    <span class="block text-sm text-blue-700 font-bold uppercase tracking-wider">Gunakan untuk potongan harga</span>
                                                </div>
                                            </div>
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" wire:model.live="useGiftCard" class="sr-only peer">
                                                <div class="w-14 h-8 bg-blue-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600"></div>
                                            </label>
                                        </div>

                                        @if($useGiftCard)
                                        <div class="mt-6 pt-6 border-t-4 border-dashed border-blue-200 animate-slide-up">
                                            @if($appliedGiftCard)
                                                <div class="bg-white border-4 border-blue-200 rounded-[1.5rem] p-6 relative overflow-hidden group">
                                                    <div class="flex justify-between items-center relative z-10">
                                                        <div>
                                                            <span class="text-blue-900 font-black text-2xl uppercase tracking-widest">{{ $giftCardCode }}</span>
                                                            <span class="block text-sm text-blue-600 font-bold mt-1">Saldo Tersisa: Rp{{ number_format($appliedGiftCard['remaining_balance'], 0, ',', '.') }}</span>
                                                        </div>
                                                        <button wire:click="removeGiftCard" class="bg-red-100 hover:bg-red-500 text-red-600 hover:text-white px-4 py-3 rounded-xl font-black text-xs uppercase tracking-widest transition-colors flex items-center gap-2">
                                                            <x-heroicon-o-x-mark class="w-4 h-4" /> Batal
                                                        </button>
                                                    </div>
                                                    <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:opacity-10 transition-opacity">
                                                        <x-heroicon-s-gift class="w-32 h-32 text-blue-900" />
                                                    </div>
                                                </div>
                                            @else
                                                <div class="flex flex-col gap-3">
                                                    <div class="flex items-center gap-3">
                                                        <input type="text" wire:model="giftCardCode" class="flex-1 bg-white border-4 border-blue-200 focus:border-blue-500 rounded-[1.5rem] px-6 py-5 text-2xl font-black transition-all outline-none uppercase placeholder-blue-300" placeholder="KODE GIFT CARD">
                                                        <button wire:click="applyGiftCard" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-5 rounded-[1.5rem] font-black text-lg shadow-xl shadow-blue-600/30 transition-all active:scale-95 whitespace-nowrap">PAKAI</button>
                                                    </div>
                                                    @if($giftCardError)
                                                        <p class="text-red-500 text-sm font-bold bg-red-50 px-4 py-2 rounded-xl inline-block w-fit">{{ $giftCardError }}</p>
                                                    @endif
                                                </div>
                                            @endif

                                            @if($this->giftCardDiscount > 0)
                                                <div class="mt-4 p-6 bg-white/60 rounded-[1.5rem] border-4 border-blue-200 flex justify-between items-center">
                                                    <span class="text-blue-900 font-black text-xl uppercase">Diskon Diterapkan</span>
                                                    <span class="text-blue-600 font-black text-4xl">- Rp{{ number_format($this->giftCardDiscount, 0, ',', '.') }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @else
                            <h2 class="text-4xl font-black text-center mb-2 tracking-tight">Metode Pembayaran</h2>
                            <p class="text-gray-500 text-center mb-8 font-medium">Silakan pilih cara Anda membayar pesanan ini.</p>
                            
                            <div class="space-y-6">
                                <!-- Digital / QRIS Option -->
                                @if(in_array($restaurant->payment_mode ?? 'kasir', ['gateway', 'both']))
                                <label class="flex justify-between items-center p-8 border-4 border-gray-50 bg-gray-50/50 rounded-[2rem] cursor-pointer transition-all duration-300 {{ $paymentMethod == 'midtrans' ? 'border-orange-500 bg-orange-50 shadow-lg shadow-orange-500/10' : 'hover:border-gray-200' }}">
                                    <div class="flex items-center">
                                        <div class="w-16 h-16 rounded-2xl bg-white shadow-sm flex items-center justify-center mr-6 text-3xl">📱</div>
                                        <div class="">
                                            <p class="font-black text-2xl text-gray-900 tracking-tight">QRIS / E-Wallet</p>
                                            <p class="text-gray-500 font-bold mt-0.5">Scan QR Code langsung dari layar</p>
                                        </div>
                                        <input type="radio" value="midtrans" wire:model.live="paymentMethod" class="hidden" />
                                    </div>
                                    @if($paymentMethod == 'midtrans')
                                        <div class="w-8 h-8 rounded-full bg-orange-500 flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                        </div>
                                    @endif
                                </label>
                                @endif

                                <!-- Cash at Counter -->
                                @if(in_array($restaurant->payment_mode ?? 'kasir', ['kasir', 'both']))
                                <label class="flex justify-between items-center p-8 border-4 border-gray-50 bg-gray-50/50 rounded-[2rem] cursor-pointer transition-all duration-300 {{ $paymentMethod == 'cash' ? 'border-black bg-gray-50 shadow-lg' : 'hover:border-gray-200' }}">
                                    <div class="flex items-center">
                                        <div class="w-16 h-16 rounded-2xl bg-white shadow-sm flex items-center justify-center mr-6 text-3xl">💵</div>
                                        <div class="">
                                            <p class="font-black text-2xl text-gray-900 tracking-tight">Bayar di Kasir</p>
                                            <p class="text-gray-500 font-bold mt-0.5">Cetak struk dan bayar langsung</p>
                                        </div>
                                        <input type="radio" value="cash" wire:model.live="paymentMethod" class="hidden" />
                                    </div>
                                    @if($paymentMethod == 'cash')
                                        <div class="w-8 h-8 rounded-full bg-black flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                        </div>
                                    @endif
                                </label>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="p-10 bg-gray-50 border-t border-gray-100/50">
                        <div class="flex justify-between items-center mb-8">
                            <span class="text-xl text-gray-400 font-black tracking-tight uppercase">Total Pembayaran</span>
                            <span class="text-5xl font-black text-black tracking-tighter">Rp {{ number_format($this->total, 0, ',', '.') }}</span>
                        </div>
                        
                        @if($createdOrder)
                            <!-- Midtrans Embed logic or Print Logic -->
                            @if($paymentMethod == 'midtrans' && $createdOrder->payment_token)
                                <button x-on:click="handleSnap('{{ $createdOrder->payment_token }}')" class="w-full py-8 bg-orange-500 hover:bg-orange-600 text-white rounded-[2rem] font-black text-3xl shadow-2xl shadow-orange-500/30 transition-all transform active:scale-[0.98] flex items-center justify-center relative overflow-hidden group">
                                    <span class="relative z-10 flex items-center gap-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h2M4 8h2m12 0h2M4 6h18M4 4h18" /></svg>
                                        SCAN QRIS SEKARANG
                                    </span>
                                    <div class="absolute inset-0 bg-gradient-to-r from-orange-600 to-rose-600 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                </button>
                                <button wire:click="markAsPaidAndDone" class="w-full py-4 text-center font-bold text-gray-300 hover:text-gray-500 transition-colors">Bypass Demo</button>
                            @else
                                <button wire:click="markAsDone" wire:loading.attr="disabled" class="w-full py-8 bg-black hover:bg-gray-800 text-white rounded-[2rem] font-black text-3xl shadow-2xl shadow-black/20 transition-all transform active:scale-[0.98] flex items-center justify-center relative group">
                                    <span wire:loading.remove class="relative z-10 flex items-center gap-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2-2v4h10z" /></svg>
                                        CETAK STRUK PESANAN
                                    </span>
                                    <div wire:loading class="relative z-10 flex items-center justify-center">
                                        <svg class="animate-spin h-10 w-10 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    </div>
                                    <div class="absolute inset-0 bg-gradient-to-r from-gray-900 to-black opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                </button>
                            @endif
                        @else
                            @if($checkoutStep === 1)
                                <button wire:click="goToPayment" class="w-full py-8 bg-black hover:bg-gray-800 text-white rounded-[2rem] font-black text-3xl shadow-2xl shadow-black/20 transition-all transform active:scale-[0.98] flex items-center justify-center relative overflow-hidden group">
                                    <span class="relative z-10 flex items-center gap-4">
                                        LANJUT KE PEMBAYARAN
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                                    </span>
                                    <div class="absolute inset-0 bg-gradient-to-r from-gray-900 to-black opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                </button>
                            @else
                                <div class="flex gap-4">
                                    <button wire:click="backToInfo" class="px-8 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-[2rem] font-black text-xl transition-all flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 17l-5-5m0 0l5-5m-5 5h12" /></svg>
                                    </button>
                                    <button wire:click="processCheckout" wire:loading.attr="disabled" class="flex-1 py-8 bg-black hover:bg-gray-800 text-white rounded-[2rem] font-black text-3xl shadow-2xl shadow-black/20 transition-all transform active:scale-[0.98] flex items-center justify-center relative overflow-hidden group">
                                        <span wire:loading.remove class="relative z-10">BAYAR SEKARANG</span>
                                        <div wire:loading class="relative z-10 flex items-center justify-center">
                                                <svg class="animate-spin h-10 w-10 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        </div>
                                        <div class="absolute inset-0 bg-gradient-to-r from-gray-900 to-black opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                    </button>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- VIEW 4: SUCCESS --}}
    @if($currentView === 'success')
        <div class="h-full w-full relative overflow-hidden flex flex-col items-center justify-center text-white p-8">
            {{-- Background Image --}}
            <div class="absolute inset-0 bg-cover bg-center"
                 style="background-image: url('{{ $restaurant->cover_image ? Storage::url($restaurant->cover_image) : 'https://images.unsplash.com/photo-1514933651103-005eab06c04d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80' }}')"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-orange-600/95 via-rose-600/95 to-orange-500/95 backdrop-blur-md"></div>

            <div class="relative z-10 flex flex-col items-center justify-center text-center">
                <div class="w-48 h-48 bg-white/20 backdrop-blur-xl rounded-[3rem] flex items-center justify-center mb-10 shadow-[0_20px_60px_rgba(0,0,0,0.3)] border border-white/30 animate-bounce duration-[2000ms]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-white drop-shadow-lg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                
                <h1 class="text-6xl font-black mb-4 tracking-tight">Terima Kasih!</h1>
                <p class="text-2xl font-bold text-white/90 max-w-lg mb-12 leading-relaxed">
                    Pesanan Anda sedang kami siapkan. <br/>
                    @if($paymentMethod == 'cash')
                        Silakan ambil struk dan bayar di kasir.
                    @else
                        Silakan tunggu nomor antrean Anda dipanggil.
                    @endif
                </p>
                
                <div class="bg-black/20 backdrop-blur-xl border border-white/20 rounded-[2.5rem] px-16 py-10 shadow-2xl">
                    <p class="text-sm font-black uppercase tracking-[0.3em] text-white/60 mb-3">Nomor Antrean Anda</p>
                    <span class="text-8xl font-black block tracking-tighter drop-shadow-xl">#{{ $createdOrder?->order_number ?? '001' }}</span>
                </div>
                
                <div class="mt-20 flex flex-col items-center">
                    <div class="w-64 h-1.5 bg-white/10 rounded-full overflow-hidden mb-4">
                        <div class="h-full bg-white/50 animate-[process_5s_linear_forwards]"></div>
                    </div>
                    <p class="text-white/40 font-bold uppercase tracking-widest text-xs" wire:poll.5s="resetKiosk">
                        Kembali otomatis dalam 5 detik...
                    </p>
                </div>
            </div>

            <style>
                @keyframes process {
                    from { width: 0%; }
                    to { width: 100%; }
                }
            </style>
        </div>
    @endif

    {{-- Universal Modal Overlays --}}
    
    {{-- Item Selection Modal --}}
    @if($selectedItem)
        <div class="fixed inset-0 z-[100] flex items-end md:items-center justify-center" aria-hidden="true">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" wire:click="closeItemModal"></div>
            <div class="bg-white w-full md:w-[500px] h-[90vh] md:h-auto md:max-h-[90vh] rounded-t-3xl md:rounded-3xl shadow-2xl relative z-10 flex flex-col overflow-hidden animate-[slideUp_0.3s_ease-out]">
                
                {{-- Modal Image --}}
                <div class="h-64 relative bg-gray-100 flex-shrink-0">
                    <button wire:click="closeItemModal" class="absolute top-4 right-4 bg-black/50 text-white w-10 h-10 rounded-full flex items-center justify-center hover:bg-black transition backdrop-blur-md z-10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                    </button>
                    @if($selectedItem->image)
                        <img src="{{ Storage::url($selectedItem->image) }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-6xl">🍽️</div>
                    @endif
                    
                    {{-- Gradient shadow for text --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                    <div class="absolute bottom-4 left-4 right-4 text-white">
                        <h2 class="text-3xl font-black leading-tight">{{ $selectedItem->name }}</h2>
                        <p class="text-sm opacity-90 line-clamp-2 mt-1">{{ $selectedItem->description }}</p>
                    </div>
                </div>

                {{-- Modal Body (Scrollable) --}}
                <div class="p-6 overflow-y-auto flex-1 hide-scrollbar bg-gray-50">
                    
                    {{-- Variants Section --}}
                    @if($selectedItem->variants->count() > 0)
                        <div class="mb-8">
                            <h4 class="font-black text-lg mb-3">Pilih Varian <span class="text-red-500">*</span></h4>
                            <div class="grid gap-3">
                                @foreach($selectedItem->variants as $variant)
                                    <label class="flex items-center justify-between p-4 bg-white border-2 rounded-2xl cursor-pointer transition-all {{ $selectedVariant == $variant->id ? 'border-black ring-1 ring-black shadow-md' : 'border-gray-100 hover:border-gray-200 shadow-sm' }}">
                                        <div class="flex items-center">
                                            <input type="radio" value="{{ $variant->id }}" wire:model.live="selectedVariant" class="h-5 w-5 text-black focus:ring-black border-gray-300">
                                            <span class="ml-4 font-bold text-gray-900">{{ $variant->name }}</span>
                                        </div>
                                        <span class="font-black">Rp {{ number_format($variant->price, 0, ',', '.') }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="mb-8">
                            <div class="flex justify-between items-center bg-white p-4 rounded-2xl border-2 border-gray-100 shadow-sm">
                                <span class="font-bold text-gray-500">Harga Standar</span>
                                @if($selectedItem->has_active_discount)
                                    <div class="flex flex-col items-end leading-tight">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-sm text-gray-400 line-through">Rp {{ number_format($selectedItem->original_price, 0, ',', '.') }}</span>
                                            @if($selectedItem->getActiveDiscount())
                                                <span class="text-[10px] bg-red-100 text-red-600 px-2 py-0.5 rounded-full font-bold uppercase tracking-wider">{{ $selectedItem->getActiveDiscount()->name }}</span>
                                            @endif
                                        </div>
                                        <span class="text-xl font-black text-red-600">{{ $selectedItem->formatted_price }}</span>
                                    </div>
                                @else
                                    <span class="text-xl font-black text-gray-900">{{ $selectedItem->formatted_price }}</span>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Addons Section --}}
                    @if($selectedItem->addons->count() > 0)
                        <div class="mb-8">
                            <h4 class="font-black text-lg mb-3 text-gray-900">Tambahan (Opsional)</h4>
                            <div class="grid gap-3">
                                @foreach($selectedItem->addons as $addon)
                                    <label class="flex items-center justify-between p-4 bg-white border-2 rounded-2xl cursor-pointer transition-all {{ in_array($addon->id, $selectedAddons) ? 'border-orange-500 ring-1 ring-orange-500 shadow-md bg-orange-50' : 'border-gray-100 hover:border-gray-200 shadow-sm' }}">
                                        <div class="flex items-center">
                                            <input type="checkbox" value="{{ $addon->id }}" wire:model.live="selectedAddons" class="h-5 w-5 text-orange-500 focus:ring-orange-500 border-gray-300 rounded">
                                            <span class="ml-4 font-bold text-gray-900">{{ $addon->name }}</span>
                                        </div>
                                        <span class="font-bold text-orange-600">+ Rp {{ number_format($addon->price, 0, ',', '.') }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Qty Component --}}
                    <div class="mb-2 bg-white p-4 rounded-2xl border-2 border-gray-100 shadow-sm flex items-center justify-between">
                        <span class="font-bold text-lg text-gray-500">Jumlah</span>
                        <div class="flex items-center space-x-6 bg-gray-50 rounded-xl p-1 border">
                            <button wire:click="decrementQuantity" class="w-12 h-12 rounded-xl bg-white shadow flex items-center justify-center font-black text-2xl text-red-500 pb-1" {{ $quantity <= 1 ? 'disabled' : '' }}>-</button>
                            <span class="font-black text-2xl w-6 text-center">{{ $quantity }}</span>
                            <button wire:click="incrementQuantity" class="w-12 h-12 rounded-xl bg-white shadow flex items-center justify-center font-black text-2xl text-green-500 pb-1">+</button>
                        </div>
                    </div>

                    {{-- Note Section --}}
                    <div class="mb-6">
                        <label class="block text-sm font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Catatan Pesanan</label>
                        <textarea wire:model.defer="note" rows="2" 
                                  class="w-full bg-white border-2 border-gray-100 focus:border-black rounded-2xl p-4 text-sm font-bold shadow-sm outline-none transition-all placeholder:text-gray-300" 
                                  placeholder="Contoh: Tidak pakai pedas, pisah sambal..."></textarea>
                    </div>
                    {{-- Inline Stock Error --}}
                    @if($stockErrorMessage)
                        <p x-data x-init="setTimeout(() => $wire.clearStockError(), 3000)"
                           class="text-red-500 text-sm font-semibold mb-6 flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                            {{ $stockErrorMessage }}
                        </p>
                    @else
                        <div class="mb-6"></div>
                    @endif

                    {{-- Smart Upselling (Recommendations) --}}
                    @if($restaurant->owner?->hasFeature('Smart Upselling') && $selectedItem->upsells->count() > 0)
                        <div class="mb-8">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="font-black text-lg text-gray-900">Pasangan Terbaik ✨</h4>
                                <span class="bg-orange-100 text-orange-600 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider">Recommended</span>
                            </div>
                            <div class="flex space-x-4 overflow-x-auto pb-4 -mx-2 px-2 hide-scrollbar">
                                @foreach($selectedItem->upsells as $upsell)
                                    @php 
                                        $uItem = $upsell->upsellItem; 
                                        $isBundled = isset($bundledItems[$uItem->id]);
                                    @endphp
                                    @if($uItem && $uItem->is_available)
                                        <div class="flex-shrink-0 w-48 bg-white rounded-3xl p-3 border-2 {{ $isBundled ? 'border-green-500 bg-green-50 shadow-md ring-1 ring-green-500' : 'border-gray-100' }} transition-all cursor-pointer group relative" 
                                            wire:click="toggleUpsell({{ $uItem->id }})"
                                        >
                                            <div class="relative h-32 mb-3 overflow-hidden rounded-2xl">
                                                @if($uItem->image)
                                                    <img src="{{ Storage::url($uItem->image) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                                @else
                                                    <div class="w-full h-full bg-gray-100 flex items-center justify-center text-3xl">🍽️</div>
                                                @endif
                                                
                                                @if($isBundled)
                                                    <div class="absolute top-2 right-2 bg-green-500 text-white p-1.5 rounded-full shadow-lg">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                        </svg>
                                                    </div>
                                                @else
                                                    <div class="absolute bottom-2 right-2">
                                                        <div class="bg-white/90 p-2 rounded-xl shadow-sm group-hover:bg-black group-hover:text-white transition-colors">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                            </svg>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <h5 class="text-sm font-black text-gray-900 line-clamp-1 mb-1">{{ $uItem->name }}</h5>
                                            @if($isBundled && isset($bundledItems[$uItem->id]['variant_name']))
                                                <p class="text-[10px] text-green-700 font-bold mb-1 line-clamp-1 italic">{{ $bundledItems[$uItem->id]['variant_name'] }}</p>
                                            @endif
                                            @if($uItem->has_active_discount)
                                                <div class="flex items-center gap-1">
                                                    <span class="text-[10px] text-gray-400 line-through">Rp{{ number_format($uItem->original_price, 0, ',', '.') }}</span>
                                                    <span class="text-xs font-black text-red-600">{{ $uItem->formatted_price }}</span>
                                                </div>
                                            @else
                                                <p class="text-xs font-black text-gray-700">{{ $uItem->formatted_price }}</p>
                                            @endif>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Quick Config Overlay for Upsells (Kiosk Version) --}}
                    @if($quickConfigItem)
                        <div class="absolute inset-0 z-[110] bg-white flex flex-col p-8 animate-[slideUp_0.3s_ease-out]">
                            <div class="flex items-center justify-between mb-8">
                                <h4 class="text-3xl font-black">Pilih Opsi: {{ $quickConfigItem->name }}</h4>
                                <button wire:click="cancelQuickConfig" class="p-2 bg-gray-100 rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div class="flex-1 overflow-y-auto hide-scrollbar pb-8">
                                {{-- Mini Variants --}}
                                @if($quickConfigItem->variants->count() > 0)
                                    <div class="mb-8">
                                        <p class="text-sm font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Varian</p>
                                        <div class="grid grid-cols-2 gap-4">
                                            @foreach($quickConfigItem->variants as $v)
                                                <button 
                                                    wire:click="$set('quickConfigVariant', {{ $v->id }})"
                                                    class="p-6 text-xl font-black rounded-3xl border-4 text-center transition-all {{ $quickConfigVariant == $v->id ? 'bg-black text-white border-black' : 'bg-white text-gray-600 border-gray-100' }}"
                                                >
                                                    {{ $v->name }} <br/>
                                                    <span class="text-xs font-bold opacity-70">(+Rp {{ number_format($v->price, 0, ',', '.') }})</span>
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- Mini Addons --}}
                                @if($quickConfigItem->addons->count() > 0)
                                    <div class="mb-8">
                                        <p class="text-sm font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Tambahan</p>
                                        <div class="grid grid-cols-1 gap-4">
                                            @foreach($quickConfigItem->addons as $a)
                                                <label class="flex items-center justify-between p-6 bg-white border-4 rounded-3xl cursor-pointer transition-all {{ in_array($a->id, $quickConfigAddons) ? 'border-green-500 bg-green-50 shadow-md' : 'border-gray-100' }}">
                                                    <div class="flex items-center">
                                                        <input type="checkbox" value="{{ $a->id }}" wire:model.live="quickConfigAddons" class="h-8 w-8 text-green-600 focus:ring-green-500 rounded-xl border-gray-300">
                                                        <span class="ml-4 text-xl font-black text-gray-900">{{ $a->name }}</span>
                                                    </div>
                                                    <span class="text-lg font-black text-green-600">+Rp {{ number_format($a->price, 0, ',', '.') }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- Mini Quantity --}}
                                <div class="flex items-center justify-between mt-8 p-6 bg-gray-100 rounded-[2rem]">
                                    <span class="text-xl font-black">Jumlah</span>
                                    <div class="flex items-center space-x-6">
                                        <button wire:click="decrementQuantity('quickConfigQuantity')" class="w-16 h-16 rounded-2xl bg-white shadow-lg flex items-center justify-center font-black text-3xl text-red-500 pb-1" {{ $quickConfigQuantity <= 1 ? 'disabled' : '' }}>-</button>
                                        <span class="font-black text-3xl w-8 text-center">{{ $quickConfigQuantity }}</span>
                                        <button wire:click="incrementQuantity('quickConfigQuantity')" class="w-16 h-16 rounded-2xl bg-white shadow-lg flex items-center justify-center font-black text-3xl text-green-500 pb-1">+</button>
                                    </div>
                                </div>
                                {{-- Inline Stock Error for Quick Config --}}
                                @if($quickConfigErrorMessage)
                                    <p x-data x-init="setTimeout(() => $wire.clearStockError('quickConfig'), 3000)"
                                       class="text-red-500 text-sm font-semibold mt-4 flex items-center gap-1.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                        {{ $quickConfigErrorMessage }}
                                    </p>
                                @endif
                                {{-- Quick Config Notes --}}
                                <div class="mt-8">
                                    <p class="text-sm font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Catatan Khusus</p>
                                    <textarea wire:model.defer="quickConfigNote" rows="2" 
                                        class="w-full bg-gray-50 border-4 border-gray-100 focus:border-green-500 rounded-3xl p-6 text-xl font-bold transition-all outline-none placeholder:text-gray-300"
                                        placeholder="Contoh: Tanpa es, double shot, dll..."></textarea>
                                </div>
                            </div>

                            <button wire:click="saveQuickConfig" class="w-full bg-green-600 text-white py-6 rounded-[2rem] font-black text-2xl shadow-xl shadow-green-500/20 hover:bg-green-700 transition">
                                Pakai Opsi Ini
                            </button>
                        </div>
                    @endif
                </div>

                {{-- Modal Footer (Kiosk Style) --}}
                <div class="p-8 bg-white border-t border-gray-100 shadow-[0_-10px_40px_rgba(0,0,0,0.05)]">
                    
                    {{-- Combo Summary (Kiosk Style) --}}
                    @if(count($bundledItems) > 0)
                        <div class="mb-6 p-6 bg-gray-50 rounded-[2rem] border-4 border-dashed border-gray-200">
                            <p class="text-xs font-black text-gray-400 uppercase tracking-[0.3em] mb-4 text-center">Ringkasan Paket</p>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-bold text-gray-700">{{ $quantity }}x {{ $selectedItem->name }}</span>
                                    <span class="text-lg font-black">Rp {{ number_format(($selectedItem->price + (\App\Models\MenuItemVariant::find($selectedVariant)?->price ?? 0) + \App\Models\MenuItemAddon::whereIn('id', $selectedAddons)->sum('price')) * $quantity, 0, ',', '.') }}</span>
                                </div>
                                @foreach($bundledItems as $b)
                                    <div class="flex flex-col text-green-600">
                                        <div class="flex justify-between items-center">
                                            <span class="text-lg font-bold">{{ $b['quantity'] }}x {{ $b['name'] }}</span>
                                            <span class="text-lg font-black">Rp {{ number_format($b['price'] * $b['quantity'], 0, ',', '.') }}</span>
                                        </div>
                                        @if(!empty($b['note']))
                                            <span class="text-xs font-bold italic opacity-80">* {{ $b['note'] }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex items-center justify-center space-x-3 mb-4 animate-bounce">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-lg font-black text-green-700">Selesaikan paket klik di bawah!</p>
                        </div>
                    @endif

                    @php
                        $mainItemUnitPrice = $selectedItem->price + (\App\Models\MenuItemVariant::find($selectedVariant)?->price ?? 0) + \App\Models\MenuItemAddon::whereIn('id', $selectedAddons)->sum('price');
                        $mainItemTotal = $mainItemUnitPrice * $quantity;
                        $bundleTotal = collect($bundledItems)->sum(fn($b) => $b['price'] * $b['quantity']);
                        $grandTotal = $mainItemTotal + $bundleTotal;
                        $totalCount = 1 + count($bundledItems);
                    @endphp

                    <button wire:click="addToCart" class="w-full bg-black hover:bg-gray-800 text-white py-6 rounded-[2rem] font-black text-2xl shadow-2xl transform transition-all active:scale-[0.98] flex items-center justify-between px-10">
                        <span>Tambah {{ $totalCount > 1 ? $totalCount . ' Menu' : 'ke Pesanan' }}</span>
                        <span class="bg-white/20 px-4 py-1.5 rounded-2xl text-xl">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Universal Toast purely CSS --}}
    <div x-data="{ 
            show: false, 
            message: '', 
            type: 'error',
            init() {
                window.addEventListener('notify', (e) => {
                    this.message = e.detail[0].message;
                    this.type = e.detail[0].type;
                    this.show = true;
                    setTimeout(() => { this.show = false }, 3000);
                });
            }
        }" 
        x-show="show" 
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="opacity-0 translate-y-10"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-10"
        class="fixed bottom-10 left-1/2 transform -translate-x-1/2 z-[200] px-6 py-4 rounded-full shadow-2xl flex items-center text-white font-bold"
        :class="type === 'error' ? 'bg-red-500' : 'bg-green-500'"
        style="display: none;"
    >
        <span x-text="type === 'error' ? '❌' : '✅'" class="mr-2 text-xl"></span>
        <span x-text="message" class="text-lg"></span>
    </div>

    <style>
        @keyframes slideUp {
            from { transform: translateY(100%); }
            to { transform: translateY(0); }
        }
    </style>
</div>
