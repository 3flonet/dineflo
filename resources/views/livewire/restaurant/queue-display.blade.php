<div class="h-screen w-screen bg-neutral-950 text-white overflow-hidden flex flex-col font-sans selection:bg-blue-500" 
     wire:poll.10s="refreshQueues" 
     x-data="queueDisplay()">
    
    <div class="flex flex-1 min-h-0 relative">
        {{-- LEFT SIDE: PROMOTION (60%) --}}
        <div class="w-[60%] h-full bg-black relative border-r border-white/5 overflow-hidden">
            @if(count($promotions) > 0)
                <template x-for="(promo, index) in promos" :key="promo.id">
                    <div x-show="promoIndex === index" 
                         x-transition:enter="transition ease-out duration-1000"
                         x-transition:enter-start="opacity-0 scale-105"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-1000"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="absolute inset-0">
                        <template x-if="promo.type === 'image'">
                            <img :src="'/storage/' + promo.file_path" class="w-full h-full object-cover">
                        </template>
                        <template x-if="promo.type === 'video'">
                            <video :src="'/storage/' + promo.file_path" 
                                   class="w-full h-full object-cover" 
                                   muted 
                                   x-on:ended="nextPromo()" 
                                   x-effect="if (promoIndex === index) { $el.muted = true; $el.play().catch(e => console.log('Autoplay blocked', e)); } else { $el.pause(); $el.currentTime = 0; }">
                            </video>
                        </template>
                    </div>
                </template>
            @else
                <div class="h-full flex flex-col items-center justify-center bg-neutral-900">
                    <div class="text-8xl mb-4 opacity-20">📡</div>
                    <p class="text-white/20 font-black uppercase tracking-widest">Belum ada konten promosi</p>
                </div>
            @endif

            {{-- CALLING OVERLAY --}}
            <div x-show="showCallOverlay" 
                 x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="translate-y-full opacity-0"
                 x-transition:enter-end="translate-y-0 opacity-100"
                 x-transition:leave="transition ease-in duration-500"
                 x-transition:leave-start="translate-y-0 opacity-100"
                 x-transition:leave-end="translate-y-full opacity-0"
                 class="absolute inset-x-0 bottom-0 top-0 bg-blue-600/98 backdrop-blur-3xl z-50 flex flex-col items-center justify-center p-12 text-center animate-pulse-bg">
                
                {{-- Glowing background circles --}}
                <div class="absolute inset-0 overflow-hidden pointer-events-none">
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-white opacity-5 rounded-full animate-ping"></div>
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-white opacity-10 rounded-full animate-pulse"></div>
                </div>

                <div class="animate-bounce mb-8 relative z-10">
                    <span class="px-8 py-3 bg-white/20 rounded-full border border-white/30 text-2xl font-black uppercase tracking-widest text-white shadow-2xl">
                        PANGGILAN SEKARANG
                    </span>
                </div>

                <div class="relative z-10 animate-scale-pulse">
                    <h2 class="text-[28rem] font-black leading-none tracking-tighter text-white drop-shadow-[0_20px_50px_rgba(0,0,0,0.4)] mb-8" x-text="callingData?.number"></h2>
                </div>

                <div class="flex flex-col items-center gap-6 relative z-10">
                    <p class="text-7xl font-black uppercase tracking-widest text-white drop-shadow-lg" x-text="callingData?.customer"></p>
                    <div class="w-32 h-2 bg-white/50 rounded-full animate-pulse"></div>
                    <p class="text-4xl font-bold text-white/90 italic drop-shadow-md">Silakan menuju meja / kasir</p>
                </div>
            </div>
        </div>

        {{-- RIGHT SIDE: QUEUE STATUS (40%) --}}
        <div class="w-[40%] h-full flex flex-col bg-neutral-900 p-8">
            {{-- Header Right --}}
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    @if($restaurant->logo)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($restaurant->logo) }}" class="h-14 w-auto object-contain" alt="{{ $restaurant->name }}">
                    @else
                        <div class="h-12 w-12 bg-blue-600 rounded-xl flex items-center justify-center font-black text-2xl text-white">
                            {{ substr($restaurant->name, 0, 1) }}
                        </div>
                    @endif
                    <div>
                        <h1 class="text-2xl font-black uppercase tracking-tight text-white">{{ $restaurant->name }}</h1>
                        <p class="text-[10px] font-black uppercase tracking-[0.4em] text-white/30">Queue Monitoring System</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-4xl font-black tracking-tight leading-none text-white" id="clock">{{ now()->format('H:i') }}</div>
                    <p class="text-[10px] uppercase font-black tracking-widest text-white/20 mt-1">{{ now()->format('l, d F Y') }}</p>
                </div>
            </div>

            {{-- NOW CALLING LIST (Top 3) --}}
            <div class="mb-8">
                <div class="flex items-center gap-4 mb-4">
                    <div class="h-px flex-1 bg-white/10"></div>
                    <h3 class="text-xs font-black uppercase tracking-[0.5em] text-blue-400">Panggilan</h3>
                    <div class="h-px flex-1 bg-white/10"></div>
                </div>
                <div class="space-y-4">
                    @forelse($callingQueues as $q)
                        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-3xl p-6 border-b-8 border-blue-900 shadow-xl flex items-center justify-between relative overflow-hidden group">
                            <div class="absolute right-0 top-0 bottom-0 w-32 bg-white/5 skew-x-[-20deg] translate-x-16 group-hover:translate-x-12 transition-transform duration-500"></div>
                            <div class="relative z-10">
                                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-white/50 mb-1">DIPANGGIL</p>
                                <h4 class="text-5xl font-black tracking-tighter leading-none text-white">{{ $q->full_number }}</h4>
                            </div>
                            <div class="text-right relative z-10">
                                <p class="text-xl font-black uppercase max-w-[150px] line-clamp-1 mb-1 text-white">{{ $q->customer_name ?: 'PELANGGAN' }}</p>
                                @if($q->table)
                                    <span class="px-3 py-1 bg-white/20 rounded-lg text-xs font-black uppercase tracking-widest text-white">Meja {{ $q->table->name }}</span>
                                @else
                                    <span class="px-3 py-1 bg-black/20 rounded-lg text-xs font-black uppercase tracking-widest text-white/60">Antrean</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="h-32 rounded-3xl border-2 border-dashed border-white/10 flex flex-col items-center justify-center opacity-30">
                            <p class="text-sm font-black uppercase tracking-widest text-white/50">Belum Ada Panggilan</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- WAITING LIST --}}
            <div class="flex-1 min-h-0 flex flex-col">
                <div class="flex items-center gap-4 mb-4">
                    <div class="h-px flex-1 bg-white/10"></div>
                    <h3 class="text-xs font-black uppercase tracking-[0.5em] text-white/30">Menunggu</h3>
                    <div class="h-px flex-1 bg-white/10"></div>
                </div>
                <div class="flex-1 overflow-y-auto grid grid-cols-2 gap-3 pr-2 hide-scrollbar">
                    @forelse($waitingQueues as $q)
                        <div class="bg-white/5 border border-white/5 rounded-2xl p-4 flex items-center justify-between group hover:bg-white/10 transition-all">
                            <span class="text-3xl font-black text-white group-hover:text-blue-400">{{ $q->full_number }}</span>
                            <div class="text-right">
                                <p class="text-[10px] font-black uppercase opacity-40 leading-none mb-1 text-white">{{ $q->customer_name ?: 'PLG' }}</p>
                                <p class="text-[9px] font-bold opacity-20 uppercase tracking-tighter text-white">{{ $q->guest_count }}P | {{ $q->prefix === 'A' ? 'S' : ($q->prefix === 'B' ? 'M' : 'L') }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="col-span-2 text-center py-10 text-white/10 font-black uppercase tracking-widest text-xs">Kosong</p>
                    @endforelse
                </div>
            </div>

            {{-- QR Code Section (Mini) --}}
            <div class="mt-8 p-4 bg-white/5 rounded-3xl flex items-center gap-4 border border-white/5">
                <div class="p-2 bg-white rounded-xl">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(80)->generate(route('frontend.restaurants.show', $restaurant->slug)) !!}
                </div>
                <div class="flex-1">
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-white/30 mb-1">Mobile Queue</p>
                    <p class="text-xs font-bold leading-tight text-white">Scan untuk ambil antrean dari perangkat Anda.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- FOOTER: RUNNING TEXT --}}
    @if($runningText)
        <div class="h-16 bg-blue-600 flex items-center border-t border-blue-400 overflow-hidden relative shadow-[0_-10px_30px_rgba(0,0,0,0.5)]">
            <div class="flex-shrink-0 bg-blue-800 px-6 h-full flex items-center font-black uppercase tracking-[0.3em] text-sm z-10 shadow-xl border-r border-blue-500/30 text-white">INFO</div>
            <div class="flex-1 overflow-hidden relative">
                <div class="whitespace-nowrap inline-block animate-marquee will-change-transform">
                    <span class="text-2xl font-black uppercase px-12 tracking-wide flex items-center gap-8 text-white">
                        @php $messages = explode('|', $runningText); @endphp
                        @foreach(range(1, 5) as $i)
                            @foreach($messages as $msg)
                                <span>{{ trim($msg) }}</span>
                                <span class="bg-white/30 h-2 w-2 rounded-full"></span>
                            @endforeach
                        @endforeach
                    </span>
                </div>
            </div>
            <div class="flex-shrink-0 bg-blue-800 px-6 h-full flex items-center font-black text-sm z-10 border-l border-blue-500/30 text-white">{{ now()->format('H:i') }}</div>
        </div>
    @endif

    {{-- AUDIO ACTIVATION OVERLAY --}}
    <div x-show="!audioEnabled" 
         class="fixed inset-0 z-[100] bg-neutral-950/90 backdrop-blur-md flex flex-col items-center justify-center p-12 text-center">
        <div class="mb-8 p-8 bg-blue-600 rounded-full animate-pulse shadow-[0_0_50px_rgba(37,99,235,0.5)]">
            <svg class="w-24 h-24 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15.536a5 5 0 001.414 1.414m2.828-9.9a9 9 0 012.828 0m-2.828 9.9a9 9 0 010-12.728M5.586 15.536a5 5 0 001.414 1.414m2.828-9.9a9 9 0 012.828 0"></path>
            </svg>
        </div>
        <h2 class="text-4xl font-black text-white mb-4 uppercase tracking-tighter">Aktifkan Suara & Panggilan</h2>
        <p class="text-white/60 text-xl max-w-md mb-8 leading-relaxed">Browser membutuhkan interaksi manual untuk mengizinkan sistem memutar suara panggilan (Text-to-Speech).</p>
        <button x-on:click="enableAudio()" 
                class="px-12 py-5 bg-blue-600 hover:bg-blue-500 text-white text-2xl font-black rounded-full shadow-2xl transform hover:scale-105 active:scale-95 transition-all uppercase tracking-widest border-b-4 border-blue-800">
            Aktifkan Sekarang
        </button>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            if (!Alpine.store('queueDisplayStore')) {
                Alpine.data('queueDisplay', () => ({
                    showCallOverlay: false,
                    audioEnabled: false,
                    callingData: null,
                    promoIndex: 0,
                    promos: @js($promotions),
                    promoTimer: null,
                    init() {
                        this.startPromoCycle();
                        window.addEventListener('trigger-call', (e) => this.handleCall(e.detail));
                        
                        // Reactivity: Sync promos when Livewire property updates
                        this.$watch('$wire.promotions', (newVal) => {
                            const oldLength = this.promos.length;
                            this.promos = newVal;
                            
                            if (this.promoIndex >= this.promos.length) {
                                this.promoIndex = 0;
                            }
                            
                            if (oldLength === 0 && this.promos.length > 0) {
                                this.startPromoCycle();
                            }
                        });
                    },
                    enableAudio() {
                        this.audioEnabled = true;
                        // Play dummy sound to unlock audio
                        this.playDingDong();
                        // Trigger a silent speech to unlock TTS
                        if ('speechSynthesis' in window) {
                            const utterance = new SpeechSynthesisUtterance('');
                            window.speechSynthesis.speak(utterance);
                        }
                    },
                    startPromoCycle() {
                        if (this.promos.length === 0) return;
                        this.resetPromoTimer();
                    },
                    resetPromoTimer() {
                        if (this.promoTimer) clearTimeout(this.promoTimer);
                        const currentPromo = this.promos[this.promoIndex];
                        if (!currentPromo) return;
                        
                        if (currentPromo.type === 'image') {
                            this.promoTimer = setTimeout(() => { this.nextPromo(); }, currentPromo.duration * 1000);
                        }
                    },
                    nextPromo() {
                        if (this.promos.length === 0) return;
                        this.promoIndex = (this.promoIndex + 1) % this.promos.length;
                        this.resetPromoTimer();
                    },
                    handleCall(detail) {
                        const data = Array.isArray(detail) ? detail[0] : detail;
                        this.callingData = data;
                        this.showCallOverlay = true;
                        
                        if (this.audioEnabled) {
                            this.playDingDong();
                            setTimeout(() => this.speak(data), 1500);
                        }
                        
                        setTimeout(() => { this.showCallOverlay = false; }, 8000);
                    },
                    playDingDong() {
                        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                        const osc1 = audioCtx.createOscillator();
                        const osc2 = audioCtx.createOscillator();
                        const gain = audioCtx.createGain();
                        osc1.type = 'sine'; osc2.type = 'sine';
                        osc1.frequency.setValueAtTime(523.25, audioCtx.currentTime);
                        osc2.frequency.setValueAtTime(392.00, audioCtx.currentTime);
                        gain.gain.setValueAtTime(0.3, audioCtx.currentTime);
                        gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 1.2);
                        osc1.connect(gain); osc2.connect(gain);
                        gain.connect(audioCtx.destination);
                        osc1.start(); osc2.start();
                        osc1.stop(audioCtx.currentTime + 1.2); osc2.stop(audioCtx.currentTime + 1.2);
                    },
                    speak(data) {
                        if (!('speechSynthesis' in window)) return;
                        const utterance = new SpeechSynthesisUtterance();
                        const customerName = data.customer || 'Pelanggan';
                        utterance.text = `Nomor antrean ${data.number}. ${customerName}. Silakan menuju konfirmasi.`;
                        utterance.lang = 'id-ID';
                        utterance.rate = 0.9;
                        utterance.pitch = 1.1;
                        window.speechSynthesis.speak(utterance);
                    }
                }));
            }
        });

        setInterval(() => {
            const clockEl = document.getElementById('clock');
            if (clockEl) {
                const now = new Date();
                const time = now.toLocaleTimeString('en-US', { hour12: false, hour: '2-digit', minute: '2-digit' });
                clockEl.innerText = time;
            }
        }, 1000);
    </script>

    <style>
        @keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
        .animate-marquee { animation: marquee 30s linear infinite; }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        @keyframes scale-pulse {
            0%, 100% { transform: scale(1); filter: drop-shadow(0 0 0px rgba(255,255,255,0)); }
            50% { transform: scale(1.15); filter: drop-shadow(0 0 50px rgba(255,255,255,0.8)); }
        }
        .animate-scale-pulse {
            animation: scale-pulse 1.5s ease-in-out infinite;
            display: inline-block;
            will-change: transform, filter;
        }

        @keyframes pulse-bg {
            0%, 100% { background-color: rgba(37, 99, 235, 0.98); }
            50% { background-color: rgba(30, 58, 138, 0.98); }
        }
        .animate-pulse-bg {
            animation: pulse-bg 2s ease-in-out infinite;
        }
    </style>
</div>
