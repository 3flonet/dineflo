<div class="min-h-screen bg-gray-50 pb-20">
    {{-- Header --}}
    <div class="bg-white shadow-sm p-4 sticky top-0 z-10">
        <div class="max-w-md mx-auto flex items-center justify-between">
            <h1 class="text-lg font-bold">Order Summary</h1>
            <span class="text-sm font-mono text-gray-500">#{{ $order->id }}</span>
        </div>
    </div>

    <div class="max-w-md mx-auto p-4 space-y-6">
        
        {{-- Status Card --}}
        <div class="bg-white rounded-xl shadow-sm p-6 text-center">
            @if($order->payment_status === 'paid')
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-green-600">Payment Successful!</h2>
                <p class="text-gray-500 mt-2">Your order is confirmed and being prepared.</p>
            @elseif($order->payment_method === 'cash')
                <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4 border-4 border-blue-100/50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <h2 class="text-2xl font-black text-gray-900 tracking-tight">Pesanan Diterima</h2>
                <p class="text-gray-500 font-medium mt-2 text-sm">Harap konfirmasi pesanan Anda ke kasir via WhatsApp agar segera diproses.</p>
            @else
                <div class="w-16 h-16 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-4 border-4 border-amber-100/50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-black text-gray-900 tracking-tight">Menunggu Pembayaran</h2>
                <p class="text-gray-500 font-medium mt-2 text-sm">Harap selesaikan pembayaran Anda dalam waktu 15 menit.</p>
            @endif
        </div>

        {{-- Order Items --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-100 bg-gray-50">
                <h3 class="font-bold text-gray-700">Order Details</h3>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($order->items as $item)
                    <div class="p-4 flex justify-between">
                        <div>
                            <div class="font-bold text-gray-900">{{ $item->quantity }}x {{ $item->menuItem->name }}</div>
                            @if($item->variant)
                                <div class="text-xs text-gray-500">{{ $item->variant->name }}</div>
                            @endif
                            @if($item->addons)
                                @foreach($item->addons as $addon)
                                    <div class="text-xs text-gray-500">+ {{ $addon['name'] }}</div>
                                @endforeach
                            @endif
                        </div>
                        <div class="text-right">
                            <div class="text-gray-900 font-medium">
                                Rp {{ number_format($item->total_price, 0, ',', '.') }}
                            </div>
                            @if($item->is_paid)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black bg-green-100 text-green-700 uppercase tracking-tighter mt-1">LUNAS</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="p-4 bg-gray-50 border-t border-gray-100 space-y-2">
                <div class="flex justify-between items-center text-sm text-gray-600">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>

                @if($order->additional_fees_details && is_array($order->additional_fees_details))
                    @foreach($order->additional_fees_details as $fee)
                        @php
                            $feeAmount = ($fee['type'] ?? '') === 'fixed' ? $fee['value'] : ($order->subtotal * ($fee['value'] / 100));
                        @endphp
                        @if($feeAmount > 0)
                            <div class="flex justify-between items-center text-sm text-gray-600">
                                <span>{{ $fee['name'] }}</span>
                                <span>Rp {{ number_format($feeAmount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    @endforeach
                @endif

                @if($order->tax_amount > 0)
                    <div class="flex justify-between items-center text-sm text-gray-600">
                        <span>Pajak</span>
                        <span>Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</span>
                    </div>
                @endif

                @if($order->voucher_discount_amount > 0)
                    <div class="flex justify-between items-center text-sm text-green-600">
                        <span>Voucher</span>
                        <span>-Rp {{ number_format($order->voucher_discount_amount, 0, ',', '.') }}</span>
                    </div>
                @endif

                @if($order->points_discount_amount > 0)
                    <div class="flex justify-between items-center text-sm text-green-600">
                        <span>Poin Loyalitas</span>
                        <span>-Rp {{ number_format($order->points_discount_amount, 0, ',', '.') }}</span>
                    </div>
                @endif

                @if($order->gift_card_discount_amount > 0)
                    <div class="flex justify-between items-center text-sm text-green-600">
                        <span>Gift Card</span>
                        <span>-Rp {{ number_format($order->gift_card_discount_amount, 0, ',', '.') }}</span>
                    </div>
                @endif

                <div class="flex justify-between items-center text-xl font-black text-gray-900 pt-2 border-t border-gray-200">
                    <span>Total</span>
                    <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Action Button --}}
        @if($order->payment_method === 'cash')
            <a href="{{ $this->whatsappUrl }}" target="_blank" class="flex items-center justify-center space-x-2 w-full bg-[#25D366] hover:bg-[#1DA851] text-white font-bold py-4 rounded-xl shadow-[0_8px_20px_rgb(37,211,102,0.25)] transition-all active:scale-[0.98]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.01-.371-.01-.568-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                <span>Kirim Pesanan ke WhatsApp</span>
            </a>
            <div class="text-center text-xs text-gray-400 mt-4 px-4 font-medium">
                Klik tombol di atas untuk mengirimkan detail pesanan secara otomatis ke kasir kami.
            </div>
        @elseif($order->payment_status !== 'paid')
            <button id="pay-button" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-[0_8px_20px_rgb(37,99,235,0.25)] transition-all transform active:scale-[0.98]">
                Bayar Sekarang (Midtrans)
            </button>
            <div class="text-center text-xs text-gray-400 mt-4 font-medium">
                Pembayaran Aman oleh Midtrans
            </div>
        @else
            <div class="space-y-4">
                <a href="{{ route('order.track', $order->tracking_hash) }}" class="flex items-center justify-center space-x-2 w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-xl shadow-[0_8px_20px_rgb(79,70,229,0.3)] transition-all animate-bounce-once">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span>Lacak Pesanan Live</span>
                </a>
                
                <a href="{{ route('restaurant.index', $order->restaurant->slug) }}" class="block w-full bg-white border-2 border-gray-100 text-gray-500 text-center font-bold py-4 rounded-xl hover:bg-gray-50 transition-all">
                    Kembali ke Menu
                </a>
            </div>
        @endif

        {{-- Beautiful PWA Install Prompt (Micro-Moment) --}}
        <div x-data="{ 
                installPrompt: null,
                showInstallButton: false,
                init() {
                    window.addEventListener('beforeinstallprompt', (e) => {
                        e.preventDefault();
                        this.installPrompt = e;
                        this.showInstallButton = true;
                    });
                },
                install() {
                    if (this.installPrompt) {
                        this.installPrompt.prompt();
                        this.installPrompt.userChoice.then((choiceResult) => {
                            if (choiceResult.outcome === 'accepted') {
                                console.log('User accepted the install prompt');
                            }
                            this.installPrompt = null;
                            this.showInstallButton = false;
                        });
                    }
                }
            }"
            x-show="showInstallButton"
            x-transition:enter="transition ease-out duration-500 delay-500"
            x-transition:enter-start="opacity-0 translate-y-8 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            style="display: none;"
            class="pt-8 pb-4"
        >
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-[2rem] p-6 text-white shadow-2xl relative overflow-hidden">
                <div class="absolute inset-0 bg-[url('https://laravel.com/assets/img/welcome/background.svg')] bg-cover opacity-20 mix-blend-overlay"></div>
                <div class="relative z-10 flex flex-col items-center text-center">
                    <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-md border border-white/30 shadow-inner mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-black mb-2 tracking-tight drop-shadow-sm">Install App Kami</h3>
                    <p class="text-indigo-100 text-[13px] font-medium leading-relaxed mb-6 px-4">
                        Pasang aplikasi Dineflo di layar HP Anda sekarang. Melacak pesanan & menu digital jadi seribu kali lebih mudah dan cepat.
                    </p>
                    <button @click="install()" class="w-full bg-white text-indigo-600 font-bold py-3.5 rounded-xl shadow-[0_8px_20px_rgb(0,0,0,0.2)] hover:shadow-xl transition-all active:scale-95 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Install ke Layar Utama
                    </button>
                    <button @click="showInstallButton = false" class="mt-4 text-[11px] font-bold text-white/60 uppercase tracking-widest hover:text-white transition-colors">
                        Lain Kali Saja
                    </button>
                </div>
            </div>
        </div>

    </div>

    {{-- Midtrans Snap Script --}}
    @if($order->payment_status !== 'paid' && $snapToken)
        @php
            $snapSrc = config('midtrans.is_production') 
                ? 'https://app.midtrans.com/snap/snap.js'
                : 'https://app.sandbox.midtrans.com/snap/snap.js';
        @endphp
        <script src="{{ $snapSrc }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
        
        <script>
            document.addEventListener('livewire:initialized', () => {
                const payButton = document.getElementById('pay-button');
                if (payButton) {
                    payButton.addEventListener('click', function () {
                        window.snap.pay('{{ $snapToken }}', {
                            onSuccess: function(result){
                                // Call Livewire Component
                                @this.call('handlePaymentSuccess', result);
                            },
                            onPending: function(result){
                                alert("Waiting for your payment!");
                                console.log(result);
                            },
                            onError: function(result){
                                alert("Payment failed!"); console.log(result);
                            },
                            onClose: function(){
                                console.log('customer closed the popup without finishing the payment');
                            }
                        });
                    });
                }
            });
        </script>
    @endif
</div>
