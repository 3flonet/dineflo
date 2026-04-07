<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- Cashier Leaderboard --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-primary-50/30 dark:bg-primary-900/10">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <x-heroicon-o-presentation-chart-line class="w-5 h-5 text-primary-500" />
                    Top Performance: Kasir
                </h3>
                <p class="text-xs text-gray-400">Peringkat berdasarkan volume penjualan & transaksi di POS</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-4 py-3 font-bold text-gray-500 uppercase text-xs">Staff</th>
                            <th class="px-4 py-3 font-bold text-gray-500 uppercase text-xs text-center">Transaksi</th>
                            <th class="px-4 py-3 font-bold text-gray-500 uppercase text-xs text-right">Total Penjualan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($cashierStats as $staff)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="bg-primary-100 dark:bg-primary-900/50 text-primary-700 dark:text-primary-300 w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs">
                                        {{ substr($staff->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $staff->name }}</div>
                                        <div class="text-[10px] text-gray-400 capitalize">{{ $staff->roles->pluck('name')->join(', ') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center font-bold text-gray-700 dark:text-gray-300">
                                {{ $staff->orders_processed_count }}
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-primary-600">
                                Rp {{ number_format($staff->orders_processed_sum_total_amount ?? 0, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-gray-400">Belum ada data transaksi kasir.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Waiter Leaderboard --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-orange-50/30 dark:bg-orange-900/10">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <x-heroicon-o-fire class="w-5 h-5 text-orange-500" />
                    Top Performance: Delivery/Served
                </h3>
                <p class="text-xs text-gray-400">Peringkat berdasarkan kecepatan antar & jumlah pesanan disajikan</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-4 py-3 font-bold text-gray-500 uppercase text-xs">Staff</th>
                            <th class="px-4 py-3 font-bold text-gray-500 uppercase text-xs text-center">Served</th>
                            <th class="px-4 py-3 font-bold text-gray-500 uppercase text-xs text-right">Avg Speed</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($waiterStats as $staff)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="bg-orange-100 dark:bg-orange-900/50 text-orange-700 dark:text-orange-300 w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs">
                                        {{ substr($staff->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $staff->name }}</div>
                                        <div class="text-[10px] text-gray-400 capitalize">{{ $staff->roles->pluck('name')->join(', ') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center font-bold text-gray-700 dark:text-gray-300">
                                {{ $staff->orders_served_count }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <span @class([
                                    'px-2 py-1 rounded text-[10px] font-bold',
                                    'bg-success-100 text-success-700' => $staff->avg_serve_time < 5,
                                    'bg-warning-100 text-warning-700' => $staff->avg_serve_time >= 5 && $staff->avg_serve_time <= 10,
                                    'bg-danger-100 text-danger-700' => $staff->avg_serve_time > 10,
                                ])>
                                    {{ round($staff->avg_serve_time ?? 0, 1) }} Min
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-gray-400">Belum ada data pesanan disajikan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-filament-panels::page>
