<div>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-950 font-sans pb-12" wire:poll.10s="refresh">
        {{-- Header: Restaurant Branding --}}
        <header class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 sticky top-0 z-50">
            <div class="container mx-auto px-4 h-16 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    @if($order->restaurant->logo)
                        <img src="{{ asset('storage/' . $order->restaurant->logo) }}" alt="{{ $order->restaurant->name }}" class="w-8 h-8 rounded-lg object-cover">
                    @endif
                    <h1 class="font-bold text-gray-900 dark:text-white">{{ $order->restaurant->name }}</h1>
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

                    <a href="https://wa.me/{{ $order->restaurant->phone }}" target="_blank" class="text-xs font-bold text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20 px-3 py-1.5 rounded-full flex items-center gap-1.5 border border-primary-100 dark:border-primary-800/50">
                        <x-filament::icon icon="heroicon-o-chat-bubble-left-right" class="w-4 h-4" />
                        <span>Hubungi Kasir</span>
                    </a>
                </div>
            </div>
        </header>

        <main class="container mx-auto px-4 mt-6 max-w-lg">
            {{-- Order Status Card --}}
            <section class="bg-white dark:bg-gray-900 rounded-3xl p-6 shadow-xl shadow-gray-200/50 dark:shadow-none border border-gray-100 dark:border-gray-800 text-center space-y-6">
                @php
                    $status = $order->status;
                    $isPending = in_array($status, ['pending', 'confirmed']);
                    $isCooking = $status === 'cooking';
                    $isReady = $status === 'ready_to_serve';
                    $isCompleted = $status === 'completed';
                    
                    // Animation or Static Illustration
                    $illustration = 'receipt';
                    $statusText = 'Pesanan Diterima';
                    $subText = 'Menunggu koki kami menyiapkan hidangan spesial Anda.';
                    
                    if ($isCooking) {
                        $illustration = 'chef';
                        $statusText = 'Sedang Dimasak';
                        $subText = 'Koki kami sedang mengolah pesanan Anda dengan penuh cinta.';
                    } elseif ($isReady) {
                        $illustration = 'bell';
                        $statusText = 'Pesanan Sudah Siap!';
                        $subText = 'Yess! Pesanan Anda sudah siap disajikan di meja.';
                    } elseif ($isCompleted) {
                        $illustration = 'smile';
                        $statusText = 'Selamat Menikmati';
                        $subText = 'Terima kasih telah berkunjung, semoga harimu menyenangkan!';
                    } elseif ($status === 'cancelled' || $status === 'failed') {
                        $illustration = 'x-circle';
                        $statusText = 'Pesanan Dibatalkan';
                        $subText = 'Mohon maaf, pesanan Anda tidak dapat kami proses.';
                    }
                @endphp

                <div class="flex justify-center">
                    <div class="w-24 h-24 bg-primary-50 dark:bg-primary-900/30 rounded-full flex items-center justify-center p-4 ring-8 ring-primary-50/50 dark:ring-primary-900/10">
                        @if($illustration === 'receipt')
                            <x-filament::icon icon="heroicon-o-document-check" class="w-12 h-12 text-primary-600 dark:text-primary-400" />
                        @elseif($illustration === 'chef')
                            <x-filament::icon icon="heroicon-o-fire" class="w-12 h-12 text-primary-600 dark:text-primary-400 animate-bounce" />
                        @elseif($illustration === 'bell')
                            <x-filament::icon icon="heroicon-o-bell-alert" class="w-12 h-12 text-success-600 dark:text-success-400 animate-pulse" />
                        @elseif($illustration === 'smile')
                            <x-filament::icon icon="heroicon-o-face-smile" class="w-12 h-12 text-primary-600 dark:text-primary-400" />
                        @else
                            <x-filament::icon icon="heroicon-o-x-circle" class="w-12 h-12 text-danger-600 dark:text-danger-400" />
                        @endif
                    </div>
                </div>

                <div class="space-y-2">
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">{{ $statusText }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">{{ $subText }}</p>
                </div>

                {{-- Status Progress Steppers --}}
                <div class="relative pt-4 px-2">
                    <div class="flex items-center justify-between">
                        @php
                            $steps = [
                                ['id' => 1, 'label' => 'Diterima', 'active' => $isPending || $isCooking || $isReady || $isCompleted],
                                ['id' => 2, 'label' => 'Dapur', 'active' => $isCooking || $isReady || $isCompleted],
                                ['id' => 3, 'label' => 'Siap', 'active' => $isReady || $isCompleted],
                                ['id' => 4, 'label' => 'Selesai', 'active' => $isCompleted]
                            ];
                        @endphp

                        @foreach($steps as $index => $step)
                            <div class="flex flex-col items-center relative z-10">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center transition-all duration-500 {{ $step['active'] ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/30 ring-4 ring-primary-50 dark:ring-primary-900/30' : 'bg-gray-100 dark:bg-gray-800 text-gray-400' }}">
                                    @if($step['active'] && $loop->index < ($isPending ? 0 : ($isCooking ? 1 : ($isReady ? 2 : 3))))
                                        <x-filament::icon icon="heroicon-m-check" class="w-5 h-5" />
                                    @else
                                        <span class="text-xs font-bold">{{ $step['id'] }}</span>
                                    @endif
                                </div>
                                <span class="text-[10px] font-bold mt-2 uppercase tracking-wide {{ $step['active'] ? 'text-primary-600 dark:text-primary-400' : 'text-gray-400' }}">
                                    {{ $step['label'] }}
                                </span>
                            </div>

                            @if(!$loop->last)
                                <div class="flex-1 h-1 mx-2 -mt-6 bg-gray-100 dark:bg-gray-800 relative z-0 rounded-full overflow-hidden">
                                    <div class="absolute inset-0 bg-primary-600 transition-all duration-1000" style="width: {{ $step['active'] && $steps[$index+1]['active'] ? '100%' : '0%' }}"></div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- Summary Details --}}
            <div class="mt-8 space-y-4">
                <div class="flex items-center justify-between px-2">
                    <h3 class="text-lg font-black text-gray-900 dark:text-white">Daftar Pesanan</h3>
                    <span class="text-xs font-mono font-bold text-gray-500">#{{ $order->order_number }}</span>
                </div>

                {{-- Item List Card --}}
                <div class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 overflow-hidden shadow-sm">
                    @foreach($order->items as $item)
                        <div class="p-4 flex gap-4 {{ !$loop->last ? 'border-b border-gray-50 dark:border-gray-800/50' : '' }}">
                            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-2xl overflow-hidden shrink-0">
                                @if($item->menuItem->image)
                                    <img src="{{ asset('storage/' . $item->menuItem->image) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <x-filament::icon icon="heroicon-o-cake" class="w-8 h-8 text-gray-300" />
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow min-w-0 py-0.5">
                                <div class="flex justify-between items-start">
                                    <h4 class="font-bold text-gray-900 dark:text-white truncate pr-2">{{ $item->menuItem->name }}</h4>
                                    <div class="flex flex-col items-end gap-1">
                                        <span class="text-sm font-black text-gray-950 dark:text-white">x{{ $item->quantity }}</span>
                                        @if($order->status === 'cooking' && $item->is_ready)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-green-600 bg-green-50 px-2 py-0.5 rounded-full uppercase tracking-wider">
                                                <x-filament::icon icon="heroicon-m-check-circle" class="w-3 h-3" />
                                                Siap
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                @if($item->variant)
                                    <p class="text-[11px] text-gray-500 font-medium">Varian: {{ $item->variant->name }}</p>
                                @endif
                                @if(!empty($item->addons))
                                    <p class="text-[11px] text-gray-400 mt-1 italic">
                                        @foreach($item->addons as $addon)
                                            + {{ $addon['name'] }}{{ !$loop->last ? ', ' : '' }}
                                        @endforeach
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Total Info Card --}}
                <div class="bg-primary-600 rounded-3xl p-5 text-white shadow-lg shadow-primary-600/20">
                    <div class="flex justify-between items-center opacity-80 mb-1">
                        <span class="text-xs font-bold uppercase tracking-widest">Metode Pembayaran</span>
                        <span class="text-xs font-bold capitalize">{{ str_replace('_', ' ', $order->payment_method) }}</span>
                    </div>
                    <div class="flex justify-between items-end">
                        <div class="flex flex-col">
                            <span class="text-xs font-bold opacity-80 uppercase tracking-widest">Total Bayar</span>
                            <span class="text-xl font-black">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex flex-col items-end">
                             <div class="px-3 py-1 bg-white/20 rounded-lg backdrop-blur-sm">
                                <span class="text-[10px] font-black uppercase">{{ $order->payment_status }}</span>
                             </div>
                        </div>
                    </div>
                </div>

                {{-- Extra Info (Table) --}}
                @if($order->table)
                    <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded-2xl flex items-center justify-between text-gray-600 dark:text-gray-400">
                        <span class="text-sm font-bold">Meja: <span class="text-gray-900 dark:text-white">{{ $order->table->name }}</span></span>
                        <div class="flex items-center gap-1">
                           <x-filament::icon icon="heroicon-s-map-pin" class="w-4 h-4 text-primary-500" />
                           <span class="text-xs font-bold">{{ $order->table->area ?? 'Utama' }}</span>
                        </div>
                    </div>
                @endif
            </div>

            <div class="mt-8 text-center text-gray-400 text-xs font-medium space-y-4">
                {{-- Share Order Tracking Button --}}
                @if(in_array($order->status, ['pending', 'confirmed', 'cooking', 'ready_to_serve']))
                    <button
                        onclick="shareTracking()"
                        id="tracking-share-btn"
                        class="w-full flex items-center justify-center gap-2 bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition py-3 rounded-2xl font-bold text-sm shadow-sm group"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                        </svg>
                        <span id="tracking-share-text">Bagikan Status Pesanan</span>
                    </button>

                    <div id="tracking-toast" class="hidden text-green-600 text-xs font-bold bg-green-50 border border-green-100 py-2 px-4 rounded-xl">
                        ✅ Link tracking berhasil disalin!
                    </div>

                    <script>
                        function shareTracking() {
                            const shareData = {
                                title: 'Tracking Pesanan \u2014 {{ addslashes($order->restaurant->name) }}',
                                text: 'Pantau status pesanan #{{ $order->order_number }} di {{ addslashes($order->restaurant->name) }}',
                                url: window.location.href
                            };
                            if (navigator.share && navigator.canShare && navigator.canShare(shareData)) {
                                navigator.share(shareData).catch(() => {});
                            } else {
                                navigator.clipboard.writeText(shareData.url).then(() => {
                                    const toast = document.getElementById('tracking-toast');
                                    const btn = document.getElementById('tracking-share-text');
                                    toast.classList.remove('hidden');
                                    btn.textContent = 'Link Disalin!';
                                    setTimeout(() => {
                                        toast.classList.add('hidden');
                                        btn.textContent = 'Bagikan Status Pesanan';
                                    }, 3000);
                                });
                            }
                        }
                    </script>
                @endif

                <p>&copy; {{ date('Y') }} {{ $order->restaurant->name }} Powered by {{ config('app.name', 'Dineflo') }}</p>
            </div>
        </main>
    </div>
</div>
