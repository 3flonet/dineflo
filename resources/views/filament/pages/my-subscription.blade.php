<x-filament-panels::page>
    <div x-data="{
        init() {
            Livewire.on('open-midtrans-payment', (event) => {
                let token = event.token;
                if (!token && event[0]) token = event[0];
                if (!token && event.detail?.token) token = event.detail.token;
                if (!token) return;
                if (window.snap) {
                    window.snap.pay(token, {
                        onSuccess: (result) => { $wire.handlePaymentSuccess(result); },
                        onPending: (result) => { console.log('pending', result); },
                        onError: (result) => { alert('Payment Failed'); },
                        onClose: () => { console.log('closed'); }
                    });
                }
            });
        }
    }" class="space-y-8">
        
        {{-- Info banner untuk staff: halaman read-only --}}
        @if($isStaff)
            <x-filament::section>
                <div class="flex items-start gap-3">
                    <x-filament::icon icon="heroicon-o-information-circle" class="w-5 h-5 text-info-500 shrink-0 mt-0.5" />
                    <div>
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">Mode Tampilan — Langganan Pemilik Restoran</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                            Anda melihat status langganan aktif milik pemilik restoran. Hanya pemilik yang dapat mengelola atau memperbarui paket langganan.
                        </p>
                    </div>
                </div>
            </x-filament::section>
        @endif

        {{-- Hero Header: Current Subscription --}}
        <x-filament::section>
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="space-y-4 text-center md:text-left flex-grow">
                    <div class="inline-flex">
                        <x-filament::badge color="primary">Status Langganan</x-filament::badge>
                    </div>

                    @if($currentSubscription && $currentSubscription->isValid())
                        <h2 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-3xl">
                            Paket <span class="text-primary-600 dark:text-primary-400">{{ $currentSubscription->plan->name }}</span>
                        </h2>
                        
                        @php
                            $now = now();
                            $start = $currentSubscription->starts_at ?? $currentSubscription->created_at;
                            $end = $currentSubscription->expires_at;
                            
                            if ($end) {
                                // Hitung total jam untuk presisi lebih tinggi
                                $totalDays = ($currentSubscription->plan && $currentSubscription->plan->duration_days > 0)
                                    ? $currentSubscription->plan->duration_days 
                                    : ($start->diffInDays($end) ?: 1);
                                
                                $totalHours = $totalDays * 24;
                                $hoursLeft = max(0, $now->diffInHours($end, false));
                                
                                // Kalkulasi presentase sisa (0-100)
                                $percentage = min(100, max(0, ($hoursLeft / $totalHours) * 100));
                                
                                // Tentukan warna berdasarkan urgensi
                                $barColor = 'rgb(var(--primary-600))'; // Default: Aman (Primary)
                                if ($percentage <= 0) {
                                    $barColor = 'rgba(156, 163, 175, 0.5)'; // Expired: Abu-abu
                                } elseif ($percentage < 20) {
                                    $barColor = 'rgb(239, 68, 68)'; // Kritis: Merah
                                } elseif ($percentage < 50) {
                                    $barColor = 'rgb(249, 115, 22)'; // Peringatan: Oranye
                                }
                                
                                $daysLeftDisplay = ceil($hoursLeft / 24);
                            } else {
                                $percentage = 100;
                                $daysLeftDisplay = 'Selamanya';
                                $barColor = 'rgb(var(--primary-600))';
                            }
                        @endphp

                        <div class="max-w-md w-full mt-4 mx-auto md:mx-0 text-left">
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-500 font-medium">Sisa Waktu:</span>
                                <span class="font-bold text-gray-950 dark:text-white">
                                    {{ $daysLeftDisplay !== 'Selamanya' ? ($daysLeftDisplay > 0 ? $daysLeftDisplay . ' Hari Lagi' : 'Habis Hari Ini') : 'Selamanya' }}
                                </span>
                            </div>
                            
                            {{-- Visual Progress Bar dengan Warna Dinamis --}}
                            <div class="w-full rounded-full h-3 overflow-hidden" style="background-color: rgba(100, 116, 139, 0.15);">
                                <div class="h-3 rounded-full transition-all duration-1000 shadow-sm" 
                                     style="width: {{ $percentage }}%; background-color: {{ $barColor }};">
                                </div>
                            </div>
                            
                            <p class="text-xs text-gray-400 mt-2">
                                Berlaku hingga: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $end ? $end->format('d M Y') : 'Tanpa Batas' }}</span>
                            </p>
                        </div>
                    @else
                        <h2 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-3xl">
                            Belum Ada <span class="text-primary-600 dark:text-primary-400">Langganan Aktif</span>
                        </h2>
                        <p class="text-gray-500 dark:text-gray-400">
                            Pilih paket di bawah untuk mengaktifkan fitur-fitur eksklusif Dineflo Business.
                        </p>
                    @endif
                </div>

                <div class="shrink-0 flex justify-center">
                    @if($currentSubscription && $currentSubscription->isValid())
                        <div class="flex flex-col items-center justify-center p-4 bg-success-50 dark:bg-success-900/20 rounded-full w-32 h-32 border-4 border-success-100 dark:border-success-900/50">
                            <x-filament::icon icon="heroicon-o-check-badge" class="w-12 h-12 text-success-600 dark:text-success-400" />
                            <span class="mt-2 text-xs font-bold text-success-600 dark:text-success-400 uppercase tracking-widest">Active</span>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center p-4 bg-gray-50 dark:bg-gray-800/50 rounded-full w-32 h-32 border-4 border-gray-100 dark:border-gray-800">
                            <x-filament::icon icon="heroicon-o-x-circle" class="w-12 h-12 text-gray-400" />
                            <span class="mt-2 text-xs font-bold text-gray-400 uppercase tracking-widest">No Plan</span>
                        </div>
                    @endif
                </div>
            </div>
        </x-filament::section>

        {{-- Section Title --}}
        <div style="margin-top: 3.5rem; margin-bottom: 0.5rem;">
            <h3 class="text-2xl md:text-3xl font-black tracking-tight text-gray-950 dark:text-white">Pilih Paket Langganan</h3>
            <p class="text-base text-gray-500 mt-2">Dapatkan akses ke lebih banyak fitur premium untuk memajukan bisnis kuliner Anda.</p>
        </div>

        {{-- Plans Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 items-stretch">
            @foreach($plans as $plan)
                @php
                    $isPopular = $plan->name === 'Professional' || $plan->price > 0 && $plan->price < 500000;
                    $isCurrent = $currentSubscription && $currentSubscription->subscription_plan_id === $plan->id && $currentSubscription->isValid();
                @endphp
                
                <div class="relative flex flex-col pt-3">
                    @if($isPopular)
                        <div class="absolute top-0 inset-x-0 flex justify-center z-10">
                            <x-filament::badge color="primary" class="shadow-sm">Best Value</x-filament::badge>
                        </div>
                    @endif

                    <x-filament::section class="h-full flex flex-col {{ $isPopular ? 'ring-2 ring-primary-500 shadow-xl shadow-primary-500/10' : '' }}">
                        <div class="flex-grow space-y-6 flex flex-col h-full">
                            <div class="text-center space-y-2 border-b border-gray-200 dark:border-gray-700 pb-4 shrink-0">
                                <h4 class="text-xl font-bold text-gray-950 dark:text-white">{{ $plan->name }}</h4>
                                <div class="flex items-end justify-center gap-1">
                                    <span class="text-3xl font-black text-gray-950 dark:text-white">
                                        {{ $plan->price == 0 ? 'Free' : 'Rp ' . number_format($plan->price, 0, ',', '.') }}
                                    </span>
                                    @if($plan->price > 0)
                                        <span class="text-sm font-medium text-gray-500">/ {{ $plan->duration_days }} hr</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Limits --}}
                            <div class="grid grid-cols-2 gap-2 shrink-0">
                                <div class="bg-gray-50 dark:bg-gray-800/50 p-3 rounded-xl flex flex-col items-center justify-center text-center ring-1 ring-gray-200 dark:ring-gray-700">
                                    <x-filament::icon icon="heroicon-o-building-storefront" class="w-5 h-5 text-primary-500 mb-1" />
                                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                        {{ (int)($plan->limits['max_restaurants'] ?? 0) === -1 ? 'Unlimited' : ($plan->limits['max_restaurants'] ?? 0) }} Resto
                                    </span>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-800/50 p-3 rounded-xl flex flex-col items-center justify-center text-center ring-1 ring-gray-200 dark:ring-gray-700">
                                    <x-filament::icon icon="heroicon-o-clipboard-document-list" class="w-5 h-5 text-success-500 mb-1" />
                                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300">
                                        {{ (int)($plan->limits['max_menus'] ?? 0) === -1 ? 'Unlimited' : ($plan->limits['max_menus'] ?? 0) }} Menu
                                    </span>
                                </div>
                            </div>

                            {{-- Features with Read More --}}
                            <div x-data="{ expanded: false }" class="flex-grow flex flex-col">
                                <ul class="space-y-3 flex-grow">
                                    @if($plan->features)
                                        @foreach($plan->features as $index => $feature)
                                            <li class="flex items-start gap-3"
                                                @if($index >= 4) x-show="expanded" style="display: none;" @endif>
                                                <x-filament::icon icon="heroicon-m-check-circle" class="w-5 h-5 text-primary-500 shrink-0" />
                                                <span class="text-sm text-gray-600 dark:text-gray-400 leading-tight">
                                                    {{ $feature }}
                                                </span>
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>

                                @if($plan->features && count($plan->features) > 4)
                                    <button @click="expanded = !expanded" type="button" class="mt-4 text-sm font-semibold text-primary-600 hover:text-primary-500 focus:outline-none flex items-center justify-center gap-1 w-full bg-primary-50/50 dark:bg-primary-900/10 py-2 rounded-lg">
                                        <span x-text="expanded ? 'Sembunyikan' : 'Lihat Semua Fitur ({{ count($plan->features) }})'"></span>
                                        <x-filament::icon icon="heroicon-m-chevron-down" class="w-4 h-4 transition-transform duration-200" x-bind:class="{ 'rotate-180': expanded }" />
                                    </button>
                                @endif
                                
                                {{-- Extra Info on checkout --}}
                                @if(!$isCurrent && $plan->price > 0)
                                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-center gap-2">
                                        <x-filament::icon icon="heroicon-o-shield-check" class="w-4 h-4 text-success-500" />
                                        <span class="text-xs text-gray-500">Mendukung pembayaran aman via Midtrans</span>
                                    </div>
                                @endif
                            </div>

                            <div class="pt-4 border-t border-gray-200 dark:border-gray-700 shrink-0">
                                @if($isStaff)
                                    {{-- Staff: tampilkan badge paket saat ini saja, tanpa tombol subscribe --}}
                                    <div class="w-full flex justify-center">
                                        <x-filament::badge color="{{ $isCurrent ? 'success' : 'gray' }}" class="text-xs py-1.5 px-4">
                                            {{ $isCurrent ? '✓ Paket Aktif Restoran Ini' : 'Tidak Aktif' }}
                                        </x-filament::badge>
                                    </div>
                                @else
                                    <x-filament::button 
                                        wire:click="subscribe({{ $plan->id }})" 
                                        wire:loading.attr="disabled"
                                        color="{{ $isCurrent ? 'gray' : 'primary' }}"
                                        class="w-full flex justify-center {{ $isCurrent ? '' : 'shadow-lg shadow-primary-500/20' }}"
                                    >
                                        <span wire:loading.remove wire:target="subscribe({{ $plan->id }})">
                                            {{ $isCurrent ? 'Paket Saat Ini' : 'Berlangganan Sekarang' }}
                                        </span>
                                        <span wire:loading wire:target="subscribe({{ $plan->id }})">
                                            Memproses...
                                        </span>
                                    </x-filament::button>
                                @endif
                            </div>
                        </div>
                    </x-filament::section>
                </div>
            @endforeach
        </div>

        {{-- Invoice History Section --}}
        @if($invoices && $invoices->count() > 0)
            <x-filament::section description="Daftar riwayat transaksi dan pembayaran langganan Anda.">
                <x-slot name="heading">
                    Riwayat Transaksi
                </x-slot>

                <div class="-mx-6 -my-4 overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700">
                                <th class="px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Paket / Order ID</th>
                                <th class="px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">Tanggal</th>
                                <th class="px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Total Tagihan</th>
                                <th class="px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($invoices as $invoice)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition duration-75">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <x-filament::badge color="{{ $invoice->status === 'paid' ? 'success' : 'warning' }}">
                                            {{ strtoupper($invoice->status) }}
                                        </x-filament::badge>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-semibold text-gray-950 dark:text-white">{{ $invoice->subscription->plan->name ?? 'Update Plan' }}</div>
                                        <div class="text-xs text-gray-500 font-mono">{{ $invoice->midtrans_id ?? 'INV-'.$invoice->id }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <div class="text-sm text-gray-600 dark:text-gray-400">{{ $invoice->paid_at ? $invoice->paid_at->format('d M Y') : $invoice->created_at->format('d M Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <span class="text-sm font-bold text-gray-950 dark:text-white">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        @if($invoice->status === 'paid')
                                            <x-filament::button
                                                tag="a"
                                                href="{{ route('subscription.invoice.download', $invoice) }}"
                                                icon="heroicon-o-arrow-down-tray"
                                                size="xs"
                                                color="gray"
                                                class="rounded-lg"
                                            >
                                                Invoice
                                            </x-filament::button>
                                        @else
                                            <span class="text-xs text-gray-400">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        @endif
    </div>
    
    @php
        $settings = app(\App\Settings\GeneralSettings::class);
        $midtransClientKey = !empty(trim($settings->midtrans_client_key ?? '')) ? trim($settings->midtrans_client_key) : config('midtrans.client_key');
        $midtransIsProd = $settings->midtrans_is_production ?? config('midtrans.is_production');
        $snapSrc = $midtransIsProd ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js';
    @endphp
    <script src="{{ $snapSrc }}" data-client-key="{{ $midtransClientKey }}"></script>
</x-filament-panels::page>
