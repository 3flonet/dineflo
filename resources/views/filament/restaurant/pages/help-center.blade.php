<x-filament-panels::page>
    @if(auth()->user()->hasFeature('Priority Support'))
        <div class="mb-6 p-4 rounded-xl border border-warning-200 bg-warning-50 dark:border-warning-700 dark:bg-warning-900/20 flex gap-4 items-start shadow-sm">
            <x-filament::icon icon="heroicon-o-star" class="w-6 h-6 text-warning-500 mt-0.5" />
            <div>
                <h3 class="font-bold text-warning-700 dark:text-warning-400">Priority Support Aktif</h3>
                <p class="text-sm text-warning-600 dark:text-warning-500 mt-1">
                    Sebagai pelanggan <span class="font-bold">Empire/Premium</span>, tiket Anda akan ditandai dengan prioritas <span class="font-bold">Tinggi (High)</span> dan akan mendapatkan respon lebih cepat dari tim kami.
                </p>
            </div>
        </div>
    @else
        <div class="mb-6 p-4 rounded-xl border border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800/50 flex gap-4 items-start">
            <x-filament::icon icon="heroicon-o-information-circle" class="w-6 h-6 text-gray-400 mt-0.5" />
            <div>
                <h3 class="font-bold text-gray-700 dark:text-gray-300">Hubungi Pusat Bantuan</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Silakan buat tiket baru jika Anda memiliki kendala teknis atau pertanyaan seputar penggunaan aplikasi {{ config('app.name', 'Dineflo') }}. Tingkatkan paket berlangganan ke Empire untuk mendapatkan <span class="font-bold text-warning-500">Priority Support</span>.
                </p>
            </div>
        </div>
    @endif

    {{ $this->table }}
</x-filament-panels::page>
