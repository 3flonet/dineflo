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
        <p class="text-xs font-bold uppercase tracking-[0.2em] mb-1" style="color:rgba(255,255,255,.4)">Verifikasi OTP</p>
        <h1 class="text-2xl font-black text-white">{{ $restaurant->name }}</h1>
    </div>

    <div class="w-full max-w-sm" style="animation: fadeInUp .5s ease .1s both">
        <div class="glass-card rounded-3xl overflow-hidden" style="box-shadow:0 32px 80px rgba(0,0,0,.5)">

            @if(!$expired)
                <div class="p-8">
                    {{-- Icon & Title --}}
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl mb-4"
                            style="background:rgba(139,92,246,.15);border:1px solid rgba(139,92,246,.25)">
                            <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="rgba(167,139,250,1)" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-black text-white">Masukkan Kode OTP</h2>
                        <p class="text-sm mt-1.5" style="color:rgba(255,255,255,.45)">Periksa WhatsApp atau Email Anda</p>
                    </div>

                    <form wire:submit="verify">
                        <div class="mb-5">
                            <label class="block text-xs font-bold uppercase tracking-widest mb-2.5 text-center" style="color:rgba(255,255,255,.4)">
                                Kode OTP 6 Digit
                            </label>
                            <input
                                type="text"
                                wire:model="otp"
                                placeholder="• • • • • •"
                                maxlength="6"
                                autofocus
                                inputmode="numeric"
                                pattern="[0-9]*"
                                class="input-dark w-full rounded-2xl px-4 py-5 text-4xl font-black tracking-[.5em] text-center"
                                style="{{ $errors->has('otp') ? 'border-color:rgba(248,113,113,.6)' : '' }}"
                            >
                            @error('otp')
                                <div class="flex items-center justify-center gap-1.5 mt-3">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="rgb(248,113,113)" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <p class="text-xs font-semibold" style="color:rgb(248,113,113)">{{ $message }}</p>
                                </div>
                            @enderror
                        </div>

                        <button type="submit" wire:loading.attr="disabled"
                            class="btn-primary w-full py-4 rounded-2xl text-white font-black text-base flex items-center justify-center gap-2">
                            <span wire:loading.remove>Verifikasi & Masuk ✓</span>
                            <span wire:loading style="display:none" class="flex items-center gap-2">
                                <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="white" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="white" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Memverifikasi...
                            </span>
                        </button>

                        <button type="button" wire:click="backToLogin"
                            class="w-full mt-3 py-3 text-sm font-semibold rounded-2xl transition-all"
                            style="color:rgba(255,255,255,.35)"
                            onmouseover="this.style.color='rgba(255,255,255,.7)';this.style.background='rgba(255,255,255,.05)'"
                            onmouseout="this.style.color='rgba(255,255,255,.35)';this.style.background='transparent'">
                            ← Ganti nomor / kirim ulang OTP
                        </button>
                    </form>
                </div>

            @else
                {{-- Expired / max attempts --}}
                <div class="p-8 text-center">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl mb-5"
                        style="background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.2)">
                        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="rgba(252,165,165,1)" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-black text-white mb-2">Kode Tidak Valid</h2>
                    <p class="text-sm mb-7" style="color:rgba(255,255,255,.45)">Terlalu banyak percobaan atau kode sudah kadaluarsa.</p>
                    <a href="{{ route('member.portal.login', $restaurant->slug) }}"
                        class="block w-full py-4 rounded-2xl text-white font-bold text-center transition-all"
                        style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.1)"
                        onmouseover="this.style.background='rgba(255,255,255,.13)'"
                        onmouseout="this.style.background='rgba(255,255,255,.08)'">
                        ← Minta Kode OTP Baru
                    </a>
                </div>
            @endif

        </div>

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
    @keyframes fadeInDown { from{opacity:0;transform:translateY(-16px)} to{opacity:1;transform:translateY(0)} }
    @keyframes fadeInUp   { from{opacity:0;transform:translateY(16px)}  to{opacity:1;transform:translateY(0)} }
    </style>

</div>
