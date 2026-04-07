<div class="bg-white rounded-lg p-3 shadow-sm border-l-4 {{ $order->status === 'pending' ? 'border-red-500' : ($order->status === 'confirmed' ? 'border-orange-500' : ($order->status === 'cooking' ? 'border-yellow-500' : 'border-green-500')) }}">
    <div class="flex justify-between items-start mb-2">
        <div>
            @if($order->table)
                <span class="font-bold text-lg text-gray-800">T-{{ $order->table->name }}</span>
            @else
                <span class="font-bold text-gray-800">Takeaway</span>
            @endif
            <div class="text-xs text-gray-500 font-medium tracking-wide">#{{ $order->id }} • {{ $order->customer_name }}</div>
        </div>
        <div class="text-xs font-mono bg-gray-100 rounded px-1.5 py-0.5 text-gray-500 flex flex-col items-end">
           <span>{{ $order->created_at->format('H:i') }}</span>
           @if($order->status === 'confirmed')
                <span class="text-[9px] text-orange-600 font-bold uppercase mt-1 px-1 bg-orange-50 rounded">Confirmed</span>
           @endif
        </div>
    </div>

    <hr class="border-dashed border-gray-200 my-2">

    <div class="space-y-2 mb-3">
        @foreach($order->items as $item)
            <div class="flex items-start text-sm">
                <span class="font-bold w-6 text-gray-600">{{ $item->quantity }}x</span>
                <div class="flex-1">
                    <span class="font-bold text-gray-800">{{ $item->menuItem->name }}</span>
                    @if($item->variant)
                        <span class="text-xs text-gray-500">({{ $item->variant->name }})</span>
                    @endif
                    @if(!empty($item->addons))
                         <div class="text-[10px] text-gray-500 pl-1 border-l-2 border-gray-200 mt-1">
                            @foreach($item->addons as $addon)
                                + {{ $addon['name'] }}<br>
                            @endforeach
                         </div>
                    @endif
                    @if($item->is_paid)
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[8px] font-black bg-emerald-100 text-emerald-700 uppercase tracking-tighter ml-1">PAID</span>
                    @endif
                    @if($item->note)
                        <div class="text-[10px] text-amber-600 font-bold italic mt-1 leading-tight tracking-tight">
                            * {{ $item->note }}
                        </div>
                    @endif
                </div>
                @if($order->status === 'cooking')
                    <div class="ml-2">
                        <input type="checkbox" 
                            wire:click="toggleItemReady({{ $item->id }})" 
                            {{ $item->is_ready ? 'checked' : '' }}
                            class="w-5 h-5 rounded border-gray-300 text-yellow-600 focus:ring-yellow-500 transition-all cursor-pointer shadow-sm">
                    </div>
                @elseif($order->status === 'ready_to_serve' || $order->status === 'completed')
                    <div class="ml-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l5-5z" clip-rule="evenodd" />
                        </svg>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    @if($order->notes)
        <div class="bg-yellow-50 p-2 rounded text-xs text-yellow-800 mb-3 font-medium border border-yellow-100">
            📝 {{ $order->notes }}
        </div>
    @endif

    <div class="pt-2">
        @php
            $isCooking = $order->status === 'cooking';
            $allReady = !$isCooking || $order->items->every->is_ready;
            $finalBtnColor = $isCooking && !$allReady ? 'bg-gray-400 cursor-not-allowed' : $btnColor;
        @endphp
        <button 
            wire:click="updateStatus({{ $order->id }}, '{{ $nextStatus }}')" 
            @if($isCooking && !$allReady) disabled @endif
            class="w-full text-white font-bold text-sm py-2 rounded-lg shadow-sm transition-all duration-300 {{ $finalBtnColor }}">
            @if($isCooking && !$allReady)
                <span class="flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Check All Items
                </span>
            @else
                {{ $btnLabel }}
            @endif
        </button>
    </div>
</div>
