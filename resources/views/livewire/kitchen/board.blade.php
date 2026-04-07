<div class="min-h-screen bg-gray-100 p-4" wire:poll.5s>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6 bg-white p-4 rounded-xl shadow-sm">
        <div class="flex items-center space-x-3">
            <h1 class="text-2xl font-bold text-gray-800">🧑‍🍳 Kitchen Display System</h1>
            <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-sm font-medium">
                {{ $restaurant->name }}
            </span>
        </div>
        <div class="text-sm text-gray-500">
            Auto-refreshing every 5s
        </div>
    </div>

    {{-- Kanban Board --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 h-[calc(100vh-140px)] overflow-hidden">
        
        {{-- Column: PENDING --}}
        <div class="flex flex-col bg-gray-200 rounded-xl h-full overflow-hidden min-h-0">
            <div class="p-3 bg-red-100 rounded-t-xl border-b border-red-200">
                <h2 class="font-bold text-red-800 flex items-center justify-between">
                    <span>🔥 Pending Orders</span>
                    <span class="bg-white px-2 py-0.5 rounded-full text-xs shadow-sm">{{ $orders->get('pending', collect())->count() }}</span>
                </h2>
            </div>
            <div class="p-3 overflow-y-auto flex-1 space-y-4">
                @forelse($orders->get('pending', []) as $order)
                    @include('livewire.kitchen.order-card', ['order' => $order, 'nextStatus' => 'cooking', 'btnLabel' => 'Start Cooking', 'btnColor' => 'bg-red-600 hover:bg-red-700'])
                @empty
                    <div class="text-center text-gray-400 py-10">No pending orders</div>
                @endforelse
            </div>
        </div>

        {{-- Column: COOKING --}}
        <div class="flex flex-col bg-gray-200 rounded-xl h-full overflow-hidden min-h-0">
            <div class="p-3 bg-yellow-100 rounded-t-xl border-b border-yellow-200">
                <h2 class="font-bold text-yellow-800 flex items-center justify-between">
                    <span>🍳 Cooking</span>
                    <span class="bg-white px-2 py-0.5 rounded-full text-xs shadow-sm">{{ $orders->get('cooking', collect())->count() }}</span>
                </h2>
            </div>
            <div class="p-3 overflow-y-auto flex-1 space-y-4">
                 @forelse($orders->get('cooking', []) as $order)
                    @include('livewire.kitchen.order-card', ['order' => $order, 'nextStatus' => 'ready_to_serve', 'btnLabel' => 'Mark Ready', 'btnColor' => 'bg-yellow-600 hover:bg-yellow-700'])
                @empty
                    <div class="text-center text-gray-400 py-10">Skillet is empty</div>
                @endforelse
            </div>
        </div>

        {{-- Column: READY --}}
        <div class="flex flex-col bg-gray-200 rounded-xl h-full overflow-hidden min-h-0">
            <div class="p-3 bg-green-100 rounded-t-xl border-b border-green-200">
                <h2 class="font-bold text-green-800 flex items-center justify-between">
                    <span>✅ Ready to Serve</span>
                    <span class="bg-white px-2 py-0.5 rounded-full text-xs shadow-sm">{{ $orders->get('ready_to_serve', collect())->count() }}</span>
                </h2>
            </div>
            <div class="p-3 overflow-y-auto flex-1 space-y-4">
                 @forelse($orders->get('ready_to_serve', []) as $order)
                    @include('livewire.kitchen.order-card', ['order' => $order, 'nextStatus' => 'completed', 'btnLabel' => 'Complete', 'btnColor' => 'bg-green-600 hover:bg-green-700'])
                @empty
                    <div class="text-center text-gray-400 py-10">Nothing for waiters</div>
                @endforelse
            </div>
        </div>

    </div>
</div>
