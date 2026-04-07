<x-filament-panels::page wire:poll.5s>
    <style>
        /* Override Filament's max-width to make KDS fullscreen */
        .fi-simple-layout {
            max-width: 100% !important;
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .fi-simple-main-ctn {
            height: 100vh;
            align-items: stretch !important;
        }
        .fi-simple-main {
            max-width: 100% !important;
            margin: 0 !important;
            padding: 1.5rem !important;
            display: flex;
            flex-direction: column;
            border-radius: 0 !important;
            box-shadow: none !important;
            height: 100%;
            flex: 1;
        }
        .fi-pa-page {
            display: flex;
            flex-direction: column;
            height: 100%;
            flex: 1;
            min-height: 0;
        }
        .fi-pa-page > .grid {
            flex: 1;
            min-height: 0;
        }
    </style>

    {{-- Header --}}
    <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-300 dark:border-gray-700">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ $restaurant->name }} - Kitchen Display</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Auto-refresh every 5s • {{ now()->format('D, d M Y H:i:s') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-3 py-1 rounded-full bg-green-500/20 text-green-600 dark:text-green-400 text-xs font-bold ring-1 ring-green-500/50 animate-pulse">
                LIVE
            </span>
            <button 
                onclick="toggleFullscreen()" 
                class="px-3 py-2 rounded-lg bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 transition-colors"
                title="Toggle Fullscreen"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Board Layout --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 flex-1 min-h-0">
        {{-- Column 1: Incoming --}}
        <div class="flex flex-col bg-white dark:bg-gray-800 rounded-xl p-4 shadow h-full overflow-hidden min-h-0 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold mb-4 flex items-center text-yellow-600 dark:text-yellow-400">
                <span class="w-3 h-3 rounded-full bg-yellow-500 mr-2 animate-pulse"></span>
                Incoming ({{ $incomingOrders->count() }})
            </h2>
            <div class="space-y-4 overflow-y-auto pr-2 custom-scrollbar flex-1">
                @forelse($incomingOrders as $order)
                    <div wire:key="incoming-{{ $order->id }}" class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 border-l-4 {{ $order->status == 'pending' ? 'border-red-500' : 'border-yellow-500' }} shadow relative">
                        <div class="flex justify-between items-start mb-2 border-b border-gray-200 dark:border-gray-700 pb-2">
                            <div>
                                <h3 class="font-bold text-lg dark:text-white">
                                    {{ $order->table ? ($order->table->name . ' (' . $order->table->area . ')') : 'Takeaway' }}
                                </h3>
                                <div class="text-xs text-gray-500 dark:text-gray-400">#{{ $order->id }} • {{ $order->customer_name }}</div>
                            </div>
                            <span class="bg-gray-200 dark:bg-gray-700 text-xs px-2 py-1 rounded font-mono dark:text-gray-300">
                                {{ $order->created_at->format('H:i') }}
                            </span>
                        </div>
                        {{-- Items --}}
                        <div class="space-y-1 mb-4">
                            @foreach($order->items as $item)
                                <div class="flex items-start text-sm">
                                    <span class="font-bold min-w-[24px] text-yellow-600 dark:text-yellow-400">{{ $item->quantity }}x</span>
                                    <div class="flex-1 dark:text-gray-200">
                                        {{ $item->menuItem->name }}
                                        @if($item->variant) <span class="text-xs text-gray-500 italic">({{ $item->variant->name }})</span> @endif
                                        @if($item->addons)
                                            @foreach($item->addons as $addon) <div class="text-xs text-gray-400">+ {{ $addon['name'] }}</div> @endforeach
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($order->notes)
                            <div class="bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-200 text-xs p-2 rounded mb-3 border border-red-100 dark:border-red-900/30">
                                <strong>Note:</strong> {{ $order->notes }}
                            </div>
                        @endif
                        {{-- Actions --}}
                        <div class="grid grid-cols-1 gap-2 mt-auto">
                            @if($order->status == 'pending')
                                <x-filament::button wire:click="updateStatus({{ $order->id }}, 'confirmed')" color="warning" size="sm">
                                    Confirm Order
                                </x-filament::button>
                            @else
                                <x-filament::button wire:click="updateStatus({{ $order->id }}, 'cooking')" color="info" size="sm">
                                    Start Cooking
                                </x-filament::button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 text-gray-400 dark:text-gray-500 italic">No incoming orders</div>
                @endforelse
            </div>
        </div>

        {{-- Column 2: Cooking --}}
        <div class="flex flex-col bg-white dark:bg-gray-800 rounded-xl p-4 shadow h-full overflow-hidden min-h-0 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold mb-4 flex items-center text-blue-600 dark:text-blue-400">
                <span class="w-3 h-3 rounded-full bg-blue-500 mr-2 animate-pulse"></span>
                Cooking ({{ $cookingOrders->count() }})
            </h2>
            <div class="space-y-4 overflow-y-auto pr-2 custom-scrollbar flex-1">
                @forelse($cookingOrders as $order)
                    <div wire:key="cooking-{{ $order->id }}" class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 border-l-4 border-blue-500 shadow relative">
                        <div class="flex justify-between items-start mb-2 border-b border-gray-200 dark:border-gray-700 pb-2">
                            <div>
                                <h3 class="font-bold text-lg dark:text-white">
                                    {{ $order->table ? ($order->table->name) : 'Takeaway' }}
                                </h3>
                                <div class="text-xs text-gray-500 dark:text-gray-400">#{{ $order->id }}</div>
                            </div>
                            <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs px-2 py-1 rounded font-mono">
                                {{ $order->created_at->format('H:i') }}
                            </span>
                        </div>
                        <div class="space-y-1 mb-4">
                            @foreach($order->items as $item)
                                <div class="flex items-center text-sm">
                                    <span class="font-bold min-w-[24px] text-blue-600 dark:text-blue-400">{{ $item->quantity }}x</span>
                                    <span class="ml-1 dark:text-gray-200">{{ $item->menuItem->name }} <span class="text-xs text-gray-500">({{ $item->variant->name ?? '' }})</span></span>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-auto">
                            <x-filament::button wire:click="updateStatus({{ $order->id }}, 'ready_to_serve')" color="success" size="sm" class="w-full">
                                Mark as Ready
                            </x-filament::button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 text-gray-400 dark:text-gray-500 italic">No orders cooking</div>
                @endforelse
            </div>
        </div>

        {{-- Column 3: Ready --}}
        <div class="flex flex-col bg-white dark:bg-gray-800 rounded-xl p-4 shadow h-full overflow-hidden min-h-0 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold mb-4 flex items-center text-green-600 dark:text-green-400">
                <span class="w-3 h-3 rounded-full bg-green-500 mr-2"></span>
                Ready ({{ $readyOrders->count() }})
            </h2>
            <div class="space-y-4 overflow-y-auto pr-2 custom-scrollbar flex-1">
                @forelse($readyOrders as $order)
                    <div wire:key="ready-{{ $order->id }}" class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 border-l-4 border-green-500 shadow relative opacity-90 hover:opacity-100 transition">
                        <div class="flex justify-between items-start mb-2">
                             <div>
                                <h3 class="font-bold text-lg text-green-600 dark:text-green-400">
                                    {{ $order->table ? ($order->table->name) : 'Takeaway' }}
                                </h3>
                                <div class="text-xs text-gray-500 dark:text-gray-400">#{{ $order->id }} • {{ $order->customer_name }}</div>
                            </div>
                            <div class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-xs px-2 py-1 rounded font-bold">READY</div>
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mb-4 pb-4 border-b border-gray-200 dark:border-gray-700">
                            {{ $order->items->sum('quantity') }} Items
                        </div>
                        <div class="mt-auto">
                            <x-filament::button wire:click="updateStatus({{ $order->id }}, 'completed')" color="gray" size="sm" class="w-full">
                                Complete Order
                            </x-filament::button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 text-gray-400 dark:text-gray-500 italic">No orders ready</div>
                @endforelse
            </div>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(156, 163, 175, 0.5); border-radius: 3px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); }
    </style>

    <script>
        function toggleFullscreen() {
            if (!document.fullscreenElement && 
                !document.mozFullScreenElement && 
                !document.webkitFullscreenElement && 
                !document.msFullscreenElement) {
                // Enter fullscreen
                const elem = document.documentElement;
                if (elem.requestFullscreen) {
                    elem.requestFullscreen();
                } else if (elem.mozRequestFullScreen) {
                    elem.mozRequestFullScreen();
                } else if (elem.webkitRequestFullscreen) {
                    elem.webkitRequestFullscreen();
                } else if (elem.msRequestFullscreen) {
                    elem.msRequestFullscreen();
                }
            } else {
                // Exit fullscreen
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                }
            }
        }

        // Optional: Listen for fullscreen changes to update button icon
        document.addEventListener('fullscreenchange', function() {
            // You can add logic here to change button icon if needed
        });
    </script>
</x-filament-panels::page>
