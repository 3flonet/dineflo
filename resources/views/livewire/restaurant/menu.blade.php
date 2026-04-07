<div 
    x-data="{
        init() {
            if (typeof window.Echo !== 'undefined') {
                console.log('Echo initialized, listening for waiter responses for table: {{ $table->id ?? 'null' }}');
                window.Echo.channel('restaurant-public.{{ $restaurant->id }}')
                    .listen('.waiter.responded', (e) => {
                        console.log('Received waiter response:', e);
                        if (e.table_id == {{ $table->id ?? 0 }}) {
                            $wire.handleWaiterResponded(e);
                        }
                    });
            }
        }
    }"
    @hide-responded-status.window="setTimeout(() => $wire.hideRespondedStatus(), 5000)"
>
    {{-- Header --}}
    <div class="sticky top-0 z-50 bg-white dark:bg-[#0B0F19] shadow-sm dark:shadow-none border-b border-gray-100 dark:border-white/5 transition-colors duration-300">
        <div class="max-w-7xl mx-auto">
            {{-- Restaurant Info --}}
            <div class="px-4 py-3 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <a href="{{ route('frontend.restaurants.show', $restaurant->slug) }}" class="p-2 -ml-2 text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors" title="Kembali ke Profil">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    @if($restaurant->logo)
                        <a href="{{ route('frontend.restaurants.show', $restaurant->slug) }}" class="flex-shrink-0">
                            <div class="h-14 flex items-center justify-center rounded-xl bg-white dark:bg-gray-800 overflow-hidden px-1.5 ring-1 ring-gray-100 dark:ring-white/10" style="max-width: 8rem; min-width: 2.5rem;">
                                <img src="{{ Storage::url($restaurant->logo) }}" alt="{{ $restaurant->name }}" class="h-12 w-auto max-w-full object-contain">
                            </div>
                        </a>
                    @else
                        <a href="{{ route('frontend.restaurants.show', $restaurant->slug) }}">
                            <h1 class="font-bold text-lg leading-tight text-gray-900 dark:text-white transition-colors hover:text-primary-600 dark:hover:text-primary-400">{{ $restaurant->name }}</h1>
                        </a>
                    @endif
                    @if($table)
                        <span class="text-xs text-green-600 dark:text-green-400 font-medium bg-green-50 dark:bg-green-900/20 px-2 py-0.5 rounded-full border border-green-100 dark:border-green-900/30">
                            {{ $table->name }}
                        </span>
                    @endif
                </div>
                {{-- Action Buttons --}}
                <div class="flex items-center space-x-1">
                    {{-- Status Badge Container --}}
                    <div class="flex items-center h-9">
                        @if($hasPendingCall)
                            <div 
                                x-transition:enter="transition transition-all cubic-bezier(0.34, 1.56, 0.64, 1) duration-700"
                                x-transition:enter-start="opacity-0 translate-x-8 scale-90"
                                x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                                x-transition:leave="transition ease-in duration-300"
                                x-transition:leave-start="opacity-100 translate-x-0"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="flex items-center gap-1.5 bg-yellow-50 text-yellow-700 px-3 py-1.5 rounded-full border border-yellow-200 shadow-sm whitespace-nowrap"
                            >
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-yellow-500"></span>
                                </span>
                                <span class="text-[10px] font-extrabold uppercase tracking-widest">Waiting Staff</span>
                            </div>
                        @elseif($showRespondedStatus)
                            <div 
                                x-transition:enter="transition transition-all cubic-bezier(0.34, 1.56, 0.64, 1) duration-700"
                                x-transition:enter-start="opacity-0 translate-x-8 scale-90"
                                x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                                x-transition:leave="transition ease-in-out duration-500"
                                x-transition:leave-start="opacity-100 translate-x-0"
                                x-transition:leave-end="opacity-0 -translate-x-8"
                                class="bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-3 py-1.5 rounded-full shadow-lg flex items-center gap-1.5 border border-gray-800 dark:border-white/10 whitespace-nowrap"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-[10px] font-extrabold uppercase tracking-widest">Waiter Coming</span>
                            </div>
                        @endif
                    </div>

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

                    {{-- Call Waiter Button --}}
                    @if($table && $restaurant->owner?->hasFeature('Waiter Call System'))
                        <div class="relative">
                            <button wire:click="callWaiter" 
                                wire:loading.attr="disabled"
                                class="p-2 {{ $hasPendingCall ? 'text-yellow-600 bg-yellow-50 dark:bg-yellow-500/10 shadow-inner' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/10' }} rounded-lg transition-all duration-300 relative" 
                                title="Call Waiter"
                            >
                                <svg wire:loading.remove wire:target="callWaiter" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 {{ $hasPendingCall ? 'animate-bounce' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                {{-- Spinner when loading --}}
                                <svg wire:loading wire:target="callWaiter" class="animate-spin h-6 w-6 text-gray-600 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </button>
                            @if($hasPendingCall)
                                <span class="absolute -top-1 -right-1 flex h-3 w-3">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-yellow-500"></span>
                                </span>
                            @endif
                        </div>
                    @endif
                    
                    {{-- Cart Button --}}
                    @if($table || $restaurant->is_online_order_enabled)
                    <a href="{{ route('restaurant.cart', $restaurant->slug) }}" class="relative p-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/10 rounded-lg transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        @if($this->cartCount > 0)
                            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/4 -translate-y-1/4 bg-red-600 rounded-full">
                                {{ $this->cartCount }}
                            </span>
                        @endif
                    </a>
                    @endif

                    {{-- Share Menu Button --}}
                    <button
                        onclick="shareMenu()"
                        class="p-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/10 rounded-lg transition-colors"
                        title="Bagikan Menu"
                        id="menu-share-btn"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                        </svg>
                    </button>

                    {{-- Copied tooltip --}}
                    <div id="menu-copied-toast" class="hidden fixed top-16 left-1/2 -translate-x-1/2 z-[100] bg-gray-900 text-white text-xs font-bold px-4 py-2 rounded-full shadow-lg">
                        🔗 Link disalin!
                    </div>

                    <script>
                        function shareMenu() {
                            const shareData = {
                                title: '{{ addslashes($restaurant->name) }} — Menu Digital',
                                text: 'Cek menu digital {{ addslashes($restaurant->name) }} di sini 🍽️',
                                url: '{{ route('frontend.restaurants.show', $restaurant->slug) }}'
                            };
                            if (navigator.share && navigator.canShare && navigator.canShare(shareData)) {
                                navigator.share(shareData).catch(() => {});
                            } else {
                                navigator.clipboard.writeText(shareData.url).then(() => {
                                    const toast = document.getElementById('menu-copied-toast');
                                    toast.classList.remove('hidden');
                                    setTimeout(() => toast.classList.add('hidden'), 2500);
                                });
                            }
                        }
                    </script>
                </div>
            </div>

            {{-- Categories --}}
            <div class="overflow-x-auto whitespace-nowrap px-4 py-4 flex space-x-3 hide-scrollbar bg-white dark:bg-[#0B0F19] items-stretch transition-colors duration-300" wire:loading.class="opacity-50 pointer-events-none transition-opacity">
                <button wire:click="selectCategory(null)" 
                    class="flex-shrink-0 flex flex-col items-center justify-center px-6 py-2 rounded-xl border-2 transition-all {{ is_null($activeCategory) ? 'border-primary-600 bg-primary-600 dark:bg-primary-500 text-white shadow-lg shadow-primary-500/20' : 'border-gray-100 dark:border-white/5 bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:border-primary-500' }} min-w-[5rem]">
                    <span class="text-sm font-bold">Semua</span>
                </button>
                @foreach($categories as $category)
                    <button wire:click="selectCategory({{ $category->id }})"
                        class="group flex-shrink-0 flex items-center p-2 rounded-xl border-2 transition-all {{ $activeCategory === $category->id ? 'border-primary-600 bg-white dark:bg-gray-800 shadow-lg shadow-primary-500/10' : 'border-gray-100 dark:border-white/5 bg-gray-50 dark:bg-gray-800 hover:border-primary-500 hover:shadow-md' }} min-w-[11rem] text-left gap-3">
                        
                        <div class="w-12 h-12 rounded-lg {{ $category->image ? 'bg-gray-100 dark:bg-gray-700' : 'bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800' }} overflow-hidden flex-shrink-0 flex items-center justify-center border border-black/5 dark:border-white/10 relative group-hover:scale-105 transition-transform duration-300">
                            @if($category->image)
                                <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}" class="absolute inset-0 w-full h-full object-cover">
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                            @endif
                        </div>

                        <div class="flex flex-col overflow-hidden max-w-[150px] py-1 pr-3">
                            <span class="text-[13px] font-extrabold truncate tracking-tight {{ $activeCategory === $category->id ? 'text-primary-600 dark:text-white' : 'text-gray-900 dark:text-gray-200 group-hover:text-primary-600 transition-colors' }}">{{ $category->name }}</span>
                            @if($category->description)
                                <span class="text-[10px] text-gray-500 dark:text-gray-500 truncate block mt-0.5 font-medium">{{ $category->description }}</span>
                            @endif
                        </div>
                    </button>
                @endforeach
            </div>
            
            {{-- Loading Indicator Spinner for Category Filter --}}
            <div wire:loading wire:target="selectCategory" class="absolute inset-0 bg-white/50 dark:bg-[#0B0F19]/50 backdrop-blur-sm z-10 flex items-center justify-center transition-colors">
                <svg class="animate-spin h-6 w-6 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>

            {{-- Reservation CTA --}}
            @if($restaurant->owner?->hasFeature('Table Reservation'))
                <div class="px-4 py-3 bg-white dark:bg-[#0B0F19] border-t border-gray-100 dark:border-white/5 transition-colors">
                    <a href="{{ route('restaurant.reserve', $restaurant->slug) }}" class="group flex items-center justify-between bg-primary-600 dark:bg-white/10 text-white px-5 py-3 rounded-2xl shadow-[0_8px_20px_rgb(0,0,0,0.08)] hover:bg-primary-500 dark:hover:bg-white/20 transition-all active:scale-[0.98]">
                        <div class="flex items-center space-x-3">
                            <div class="bg-white/10 p-2 rounded-xl text-white backdrop-blur-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="flex flex-col text-left">
                                <span class="text-[13px] font-black tracking-wide uppercase text-white">Reservasi Meja</span>
                                <span class="text-[11px] font-medium text-primary-100/70 dark:text-gray-400">Amankan kursi incaran Anda</span>
                            </div>
                        </div>
                        <div class="bg-white/10 dark:bg-white/20 text-white text-[11px] font-bold px-3 py-2 rounded-xl group-hover:bg-white/20 transition-colors flex items-center gap-1 shadow-sm">
                            Booking
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Content --}}
    <div class="p-4 pb-24 max-w-7xl mx-auto">
        {{-- Search --}}
        <div class="mb-6">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search menu..." 
                class="w-full bg-gray-100 dark:bg-gray-800 border-none rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-primary-500 dark:text-white dark:placeholder-gray-500 transition-all">
        </div>

        {{-- Menu Items --}}
        {{-- SKELETON LOADING (Ditampilkan saat data masih ditarik oleh Livewire target init/search/category) --}}
        <div wire:loading wire:target="search, selectCategory, mount" class="w-full">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @for ($i = 0; $i < 6; $i++)
                    <div class="flex bg-white dark:bg-gray-800/50 rounded-xl p-3 shadow-sm border border-gray-100 dark:border-white/5 animate-pulse">
                        <div class="flex-1 pr-4">
                            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded-full w-16 mb-2"></div>
                            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4 mb-2"></div>
                            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-full mb-1"></div>
                            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-5/6 mb-3"></div>
                            <div class="flex items-center justify-between mt-auto pt-2">
                                <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/3"></div>
                                <div class="h-7 w-16 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
                            </div>
                        </div>
                        <div class="w-24 h-24 bg-gray-200 dark:bg-gray-700 rounded-xl flex-shrink-0"></div>
                    </div>
                @endfor
            </div>
        </div>

        {{-- ACTUAL CONTENT --}}
        <div wire:loading.remove wire:target="search, selectCategory, mount" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($menuItems as $item)
                @php $isOutOfStock = $item->manage_stock && $item->stock_quantity <= 0; @endphp
                <div class="flex bg-white dark:bg-gray-800/80 rounded-xl p-3 shadow-sm border border-gray-100 dark:border-white/5 transition-all {{ $isOutOfStock ? 'opacity-60' : '' }}">
                    <div class="flex-1 pr-4">
                        <div class="flex flex-wrap gap-1 mb-1">
                            @if($item->allergens)
                                @foreach($item->allergens as $allergen)
                                    <span class="text-[10px] uppercase font-bold text-gray-400 dark:text-gray-500 border border-gray-200 dark:border-white/10 px-1 rounded">{{ $allergen }}</span>
                                @endforeach
                            @endif
                        </div>
                        <h3 class="font-bold text-gray-900 dark:text-white mb-1 transition-colors">{{ $item->name }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 mb-2 transition-colors">{{ $item->description }}</p>
                        <div class="flex items-center justify-between mt-auto">
                            @if($item->has_active_discount)
                                <div class="flex flex-col items-end leading-tight">
                                    <span class="text-[10px] text-gray-400 dark:text-gray-500 line-through">Rp {{ number_format($item->original_price, 0, ',', '.') }}</span>
                                    <span class="font-bold text-red-600 dark:text-red-400">{{ $item->formatted_price }}</span>
                                </div>
                            @else
                                <span class="font-bold text-gray-900 dark:text-white transition-colors">{{ $item->formatted_price }}</span>
                            @endif
                            @if($isOutOfStock)
                                <span class="bg-red-100 dark:bg-red-900/20 text-red-500 dark:text-red-400 px-4 py-1.5 rounded-lg text-xs font-bold cursor-not-allowed border border-red-200 dark:border-red-900/30 transition-colors">
                                    Habis
                                </span>
                            @else
                                @if($table || $restaurant->is_online_order_enabled)
                                    <button wire:click="openItemModal({{ $item->id }})" class="bg-primary-600 hover:bg-primary-500 text-white dark:bg-primary-500 dark:hover:bg-primary-400 px-4 py-1.5 rounded-lg text-xs font-bold shadow-lg shadow-primary-500/10 transition transform active:scale-95">
                                        ADD
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                    @if($item->image)
                        <div class="w-24 h-24 flex-shrink-0 relative">
                            <img src="{{ Storage::url($item->image) }}" loading="lazy" class="w-full h-full object-cover rounded-xl" alt="{{ $item->name }}">
                            @if($isOutOfStock)
                                <div class="absolute inset-0 bg-black/40 backdrop-blur-[1px] rounded-xl flex items-center justify-center">
                                    <span class="bg-red-500 text-white font-black px-2 py-0.5 text-[10px] rounded-md transform -rotate-12 shadow-lg border border-white/50">HABIS</span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        @if($menuItems->isEmpty())
            <div class="text-center py-10 text-gray-400">
                <p>No items found.</p>
            </div>
        @endif
    </div>

    {{-- Item Modal --}}
    @if($selectedItem)
        <div class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500/75 dark:bg-black/75 transition-opacity" aria-hidden="true" wire:click="closeItemModal"></div>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-t-2xl sm:rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full fixed bottom-0 left-0 right-0 sm:relative border-t dark:border-white/5 sm:border-none">
                    
                    @if($selectedItem->image)
                        <div class="relative h-48 sm:h-64">
                             <img src="{{ Storage::url($selectedItem->image) }}" class="w-full h-full object-cover">
                        </div>
                    @endif

                    <div class="p-6 transition-colors">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 transition-colors">{{ $selectedItem->name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 transition-colors">{{ $selectedItem->description }}</p>

                        {{-- Variants --}}
                        @if($selectedItem->variants->count() > 0)
                            <div class="mb-6">
                                <h4 class="font-bold text-sm mb-3 dark:text-white transition-colors">Choose Size</h4>
                                <div class="space-y-2">
                                    @foreach($selectedItem->variants as $variant)
                                        <label class="flex items-center justify-between p-3 border rounded-lg cursor-pointer {{ $selectedVariant == $variant->id ? 'border-primary-600 bg-primary-50 dark:bg-primary-900/10 ring-1 ring-primary-600' : 'border-gray-200 dark:border-white/10 dark:bg-gray-700/50' }}">
                                            <div class="flex items-center">
                                                <input type="radio" value="{{ $variant->id }}" wire:model.live="selectedVariant" class="h-4 w-4 text-primary-600 focus:ring-primary-600 border-gray-300 dark:border-gray-600 dark:bg-gray-800">
                                                <span class="ml-3 text-sm font-medium dark:text-gray-200 transition-colors">{{ $variant->name }}</span>
                                            </div>
                                            <span class="text-sm font-bold dark:text-white transition-colors">Rp {{ number_format($variant->price, 0, ',', '.') }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @else
                             <div class="mb-6 flex justify-between items-center">
                                <h4 class="font-bold text-sm">Price</h4>
                                @if($selectedItem->has_active_discount)
                                    <div class="flex flex-col items-end leading-tight">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-xs text-gray-400 line-through">Rp {{ number_format($selectedItem->original_price, 0, ',', '.') }}</span>
                                            @if($selectedItem->getActiveDiscount())
                                                <span class="text-[10px] bg-red-100 text-red-600 px-2 py-0.5 rounded-full font-bold uppercase tracking-wider">{{ $selectedItem->getActiveDiscount()->name }}</span>
                                            @endif
                                        </div>
                                        <span class="text-lg font-bold text-red-600">{{ $selectedItem->formatted_price }}</span>
                                    </div>
                                @else
                                    <span class="text-lg font-bold">{{ $selectedItem->formatted_price }}</span>
                                @endif
                            </div>
                        @endif

                        {{-- Addons --}}
                        @if($selectedItem->addons->count() > 0)
                            <div class="mb-6">
                                <h4 class="font-bold text-sm mb-3 dark:text-white transition-colors">Add-ons</h4>
                                <div class="space-y-2">
                                    @foreach($selectedItem->addons as $addon)
                                        <label class="flex items-center justify-between p-3 border rounded-lg cursor-pointer {{ in_array($addon->id, $selectedAddons) ? 'border-primary-600 bg-primary-50 dark:bg-primary-900/10 ring-1 ring-primary-600' : 'border-gray-200 dark:border-white/10 dark:bg-gray-700/50' }}">
                                            <div class="flex items-center">
                                                <input type="checkbox" value="{{ $addon->id }}" wire:model.live="selectedAddons" class="h-4 w-4 text-primary-600 focus:ring-primary-600 border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded">
                                                <span class="ml-3 text-sm font-medium dark:text-gray-200 transition-colors">{{ $addon->name }}</span>
                                            </div>
                                            <span class="text-sm dark:text-white transition-colors">+ Rp {{ number_format($addon->price, 0, ',', '.') }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        {{-- Smart Upselling (Recommendations) --}}
                        @if($restaurant->owner?->hasFeature('Smart Upselling') && $selectedItem->upsells->count() > 0)
                            <div class="mb-6 pt-2">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-bold text-sm dark:text-white transition-colors">Pasangan Terbaik ✨</h4>
                                    <span class="text-[10px] bg-amber-100 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 px-2 py-0.5 rounded-full font-bold uppercase tracking-wider">Recommended</span>
                                </div>
                                <div class="flex space-x-3 overflow-x-auto pb-2 -mx-2 px-2 hide-scrollbar">
                                    @foreach($selectedItem->upsells as $upsell)
                                        @php 
                                            $uItem = $upsell->upsellItem; 
                                            $isBundled = isset($bundledItems[$uItem->id]);
                                        @endphp
                                        @if($uItem && $uItem->is_available)
                                            <div class="flex-shrink-0 w-36 bg-gray-50 dark:bg-gray-700/50 rounded-xl p-2 border {{ $isBundled ? 'border-primary-600 bg-primary-50 dark:bg-primary-900/10 ring-1 ring-primary-600' : 'border-gray-100 dark:border-white/5' }} transition-all cursor-pointer group relative" 
                                                wire:click="{{ ($uItem->variants->count() > 0 || $uItem->addons->count() > 0) ? 'toggleUpsell('.$uItem->id.')' : 'toggleUpsell('.$uItem->id.')' }}"
                                            >
                                                <div class="relative h-24 mb-2 overflow-hidden rounded-lg">
                                                    @if($uItem->image)
                                                        <img src="{{ Storage::url($uItem->image) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                                    @else
                                                        <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                            </svg>
                                                        </div>
                                                    @endif
                                                    
                                                    @if($isBundled)
                                                        <div class="absolute top-1 right-1 bg-green-500 text-white p-1 rounded-full shadow-lg">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                            </svg>
                                                        </div>
                                                    @else
                                                        <div class="absolute bottom-1 right-1">
                                                            <div class="bg-white/90 p-1 rounded-md shadow-sm group-hover:bg-black group-hover:text-white transition-colors">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                                </svg>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                <h5 class="text-[11px] font-bold text-gray-900 dark:text-white line-clamp-1 mb-1 transition-colors">{{ $uItem->name }}</h5>
                                                @if($isBundled && isset($bundledItems[$uItem->id]['variant_name']))
                                                    <p class="text-[9px] text-primary-700 dark:text-primary-400 font-medium mb-1 line-clamp-1 italic">{{ $bundledItems[$uItem->id]['variant_name'] }}</p>
                                                @endif
                                                @if($uItem->has_active_discount)
                                                    <div class="flex items-center gap-1">
                                                        <span class="text-[9px] text-gray-400 dark:text-gray-500 line-through">Rp{{ number_format($uItem->original_price, 0, ',', '.') }}</span>
                                                        <span class="text-[10px] font-bold text-red-600 dark:text-red-400">{{ $uItem->formatted_price }}</span>
                                                    </div>
                                                @else
                                                    <p class="text-[10px] font-bold text-gray-700 dark:text-gray-300 transition-colors">{{ $uItem->formatted_price }}</p>
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Quick Config Overlay for Upsells --}}
                        @if($quickConfigItem)
                            <div class="absolute inset-x-0 bottom-0 z-[70] bg-white dark:bg-gray-800 rounded-t-3xl shadow-2xl border-t dark:border-white/10 p-6 transform transition-transform duration-300" 
                                x-data x-init="$el.classList.add('translate-y-0')"
                            >
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="font-bold text-lg dark:text-white transition-colors">Pilih Opsi: {{ $quickConfigItem->name }}</h4>
                                    <button wire:click="cancelQuickConfig" class="p-1 text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="max-h-[60vh] overflow-y-auto hide-scrollbar pb-4">
                                    {{-- Mini Variants --}}
                                    @if($quickConfigItem->variants->count() > 0)
                                        <div class="mb-4">
                                            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2 transition-colors">Varian</p>
                                            <div class="grid grid-cols-2 gap-2">
                                                @foreach($quickConfigItem->variants as $v)
                                                    <button 
                                                        wire:click="$set('quickConfigVariant', {{ $v->id }})"
                                                        class="p-2 text-xs font-bold rounded-lg border transition-all {{ $quickConfigVariant == $v->id ? 'bg-primary-600 text-white border-primary-600 shadow-md' : 'bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300 border-gray-100 dark:border-white/5' }}"
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
                                            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2 transition-colors">Tambahan</p>
                                            <div class="space-y-2">
                                                @foreach($quickConfigItem->addons as $a)
                                                    <label class="flex items-center justify-between p-2 rounded-lg border transition-colors {{ in_array($a->id, $quickConfigAddons) ? 'bg-primary-50 dark:bg-primary-900/10 border-primary-200 dark:border-primary-500/30' : 'bg-gray-50 dark:bg-gray-700/50 border-gray-100 dark:border-white/5' }}">
                                                        <div class="flex items-center">
                                                            <input type="checkbox" value="{{ $a->id }}" wire:model.live="quickConfigAddons" class="h-4 w-4 text-primary-600 focus:ring-primary-500 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-800">
                                                            <span class="ml-2 text-xs font-bold dark:text-gray-200">{{ $a->name }}</span>
                                                        </div>
                                                        <span class="text-[10px] font-bold dark:text-white">+{{ number_format($a->price, 0, ',', '.') }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Mini Quantity --}}
                                    <div class="flex items-center justify-between mt-4 bg-gray-50 dark:bg-gray-900/50 p-3 rounded-xl border border-gray-100 dark:border-white/5 transition-colors">
                                        <span class="text-xs font-bold dark:text-gray-300">Jumlah</span>
                                        <div class="flex items-center space-x-3">
                                            <button wire:click="decrementQuantity('quickConfigQuantity')" class="w-6 h-6 rounded-full bg-white dark:bg-gray-700 shadow-sm flex items-center justify-center font-bold text-lg dark:text-white" {{ $quickConfigQuantity <= 1 ? 'disabled' : '' }}>-</button>
                                            <span class="font-bold text-sm dark:text-white">{{ $quickConfigQuantity }}</span>
                                            <button wire:click="incrementQuantity('quickConfigQuantity')" class="w-6 h-6 rounded-full bg-white dark:bg-gray-700 shadow-sm flex items-center justify-center font-bold text-lg dark:text-white">+</button>
                                        </div>
                                     </div>
                                    {{-- Inline Stock Error for Quick Config --}}
                                    @if($quickConfigErrorMessage)
                                        <p x-data x-init="setTimeout(() => $wire.clearStockError('quickConfig'), 3000)"
                                           class="text-red-500 text-[10px] font-semibold mt-2 flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                            {{ $quickConfigErrorMessage }}
                                        </p>
                                    @endif

                                    {{-- Quick Config Notes --}}
                                    <div class="mt-4">
                                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2 transition-colors">Catatan Khusus</p>
                                        <textarea wire:model.defer="quickConfigNote" rows="2" 
                                            class="w-full bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-white/5 focus:border-primary-500 rounded-xl p-3 text-xs font-medium transition-all outline-none placeholder:text-gray-300 dark:text-white"
                                            placeholder="Contoh: Tanpa es, double shot, dll..."></textarea>
                                    </div>
                                </div>

                                <button wire:click="saveQuickConfig" class="w-full bg-primary-600 dark:bg-primary-500 text-white py-3 rounded-xl font-bold mt-2 shadow-lg shadow-primary-500/20 hover:bg-primary-500 transition">
                                    Gunakan Pilihan Ini
                                </button>
                            </div>
                        @endif

                        {{-- Note --}}
                        <div class="mb-6">
                            <h4 class="font-bold text-sm mb-3 dark:text-white transition-colors">Catatan Pesanan <span class="text-xs font-normal text-gray-400">(Opsional)</span></h4>
                            <textarea 
                                wire:model.defer="note"
                                class="w-full bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-white/5 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary-600 dark:focus:ring-primary-500 focus:border-transparent transition-all placeholder:text-gray-400 dark:text-white"
                                placeholder="Contoh: Tidak pakai pedas, tanpa bawang, dll."
                                rows="2"
                            ></textarea>
                        </div>

                        {{-- Quantity --}}
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-bold text-sm dark:text-white transition-colors">Quantity</h4>
                            <div class="flex items-center space-x-4">
                                <button wire:click="decrementQuantity" class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white flex items-center justify-center font-bold pb-1 text-xl transition-colors" {{ $quantity <= 1 ? 'disabled' : '' }}>-</button>
                                <span class="font-bold text-lg w-4 text-center dark:text-white transition-colors">{{ $quantity }}</span>
                                <button wire:click="incrementQuantity" class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white flex items-center justify-center font-bold pb-1 text-xl transition-colors">+</button>
                            </div>
                        </div>
                        {{-- Inline Stock Error --}}
                        @if($stockErrorMessage)
                            <p x-data x-init="setTimeout(() => $wire.clearStockError(), 3000)"
                               class="text-red-500 text-xs font-semibold mb-4 flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                {{ $stockErrorMessage }}
                            </p>
                        @else
                            <div class="mb-4"></div>
                        @endif

                        {{-- Add to Cart Button Logic --}}
                        @php
                            $mainItemVariant = $selectedItem->variants->find($selectedVariant);
                            $mainItemAddons = $selectedItem->addons->whereIn('id', $selectedAddons);
                            $mainItemUnitPrice = $selectedItem->price + ($mainItemVariant?->price ?? 0) + $mainItemAddons->sum('price');
                            $mainItemTotalPrice = $mainItemUnitPrice * $quantity;
                            
                            $bundleTotalPrice = collect($bundledItems)->sum(fn($b) => $b['price'] * $b['quantity']);
                            $totalOrderPrice = $mainItemTotalPrice + $bundleTotalPrice;
                            $totalItemCount = 1 + count($bundledItems);
                        @endphp

                        {{-- Combo Summary & Helper Text --}}
                        @if(count($bundledItems) > 0)
                            <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-dashed border-gray-200 dark:border-white/10 transition-colors">
                                <p class="text-[10px] font-extrabold text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-3 text-center transition-colors">Ringkasan Paket Pesanan</p>
                                <div class="space-y-2">
                                     <div class="flex justify-between items-center text-xs">
                                         <div class="flex items-center">
                                             <div class="w-1.5 h-1.5 rounded-full bg-gray-400 dark:bg-gray-600 mr-2"></div>
                                             <span class="text-gray-700 dark:text-gray-300 font-medium">{{ $quantity }}x {{ $selectedItem->name }}</span>
                                         </div>
                                         <span class="font-bold text-gray-900 dark:text-white">Rp {{ number_format($mainItemTotalPrice, 0, ',', '.') }}</span>
                                     </div>
                                     @foreach($bundledItems as $b)
                                         <div class="flex flex-col text-xs">
                                             <div class="flex justify-between items-center">
                                                 <div class="flex items-center">
                                                     <div class="w-1.5 h-1.5 rounded-full bg-primary-500 mr-2"></div>
                                                     <span class="text-gray-700 dark:text-gray-300 font-medium">{{ $b['quantity'] }}x {{ $b['name'] }}</span>
                                                 </div>
                                                 <span class="font-bold text-gray-900 dark:text-white">Rp {{ number_format($b['price'] * $b['quantity'], 0, ',', '.') }}</span>
                                             </div>
                                             @if(!empty($b['note']))
                                                 <div class="pl-3.5 mt-0.5 text-[9px] font-bold text-amber-600 dark:text-amber-400/80 italic">
                                                     * {{ $b['note'] }}
                                                 </div>
                                             @endif
                                         </div>
                                     @endforeach
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-center space-x-2 mb-3 animate-pulse">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-[11px] font-bold text-primary-700 dark:text-primary-400">Klik tombol di bawah untuk memproses paket</p>
                            </div>
                        @endif

                        <button wire:click="addToCart" class="w-full bg-primary-600 dark:bg-primary-500 text-white py-4 rounded-xl font-bold flex items-center justify-between px-6 shadow-xl shadow-primary-500/20 hover:bg-primary-500 transition relative overflow-hidden group">
                           <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                            <span class="flex items-center">
                                Tambahkan {{ $totalItemCount > 1 ? $totalItemCount . ' Item' : '' }}
                            </span>
                            <span class="bg-white/20 px-3 py-1 rounded-lg text-sm">
                                Rp {{ number_format($totalOrderPrice, 0, ',', '.') }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>
    @endif

    {{-- Floating Cart Bottom Bar --}}
    @if($this->cartCount > 0 && ($table || $restaurant->is_online_order_enabled))
        <div class="fixed bottom-0 left-0 right-0 p-4 z-40 bg-gradient-to-t from-white via-white/95 to-transparent dark:from-[#0B0F19] dark:via-[#0B0F19]/95 pointer-events-none pb-safe transition-colors duration-300">
            <div class="max-w-7xl mx-auto pointer-events-auto">
                <a href="{{ route('restaurant.cart', $restaurant->slug) }}" class="w-full bg-primary-600 dark:bg-primary-500 text-white px-5 py-3.5 rounded-2xl shadow-2xl shadow-primary-500/20 flex items-center justify-between hover:bg-primary-500 transition-all transform active:scale-95 cursor-pointer ring-1 ring-white/10">
                    <div class="flex items-center space-x-3">
                        <div class="bg-white/10 p-2.5 rounded-xl block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-white leading-tight">{{ $this->cartCount }} Item Tersimpan</span>
                            <span class="text-[10px] text-white/70 font-medium tracking-wide">Lanjut Pembayaran</span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-1.5">
                        <span class="font-bold text-sm tracking-tight">Rp {{ number_format(collect($cart)->sum('total_price'), 0, ',', '.') }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white/50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </a>
            </div>
        </div>
    @endif
</div>
