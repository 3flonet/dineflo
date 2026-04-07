<x-filament-panels::page>
    {{-- Header Banner --}}
    <div class="mb-6 rounded-xl border border-primary-200 dark:border-primary-900 bg-primary-50 dark:bg-primary-900/20 p-6 flex flex-col md:flex-row items-start md:items-center gap-6 shadow-sm relative overflow-hidden">
        <div class="absolute right-0 top-0 w-64 h-64 bg-primary-500/10 rounded-full blur-3xl transform translate-x-1/2 -translate-y-1/2 pointer-events-none"></div>
        
        <div class="p-4 bg-primary-600 rounded-2xl shadow-sm relative z-10 flex-shrink-0 text-white">
            <x-heroicon-o-presentation-chart-bar class="w-8 h-8" />
        </div>
        <div class="relative z-10">
            <h2 class="text-2xl font-bold tracking-tight text-primary-900 dark:text-primary-100">Analisis Stok Lanjutan</h2>
            <p class="text-primary-700 dark:text-primary-300 text-sm mt-1 max-w-2xl">
                Pantau tingkat perputaran bahan baku, peringatan stok kritis, nilai bahan yang rusak (wastage), hingga evaluasi kesehatan margin HPP menu Anda.
            </p>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 gap-6 md:grid-cols-3 mb-6">
        {{-- Stat 1: Low Stock --}}
        <x-filament::section>
            <div class="flex items-center gap-4 border-b border-gray-100 dark:border-white/10 pb-4 mb-4">
                <div class="p-3 bg-danger-100 dark:bg-danger-500/20 rounded-xl">
                    <x-heroicon-o-exclamation-triangle class="w-6 h-6 text-danger-600 dark:text-danger-400" />
                </div>
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Peringatan Restock</h3>
                    <p class="text-3xl font-black text-gray-900 dark:text-white mt-1">{{ $lowStockItems->count() }} <span class="text-base font-semibold text-gray-500">Bahan</span></p>
                </div>
            </div>
            <div class="flex justify-between items-center text-sm font-medium mb-2">
                <span class="text-gray-500 dark:text-gray-400">Tingkat Kritis</span>
                <span class="text-danger-600 dark:text-danger-400">{{ $totalIngredientsCount > 0 ? round(($lowStockItems->count() / $totalIngredientsCount) * 100) : 0 }}%</span>
            </div>
            <div class="w-full bg-gray-100 dark:bg-gray-800 h-2 rounded-full overflow-hidden">
                <div class="bg-danger-500 dark:bg-danger-400 h-full rounded-full transition-all duration-500" style="width: {{ $totalIngredientsCount > 0 ? ($lowStockItems->count() / $totalIngredientsCount) * 100 : 0 }}%"></div>
            </div>
        </x-filament::section>

        {{-- Stat 2: Wastage --}}
        <x-filament::section>
            <div class="flex items-center gap-4 border-b border-gray-100 dark:border-white/10 pb-4 mb-4">
                <div class="p-3 bg-warning-100 dark:bg-warning-500/20 rounded-xl">
                    <x-heroicon-o-trash class="w-6 h-6 text-warning-600 dark:text-warning-400" />
                </div>
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Wastage (30 Hari)</h3>
                    <p class="text-3xl font-black text-warning-600 dark:text-warning-500 mt-1">Rp {{ number_format($wastageValue, 0, ',', '.') }}</p>
                </div>
            </div>
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 leading-relaxed bg-gray-50 dark:bg-white/5 p-3 rounded-lg border border-gray-100 dark:border-white/5">
                Estimasi kerugian dari bahan yang dilaporkan rusak, busuk, atau hilang selama 30 hari terakhir.
            </p>
        </x-filament::section>

        {{-- Stat 3: Recipe Health --}}
        <x-filament::section>
            <div class="flex items-center gap-4 border-b border-gray-100 dark:border-white/10 pb-4 mb-4">
                <div class="p-3 bg-success-100 dark:bg-success-500/20 rounded-xl">
                    <x-heroicon-o-sparkles class="w-6 h-6 text-success-600 dark:text-success-400" />
                </div>
                <div>
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Efisiensi Resep</h3>
                    <p class="text-3xl font-black text-success-600 dark:text-success-500 mt-1">Optimal</p>
                </div>
            </div>
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 leading-relaxed bg-gray-50 dark:bg-white/5 p-3 rounded-lg border border-gray-100 dark:border-white/5">
                Berdasarkan rasio keseluruhan antara Harga Pokok Penjualan (HPP) berbanding harga jual.
            </p>
        </x-filament::section>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Shopping List Table --}}
        <x-filament::section icon="heroicon-o-shopping-cart" icon-color="primary" heading="Daftar Belanja (Stok Kritis)">
            <div class="overflow-x-auto -mx-6 -mb-6 mt-4">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-gray-50 dark:bg-white/5 border-y border-gray-200 dark:border-white/10">
                        <tr>
                            <th class="px-6 py-3 font-semibold text-gray-600 dark:text-gray-300">Bahan Baku</th>
                            <th class="px-6 py-3 font-semibold text-gray-600 dark:text-gray-300 text-center">Stok Sisa</th>
                            <th class="px-6 py-3 font-semibold text-gray-600 dark:text-gray-300 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                        @forelse($lowStockItems as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-900 dark:text-white flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-500/20 text-primary-600 dark:text-primary-400 flex justify-center items-center font-bold text-xs ring-1 ring-primary-200 dark:ring-primary-500/30">
                                    {{ substr($item->name, 0, 1) }}
                                </div>
                                {{ $item->name }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-black text-danger-600 dark:text-danger-400 text-base">{{ $item->stock }}</span> 
                                <span class="text-xs font-medium text-gray-500">{{ $item->unit }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <x-filament::badge color="danger">Kritis</x-filament::badge>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                <x-heroicon-o-check-circle class="w-12 h-12 mx-auto mb-3 text-success-500 opacity-50" />
                                <p class="font-bold text-base text-gray-900 dark:text-white">Stok bahan baku aman!</p>
                                <p class="text-sm mt-1">Tidak ada bahan baku yang berada di bawah batas minimum.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        {{-- Top Consumed --}}
        <x-filament::section icon="heroicon-o-arrow-trending-down" icon-color="primary" heading="Konsumsi Tertinggi (30 Hari)">
            <div class="overflow-x-auto -mx-6 -mb-6 mt-4">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-gray-50 dark:bg-white/5 border-y border-gray-200 dark:border-white/10">
                        <tr>
                            <th class="px-6 py-3 font-semibold text-gray-600 dark:text-gray-300">Bahan Baku</th>
                            <th class="px-6 py-3 font-semibold text-gray-600 dark:text-gray-300 text-right">Total Pemakaian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                        @forelse($topConsumption as $stat)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-900 dark:text-white flex items-center gap-3">
                                <div class="w-7 h-7 rounded-lg bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-300 flex justify-center items-center font-bold text-xs ring-1 ring-gray-200 dark:ring-white/20">
                                    {{ $loop->iteration }}
                                </div>
                                {{ $stat->ingredient?->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-black text-primary-600 dark:text-primary-400 text-base">{{ number_format($stat->total_qty, 1) }}</span>
                                <span class="text-xs font-medium text-gray-500 ml-1">{{ $stat->ingredient?->unit }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                <x-heroicon-o-chart-pie class="w-12 h-12 mx-auto mb-3 text-gray-400 opacity-50" />
                                <p class="font-bold text-base text-gray-900 dark:text-white">Data tidak tersedia.</p>
                                <p class="text-sm mt-1">Sistem belum mencatat pengurangan stok akibat pesanan.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        {{-- High Cost Menu (BOM Analysis) --}}
        <x-filament::section icon="heroicon-o-calculator" icon-color="primary" heading="Food Cost Analysis (HPP Tertinggi)" class="lg:col-span-2">
            <div class="overflow-x-auto -mx-6 -mb-6 mt-4">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-gray-50 dark:bg-white/5 border-y border-gray-200 dark:border-white/10">
                        <tr>
                            <th class="px-6 py-3 font-semibold text-gray-600 dark:text-gray-300">Menu Item</th>
                            <th class="px-6 py-3 font-semibold text-gray-600 dark:text-gray-300">Modal (HPP)</th>
                            <th class="px-6 py-3 font-semibold text-gray-600 dark:text-gray-300">Harga Jual</th>
                            <th class="px-6 py-3 font-semibold text-gray-600 dark:text-gray-300 text-right">Profit Margin (%)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                        @forelse($highCostMenus as $menu)
                        <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full bg-primary-500"></div>
                                    {{ $menu['name'] }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-gray-700 dark:text-gray-300">Rp {{ number_format($menu['cost'], 0, ',', '.') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-medium text-gray-600 dark:text-gray-400">Rp {{ number_format($menu['price'], 0, ',', '.') }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @php
                                    $margin = $menu['margin'];
                                    $color = $margin >= 50 ? 'success' : ($margin >= 30 ? 'warning' : 'danger');
                                @endphp
                                <x-filament::badge :color="$color">
                                    {{ number_format($margin, 1) }}%
                                </x-filament::badge>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                <x-heroicon-o-book-open class="w-12 h-12 mx-auto mb-3 text-gray-400 opacity-50" />
                                <p class="font-bold text-base text-gray-900 dark:text-white">Belum ada resep bahan baku.</p>
                                <p class="text-sm mt-1">Tambahkan bahan baku ke dalam setiap menu Anda untuk mulai melacak HPP.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
