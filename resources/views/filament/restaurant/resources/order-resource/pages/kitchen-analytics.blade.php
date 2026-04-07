<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        {{-- Stats Cards --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col items-center justify-center text-center">
            <div class="p-3 bg-orange-100 dark:bg-orange-900/30 rounded-full mb-3">
                <x-heroicon-o-clock class="w-8 h-8 text-orange-600" />
            </div>
            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider">Rata-rata Waktu Masak</h3>
            <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $avgPrepTime }} <span class="text-lg font-normal text-gray-400">Menit</span></p>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 md:col-span-2">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white flex items-center gap-2">
                <x-heroicon-o-chart-bar class="w-5 h-5 text-primary-500" />
                Efisiensi Dapur per Jam
            </h3>
            <div class="w-full h-32 flex items-end gap-2">
                @foreach(range(0, 23) as $h)
                    @php 
                        $val = $hourlyData[$h] ?? 0;
                        $max = count($hourlyData) > 0 ? max($hourlyData) : 1;
                        $height = $max > 0 ? ($val / $max) * 100 : 0;
                    @endphp
                    <div class="flex-1 group relative">
                        <div class="bg-primary-500/20 group-hover:bg-primary-500/40 transition-all rounded-t-sm w-full" style="height: {{ max(5, $height) }}%"></div>
                        <div class="text-[8px] text-gray-400 text-center mt-1">{{ sprintf('%02d', $h) }}</div>
                        {{-- Tooltip --}}
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 bg-gray-900 text-white text-[10px] py-1 px-2 rounded opacity-0 group-hover:opacity-100 whitespace-nowrap z-10 transition-opacity">
                            {{ $val }} Menit
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        {{-- Menu Efficiency --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <x-heroicon-o-document-chart-bar class="w-5 h-5 text-indigo-500" />
                    Efisiensi Per Menu
                </h3>
                <p class="text-xs text-gray-400">Rerata waktu masak berdasarkan jenis menu</p>
            </div>
            
            <div class="overflow-x-auto max-h-[300px] overflow-y-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900/50 sticky top-0">
                        <tr>
                            <th class="px-4 py-3 font-bold text-gray-500 uppercase text-xs">Menu</th>
                            <th class="px-4 py-3 font-bold text-gray-500 uppercase text-xs text-center">Total</th>
                            <th class="px-4 py-3 font-bold text-gray-500 uppercase text-xs text-right">Avg Prep</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($menuStats as $stat)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $stat->name }}</td>
                            <td class="px-4 py-3 text-center text-gray-500">{{ $stat->total_sold }} orders</td>
                            <td class="px-4 py-3 text-right">
                                <span @class([
                                    'font-bold',
                                    'text-danger-600' => $stat->avg_prep_time > 20,
                                    'text-warning-600' => $stat->avg_prep_time > 10 && $stat->avg_prep_time <= 20,
                                    'text-success-600' => $stat->avg_prep_time <= 10,
                                ])>
                                    {{ round($stat->avg_prep_time, 1) }} Menit
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-gray-400">Belum ada data menu.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Order Audit (Daftar Order - Yang sudah ada sebelumnya dipindah ke sini) --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-danger-500" />
                    Audit: 10 Pesanan Terlama
                </h3>
                <p class="text-xs text-gray-400">Deteksi bottleneck pada pesanan spesifik</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr class="bg-gray-50 dark:bg-gray-900/50">
                            <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase">Order #</th>
                            <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase">Waktu Pesan</th>
                            <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase text-center">Durasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($extremeOrders as $order)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-4 py-3 font-medium text-primary-600">#{{ $order->order_number }}</td>
                            <td class="px-4 py-3 text-gray-500 text-xs">{{ $order->created_at->format('H:i') }}</td>
                            <td class="px-4 py-3 text-center">
                                <span @class([
                                    'inline-flex items-center gap-1 font-bold',
                                    'text-danger-600' => $order->duration > 20,
                                    'text-warning-600' => $order->duration > 10 && $order->duration <= 20,
                                    'text-success-600' => $order->duration <= 10,
                                ])>
                                    {{ $order->duration }} Min
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-gray-400">Belum ada data audit.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
