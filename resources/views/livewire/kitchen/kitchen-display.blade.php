<div class="min-h-screen bg-gray-900 text-white p-4 font-sans" wire:poll.5s>
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-700">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">{{ $restaurant->name }} - Kitchen Display</h1>
            <p class="text-sm text-gray-400">Auto-refresh every 5s • {{ now()->format('D, d M Y H:i:s') }}</p>
        </div>
        <div>
            <span class="px-3 py-1 rounded-full bg-green-500/20 text-green-400 text-xs font-bold ring-1 ring-green-500/50 blink-animation">
                LIVE
            </span>
        </div>
    </div>

    {{-- Board Layout --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 h-full">

        {{-- Column 1: Incoming (Pending/Confirmed) --}}
        <div class="flex flex-col bg-gray-800/50 rounded-xl p-4 border border-gray-700/50">
            <h2 class="text-xl font-bold mb-4 flex items-center text-yellow-500">
                <span class="w-3 h-3 rounded-full bg-yellow-500 mr-2"></span>
                Incoming ({{ $incomingOrders->count() }})
            </h2>
            
            <div class="space-y-4 overflow-y-auto pr-2 custom-scrollbar flex-1">
                @forelse($incomingOrders as $order)
                    <div wire:key="incoming-{{ $order->id }}" class="bg-gray-800 rounded-lg p-4 border-l-4 {{ $order->status == 'pending' ? 'border-red-500' : 'border-yellow-500' }} shadow-lg">
                        <div class="flex justify-between items-start mb-3 border-b border-gray-700 pb-2">
                            <div>
                                <h3 class="font-bold text-lg">
                                    {{ $order->table ? ($order->table->name . ' (' . $order->table->area . ')') : 'Takeaway' }}
                                </h3>
                                <div class="text-xs text-gray-400">#{{ $order->id }} • {{ $order->customer_name }}</div>
                            </div>
                            <div class="text-right">
                                <span class="bg-gray-700 text-xs px-2 py-1 rounded font-mono">
                                    {{ $order->created_at->format('H:i') }}
                                </span>
                                <div class="text-xs text-red-400 mt-1 font-bold">
                                    {{ $order->created_at->diffForHumans(null, true, true) }}
                                </div>
                            </div>
                        </div>

                        {{-- Order Items --}}
                        <div class="space-y-2 mb-4">
                            @foreach($order->items as $item)
                                <div class="flex items-start">
                                    <span class="font-bold text-lg min-w-[24px] text-yellow-400">{{ $item->quantity }}x</span>
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-200">{{ $item->menuItem->name }}</div>
                                        @if($item->variant)
                                            <div class="text-xs text-gray-400 italic">+ {{ $item->variant->name }}</div>
                                        @endif
                                        @if($item->addons)
                                            @foreach($item->addons as $addon)
                                                <div class="text-xs text-gray-400">+ {{ $addon['name'] }}</div>
                                            @endforeach
                                        @endif
                                        @if($item->note)
                                            <div class="text-[11px] text-yellow-500 font-extrabold italic mt-1 leading-tight uppercase tracking-tight bg-yellow-500/10 px-1 rounded">
                                                * {{ $item->note }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Order Notes --}}
                        @if($order->notes)
                            <div class="bg-red-900/20 text-red-200 text-xs p-2 rounded mb-3 border border-red-900/30">
                                <strong>Note:</strong> {{ $order->notes }}
                            </div>
                        @endif

                        {{-- Actions --}}
                        <div class="grid grid-cols-2 gap-2 mt-auto">
                            @if($order->status == 'pending')
                                <button wire:click="updateStatus({{ $order->id }}, 'confirmed')" 
                                    class="col-span-2 bg-yellow-600 hover:bg-yellow-500 text-white py-2 rounded font-bold text-sm transition">
                                    Confirm Order
                                </button>
                            @else
                                <button wire:click="updateStatus({{ $order->id }}, 'cooking')" 
                                    class="col-span-2 bg-blue-600 hover:bg-blue-500 text-white py-2 rounded font-bold text-sm transition flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z" />
                                    </svg>
                                    Start Cooking
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 text-gray-500 italic">No incoming orders</div>
                @endforelse
            </div>
        </div>

        {{-- Column 2: Cooking --}}
        <div class="flex flex-col bg-gray-800/50 rounded-xl p-4 border border-gray-700/50">
            <h2 class="text-xl font-bold mb-4 flex items-center text-blue-500">
                <span class="w-3 h-3 rounded-full bg-blue-500 mr-2 animate-pulse"></span>
                Cooking ({{ $cookingOrders->count() }})
            </h2>
            
            <div class="space-y-4 overflow-y-auto pr-2 custom-scrollbar flex-1">
                @forelse($cookingOrders as $order)
                    <div wire:key="cooking-{{ $order->id }}" class="bg-gray-800 rounded-lg p-4 border-l-4 border-blue-500 shadow-lg">
                        <div class="flex justify-between items-start mb-3 border-b border-gray-700 pb-2">
                             <div>
                                <h3 class="font-bold text-lg">
                                    {{ $order->table ? ($order->table->name) : 'Takeaway' }}
                                </h3>
                                <div class="text-xs text-gray-400">#{{ $order->id }}</div>
                            </div>
                            <div class="text-right">
                                <span class="bg-blue-900 text-blue-200 text-xs px-2 py-1 rounded font-mono">
                                    Cooking
                                </span>
                                <div class="text-xs text-gray-400 mt-1">
                                    {{ $order->created_at->format('H:i') }}
                                </div>
                            </div>
                        </div>

                         {{-- Order Items Condensed --}}
                        <div class="space-y-2 mb-4">
                            @foreach($order->items as $item)
                                <div class="flex items-center">
                                    <span class="font-bold text-lg min-w-[24px] text-blue-400">{{ $item->quantity }}x</span>
                                    <div class="flex-1 flex justify-between items-center">
                                        <div>
                                            <span class="text-sm text-gray-200 ml-1">{{ $item->menuItem->name }}
                                                @if($item->variant) <span class="text-xs text-gray-500">({{ $item->variant->name }})</span> @endif
                                            </span>
                                            @if($item->addons)
                                                <div class="pl-7">
                                                    @foreach($item->addons as $addon)
                                                        <div class="text-[10px] text-gray-500 font-medium tracking-tight">+ {{ $addon['name'] }}</div>
                                                    @endforeach
                                                </div>
                                            @endif
                                            @if($item->note)
                                                <div class="pl-7 mt-1">
                                                    <div class="text-[11px] text-yellow-500 font-extrabold italic leading-tight uppercase tracking-tight bg-yellow-500/10 px-1 rounded inline-block">
                                                        * {{ $item->note }}
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <input type="checkbox" 
                                                wire:click="toggleItemReady({{ $item->id }})" 
                                                {{ $item->is_ready ? 'checked' : '' }}
                                                class="w-6 h-6 rounded border-gray-600 bg-gray-700 text-yellow-600 focus:ring-yellow-500 transition-all cursor-pointer">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                         
                         @if($order->notes)
                            <div class="text-xs text-gray-400 mb-3 italic">"{{ $order->notes }}"</div>
                        @endif

                        @php
                            $allReady = $order->items->every->is_ready;
                        @endphp
                        <button wire:click="updateStatus({{ $order->id }}, 'ready_to_serve')" 
                            @if(!$allReady) disabled @endif
                            class="w-full {{ $allReady ? 'bg-green-600 hover:bg-green-500' : 'bg-gray-700 cursor-not-allowed text-gray-500' }} text-white py-2 rounded font-bold text-sm transition flex items-center justify-center gap-2">
                            @if(!$allReady)
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                Check All Menu
                            @else
                                Mark as Ready
                            @endif
                        </button>
                    </div>
                @empty
                     <div class="text-center py-10 text-gray-500 italic">No orders cooking</div>
                @endforelse
            </div>
        </div>

        {{-- Column 3: Ready to Serve --}}
        <div class="flex flex-col bg-gray-800/50 rounded-xl p-4 border border-gray-700/50">
            <h2 class="text-xl font-bold mb-4 flex items-center text-green-500">
                <span class="w-3 h-3 rounded-full bg-green-500 mr-2"></span>
                Ready to Serve ({{ $readyOrders->count() }})
            </h2>
            
            <div class="space-y-4 overflow-y-auto pr-2 custom-scrollbar flex-1">
                @forelse($readyOrders as $order)
                    <div wire:key="ready-{{ $order->id }}" class="bg-gray-800 rounded-lg p-4 border-l-4 border-green-500 shadow-lg opacity-90 hover:opacity-100 transition">
                         <div class="flex justify-between items-start mb-3">
                             <div>
                                <h3 class="font-bold text-lg text-green-400">
                                    {{ $order->table ? ($order->table->name) : 'Takeaway' }}
                                </h3>
                                <div class="text-xs text-gray-400">#{{ $order->id }} • {{ $order->customer_name }}</div>
                            </div>
                            <div class="bg-green-900 text-green-200 text-xs px-2 py-1 rounded font-bold">
                                READY
                            </div>
                        </div>

                         <div class="space-y-2 mb-4">
                            @foreach($order->items as $item)
                                <div class="flex items-center text-xs">
                                    <span class="font-bold text-green-400 mr-2">{{ $item->quantity }}x</span>
                                    <span class="text-gray-300">{{ $item->menuItem->name }}</span>
                                    @if($item->note)
                                        <span class="ml-2 text-[10px] text-yellow-500 font-bold italic truncate flex-1">* {{ $item->note }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <button wire:click="updateStatus({{ $order->id }}, 'completed')" 
                            class="w-full bg-white text-green-900 hover:bg-gray-200 py-2 rounded font-bold text-sm transition shadow">
                            Complete Order
                        </button>
                    </div>
                @empty
                     <div class="text-center py-10 text-gray-500 italic">No orders ready</div>
                @endforelse
            </div>
        </div>

    </div>
    <style>
        /* Custom Scrollbar for Dark Mode */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        @keyframes blink {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        .blink-animation {
            animation: blink 2s infinite;
        }
    </style>
</div>
