<div class="min-h-screen flex flex-col items-center justify-center p-5 py-14">

    {{-- Brand Header --}}
    <div class="mb-10 text-center" style="animation: fadeInDown .5s ease both">
        @if($restaurant->logo)
            <div class="inline-flex items-center justify-center mb-4 p-2 rounded-2xl" style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
                <img src="{{ Storage::url($restaurant->logo) }}" alt="{{ $restaurant->name }}"
                    class="rounded-xl object-contain" style="height:56px;max-width:160px">
            </div>
        @else
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl mb-4 text-2xl font-black text-white shadow-xl"
                style="background:linear-gradient(135deg,#f59e0b,#ea580c)">
                {{ substr($restaurant->name, 0, 1) }}
            </div>
        @endif
        <p class="text-xs font-bold uppercase tracking-[0.2em] mb-1" style="color:rgba(255,255,255,.4)">Portal Member</p>
        <h1 class="text-2xl font-black text-white">{{ $restaurant->name }}</h1>
    </div>

    {{-- Main Card --}}
    <div class="w-full max-w-sm" style="animation: fadeInUp .5s ease .1s both">
        <div class="glass-card rounded-3xl overflow-hidden" style="box-shadow:0 32px 80px rgba(0,0,0,.5)">

            @if(!$otpSent)

                {{-- ── STEP 1: Input Nomor WA ────────────────────────── --}}
                <div class="p-8">

                    {{-- Icon & Title --}}
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl mb-4"
                            style="background:rgba(34,197,94,.12);border:1px solid rgba(34,197,94,.2)">
                            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="rgba(74,222,128,1)" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-black text-white">Masuk ke Akun Member</h2>
                        <p class="text-sm mt-1.5" style="color:rgba(255,255,255,.45)">Masukkan nomor WhatsApp yang terdaftar</p>
                    </div>

                    {{-- Form --}}
                    <form wire:submit="sendOtp">
                        <div class="mb-5">
                            <label class="block text-xs font-bold uppercase tracking-widest mb-2.5" style="color:rgba(255,255,255,.4)">
                                Nomor WhatsApp
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold" style="color:rgba(255,255,255,.3)">📱</span>
                                <input
                                    type="tel"
                                    wire:model="whatsapp"
                                    placeholder="08xxxxxxxxxx"
                                    autofocus
                                    class="input-dark w-full rounded-2xl pl-12 pr-4 py-4 text-lg font-bold"
                                    style="{{ $errors->has('whatsapp') ? 'border-color:rgba(248,113,113,.6)' : '' }}"
                                >
                            </div>
                            @error('whatsapp')
                                <div class="flex items-center gap-1.5 mt-2.5">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="rgb(248,113,113)" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <p class="text-xs font-semibold" style="color:rgb(248,113,113)">{{ $message }}</p>
                                </div>
                            @enderror
                        </div>

                        <button type="submit" wire:loading.attr="disabled"
                            class="btn-primary w-full py-4 rounded-2xl text-white font-black text-base flex items-center justify-center gap-2">
                            <span wire:loading.remove wire:target="sendOtp">
                                Kirim Kode OTP
                                <svg class="w-4 h-4 inline ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </span>
                            <span wire:loading wire:target="sendOtp" class="items-center gap-2" style="display:none">
                                <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="white" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="white" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Mengirim OTP...
                            </span>
                        </button>
                    </form>

                    {{-- Separator --}}
                    <div class="flex items-center gap-3 my-6">
                        <div class="flex-1 h-px" style="background:rgba(255,255,255,.07)"></div>
                        <span class="text-xs font-semibold" style="color:rgba(255,255,255,.2)">INFO</span>
                        <div class="flex-1 h-px" style="background:rgba(255,255,255,.07)"></div>
                    </div>

                    {{-- Info chips --}}
                    <div class="space-y-2.5">
                        <div class="flex items-center gap-2.5 text-xs font-medium" style="color:rgba(255,255,255,.4)">
                            <span style="color:rgba(167,139,250,1)">🔐</span> Login tanpa password — cukup OTP 6 digit
                        </div>
                        <div class="flex items-center gap-2.5 text-xs font-medium" style="color:rgba(255,255,255,.4)">
                            <span>⏱️</span> Kode OTP berlaku 5 menit
                        </div>
                        <div class="flex items-center gap-2.5 text-xs font-medium" style="color:rgba(255,255,255,.4)">
                            <span>🎖️</span> Lihat poin, tier, dan histori belanja Anda
                        </div>
                    </div>
                </div>

            @else

                {{-- ── STEP 2: Konfirmasi Terkirim ─────────────────────── --}}
                <div class="p-8 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl mb-5"
                        style="background:rgba(34,197,94,.12);border:1px solid rgba(34,197,94,.2)">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="rgba(74,222,128,1)" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-black text-white mb-1">OTP Berhasil Dikirim!</h2>
                    <p class="text-sm mb-6" style="color:rgba(255,255,255,.45)">Kode dikirim ke:</p>

                    {{-- Kanal terkirim --}}
                    <div class="space-y-3 mb-7">
                        @foreach($sentVia as $via)
                            <div class="badge-sent rounded-2xl px-4 py-3.5 flex items-center gap-3 text-left">
                                @if($via['channel'] === 'whatsapp')
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 text-lg"
                                        style="background:rgba(34,197,94,.15)">📱</div>
                                    <div>
                                        <p class="text-xs font-bold uppercase tracking-wider" style="color:rgba(74,222,128,.8)">WhatsApp</p>
                                        <p class="text-white font-bold text-sm mt-0.5">{{ $via['destination'] }}</p>
                                    </div>
                                @else
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 text-lg"
                                        style="background:rgba(96,165,250,.15)">✉️</div>
                                    <div>
                                        <p class="text-xs font-bold uppercase tracking-wider" style="color:rgba(147,197,253,.8)">Email</p>
                                        <p class="text-white font-bold text-sm mt-0.5">{{ $via['destination'] }}</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <a href="{{ route('member.portal.otp', $restaurant->slug) }}"
                        class="btn-primary block w-full py-4 rounded-2xl text-white font-black text-base text-center">
                        Masukkan Kode OTP →
                    </a>

                    <p class="text-xs mt-4" style="color:rgba(255,255,255,.2)">Kode berlaku 5 menit · Jangan bagikan ke siapapun</p>
                </div>
            @endif

        </div>

        {{-- Footer link --}}
        <div class="text-center mt-6">
            <a href="{{ route('frontend.restaurants.show', $restaurant->slug) }}"
                class="text-xs font-medium transition-all"
                style="color:rgba(255,255,255,.25)"
                onmouseover="this.style.color='rgba(255,255,255,.5)'"
                onmouseout="this.style.color='rgba(255,255,255,.25)'">
                ← Kembali ke halaman {{ $restaurant->name }}
            </a>
        </div>
    </div>

    <style>
    @keyframes fadeInDown {
        from { opacity:0; transform:translateY(-16px); }
        to   { opacity:1; transform:translateY(0); }
    }
    @keyframes fadeInUp {
        from { opacity:0; transform:translateY(16px); }
        to   { opacity:1; transform:translateY(0); }
    }
    </style>

</div>
