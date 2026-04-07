<div class="min-h-screen flex flex-col">

    {{-- ── HEADER ─────────────────────────────────────────────────── --}}
    <header style="position:sticky;top:0;z-index:50;background:rgba(8,8,16,.85);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border-bottom:1px solid rgba(255,255,255,.06)">
        <div class="max-w-2xl mx-auto px-5 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                @if($restaurant->logo)
                    <div class="p-1.5 rounded-xl" style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.08)">
                        <img src="{{ Storage::url($restaurant->logo) }}" class="h-8 w-auto rounded-lg object-contain" style="max-width:80px" alt="{{ $restaurant->name }}">
                    </div>
                @else
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm font-black text-white"
                        style="background:linear-gradient(135deg,#f59e0b,#ea580c)">
                        {{ substr($restaurant->name, 0, 1) }}
                    </div>
                @endif
                <div>
                    <p class="text-white font-bold text-sm leading-tight">{{ $member->name }}</p>
                    <p class="text-xs leading-tight" style="color:rgba(255,255,255,.35)">{{ $restaurant->name }}</p>
                </div>
            </div>
            <button wire:click="logout"
                class="flex items-center gap-1.5 text-xs font-bold px-3 py-2 rounded-xl transition-all"
                style="color:rgba(255,255,255,.35);background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.07)"
                onmouseover="this.style.color='rgba(252,165,165,1)';this.style.borderColor='rgba(239,68,68,.3)';this.style.background='rgba(239,68,68,.08)'"
                onmouseout="this.style.color='rgba(255,255,255,.35)';this.style.borderColor='rgba(255,255,255,.07)';this.style.background='rgba(255,255,255,.05)'">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Keluar
            </button>
        </div>
    </header>

    {{-- ── MAIN CONTENT ───────────────────────────────────────────── --}}
    <div class="flex-1 max-w-2xl mx-auto w-full px-4 py-6 space-y-5">

        @php
            $tierEmoji = match($member->tier) { 'gold' => '🥇', 'silver' => '🥈', default => '🥉' };
            $tierLabel = match($member->tier) { 'gold' => 'Gold Member', 'silver' => 'Silver Member', default => 'Bronze Member' };
            $tierGradient = match($member->tier) {
                'gold'   => 'linear-gradient(135deg, rgba(202,138,4,.25) 0%, rgba(161,98,7,.12) 100%)',
                'silver' => 'linear-gradient(135deg, rgba(148,163,184,.2) 0%, rgba(100,116,139,.1) 100%)',
                default  => 'linear-gradient(135deg, rgba(180,83,9,.22) 0%, rgba(146,64,14,.1) 100%)',
            };
            $tierBorder = match($member->tier) {
                'gold'   => 'rgba(202,138,4,.4)',
                'silver' => 'rgba(148,163,184,.3)',
                default  => 'rgba(180,83,9,.35)',
            };
            $tierBarColor = match($member->tier) {
                'gold'   => 'linear-gradient(90deg,#b45309,#ca8a04,#fbbf24)',
                'silver' => 'linear-gradient(90deg,#64748b,#94a3b8,#e2e8f0)',
                default  => 'linear-gradient(90deg,#92400e,#b45309,#f97316)',
            };
        @endphp

        {{-- ── TIER CARD ─────────────────────────────────────────── --}}
        <div class="rounded-3xl p-6 relative overflow-hidden" style="background:{{ $tierGradient }};border:1px solid {{ $tierBorder }};box-shadow:0 20px 60px rgba(0,0,0,.4)">
            {{-- Watermark text --}}
            <div class="absolute -right-4 top-1/2 -translate-y-1/2 font-black select-none pointer-events-none"
                style="font-size:5.5rem;line-height:1;letter-spacing:-.02em;color:rgba(255,255,255,.04);transform:translateY(-50%) rotate(-8deg)">
                {{ strtoupper($member->tier) }}
            </div>

            <div class="relative">
                <p class="text-xs font-bold uppercase tracking-[.15em] mb-2" style="color:rgba(255,255,255,.4)">Status Keanggotaan</p>
                <div class="flex items-center gap-3 mb-6">
                    <span style="font-size:2.5rem;line-height:1">{{ $tierEmoji }}</span>
                    <h2 class="text-3xl font-black text-white leading-tight">{{ $tierLabel }}</h2>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-2xl p-4" style="background:rgba(0,0,0,.2)">
                        <p class="text-xs font-bold uppercase tracking-wider mb-1.5" style="color:rgba(255,255,255,.35)">Saldo Poin</p>
                        <p class="text-4xl font-black text-white leading-none">{{ number_format($member->points_balance) }}</p>
                        <p class="text-xs mt-1 font-semibold" style="color:rgba(255,255,255,.25)">Poin</p>
                    </div>
                    <div class="rounded-2xl p-4" style="background:rgba(0,0,0,.2)">
                        <p class="text-xs font-bold uppercase tracking-wider mb-1.5" style="color:rgba(255,255,255,.35)">Total Belanja</p>
                        <p class="text-xl font-black text-white leading-none">Rp {{ number_format($member->total_spent, 0, ',', '.') }}</p>
                        <p class="text-xs mt-1 font-semibold" style="color:rgba(255,255,255,.25)">Kumulatif</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── PROGRESS BAR ──────────────────────────────────────── --}}
        @if($tierProgress['next'])
            <div class="glass-card rounded-2xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm font-semibold" style="color:rgba(255,255,255,.5)">
                        Progres ke <strong class="text-white">{{ $tierProgress['next'] }}</strong>
                    </p>
                    <span class="text-xs font-black px-2.5 py-1 rounded-lg" style="background:rgba(255,255,255,.08);color:rgba(255,255,255,.6)">
                        {{ $tierProgress['percentage'] }}%
                    </span>
                </div>
                <div class="h-2.5 rounded-full overflow-hidden mb-3" style="background:rgba(255,255,255,.08)">
                    <div class="h-full rounded-full transition-all duration-[1.5s]"
                        style="width:{{ $tierProgress['percentage'] }}%;background:{{ $tierBarColor }}">
                    </div>
                </div>
                <p class="text-xs" style="color:rgba(255,255,255,.35)">
                    Belanja
                    <span class="font-bold" style="color:rgba(255,255,255,.6)">Rp {{ number_format($tierProgress['remaining'], 0, ',', '.') }}</span>
                    lagi untuk naik ke tier {{ $tierProgress['next'] }}
                </p>
            </div>
        @else
            <div class="rounded-2xl p-5 flex items-center gap-4" style="background:rgba(202,138,4,.12);border:1px solid rgba(202,138,4,.25)">
                <span style="font-size:2rem">🏆</span>
                <div>
                    <p class="font-bold" style="color:rgba(253,224,71,1)">Tier Tertinggi!</p>
                    <p class="text-sm" style="color:rgba(255,255,255,.45)">Anda adalah Gold Member — tier paling eksklusif.</p>
                </div>
            </div>
        @endif

        {{-- ── GIFT CARDS ─────────────────────────────────────────── --}}
        @if($giftCards->isNotEmpty())
            <div>
                <h3 class="text-base font-black text-white mb-3 flex items-center gap-2.5">
                    <span class="text-lg">🎁</span> Gift Card Aktif
                    <span class="text-xs font-bold px-2 py-0.5 rounded-lg ml-auto" style="background:rgba(236,72,153,.15);color:rgba(244,114,182,1)">
                        {{ $giftCards->count() }} Tersedia
                    </span>
                </h3>
                <div class="space-y-3">
                    @foreach($giftCards as $gc)
                        <div class="rounded-2xl p-5 relative overflow-hidden" 
                            style="background:linear-gradient(135deg, rgba(236,72,153,.15) 0%, rgba(190,24,93,.05) 100%);border:1px solid rgba(236,72,153,.25)">
                            <div class="absolute -right-4 -top-6 text-[6rem] opacity-5 pointer-events-none select-none">🎁</div>
                            <div class="relative flex justify-between gap-4">
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-widest mb-1" style="color:rgba(244,114,182,.8)">Kode Kupon</p>
                                    <p class="text-lg font-black tracking-widest text-white leading-none">{{ $gc->code }}</p>
                                    <p class="text-xs mt-2 font-medium" style="color:rgba(255,255,255,.5)">
                                        Berlaku s/d: <strong class="text-white">{{ $gc->expires_at ? $gc->expires_at->timezone('Asia/Jakarta')->format('d M Y') : 'Tanpa Batas' }}</strong>
                                    </p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-[10px] font-bold uppercase tracking-widest mb-1" style="color:rgba(255,255,255,.4)">Sisa Saldo</p>
                                    <p class="text-xl font-black text-white leading-none">Rp {{ number_format($gc->remaining_balance, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ── HISTORI BELANJA ────────────────────────────────────── --}}
        <div>
            <h3 class="text-base font-black text-white mb-3 flex items-center gap-2.5">
                <span class="text-lg">📋</span> Histori Belanja
                <span class="text-xs font-semibold px-2 py-0.5 rounded-lg ml-auto" style="background:rgba(255,255,255,.08);color:rgba(255,255,255,.4)">
                    10 terbaru
                </span>
            </h3>

            @if($recentOrders->isEmpty())
                <div class="glass-card rounded-2xl p-8 text-center">
                    <p class="text-3xl mb-3">🛒</p>
                    <p class="font-bold" style="color:rgba(255,255,255,.5)">Belum ada transaksi</p>
                    <p class="text-sm mt-1" style="color:rgba(255,255,255,.25)">Pesanan Anda akan muncul di sini</p>
                </div>
            @else
                <div class="space-y-2.5">
                    @foreach($recentOrders as $order)
                        <div class="glass-card rounded-2xl p-4 transition-all"
                            style="border:1px solid rgba(255,255,255,.07)"
                            onmouseover="this.style.borderColor='rgba(255,255,255,.14)';this.style.background='rgba(255,255,255,.06)'"
                            onmouseout="this.style.borderColor='rgba(255,255,255,.07)';this.style.background='rgba(255,255,255,.04)'">
                            <div class="flex items-start justify-between mb-2.5">
                                <div>
                                    <p class="text-white font-black text-sm">#{{ $order->order_number }}</p>
                                    <p class="text-xs mt-0.5" style="color:rgba(255,255,255,.35)">
                                        {{ $order->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-white font-black text-sm">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                                    @if($order->is_loyalty_processed && $order->points_used > 0)
                                        <p class="text-xs font-bold mt-0.5" style="color:rgba(167,139,250,.9)">-{{ $order->points_used }} poin</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($order->items->take(3) as $item)
                                    <span class="text-xs px-2.5 py-1 rounded-lg font-medium"
                                        style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.07);color:rgba(255,255,255,.5)">
                                        {{ $item->quantity }}× {{ $item->menuItem->name ?? '-' }}
                                    </span>
                                @endforeach
                                @if($order->items->count() > 3)
                                    <span class="text-xs px-2.5 py-1 rounded-lg font-medium"
                                        style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.05);color:rgba(255,255,255,.25)">
                                        +{{ $order->items->count() - 3 }} lainnya
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Footer --}}
        <div class="text-center pt-2 pb-8">
            <a href="{{ route('restaurant.index', $restaurant->slug) }}"
                class="text-sm font-medium transition-all"
                style="color:rgba(255,255,255,.25)"
                onmouseover="this.style.color='rgba(255,255,255,.6)'"
                onmouseout="this.style.color='rgba(255,255,255,.25)'">
                🍽️ Lihat Menu {{ $restaurant->name }}
            </a>
        </div>
    </div>
</div>
