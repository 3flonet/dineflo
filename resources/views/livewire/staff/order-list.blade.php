<div class="min-h-screen bg-gray-50 p-4" wire:poll.10s>
    {{-- Header & Filters --}}
    {{-- View Mode Switcher --}}
    <div class="mb-6 flex items-center space-x-4 border-b border-gray-200">
        <button wire:click="setViewMode('orders')" class="pb-4 px-4 text-sm font-medium transition-all relative {{ $viewMode === 'orders' ? 'text-black font-bold' : 'text-gray-500 hover:text-black' }}">
            📦 Manajemen Pesanan
            @if($viewMode === 'orders')
                <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-black rounded-full"></div>
            @endif
        </button>
        <button wire:click="setViewMode('tables')" class="pb-4 px-4 text-sm font-medium transition-all relative {{ $viewMode === 'tables' ? 'text-black font-bold' : 'text-gray-500 hover:text-black' }}">
            @if(!$restaurant->owner->hasFeature('Table Management System'))
                <svg class="h-3 w-3 inline mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            @endif
            🪑 Kontrol Meja
            @if($viewMode === 'tables')
                <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-black rounded-full"></div>
            @endif
        </button>
        <button wire:click="setViewMode('queues')" class="pb-4 px-4 text-sm font-medium transition-all relative {{ $viewMode === 'queues' ? 'text-black font-bold' : 'text-gray-500 hover:text-black' }}">
            @if(!$restaurant->owner->hasFeature('Queue Management System'))
                <svg class="h-3 w-3 inline mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            @endif
            🔊 Antrean
            @if($viewMode === 'queues')
                <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-black rounded-full"></div>
            @endif
        </button>
    </div>

    @if($viewMode === 'orders')
        {{-- Filters (Existing) --}}
        <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center space-x-2 bg-white p-1 rounded-lg shadow-sm w-fit">
                <button wire:click="setFilter('active')" class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $statusFilter === 'active' ? 'bg-black text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                    Menunggu / Aktif
                </button>
                <button wire:click="setFilter('completed')" class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $statusFilter === 'completed' ? 'bg-black text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                    Riwayat Selesai
                </button>
            </div>
            
            <div class="relative flex-1 max-w-md">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama atau nomor pesanan..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border-none shadow-sm focus:ring-2 focus:ring-black">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 absolute left-3 top-3" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                </svg>
            </div>
        </div>

        {{-- Orders Table --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">ID / Waktu</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Pelanggan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Pembayaran</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Total Ambil</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-50">
                        @forelse($orders as $order)
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">#{{ $order->order_number ?: $order->id }}</div>
                                    <div class="text-xs text-gray-400">{{ $order->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">{{ $order->customer_name }}</div>
                                    <div class="flex items-center text-xs text-gray-500 mt-0.5">
                                        <svg class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        {{ $order->table ? 'Meja ' . $order->table->name : 'Takeaway / Delivery' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-rose-50 text-rose-600 border-rose-100',
                                            'confirmed' => 'bg-blue-50 text-blue-600 border-blue-100',
                                            'cooking' => 'bg-amber-50 text-amber-600 border-amber-100',
                                            'ready_to_serve' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                            'completed' => 'bg-slate-100 text-slate-600 border-slate-200',
                                        ];
                                        $color = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-lg border {{ $color }}">
                                        {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($order->payment_status === 'paid')
                                        <span class="flex items-center text-emerald-600 text-xs font-bold">
                                            <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            Lunas
                                        </span>
                                    @else
                                        <span class="flex items-center text-rose-500 text-xs font-bold">
                                            <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Belum Bayar
                                        </span>
                                        <div class="text-[10px] text-gray-400 mt-1 uppercase tracking-wider font-semibold">{{ $order->payment_method }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-black">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-1">
                                    @if($order->status === 'pending')
                                        <button wire:click="updateStatus({{ $order->id }}, 'confirmed')" class="bg-blue-600 text-white px-3 py-1.5 rounded-lg hover:bg-blue-700 transition-colors shadow-sm">Confirm</button>
                                    @endif
                                    
                                    @if($order->status === 'ready_to_serve')
                                        <button wire:click="updateStatus({{ $order->id }}, 'completed')" class="bg-emerald-600 text-white px-3 py-1.5 rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">Selesai</button>
                                    @endif
    
                                    @if($order->payment_status === 'unpaid')
                                        <button wire:click="markAsPaid({{ $order->id }})" class="bg-slate-900 text-white px-3 py-1.5 rounded-lg hover:bg-black transition-colors shadow-sm">Bayar</button>
                                    @endif
                                    
                                    <a href="{{ route('order.summary', $order->id) }}" target="_blank" class="inline-block p-1.5 text-gray-400 hover:text-black transition-colors" title="Lihat Nota">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="h-16 w-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                            <svg class="h-8 w-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <p class="text-gray-500 font-medium">Belum ada pesanan yang ditemukan</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($orders && count($orders) > 0)
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    @elseif($viewMode === 'tables')
        {{-- Table Management View --}}
        <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Visual Denah Meja</h2>
                <p class="text-xs text-gray-500">Klik status meja untuk mengubah secara manual atau klik tombol Bersihkan.</p>
            </div>
            
            <div class="relative flex-1 max-w-md">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nomor meja atau area..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border-none shadow-sm focus:ring-2 focus:ring-black">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 absolute left-3 top-3" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                </svg>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
            @forelse($tables as $table)
                @php
                    $statusInfo = $tableStatuses[$table->status] ?? $tableStatuses['available'];
                @endphp
                <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex flex-col justify-between transition-all hover:shadow-md relative min-h-[190px] group">
                    {{-- Status Indicator Bar --}}
                    <div class="absolute top-0 left-0 right-0 h-1 {{ $statusInfo['bg'] }} rounded-t-2xl"></div>
                    
                    <div class="mb-3">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">{{ $table->area }}</span>
                            <span class="text-[10px] font-bold text-gray-400">Cap: {{ $table->capacity }}</span>
                        </div>
                        <h3 class="text-xl font-black text-gray-900">MEJA {{ $table->name }}</h3>
                    </div>

                    <div class="space-y-3">
                        {{-- Status Badge (Clickable to Toggle Options) --}}
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="w-full flex items-center justify-center space-x-2 py-2 rounded-xl {{ $statusInfo['bg'] }} {{ $statusInfo['text'] }} transition-transform active:scale-95">
                                <span class="text-xs font-bold uppercase">{{ $statusInfo['label'] }}</span>
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            <div x-show="open" @click.away="open = false" class="absolute z-20 mt-1 w-full bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden py-1">
                                @foreach($tableStatuses as $sKey => $sVal)
                                    <button wire:click="updateTableStatus({{ $table->id }}, '{{ $sKey }}'); open = false" class="w-full text-left px-4 py-2 text-xs font-bold hover:bg-gray-50 flex items-center space-x-2 {{ $table->status === $sKey ? 'text-black bg-gray-50' : 'text-gray-500' }}">
                                        <div class="h-2 w-2 rounded-full {{ $sVal['bg'] }}"></div>
                                        <span>{{ $sVal['label'] }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Quick Action: Bersihkan (Only for Dirty/Occupied) --}}
                        @if($table->status !== 'available')
                            <button wire:click="updateTableStatus({{ $table->id }}, 'available')" class="w-full py-2 bg-slate-900 border border-slate-900 text-white rounded-xl text-xs font-bold hover:bg-black transition-all flex items-center justify-center shadow-lg shadow-gray-200">
                                <svg class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                BERSIHKAN / CLEAR
                            </button>
                        @else
                            <div class="h-[34px] flex items-center justify-center">
                                <span class="text-[10px] text-gray-300 font-bold uppercase italic">Meja Siap Digunakan</span>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 bg-white rounded-2xl text-center border border-dashed border-gray-200">
                    <p class="text-gray-400 font-medium">Belum ada meja yang terdaftar.</p>
                </div>
            @endforelse
        </div>

        @if($tables && count($tables) > 0)
            <div class="mt-6">
                {{ $tables->links() }}
            </div>
        @endif
    @else
        {{-- Queue Management View --}}
        <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Manajemen Antrean Pelanggan</h2>
                <p class="text-xs text-gray-500">Pantau dan panggil antrean aktif hari ini.</p>
            </div>
            
            <div class="relative flex-1 max-w-md">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama pelanggan atau nomor..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border-none shadow-sm focus:ring-2 focus:ring-black">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 absolute left-3 top-3" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-50">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Antrean</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Pelanggan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Orang</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Waktu Tunggu</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-50">
                        @forelse($queues as $q)
                            <tr class="hover:bg-gray-50/80 transition-colors {{ $q->status === 'calling' ? 'bg-blue-50/30' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-3">
                                        <div class="h-12 w-12 rounded-xl flex items-center justify-center font-black text-lg border-2 {{ $q->status === 'calling' ? 'bg-blue-600 text-white border-blue-600 animate-pulse' : 'bg-white text-gray-900 border-gray-100' }}">
                                            {{ $q->full_number }}
                                        </div>
                                        @if($q->status === 'calling')
                                            <span class="text-[10px] font-bold text-blue-600 uppercase tracking-widest">Memanggil...</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">{{ $q->customer_name ?: 'Pelanggan Walk-in' }}</div>
                                    <div class="text-xs text-gray-400 font-medium">{{ ucfirst($q->source) }} Queue</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 bg-gray-100 rounded-lg text-xs font-bold text-gray-600">
                                        {{ $q->guest_count }} Orang
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-xs font-bold text-gray-900">{{ $q->created_at->diffForHumans(null, true) }}</div>
                                    <div class="text-[10px] text-gray-400 mt-0.5">{{ $q->created_at->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                                    {{-- Call Action --}}
                                    <button wire:click="callQueue({{ $q->id }})" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm group" title="Panggil Antrean">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/></svg>
                                    </button>

                                    {{-- Seat Action (Dropdown Tables) --}}
                                    <div x-data="{ open: false }" class="inline-block relative">
                                        <button @click="open = !open" class="p-2 bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-600 hover:text-white transition-all shadow-sm" title="Berikan Meja">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 10h.01M16 14h.01M16 20h.01M14 16h3.586a1 1 0 01.707.293l2.414 2.414a1 1 0 01-1.414 1.414L19 19.414V20a2 2 0 01-2 2h-4a2 2 0 01-2-2v-4zM2 13a1 1 0 011-1h1.586l4.707-4.707C9.923 6.663 11 7.109 11 8v14c0 .891-1.077 1.337-1.707.707L4.586 18H3a1 1 0 01-1-1v-4z"/></svg>
                                        </button>
                                        
                                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-100 z-50 overflow-hidden">
                                            <div class="px-4 py-2 bg-gray-50 border-b border-gray-100 text-[10px] font-bold text-gray-400 uppercase">Pilih Meja Tersedia</div>
                                            <div class="max-h-48 overflow-y-auto">
                                                @forelse($availableTables as $at)
                                                    <button wire:click="seatedQueue({{ $q->id }}, {{ $at->id }}); open = false" class="w-full text-left px-4 py-2.5 text-xs font-bold hover:bg-emerald-50 text-gray-700 transition-colors flex justify-between items-center">
                                                        <span>Meja {{ $at->name }}</span>
                                                        <span class="text-[10px] text-gray-400 font-medium">Cap: {{ $at->capacity }}</span>
                                                    </button>
                                                @empty
                                                    <div class="px-4 py-3 text-[10px] text-rose-500 font-bold italic text-center">Tidak ada meja kosong</div>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Skip / Cancel --}}
                                    <div x-data="{ open: false }" class="inline-block relative">
                                        <button @click="open = !open" class="p-2 bg-slate-50 text-slate-400 rounded-lg hover:bg-slate-900 hover:text-white transition-all shadow-sm">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"/></svg>
                                        </button>
                                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-32 bg-white rounded-xl shadow-xl border border-gray-100 z-50 overflow-hidden py-1">
                                            <button wire:click="skipQueue({{ $q->id }})" class="w-full text-left px-4 py-2 text-xs font-bold text-amber-600 hover:bg-amber-50">Lewati (Skip)</button>
                                            <button wire:click="cancelQueue({{ $q->id }})" class="w-full text-left px-4 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50 border-t border-gray-50">Batalkan</button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="h-20 w-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                            <svg class="h-10 w-10 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        </div>
                                        <p class="text-gray-400 font-bold">Saat ini tidak ada antrean aktif.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($queues && count($queues) > 0)
                <div class="px-6 py-4 border-t border-gray-50">
                    {{ $queues->links() }}
                </div>
            @endif
        </div>
    @endif
</div>
