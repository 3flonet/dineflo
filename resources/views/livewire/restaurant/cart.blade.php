<div class="min-h-screen bg-gray-50 dark:bg-[#0B0F19] transition-colors duration-300">
    {{-- SKELETON LOADING (Selama Livewire Cart Component Mount/Refresh) --}}
    <div wire:loading wire:target="mount, updateQuantity, removeItem" class="fixed inset-0 z-[60] bg-white/70 dark:bg-black/70 flex flex-col items-center justify-center backdrop-blur-sm transition-opacity">
        <svg class="animate-spin h-10 w-10 text-primary-600 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-32 animate-pulse mb-1"></div>
        <div class="h-3 bg-gray-100 dark:bg-gray-800 rounded w-24 animate-pulse"></div>
    </div>

    {{-- Header --}}
    <div class="sticky top-0 z-50 bg-white/80 dark:bg-[#0B0F19]/80 backdrop-blur-md shadow-sm border-b dark:border-white/5 transition-colors">
            <div class="flex items-center justify-between h-16 px-4">
                <div class="flex items-center gap-3">
                    <a href="{{ route('restaurant.index', $restaurant->slug) }}" class="p-2 -ml-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/10 rounded-full transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <h1 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Keranjang Pesanan</h1>
                </div>
                
                <div class="flex items-center gap-2">
                    {{-- Theme Toggle --}}
                    <button @click="theme = theme === 'light' ? 'dark' : 'light'; updateTheme()" 
                            class="p-2 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/10 transition-all flex items-center justify-center">
                        <svg x-show="theme === 'light'" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                        <svg x-show="theme === 'dark' || theme === 'system'" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                        </svg>
                    </button>

                    <button wire:click="clearCart" 
                            onclick="confirm('Bersihkan keranjang?') || event.stopImmediatePropagation()"
                            class="text-xs font-bold text-red-500 hover:text-red-600 dark:text-red-400 dark:hover:text-red-300 px-3 py-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-all uppercase tracking-widest">
                        Bersihkan
                    </button>
                </div>
            </div>
    </div>

    @if(empty($cart))
        {{-- Empty Cart State --}}
        <div class="max-w-2xl mx-auto" wire:loading.remove wire:target="mount">
            <div class="flex flex-col items-center justify-center py-20 px-4">
                <div class="text-6xl mb-6 transform hover:scale-110 transition-transform">🛒</div>
                <h2 class="text-2xl font-black text-gray-900 dark:text-white mb-2 transition-colors">Cart is Empty</h2>
                <p class="text-gray-500 dark:text-gray-400 mb-8 text-center max-w-xs transition-colors">Looks like you haven't added anything to your cart yet.</p>
                <a href="{{ route('restaurant.index', $restaurant->slug) }}" 
                   class="bg-primary-600 hover:bg-primary-500 text-white px-8 py-3.5 rounded-xl font-bold shadow-xl shadow-primary-500/20 transition-all transform active:scale-95">
                    Browse Menu
                </a>
            </div>
        </div>
    @else
        <div class="max-w-5xl mx-auto p-4 pb-40">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
                {{-- Left Column: Cart Items --}}
                <div class="md:col-span-12 lg:col-span-7 space-y-4">
                    <div class="bg-white dark:bg-gray-800/50 rounded-2xl shadow-sm p-5 border border-gray-100 dark:border-white/5 transition-all">
                        <h2 class="font-bold text-xl mb-6 dark:text-white transition-colors">Pesanan Anda ({{ count($cart) }})</h2>
                        
                        {{-- SKELETON LOADING (saat qty / remove ditekan) --}}
                        <div wire:loading wire:target="updateQuantity, removeItem" class="w-full space-y-4">
                            @for($i=0; $i<count($cart); $i++)
                                <div class="flex border-b border-gray-50 dark:border-white/5 pb-4 last:border-0 animate-pulse">
                                    <div class="flex-1">
                                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/2 mb-2"></div>
                                        <div class="h-3 bg-gray-100 dark:bg-gray-800 rounded w-1/3 mb-1"></div>
                                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-24 mt-2"></div>
                                    </div>
                                    <div class="flex flex-col items-end justify-between ml-4">
                                        <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-12 mb-3"></div>
                                        <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded-lg w-20"></div>
                                    </div>
                                </div>
                            @endfor
                        </div>

                        {{-- ACTUAL ITEMS --}}
                        <div wire:loading.remove wire:target="updateQuantity, removeItem" class="space-y-6">
                            @foreach($cart as $item)
                                <div class="flex border-b border-gray-100 dark:border-white/5 pb-6 last:border-0 transition-colors">
                                    <div class="flex-1">
                                        <h3 class="font-bold text-gray-900 dark:text-white transition-colors">{{ $item['name'] }}</h3>
                                        
                                        @if($item['variant'])
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 transition-colors">• {{ $item['variant']['name'] }}</p>
                                        @endif
 
                                        @if(!empty($item['addons']))
                                            @foreach($item['addons'] as $addon)
                                                <p class="text-xs text-gray-500 dark:text-gray-400">• {{ $addon['name'] }}</p>
                                            @endforeach
                                        @endif
 
                                        @if(!empty($item['note']))
                                            <p class="text-[10px] font-bold text-amber-600 dark:text-amber-400 italic mt-1.5 flex items-center gap-1 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>
                                                Note: {{ $item['note'] }}
                                            </p>
                                        @endif
 
                                        <div class="mt-2 text-sm font-bold text-gray-900 dark:text-white transition-colors">
                                            @if(isset($item['original_price']) && $item['original_price'] > $item['price'])
                                                <div class="flex flex-col leading-tight mt-1">
                                                    <div class="flex items-center gap-1.5">
                                                        <span class="text-[10px] text-gray-400 dark:text-gray-500 line-through font-normal">Rp {{ number_format(($item['original_price'] + collect($item['addons'] ?? [])->sum('price')) * $item['quantity'], 0, ',', '.') }}</span>
                                                        @if(isset($item['discount_name']))
                                                            <span class="text-[10px] bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400 px-1.5 py-0.5 rounded-md font-bold uppercase tracking-wider">{{ $item['discount_name'] }}</span>
                                                        @endif
                                                    </div>
                                                    <span class="text-sm font-bold text-red-600 dark:text-red-400">Rp {{ number_format($item['total_price'], 0, ',', '.') }}</span>
                                                </div>
                                            @else
                                                Rp {{ number_format($item['total_price'], 0, ',', '.') }}
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex flex-col items-end justify-between ml-4">
                                        <button wire:click="removeItem('{{ $item['id'] }}')" 
                                                class="text-red-500 dark:text-red-400 text-xs font-bold hover:text-red-700 dark:hover:text-red-300 transition-colors uppercase tracking-wider">
                                            Remove
                                        </button>
                                        
                                        <div class="flex flex-col items-end gap-1.5">
                                            <div class="flex items-center space-x-1 bg-gray-100 dark:bg-gray-700/50 rounded-xl p-1 transition-colors">
                                                <button wire:click="updateQuantity('{{ $item['id'] }}', -1)" 
                                                        class="w-8 h-8 flex items-center justify-center font-bold text-gray-700 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-600 rounded-lg shadow-sm transition-all transform active:scale-95">
                                                    −
                                                </button>
                                                <span class="text-sm font-black w-7 text-center dark:text-white">{{ $item['quantity'] }}</span>
                                                <button wire:click="updateQuantity('{{ $item['id'] }}', 1)" 
                                                        class="w-8 h-8 flex items-center justify-center font-bold text-gray-700 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-600 rounded-lg shadow-sm transition-all transform active:scale-95">
                                                    +
                                                </button>
                                            </div>
                                            {{-- Inline Stock Error per item --}}
                                            @if(isset($stockErrors[$item['id']]))
                                                <p x-data x-init="setTimeout(() => $wire.clearStockError('{{ $item['id'] }}'), 3000)"
                                                   class="text-red-500 text-[10px] font-semibold flex items-center gap-0.5 max-w-[120px] text-right leading-tight">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                                    {{ $stockErrors[$item['id']] }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Upsell Recommendations --}}
                    @if($this->upsellRecommendations->count() > 0)
                        <div class="mt-8 transition-colors">
                            <h3 class="font-bold text-lg mb-4 flex items-center text-gray-900 dark:text-white transition-colors">
                                <span class="mr-2">💡</span> Sering Dipesan Bersama
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($this->upsellRecommendations as $upsell)
                                    <div class="bg-white dark:bg-gray-800/50 rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 p-4 flex flex-col items-center text-center transition-all">
                                        @if($upsell->image)
                                            <img src="{{ Storage::url($upsell->image) }}" class="w-20 h-20 object-cover rounded-full mb-3 shadow-md" alt="{{ $upsell->name }}">
                                        @else
                                            <div class="w-20 h-20 rounded-full bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center text-orange-200 dark:text-orange-700 mb-3 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                            </div>
                                        @endif
                                        <h4 class="font-bold text-sm text-gray-900 dark:text-white mb-1 line-clamp-1 w-full transition-colors">{{ $upsell->name }}</h4>
                                        @if($upsell->has_active_discount)
                                            <div class="flex items-center gap-1 mb-4">
                                                <span class="text-[9px] text-gray-400 dark:text-gray-500 line-through transition-colors">Rp{{ number_format($upsell->original_price, 0, ',', '.') }}</span>
                                                <span class="text-xs font-bold text-red-600 dark:text-red-400 transition-colors">{{ $upsell->formatted_price }}</span>
                                            </div>
                                        @else
                                            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 mb-4 transition-colors">{{ $upsell->formatted_price }}</p>
                                        @endif
                                        
                                        <button wire:click="addUpsellItem({{ $upsell->id }})" class="mt-auto w-full bg-primary-600 hover:bg-primary-500 text-white text-xs font-bold py-2.5 rounded-xl shadow-lg shadow-primary-500/10 transition-all transform active:scale-95">
                                            + ADD
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Right Column: Checkout Form --}}
                <div class="md:col-span-12 lg:col-span-5 space-y-6 lg:sticky lg:top-24">
                    <div class="bg-white dark:bg-gray-800/50 rounded-2xl shadow-sm p-5 border border-gray-100 dark:border-white/5 transition-all">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="font-bold text-xl dark:text-white transition-colors">Order Details</h3>
                        </div>

                        {{-- Voucher Code Section --}}
                        @if($restaurant->owner->hasFeature('Voucher & Marketing'))
                            <div class="mb-6 p-5 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-dashed border-gray-200 dark:border-white/10 transition-colors">
                                <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-3 transition-colors">Punya Kode Voucher?</label>
                                
                                @if($appliedVoucher)
                                    <div class="flex items-center justify-between bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-500/30 p-4 rounded-xl transition-colors">
                                        <div class="flex items-center">
                                            <span class="text-emerald-600 dark:text-emerald-400 mr-3 text-xl">🎟️</span>
                                            <div>
                                                <p class="text-xs font-black text-emerald-800 dark:text-emerald-400 tracking-wider uppercase">{{ $appliedVoucher->code }}</p>
                                                <p class="text-[10px] text-emerald-600 dark:text-emerald-500 font-bold uppercase transition-colors">Diskon Berhasil Dipasang!</p>
                                            </div>
                                        </div>
                                        <button wire:click="removeVoucher" class="text-[10px] font-black text-red-600 dark:text-red-400 hover:bg-white dark:hover:bg-gray-800 px-3 py-1.5 rounded-lg border border-red-100 dark:border-red-900/30 shadow-sm transition-all uppercase">
                                            Hapus
                                        </button>
                                    </div>
                                @else
                                    <div class="flex gap-2">
                                        <input type="text" 
                                            wire:model="voucherCode" 
                                            placeholder="KODE PROMO"
                                            class="flex-1 px-4 py-3 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-primary-500 dark:text-white uppercase font-black text-sm placeholder:text-gray-300 dark:placeholder:text-gray-600 transition-all"
                                        >
                                        <button wire:click="applyVoucher" class="bg-primary-600 hover:bg-primary-500 text-white px-6 py-3 rounded-xl font-bold text-xs shadow-lg shadow-primary-500/10 transition-all">
                                            PAKAI
                                        </button>
                                    </div>
                                    @if($voucherError)
                                        <p class="text-red-500 text-[10px] mt-2 font-bold uppercase tracking-wider ml-1">{{ $voucherError }}</p>
                                    @endif
                                @endif
                            </div>
                        @endif

                        {{-- Gift Card Section --}}
                        @if($restaurant->owner->hasFeature('Gift Cards'))
                            <div class="mb-6 p-5 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-dashed border-gray-200 dark:border-white/10 transition-colors">
                                <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-3 transition-colors">Punya Gift Card?</label>
                                
                                @if($appliedGiftCard)
                                    <div class="flex items-center justify-between bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-500/30 p-4 rounded-xl transition-colors">
                                        <div class="flex items-center">
                                            <span class="text-blue-600 dark:text-blue-400 mr-3 text-xl">🎁</span>
                                            <div>
                                                <p class="text-xs font-black text-blue-800 dark:text-blue-400 tracking-wider uppercase">{{ $appliedGiftCard->code }}</p>
                                                <p class="text-[10px] text-blue-600 dark:text-blue-500 font-bold uppercase">Saldo dipakai: Rp {{ number_format($giftCardDiscount, 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                        <button wire:click="removeGiftCard" class="text-[10px] font-black text-red-600 dark:text-red-400 hover:bg-white dark:hover:bg-gray-800 px-3 py-1.5 rounded-lg border border-red-100 dark:border-red-900/30 shadow-sm transition-all uppercase">
                                            Hapus
                                        </button>
                                    </div>
                                @else
                                    <div class="flex gap-2">
                                        <input type="text" 
                                            wire:model="giftCardCode" 
                                            placeholder="KODE GIFT CARD"
                                            class="flex-1 px-4 py-3 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-primary-500 dark:text-white uppercase font-black text-sm placeholder:text-gray-300 dark:placeholder:text-gray-600 transition-all"
                                        >
                                        <button wire:click="applyGiftCard" class="bg-primary-600 hover:bg-primary-500 text-white px-6 py-3 rounded-xl font-bold text-xs shadow-lg shadow-primary-500/10 transition-all">
                                            PAKAI
                                        </button>
                                    </div>
                                    @if($giftCardError)
                                        <p class="text-red-500 text-[10px] mt-2 font-bold uppercase tracking-wider ml-1">{{ $giftCardError }}</p>
                                    @endif
                                @endif
                            </div>
                        @endif
                        
                        <div class="space-y-5">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-2 transition-colors">
                                    Your Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                    wire:model="customerName" 
                                    class="w-full px-4 py-3.5 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-primary-500 dark:text-white placeholder:text-gray-300 dark:placeholder:text-gray-600 transition-all font-medium" 
                                    placeholder="John Doe">
                                @error('customerName') 
                                    <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider mt-1.5 block ml-1">{{ $message }}</span> 
                                @enderror
                            </div>

                            @if($table)
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-2 transition-colors">Table</label>
                                    <div class="w-full px-4 py-3.5 rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-white/5 text-gray-700 dark:text-gray-200 font-bold transition-colors">
                                        {{ $table->name }} ({{ $table->area }})
                                    </div>
                                </div>
                            @endif

                            <div>
                                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-2 transition-colors">
                                    WhatsApp <span class="text-gray-400 font-normal">(Optional)</span>
                                </label>
                                <input type="tel" 
                                    wire:model.live="customerPhone" 
                                    class="w-full px-4 py-3.5 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-primary-500 dark:text-white placeholder:text-gray-300 dark:placeholder:text-gray-600 transition-all font-medium" 
                                    placeholder="0812...">
                                
                                {{-- Member Recognition Badge --}}
                                @if($isMember && $member)
                                    <div class="mt-3 p-4 bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 border border-emerald-100 dark:border-emerald-500/30 rounded-2xl flex items-center justify-between transition-all">
                                         <div class="flex items-center">
                                             <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400 mr-4 shadow-sm">
                                                 <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                                     <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                 </svg>
                                             </div>
                                             <div>
                                                 <p class="text-[10px] font-black text-emerald-800 dark:text-emerald-400 uppercase tracking-[0.1em]">Member Terdeteksi!</p>
                                                 <p class="text-sm font-black text-emerald-700 dark:text-emerald-300 capitalize transition-colors">{{ $member->tier }} • {{ $member->points_balance }} Poin</p>
                                             </div>
                                         </div>
                                         <div class="text-[10px] font-black px-2.5 py-1.5 bg-emerald-200 dark:bg-emerald-500/30 text-emerald-800 dark:text-emerald-300 rounded-lg uppercase tracking-wider">
                                             {{ $member->tier }}
                                         </div>
                                    </div>
                                @endif

                                {{-- Point Redemption Section --}}
                                @if($isMember && $member && $restaurant->loyalty_redemption_enabled && $member->points_balance > 0)
                                    <div class="mt-4 p-5 bg-orange-50 dark:bg-orange-900/10 border-2 border-orange-100 dark:border-orange-500/20 rounded-2xl shadow-sm overflow-hidden relative group transition-all hover:bg-orange-100/30 dark:hover:bg-orange-900/20">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center gap-3">
                                                <div class="p-2.5 bg-orange-500 rounded-xl shadow-lg shadow-orange-500/20">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M5 5a3 3 0 015-2.236A3 3 0 0114.83 6H16a2 2 0 110 4h-5V9a1 1 0 10-2 0v1H4a2 2 0 110-4h1.17C5.06 5.687 5 5.35 5 5zm4 1V5a1 1 0 10-1.223.979L9 6.223zM11 5v1.223l1.223-.244A1 1 0 1011 5zM4 12a1 1 0 011-1h5.25c.15 0 .3.05.4.15l2.5 2.5a.5.5 0 010 .7l-2.5 2.5a.5.5 0 01-.4.15H5a1 1 0 01-1-1v-4z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <span class="block text-[11px] font-black text-orange-900 dark:text-orange-400 uppercase tracking-tight transition-colors">Tukar Poin Jadi Diskon?</span>
                                                    <span class="block text-[9px] text-orange-700 dark:text-orange-500 font-bold uppercase tracking-[0.1em] transition-colors">1 Poin = Rp{{ number_format($restaurant->loyalty_point_redemption_value, 0, ',', '.') }}</span>
                                                </div>
                                            </div>
                                            <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                                                <input type="checkbox" wire:model.live="usePoints" id="toggle-points" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white dark:bg-gray-400 border-4 appearance-none cursor-pointer outline-none transition-transform duration-200 ease-in translate-x-0 checked:translate-x-4 checked:bg-orange-500 border-orange-200 dark:border-orange-900/50 checked:border-orange-500"/>
                                                <label for="toggle-points" class="toggle-label block overflow-hidden h-6 rounded-full bg-orange-200 dark:bg-orange-900/30 cursor-pointer transition-colors"></label>
                                            </div>
                                        </div>
                                        
                                        @if($usePoints)
                                            <div class="mt-4 pt-4 border-t border-orange-200/50 dark:border-orange-500/20 animate-fade-in">
                                                <div class="flex items-center bg-white/60 dark:bg-black/20 border-2 border-orange-100 dark:border-orange-500/30 rounded-xl p-1 focus-within:border-orange-500 transition-all">
                                                    <span class="bg-orange-100 dark:bg-orange-900/40 px-3 py-2 rounded-lg text-orange-700 dark:text-orange-400 font-bold text-[10px] uppercase tracking-widest leading-none">Poin Digunakan</span>
                                                    <input type="number" wire:model.live.debounce.500ms="pointsToUse" class="w-full bg-transparent text-right font-black text-lg outline-none px-2 text-orange-900 dark:text-white transition-colors" placeholder="0">
                                                </div>
                                                @if($this->pointDiscount > 0)
                                                    <p class="text-[10px] text-orange-600 dark:text-orange-400 mt-3 font-black italic text-right uppercase tracking-[0.1em] bg-orange-100/50 dark:bg-orange-900/30 py-2 px-4 rounded-xl border border-orange-200/30 transition-all">Potongan: - Rp{{ number_format($this->pointDiscount, 0, ',', '.') }}</p>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                @error('customerPhone') 
                                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> 
                                @enderror

                                {{-- Registration Checkbox --}}
                                @if(!$isMember && strlen($customerPhone) >= 10)
                                    <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-900/30 rounded-2xl border-2 border-dashed border-gray-200 dark:border-white/10 animate-pulse-once transition-all">
                                        <label class="flex items-center cursor-pointer group">
                                            <div class="relative flex items-center">
                                                <input type="checkbox" wire:model="wantsToRegister" class="w-5 h-5 text-primary-600 dark:text-primary-500 border-2 border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-800 focus:ring-0 transition-all cursor-pointer">
                                            </div>
                                            <div class="ml-3">
                                                <span class="block text-sm font-black text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">Gabung Member & Kumpulkan Poin? ✨</span>
                                                <span class="block text-[10px] text-gray-400 dark:text-gray-500 font-bold uppercase tracking-wider transition-colors">Dapatkan promo eksklusif untuk kunjungan Anda berikutnya</span>
                                            </div>
                                        </label>
                                    </div>
                                @endif
                            </div>

                            {{-- Email Field (Auto-filled for Members) --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-2 flex items-center justify-between transition-colors">
                                    <span>Your Email <span class="text-gray-400 font-normal ml-1">(@if($isMember && $member?->email) Terisi Otomatis @else Opsional @endif)</span></span>
                                    @if($isMember && $member?->email)
                                        <span class="text-[10px] bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 px-2 py-0.5 rounded-lg font-black uppercase tracking-tighter transition-colors">Profil Member</span>
                                    @endif
                                </label>
                                <input type="email" 
                                    wire:model.live="customerEmail" 
                                    class="w-full px-4 py-3.5 rounded-xl border {{ $isMember && $member?->email ? 'border-emerald-300 dark:border-emerald-500/30 bg-emerald-50/20 dark:bg-emerald-900/10' : 'border-gray-200 dark:border-white/10 bg-white dark:bg-gray-800' }} focus:ring-2 focus:ring-primary-500 dark:text-white placeholder:text-gray-300 dark:placeholder:text-gray-600 transition-all font-medium" 
                                    placeholder="john@example.com">
                                @error('customerEmail') 
                                    <span class="text-red-500 text-[10px] font-bold uppercase tracking-wider mt-1.5 block ml-1">{{ $message }}</span> 
                                @enderror
                            </div>


                            <div>
                                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-2 transition-colors">
                                    Notes <span class="text-gray-400 font-normal">(Optional)</span>
                                </label>
                                <textarea wire:model="notes" 
                                        rows="3" 
                                        class="w-full px-4 py-3.5 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-primary-500 dark:text-white placeholder:text-gray-300 dark:placeholder:text-gray-600 transition-all font-medium" 
                                        placeholder="Additional request..."></textarea>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Payment Method Selection --}}
                    <div class="bg-white dark:bg-gray-800/50 rounded-2xl shadow-sm p-5 border border-gray-100 dark:border-white/5 transition-all">
                        <h3 class="font-bold text-xl mb-6 dark:text-white transition-colors">Payment Method</h3>
                        <div class="space-y-4">
                            @if(in_array($restaurant->payment_mode ?? 'kasir', ['kasir', 'both']))
                            <label class="flex items-center justify-between p-4 border rounded-2xl cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800/80 transition-all {{ $paymentMethod == 'cash' ? 'border-primary-600 bg-primary-50 dark:bg-primary-900/10 ring-1 ring-primary-600' : 'border-gray-200 dark:border-white/5' }}">
                                <div class="flex items-center">
                                    <input type="radio" value="cash" wire:model.live="paymentMethod" class="text-primary-600 focus:ring-primary-600 h-5 w-5 bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600">
                                    <div class="ml-4">
                                        <span class="block font-bold text-gray-900 dark:text-white transition-colors">Bayar di Kasir / WA</span>
                                        <span class="block text-xs text-gray-500 dark:text-gray-400 transition-colors">Pesan dulu, bayar nanti</span>
                                    </div>
                                </div>
                                <div class="text-2xl filter dark:brightness-125">💵</div>
                            </label>
                            @endif

                            @if(in_array($restaurant->payment_mode ?? 'kasir', ['gateway', 'both']))
                            <label class="flex items-center justify-between p-4 border rounded-2xl cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800/80 transition-all {{ $paymentMethod == 'midtrans' ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/10 ring-1 ring-blue-600' : 'border-gray-200 dark:border-white/5' }}">
                                <div class="flex items-center">
                                    <input type="radio" value="midtrans" wire:model.live="paymentMethod" class="text-blue-600 dark:text-blue-500 focus:ring-blue-500 h-5 w-5 bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600">
                                    <div class="ml-4">
                                        <span class="block font-bold text-gray-900 dark:text-white transition-colors">Bayar Online (QRIS)</span>
                                        <span class="block text-xs text-gray-500 dark:text-gray-400 transition-colors">QRIS, GoPay, ShopeePay</span>
                                    </div>
                                </div>
                                <div class="text-2xl filter dark:brightness-125">💳</div>
                            </label>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bottom Action --}}
        <div class="fixed bottom-0 left-0 right-0 bg-white/80 dark:bg-[#0B0F19]/80 backdrop-blur-xl border-t dark:border-white/5 rounded-t-[2.5rem] shadow-[0_-10px_60px_-15px_rgba(0,0,0,0.15)] z-40 pb-safe transition-all duration-300">
            <div class="max-w-2xl mx-auto p-6 md:px-8">
                @if($this->tax > 0 || $this->additionalFees > 0)
                    <div class="flex justify-between items-center mb-1 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest transition-colors">
                        <span>Subtotal</span>
                        <span class="dark:text-gray-400">Rp {{ number_format($this->subtotal, 0, ',', '.') }}</span>
                    </div>
                @endif
 
                @if($restaurant->additional_fees)
                    @foreach($restaurant->additional_fees as $fee)
                        @php
                            $feeAmount = ($fee['type'] ?? '') === 'fixed' ? $fee['value'] : ($this->subtotal * ($fee['value'] / 100));
                        @endphp
                        @if($feeAmount > 0)
                            <div class="flex justify-between items-center mb-1 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest transition-colors">
                                <span>{{ $fee['name'] }}</span>
                                <span class="dark:text-gray-400">Rp {{ number_format($feeAmount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    @endforeach
                @endif
 
                @if($this->tax > 0)
                    <div class="flex justify-between items-center mb-1 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest transition-colors">
                        <span>Pajak ({{ $restaurant->tax_percentage }}%)</span>
                        <span class="dark:text-gray-400">Rp {{ number_format($this->tax, 0, ',', '.') }}</span>
                    </div>
                @endif
 
                @if($this->voucherDiscount > 0)
                    <div class="flex justify-between items-center mb-2 text-xs font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest transition-colors">
                        <span>Voucher Diskon</span>
                        <span>- Rp {{ number_format($this->voucherDiscount, 0, ',', '.') }}</span>
                    </div>
                @endif
                
                @if($this->giftCardDiscount > 0)
                    <div class="flex justify-between items-center mb-2 text-xs font-black text-blue-600 dark:text-blue-400 uppercase tracking-widest transition-colors">
                        <span>Gift Card Dipakai</span>
                        <span>- Rp {{ number_format($this->giftCardDiscount, 0, ',', '.') }}</span>
                    </div>
                @endif
 
                <div class="flex justify-between items-center mb-5 transition-colors">
                    <span class="text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest text-[10px]">Total Pembayaran</span>
                    <span class="text-3xl font-black text-gray-900 dark:text-white transition-colors">Rp {{ number_format($this->total, 0, ',', '.') }}</span>
                </div>
                
                <button wire:click="checkout" 
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-50 cursor-not-allowed"
                    class="w-full h-16 {{ $paymentMethod == 'midtrans' ? 'bg-blue-600 hover:bg-blue-500 shadow-blue-500/20' : 'bg-primary-600 hover:bg-primary-500 shadow-primary-500/20' }} text-white rounded-2xl font-black text-lg shadow-2xl transition-all flex items-center justify-center space-x-2 relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                    
                    {{-- Default State --}}
                    <span wire:loading.remove wire:target="checkout" class="flex items-center space-x-3">
                        @if($paymentMethod == 'cash')
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                        @elseif($paymentMethod == 'midtrans')
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        @endif
                        <span class="tracking-widest uppercase text-sm">{{ $paymentMethod == 'midtrans' ? 'Bayar Sekarang' : 'Pesan via WhatsApp' }}</span>
                    </span>
 
                    {{-- Loading State --}}
                    <span wire:loading wire:target="checkout" class="flex items-center space-x-3">
                        <svg class="animate-spin h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="tracking-widest uppercase text-sm">Processing...</span>
                    </span>
                </button>
            </div>
        </div>
    @endif

    {{-- Quick Config Overlay for Upsells --}}
    @if($quickConfigItem)
        <div class="fixed inset-0 z-[70] flex items-end justify-center sm:items-center sm:p-4">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="cancelQuickConfig"></div>

            {{-- Modal Panel --}}
            <div class="relative w-full max-w-lg bg-white rounded-t-3xl sm:rounded-2xl shadow-2xl p-6 transform transition-all overflow-hidden max-h-[90vh] flex flex-col">
                <div class="flex items-center justify-between mb-4 flex-shrink-0">
                    <h4 class="font-bold text-lg">Pilih Opsi: {{ $quickConfigItem->name }}</h4>
                    <button wire:click="cancelQuickConfig" class="p-1 rounded-full hover:bg-gray-100 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="overflow-y-auto hide-scrollbar pb-4 flex-1">
                    {{-- Mini Variants --}}
                    @if($quickConfigItem->variants->count() > 0)
                        <div class="mb-4">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Varian</p>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach($quickConfigItem->variants as $v)
                                    <button 
                                        wire:click="$set('quickConfigVariant', {{ $v->id }})"
                                        class="p-2 text-xs font-bold rounded-lg border text-center transition-all {{ $quickConfigVariant == $v->id ? 'bg-black text-white border-black' : 'bg-gray-50 text-gray-600 border-gray-100' }}"
                                    >
                                        {{ $v->name }} (+{{ number_format($v->price, 0, ',', '.') }})
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Mini Addons --}}
                    @if($quickConfigItem->addons->count() > 0)
                        <div class="mb-4">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Tambahan</p>
                            <div class="space-y-2">
                                @foreach($quickConfigItem->addons as $a)
                                    <label class="flex items-center justify-between p-3 rounded-xl border border-gray-200 cursor-pointer hover:bg-gray-50 {{ in_array($a->id, $quickConfigAddons) ? 'border-black bg-gray-50 ring-1 ring-black' : 'border-gray-200' }}">
                                        <div class="flex items-center">
                                            <input type="checkbox" value="{{ $a->id }}" wire:model.live="quickConfigAddons" class="h-4 w-4 text-black focus:ring-black rounded border-gray-300">
                                            <span class="ml-3 text-sm font-medium text-gray-900">{{ $a->name }}</span>
                                        </div>
                                        <span class="text-sm font-bold text-gray-700">+ Rp {{ number_format($a->price, 0, ',', '.') }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Mini Quantity --}}
                    <div class="flex items-center justify-between mt-4 bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <span class="text-sm font-bold text-gray-900">Jumlah</span>
                        <div class="flex items-center space-x-4">
                            <button wire:click="decrementQuantity('quickConfigQuantity')" class="w-8 h-8 rounded-full bg-white shadow-sm flex items-center justify-center font-bold pb-1 text-xl text-gray-700 border border-gray-200" {{ $quickConfigQuantity <= 1 ? 'disabled' : '' }}>-</button>
                            <span class="font-bold text-lg w-4 text-center">{{ $quickConfigQuantity }}</span>
                            <button wire:click="incrementQuantity('quickConfigQuantity')" class="w-8 h-8 rounded-full bg-white shadow-sm flex items-center justify-center font-bold pb-1 text-xl text-gray-700 border border-gray-200">+</button>
                        </div>
                    </div>
                    {{-- Inline Stock Error for Quick Config --}}
                    @if($quickConfigErrorMessage)
                        <p x-data x-init="setTimeout(() => $wire.clearStockError('quickConfig'), 3000)"
                           class="text-red-500 text-xs font-semibold mt-2 flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                            {{ $quickConfigErrorMessage }}
                        </p>
                    @endif

                    {{-- Quick Config Notes --}}
                    <div class="mt-4">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Catatan Khusus</p>
                        <textarea wire:model.defer="quickConfigNote" rows="2" 
                            class="w-full bg-gray-50 border border-gray-100 focus:border-black rounded-xl p-3 text-sm font-medium transition-all outline-none placeholder:text-gray-300"
                            placeholder="Contoh: Tanpa es, double shot, dll..."></textarea>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-100 flex-shrink-0">
                    <button wire:click="saveQuickConfig" class="w-full bg-black text-white py-4 rounded-xl font-bold flex items-center justify-center space-x-2 shadow-lg hover:bg-gray-800 transition active:scale-[0.98]">
                        <span>Tambahkan ke Keranjang</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>@script
<script>
    $wire.on('checkout-completed', (event) => {
        const url = event.url || event[0]?.url;
        if (url) {
            window.location.href = url;
        }
    });
</script>
@endscript
