<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Matrix Overview --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @php
                $restaurantId = \Filament\Facades\Filament::getTenant()->id;
                $items = \App\Models\MenuItem::where('restaurant_id', $restaurantId)->get();
                $stats = [
                    'star' => $items->filter(fn($i) => $i->menu_insight === 'star')->count(),
                    'plowhorse' => $items->filter(fn($i) => $i->menu_insight === 'plowhorse')->count(),
                    'puzzle' => $items->filter(fn($i) => $i->menu_insight === 'puzzle')->count(),
                    'dog' => $items->filter(fn($i) => $i->menu_insight === 'dog')->count(),
                ];
            @endphp

            <x-filament::section class="border-l-4 border-success-500">
                <div class="flex items-center space-x-3">
                    <span class="text-3xl">⭐</span>
                    <div>
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Stars</div>
                        <div class="text-2xl font-bold">{{ $stats['star'] }}</div>
                    </div>
                </div>
                <div class="mt-2 text-xs text-gray-400">Menu paling menguntungkan & favorit pelanggan.</div>
            </x-filament::section>

            <x-filament::section class="border-l-4 border-info-500">
                <div class="flex items-center space-x-3">
                    <span class="text-3xl">🐎</span>
                    <div>
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Plowhorses</div>
                        <div class="text-2xl font-bold">{{ $stats['plowhorse'] }}</div>
                    </div>
                </div>
                <div class="mt-2 text-xs text-gray-400">Laris tapi untung tipis, perlu efisiensi porsi/harga.</div>
            </x-filament::section>

            <x-filament::section class="border-l-4 border-warning-500">
                <div class="flex items-center space-x-3">
                    <span class="text-3xl">🧩</span>
                    <div>
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Puzzles</div>
                        <div class="text-2xl font-bold">{{ $stats['puzzle'] }}</div>
                    </div>
                </div>
                <div class="mt-2 text-xs text-gray-400">Untung besar tapi jarang dipesan, perlu promo khusus.</div>
            </x-filament::section>

            <x-filament::section class="border-l-4 border-danger-500">
                <div class="flex items-center space-x-3">
                    <span class="text-3xl">🐕</span>
                    <div>
                        <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Dogs</div>
                        <div class="text-2xl font-bold">{{ $stats['dog'] }}</div>
                    </div>
                </div>
                <div class="mt-2 text-xs text-gray-400">Kurang lirik & tidak untung, pertimbangkan dihapus.</div>
            </x-filament::section>
        </div>

        {{-- Table --}}
        <div class="bg-white dark:bg-gray-900 shadow-sm rounded-xl overflow-hidden">
            {{ $this->table }}
        </div>

        {{-- Strategic Advice --}}
        <x-filament::section collapsible collapsed>
            <x-slot name="heading">
                💡 Panduan Strategi Menu Engineering
            </x-slot>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-2">
                <div class="space-y-2">
                    <h4 class="font-bold text-success-600 flex items-center gap-2">⭐ Star (Bintang)</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Ini adalah juara Anda. Pertahankan konsistensi kualitasnya. Berikan posisi visual yang paling mencolok di buku menu atau katalog.</p>
                </div>
                <div class="space-y-2">
                    <h4 class="font-bold text-info-600 flex items-center gap-2">🐎 Plowhorse (Kuda Beban)</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Pelanggan menyukainya, tapi Anda kurang untung. Coba lakukan porsi re-engineering (kurangi ukuran) atau naikkan harga sedikit demi sedikit.</p>
                </div>
                <div class="space-y-2">
                    <h4 class="font-bold text-warning-600 flex items-center gap-2">🧩 Puzzle (Teka-teki)</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Sangat menguntungkan tapi malas dipesan. Berikan promo beli 1 gratis 1 atau sarankan via Upselling (Pasangan Terbaik) untuk meningkatkan penjualannya.</p>
                </div>
                <div class="space-y-2">
                    <h4 class="font-bold text-danger-600 flex items-center gap-2">🐕 Dog (Anjing)</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Menu ini membebani inventori Anda. Pertimbangkan untuk mengganti resep atau menghapusnya sama sekali dari daftar menu Anda.</p>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
