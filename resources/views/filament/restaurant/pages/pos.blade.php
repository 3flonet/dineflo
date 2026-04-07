<div class="pos-livewire-root w-full h-full relative" x-data="{}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/dexie/dist/dexie.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0ea5e9',
                            600: '#0284c7',
                        },
                        secondary: '#64748b',
                        dark: '#0f172a',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <style>
        /* RESET FILAMENT LAYOUT */
        .fi-sidebar, .fi-header, .fi-topbar, .fi-footer, nav.fi-topbar-nav { display: none !important; }
        .fi-main-ctn, .fi-main { margin: 0 !important; padding: 0 !important; width: 100vw !important; max-width: 100vw !important; height: 100vh !important; }
        body { overflow: hidden !important; background-color: #f1f5f9; }
        
        /* SCROLLBAR CUSTOM */
        .custom-scroll::-webkit-scrollbar { width: 4px; height: 4px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .hide-scroll::-webkit-scrollbar { display: none; }

        [x-cloak] { display: none !important; }

        /* Animation */
        .animate-fade-in { animation: fadeIn 0.2s ease-out; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>

    <div class="fixed inset-0 z-[50] flex h-screen w-screen bg-slate-100 font-sans text-slate-800"
         x-data="{ 
            mobileCartOpen: false,
            isOnline: window.navigator.onLine,
            db: null,
            paymentMethod: @entangle('paymentMethod'),
            cashAmount: @entangle('cashReceived'),
            isSplitBill: @entangle('isSplitBill'),
            splitType: @entangle('splitType'),
            selectedSplitItems: @entangle('selectedSplitItems'),
            splitAmount: @entangle('splitAmount'),
            remainingBalance: @entangle('remainingBalance'),
            cart: @entangle('cart'),
            customerName: @entangle('customerName'),
            customerPhone: @entangle('customerPhone'),
            wantsToRegister: @entangle('wantsToRegister'),
            selectedTableId: @entangle('selectedTableId'),
            voucherId: @entangle('voucherId'),
            voucherCode: @entangle('voucherCode'),
            voucherDiscountAmount: @entangle('voucherDiscountAmount'),
            pointsToUse: @entangle('pointsToUse'),
            pointDiscountAmount: @entangle('pointDiscountAmount'),
            giftCardDiscountAmount: @entangle('giftCardDiscountAmount'),
            selectedBank: @entangle('selectedBank'),
            edcReference: @entangle('edcReference'),
            restaurantId: {{ $restaurant->id }},
            showToast: false,
            toastMessage: '',
            toastType: 'info', // info, success, warning, danger

            triggerToast(message, type = 'info') {
                this.toastMessage = message;
                this.toastType = type;
                this.showToast = true;
                setTimeout(() => { this.showToast = false; }, 3000);
            },

            init() {
                // Initialize IndexedDB
                this.db = new Dexie('DinefloPOS');
                this.db.version(2).stores({
                    menu_items: 'id, name, price, category_id',
                    categories: 'id, name',
                    settings: 'id, name',
                    pending_orders: '++id, offline_id, restaurant_id, status'
                });

                this.syncMenuToLocal();

                window.addEventListener('online', () => {
                    this.isOnline = true;
                    this.syncOfflineOrders();
                    this.syncMenuToLocal();
                });
                window.addEventListener('offline', () => {
                    this.isOnline = false;
                });

                // Auto-sync every 30 seconds if online
                setInterval(() => {
                    if (this.isOnline) {
                        this.syncOfflineOrders();
                    }
                }, 30000);

                // Watch for connection changes
                this.$watch('isOnline', value => {
                    if (!value && this.paymentMethod === 'qris') {
                        this.paymentMethod = 'cash';
                        this.triggerToast('Koneksi terputus, beralih ke metode TUNAI.', 'warning');
                    }
                });
            },

            async syncMenuToLocal() {
                if (!this.isOnline) return;
                
                try {
                    // 1. Save Menu Items
                    if (window.posMenuData) {
                        await this.db.menu_items.clear();
                        await this.db.menu_items.bulkAdd(window.posMenuData);
                        console.log('[POS] Menu items synced to local storage');
                    }

                    // 2. Fetch extra data via API Init if needed (future expansion)
                    // For now, we trust the Blade-rendered window.posMenuData
                } catch (e) {
                    console.error('Menu Local Sync Failed:', e);
                }
            },

            async saveOffline() {
                if (this.paymentMethod && this.paymentMethod !== 'cash') {
                    this.triggerToast('Pembayaran QRIS memerlukan koneksi internet. Gunakan Tunai (Cash) saat offline.', 'warning');
                    this.paymentMethod = 'cash';
                    return;
                }

                const offlineOrder = {
                    offline_id: 'OFF-' + Date.now(),
                    restaurant_id: this.restaurantId,
                    customer_name: this.customerName || 'Walk-in Guest',
                    customer_phone: this.customerPhone,
                    wants_to_register: this.wantsToRegister,
                    payment_method: 'cash',
                    cart: JSON.parse(JSON.stringify(this.cart)),
                    data: {
                        table_id: this.selectedTableId === 'takeaway' ? null : this.selectedTableId,
                        customer_name: this.customerName || 'Walk-in Guest',
                        customer_phone: this.customerPhone,
                        subtotal: this.totalPrice, // Simplified for offline
                        total_amount: this.totalPrice,
                        discount_id: this.voucherId,
                        voucher_code: this.voucherCode,
                        voucher_discount_amount: this.voucherDiscountAmount,
                        points_used: this.pointsToUse,
                        points_discount_amount: this.pointDiscountAmount,
                        payment_method: 'cash'
                    },
                    created_at: new Date().toISOString()
                };

                await this.db.pending_orders.add(offlineOrder);
                
                // Print Offline Receipt
                this.printOfflineReceipt(offlineOrder);

                // Show Notification (Mimic Filament Notification)
                this.triggerToast('Pesanan disimpan secara OFFLINE. Akan disinkronkan otomatis saat internet kembali.', 'warning');

                // Reset Locally
                this.cart = [];
                this.customerName = '';
                this.customerPhone = '';
                this.paymentMethod = null;
                this.cashAmount = 0;
                this.selectedTableId = null;
            },

            printOfflineReceipt(order) {
                // Pindah menggunakan fungsi JS global agar HTML tag tidak terdeteksi oleh Livewire Parser
                const tableText = this.selectedTableId === 'takeaway' ? 'TAKEAWAY' : (document.querySelector('select[x-model=selectedTableId] option:checked')?.text || '-');
                window.renderOfflineReceipt(order, '{{ addslashes($restaurant->name) }}', tableText, this.cashAmount, this.change);
            },


            async syncOfflineOrders() {
                if (!this.isOnline) return;
                
                const pending = await this.db.pending_orders.toArray();
                if (pending.length === 0) return;

                console.log(`[POS] Synching ${pending.length} offline orders...`);

                try {
                    const response = await fetch('{{ route('pos.offline_sync') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ orders: pending })
                    });

                    const result = await response.json();
                    if (result.success) {
                        let successCount = 0;
                        for (const res of result.results) {
                            if (res.status === 'success') {
                                await this.db.pending_orders.where('offline_id').equals(res.offline_id).delete();
                                successCount++;
                            }
                        }
                        
                        if (successCount > 0) {
                            this.triggerToast(`${successCount} pesanan offline berhasil disinkronkan.`, 'success');
                            // Refresh orders list if currently showing orders
                            if (this.showOrders) {
                                window.Livewire.dispatch('refreshUnpaidOrders');
                            }
                        }
                    }
                } catch (e) {
                    console.error('Offline Sync Failed:', e);
                }
            },

            get amountToPay() {
                if (!this.isSplitBill) return this.totalPrice;
                return (parseFloat(this.splitAmount) || 0);
            },
            get totalPrice() {
                let base = this.cart.reduce((sum, item) => sum + (parseFloat(item.total_price) || 0), 0);
                let discount = (parseFloat(this.voucherDiscountAmount) || 0) + 
                               (parseFloat(this.pointDiscountAmount) || 0) + 
                               (parseFloat(this.giftCardDiscountAmount) || 0);
                return Math.max(0, base - discount);
            },
            addCash(amount) { 
                let current = parseFloat(this.cashAmount) || 0;
                this.cashAmount = current + amount;
            },
            get change() {
                let paid = parseFloat(this.cashAmount) || 0;
                let diff = paid - this.amountToPay;
                return Math.max(0, diff);
            },
            addItem(itemId) {
                const itemData = window.posMenuData.find(i => i.id === itemId);
                if (!itemData) return;

                if (itemData.is_out_of_stock) {
                    this.triggerToast('Stok Habis!', 'danger');
                    return;
                }

                // If online AND has variants/addons, let Livewire handle it (opens modal)
                if (this.isOnline && itemData.variants_count > 0) {
                    window.Livewire.find('{{ $_instance->getId() }}').addToCart(itemId);
                    return;
                }

                if (!this.isOnline && itemData.variants_count > 0) {
                     this.triggerToast('Pilih varian hanya bisa dilakukan saat online.', 'warning');
                     // Just add base item for now to keep flow going
                }

                let existing = this.cart.find(i => i.id === itemId && !i.variant_id);
                if (existing) {
                    let idx = this.cart.indexOf(existing);
                    this.updateQty(idx, 1);
                } else {
                    this.cart.push({
                        id: itemData.id,
                        name: itemData.name,
                        price: itemData.price,
                        total_price: itemData.price,
                        quantity: 1,
                        image: itemData.image,
                        variant_id: null,
                        variant_name: null,
                        addons: [],
                        note: ''
                    });
                }

                if (this.isOnline) {
                    // Sync to server
                    this.cart = [...this.cart]; 
                }
            },
            updateQty(index, change) {
                let item = this.cart[index];
                if (!item) return;

                let newQty = item.quantity + change;
                if (newQty <= 0) {
                    this.removeItem(index);
                    return;
                }

                item.quantity = newQty;
                item.total_price = item.price * newQty;
                this.cart = [...this.cart]; // Trigger reactivity
            },
            removeItem(index) {
                this.cart.splice(index, 1);
                this.cart = [...this.cart];
            },
            processTransaction() {
                if (!this.selectedTableId) {
                    this.triggerToast('Mohon pilih LOKASI atau NOMOR MEJA terlebih dahulu.', 'danger');
                    return;
                }
                if (!this.isOnline) {
                    this.saveOffline();
                } else {
                    window.Livewire.find('{{ $_instance->getId() }}').processCheckout();
                }
            }
         }">

        {{-- LEFT SIDE: MENU --}}
        <main class="flex-1 flex flex-col h-full min-w-0 bg-slate-50/50">
            {{-- Header --}}
            <header class="px-6 py-4 bg-white border-b border-slate-200 flex items-center justify-between gap-4 shrink-0">
                <div class="flex items-center gap-4">
                    <a href="{{ route('filament.restaurant.pages.dashboard', ['tenant' => $restaurant]) }}" class="p-2.5 text-slate-400 hover:text-primary-500 hover:bg-primary-50 rounded-xl transition-all">
                        <x-heroicon-o-home class="w-6 h-6" />
                    </a>
                    
                    @if(auth()->user()->hasFeature('Cash Drawer Integration'))
                        @if($activeSession)
                        <div class="hidden xl:flex items-center gap-2 px-4 py-2 bg-slate-900 border border-slate-700 rounded-xl">
                            <div class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div>
                            <div class="text-[10px] font-black uppercase text-slate-300">
                                Cash in Drawer: <span class="text-sm tracking-tighter ml-1 text-white">Rp{{ number_format($activeSession->expected_cash, 0, ',', '.') }}</span>
                            </div>
                            
                            <div class="w-px h-4 bg-slate-700 mx-1"></div>
                            
                            <button wire:click="$set('showAdjustmentModal', true)" class="flex items-center gap-2 p-1.5 px-3 hover:bg-slate-800 rounded-lg text-emerald-400 transition-colors border border-slate-700" title="Kelola Kas / Buka Laci">
                                <x-heroicon-o-lock-open class="w-4 h-4" />
                                <span class="text-[9px] font-black uppercase tracking-widest leading-none">Manajemen Laci</span>
                            </button>
                            
                            <button wire:click="$set('showRegisterModal', true)" class="p-1.5 px-3 hover:bg-rose-500/20 rounded-lg text-rose-400 transition-colors border border-rose-500/30 flex items-center gap-2" title="End Shift (Tutup Kasir)">
                                <x-heroicon-o-power class="w-4 h-4" />
                                <span class="text-[9px] font-black uppercase tracking-widest leading-none">Tutup Kasir</span>
                            </button>
                        </div>
                        @else
                            {{-- Option to just open drawer without session, though session is forced --}}
                            <button wire:click="$set('showAdjustmentModal', true)" class="p-2.5 text-amber-500 hover:bg-amber-50 rounded-xl transition-all border border-amber-100 flex items-center gap-2" title="Kelola Kas / Buka Laci">
                                <x-heroicon-o-lock-open class="w-5 h-5" />
                                <span class="text-[10px] font-black uppercase tracking-widest hidden xl:block">Manajemen Laci</span>
                            </button>
                        @endif
                    @endif

                    <h1 class="text-xl font-black text-slate-900 tracking-tight hidden sm:block">POS SYSTEM</h1>
                    
                    {{-- Connection Status Badge --}}
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-full border transition-all duration-500"
                         :class="isOnline ? 'bg-emerald-50 border-emerald-100 text-emerald-600' : 'bg-rose-50 border-rose-100 text-rose-600'">
                        <div class="w-1.5 h-1.5 rounded-full" :class="isOnline ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500'"></div>
                        <span class="text-[9px] font-black uppercase tracking-widest" x-text="isOnline ? 'Online' : 'Mode Offline'"></span>
                    </div>
                </div>

                <div class="relative flex-1 max-w-md">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <x-heroicon-o-magnifying-glass class="w-5 h-5" />
                    </span>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari menu favorit..." class="w-full bg-slate-100 border-none rounded-xl py-3 pl-10 pr-4 text-sm font-bold focus:ring-2 focus:ring-primary-500 placeholder-slate-400">
                </div>

                <div class="flex items-center justify-center gap-1.5 p-1 bg-slate-100 rounded-xl relative mr-auto md:mr-0 ml-4 hidden sm:flex">
                    <button wire:click="$set('showOrders', false)" class="relative z-10 px-4 py-2 rounded-lg text-xs font-black tracking-widest uppercase transition-all {{ !$showOrders ? 'text-primary-600 bg-white shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">MENU PO</button>
                    <button wire:click="$set('showOrders', true)" class="relative z-10 px-4 py-2 rounded-lg text-xs font-black tracking-widest uppercase transition-all flex items-center gap-2 {{ $showOrders ? 'text-primary-600 bg-white shadow-sm' : 'text-slate-500 hover:text-slate-700' }}">
                        ORDERAN 
                        @if($unpaidOrders->count() > 0)
                            <span class="bg-red-500 text-white text-[9px] px-1.5 py-0.5 rounded-md font-bold">{{ $unpaidOrders->count() }}</span>
                        @endif
                    </button>
                </div>

                <div class="flex items-center gap-3">
                    <button @click="mobileCartOpen = true" class="lg:hidden bg-primary-500 text-white p-3 rounded-xl shadow-lg relative">
                        <x-heroicon-o-shopping-cart class="w-6 h-6" />
                        @if(count($cart) > 0)
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] w-5 h-5 flex items-center justify-center rounded-full font-bold border-2 border-white">{{ count($cart) }}</span>
                        @endif
                    </button>
                    <div class="hidden sm:flex items-center gap-3 pl-4 border-l border-slate-200">
                        <div class="text-right">
                            <p class="text-[10px] font-black text-slate-400 uppercase leading-none">{{ $restaurant->name }}</p>
                            <p class="text-xs font-bold text-slate-700">Kasir: {{ auth()->user()->name }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-slate-800 text-white flex items-center justify-center font-black text-sm">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                    </div>
                </div>
            </header>

            @if($this->isPastClosing && $activeSession)
            <div class="px-6 py-3 bg-amber-50 border-b border-amber-200 animate-fade-in shrink-0">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-amber-100 rounded-lg text-amber-600">
                            <x-heroicon-m-clock class="w-5 h-5" />
                        </div>
                        <div>
                            <p class="text-sm font-black text-amber-900 leading-tight">Jam Operasional Telah Berakhir</p>
                            <p class="text-[10px] font-bold text-amber-700 uppercase tracking-wider">Sistem akan menutup kasir otomatis dalam kurun waktu 1 jam setelah tutup.</p>
                        </div>
                    </div>
                    @if($restaurant->auto_close_cashier)
                    <div class="hidden md:flex items-center gap-2 px-3 py-1.5 bg-white border border-amber-200 rounded-lg shadow-sm">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                        <span class="text-[10px] font-black text-rose-600 uppercase tracking-widest">Auto-Close Aktif</span>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            @if(!$showOrders)
                {{-- Category Filter --}}
                <div class="px-6 py-3 bg-white border-b border-slate-200/50 overflow-x-auto hide-scroll flex gap-2 shrink-0">
                    <button wire:click="$set('selectedCategory', 'all')" class="px-6 py-2.5 rounded-full text-xs font-black uppercase tracking-wide transition-all {{ $selectedCategory === 'all' ? 'bg-slate-800 text-white shadow-md' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}">SEMUA</button>
                    @foreach($categories as $category)
                        <button wire:click="$set('selectedCategory', {{ $category->id }})" class="px-6 py-2.5 rounded-full text-xs font-black uppercase tracking-wide transition-all whitespace-nowrap {{ $selectedCategory == $category->id ? 'bg-slate-800 text-white shadow-md' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}">
                            {{ strtoupper($category->name) }}
                        </button>
                    @endforeach
                </div>

                {{-- Grid Menu --}}
                <div class="flex-1 overflow-y-auto p-6 custom-scroll">
                    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-5 pb-20 lg:pb-0">
                        @forelse($menuItems as $item)
                            @php
                                $isOutOfStock = $item->manage_stock && $item->stock_quantity <= 0;
                            @endphp
                            <div 
                                @click="addItem({{ $item->id }})"
                                class="bg-white p-3 rounded-2xl shadow-sm border border-slate-100 {{ $isOutOfStock ? 'opacity-60 cursor-not-allowed' : 'hover:border-primary-500/50 hover:shadow-xl cursor-pointer transition-all active:scale-[0.98] group' }} flex flex-col h-full relative overflow-hidden">
                                <div class="aspect-square bg-slate-100 rounded-xl mb-3 overflow-hidden flex items-center justify-center relative">
                                    @if($item->image)
                                        <img src="{{ Storage::url($item->image) }}" class="w-full h-full object-cover {{ !$isOutOfStock ? 'group-hover:scale-110' : '' }} transition-transform duration-700">
                                    @else
                                        <x-heroicon-o-photo class="w-12 h-12 text-slate-300 opacity-20" />
                                    @endif
                                    
                                    {{-- Variant Badge Tucked in Corner --}}
                                    @if($item->variants->count() > 0)
                                        <div class="absolute top-0 right-0">
                                            <div class="bg-primary-500 text-white text-[9px] font-black px-2.5 py-1 rounded-bl-xl shadow-sm uppercase tracking-tighter">
                                                {{ $item->variants->count() }} Varian
                                            </div>
                                        </div>
                                    @endif

                                    @if($isOutOfStock)
                                        <div class="absolute inset-0 bg-white/40 backdrop-blur-[1px] flex items-center justify-center">
                                            <span class="bg-red-500 text-white text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-tighter shadow-lg">Habis</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="mt-auto px-1">
                                    <h3 class="text-sm font-black text-slate-800 leading-tight mb-2 line-clamp-2 uppercase tracking-tight">{{ $item->name }}</h3>
                                    <div class="flex items-center justify-between">
                                        @if($item->has_active_discount)
                                            <div class="flex flex-col items-start leading-tight">
                                                <span class="text-[10px] text-slate-400 line-through">Rp{{ number_format($item->original_price, 0, ',', '.') }}</span>
                                                <span class="text-sm font-black text-red-600">{{ $item->formatted_price }}</span>
                                            </div>
                                        @else
                                            <p class="text-sm font-black text-primary-500">
                                                {{ $item->formatted_price }}
                                            </p>
                                        @endif
                                        <div class="w-7 h-7 bg-slate-50 group-hover:bg-primary-500 group-hover:text-white rounded-lg flex items-center justify-center text-slate-400 transition-colors">
                                            <x-heroicon-o-plus class="w-4 h-4" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full h-96 flex flex-col items-center justify-center text-slate-300">
                                <x-heroicon-o-magnifying-glass class="w-16 h-16 mb-4 opacity-10" />
                                <p class="font-black text-sm uppercase tracking-widest">Menu tidak ditemukan</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @else
                {{-- Order List --}}
                <div class="px-6 py-4 bg-white border-b border-slate-200/50 shrink-0 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-black text-slate-800 tracking-tight uppercase">Daftar Order Belum Lunas</h2>
                        <p class="text-xs font-bold text-slate-400 mt-1">Pilih orderan untuk memproses pembayaran di kasir</p>
                    </div>
                    <button wire:click="$set('showOrders', false)" class="text-sm font-black text-primary-500 hover:text-primary-600 flex items-center gap-1 uppercase tracking-widest">
                        <x-heroicon-o-arrow-left class="w-4 h-4" /> KEMBALI
                    </button>
                </div>
                <div class="flex-1 overflow-y-auto p-6 custom-scroll">
                    <div wire:poll.5s class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 pb-20 lg:pb-0">
                        @forelse($unpaidOrders as $order)
                            @php
                                $paidSoFar = $order->order_payments_sum_amount ?? 0;
                                $sisa = max(0, $order->total_amount - $paidSoFar);
                            @endphp
                            <div wire:click="selectOrder({{ $order->id }})" class="bg-white p-5 rounded-2xl shadow-sm border-2 border-slate-100 hover:border-primary-500 cursor-pointer transition-all relative group flex flex-col h-full transform active:scale-95">
                                {{-- Status Badge --}}
                                <div class="absolute top-4 right-4">
                                    @if($sisa < $order->total_amount && $sisa > 0)
                                        <span class="bg-amber-100 text-amber-600 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">Dibayar Sebagian</span>
                                    @else
                                        <span class="bg-rose-100 text-rose-500 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">Belum Dibayar</span>
                                    @endif
                                </div>
                                
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-black text-sm">
                                        #{{ substr($order->order_number, -4) }}
                                    </div>
                                    <div>
                                        <h3 class="font-black text-slate-800 truncate max-w-[150px]">{{ $order->customer_name ?: 'Guest' }}</h3>
                                        <div class="flex items-center gap-1.5 text-xs text-slate-400 font-bold">
                                            <span>M{{ $order->table ? $order->table->name : 'TW' }}</span>
                                            <span>•</span>
                                            <span>{{ $order->created_at->format('H:i') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-sm text-slate-500 mb-4 flex-1 line-clamp-2">
                                    {{ $order->items->count() }} Items: 
                                    @foreach($order->items->take(3) as $oItem)
                                        {{ $oItem->menuItem->name }},
                                    @endforeach
                                    {{ $order->items->count() > 3 ? '...' : '' }}
                                </div>

                                <div class="mt-auto pt-4 border-t border-slate-100 flex items-center justify-between">
                                    <div class="flex flex-col">
                                        <span class="text-[9px] text-slate-400 font-black uppercase tracking-widest">Tagihan</span>
                                        <span class="text-lg font-black text-primary-600 tracking-tight">Rp{{ number_format($sisa, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="w-8 h-8 rounded-full bg-primary-50 text-primary-500 group-hover:bg-primary-500 group-hover:text-white flex items-center justify-center transition-colors">
                                        <x-heroicon-o-arrow-right class="w-4 h-4" />
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full h-64 flex flex-col items-center justify-center text-slate-400">
                                <x-heroicon-o-document-check class="w-16 h-16 opacity-30 mb-4" />
                                <h3 class="font-black uppercase tracking-widest text-lg opacity-50">Semua Orderan Sudah Lunas</h3>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif
        </main>

        {{-- RIGHT SIDE: CART --}}
        <aside class="fixed inset-y-0 right-0 z-50 w-full sm:w-[420px] lg:static lg:w-[400px] bg-white border-l border-slate-200 shadow-2xl lg:shadow-none transform transition-transform duration-300 ease-in-out flex flex-col"
            :class="mobileCartOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0'" x-cloak>
            
            <div class="flex flex-col h-full">
                {{-- Cart Top Bar --}}
                <div class="p-6 border-b border-dashed border-slate-200 flex items-center justify-between shrink-0 bg-white">
                    <div>
                        <h2 class="text-xl font-black text-slate-900 tracking-tight uppercase">Order Details</h2>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest line-clamp-1">{{ $restaurant->name }}</span>
                            <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                            <span class="text-[10px] font-black text-primary-500 uppercase">{{ count($cart) }} items</span>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button wire:click="clearCart" class="p-2.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all" title="Clear Cart">
                            <x-heroicon-o-trash class="w-6 h-6" />
                        </button>
                        <button @click="mobileCartOpen = false" class="lg:hidden p-2.5 text-slate-400 hover:bg-slate-100 rounded-xl">
                            <x-heroicon-o-x-mark class="w-6 h-6" />
                        </button>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto custom-scroll relative">
                    {{-- Voucher Section --}}
                @if($restaurant->owner->hasFeature('Voucher & Marketing'))
                    <div class="px-6 py-4 bg-white border-b border-dashed border-slate-100">
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 block">KODE VOUCHER / PROMO</label>
                        
                        @if($voucherId)
                            <div class="flex items-center justify-between bg-green-50 border border-green-200 p-3 rounded-xl">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-green-500 text-white flex items-center justify-center">
                                        <x-heroicon-o-ticket class="w-5 h-5 font-black" />
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-green-800 uppercase tracking-widest leading-none">{{ $voucherCode }}</p>
                                        <p class="text-[9px] font-bold text-green-600 mt-0.5">Voucher Terpasang</p>
                                    </div>
                                </div>
                                <button wire:click="removeVoucher" class="p-1.5 hover:bg-white rounded-lg text-red-500 transition-colors">
                                    <x-heroicon-o-x-mark class="w-5 h-5" />
                                </button>
                            </div>
                        @else
                            <div class="flex gap-2">
                                <input wire:model="voucherCode" type="text" class="flex-1 bg-slate-50 border-slate-200 rounded-xl py-2.5 px-4 text-xs font-black focus:ring-2 focus:ring-primary-500 uppercase placeholder-slate-300" placeholder="MASUKKAN KODE">
                                <button wire:click="applyVoucher" class="bg-slate-900 text-white px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-primary-600 transition-all shadow-sm">PAKAI</button>
                            </div>
                            @if($voucherError)
                                <p class="text-red-500 text-[9px] mt-1.5 font-bold italic">{{ $voucherError }}</p>
                            @endif
                        @endif
                    </div>
                @endif

                {{-- Order Config --}}
                <div class="px-6 py-4 space-y-3 bg-slate-50/50 border-b border-slate-100">
                    <div class="grid grid-cols-1 gap-3">
                        <div>
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1.5 block">NAMA PELANGGAN</label>
                            <input wire:model="customerName" type="text" class="w-full bg-white border-slate-200 rounded-xl py-3 px-4 text-sm font-bold focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all" placeholder="Walk-in Guest">
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-1.5 ">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] block">NOMOR WHATSAPP (OPSIONAL)</label>
                                @if($isMember && $member)
                                    <span class="text-[9px] font-black text-green-600 bg-green-100 px-2 py-0.5 rounded-full uppercase tracking-widest animate-pulse-once">MEMBER TERDAFTAR</span>
                                @endif
                            </div>
                            <input wire:model.live="customerPhone" type="tel" class="w-full bg-white border-slate-200 rounded-xl py-3 px-4 text-sm font-bold focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all {{ $isMember ? 'border-green-300 ring-2 ring-green-100' : '' }}" placeholder="628123xxx">
                            
                            {{-- Registration Logic --}}
                            @if(!$isMember && strlen($customerPhone) >= 10)
                                <div class="mt-2 p-3 bg-primary-50 rounded-xl border-2 border-dashed border-primary-100 animate-pulse-once">
                                    <label class="flex items-center cursor-pointer group">
                                        <input type="checkbox" wire:model="wantsToRegister" class="w-4 h-4 text-primary-600 border-2 border-primary-200 rounded focus:ring-0 transition-all cursor-pointer">
                                        <div class="ml-2.5">
                                            <span class="block text-[10px] font-black text-slate-900 leading-tight uppercase tracking-tight">Daftarkan Member Baru? ✨</span>
                                            <span class="block text-[8px] text-slate-400 font-bold uppercase tracking-wider">Berikan poin & diskon untuk pelanggan ini</span>
                                        </div>
                                    </label>
                                </div>
                            @endif
                            
                            {{-- Point Redemption UI --}}
                            @if($isMember && $member && $restaurant->loyalty_redemption_enabled && ($member->points_balance > 0 || $this->pointsToUse > 0))
                                <div class="mt-3 p-3 bg-amber-50 rounded-2xl border-2 border-amber-100/50 shadow-sm relative overflow-hidden group transition-all hover:bg-amber-100/30">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-2">
                                            <div class="p-1.5 bg-amber-500 rounded-lg shadow-lg shadow-amber-500/20">
                                                <x-heroicon-m-gift-top class="w-3.5 h-3.5 text-white" />
                                            </div>
                                            <span class="text-[10px] font-black text-amber-900 uppercase tracking-tight">
                                                {{ $this->initialPoints > 0 ? 'Poin Member Terpakai' : 'Potong Poin Member?' }}
                                            </span>
                                        </div>
                                        @if($this->initialPoints > 0)
                                            <div class="bg-amber-500 text-white rounded-md p-0.5 shadow-sm">
                                                <x-heroicon-m-check class="w-3 h-3" />
                                            </div>
                                        @else
                                            <input type="checkbox" wire:model.live="usePoints" class="w-4 h-4 text-amber-500 border-2 border-amber-200 rounded-md focus:ring-0 transition-all cursor-pointer">
                                        @endif
                                    </div>
                                    
                                    @if($usePoints)
                                        <div class="mt-2.5 animate-fade-in">
                                            <div class="flex items-center justify-between text-[9px] font-bold text-amber-600 uppercase mb-2">
                                                <span>Saldo: {{ number_format($member->points_balance) }} Poin</span>
                                                <span>1 Poin = Rp{{ number_format($restaurant->loyalty_point_redemption_value, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="flex items-center bg-white/60 border-2 border-amber-200 rounded-xl p-0.5 focus-within:border-amber-500 transition-colors">
                                                <span class="bg-amber-100 px-3 py-1.5 rounded-lg text-amber-700 font-bold text-[9px] uppercase tracking-widest leading-none">Poin Digunakan</span>
                                                @if($this->initialPoints > 0)
                                                    <div class="w-full text-right font-black text-sm px-2 text-amber-900">{{ number_format($this->pointsToUse) }}</div>
                                                @else
                                                    <input type="number" wire:model.live.debounce.500ms="pointsToUse" class="w-full bg-transparent text-right font-black text-sm outline-none px-2 text-amber-900" placeholder="0">
                                                @endif
                                            </div>
                                            @if($this->pointDiscount > 0)
                                                <p class="text-[9px] text-amber-600 mt-2 font-black italic text-right uppercase tracking-wider">Potongan: - Rp{{ number_format($this->pointDiscount, 0, ',', '.') }}</p>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div>
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1.5 block">LOKASI / MEJA</label>
                            <div class="relative">
                                <select wire:model.live="selectedTableId" class="w-full appearance-none bg-white border border-slate-200 rounded-xl py-3 pl-4 pr-10 text-xs font-black text-slate-800 uppercase tracking-wider focus:ring-2 focus:ring-primary-500 transition-all">
                                    <option value="">-- PILIH --</option>
                                    <option value="takeaway">🥡 Bawa Pulang (Takeaway)</option>
                                    @foreach($tables as $table)
                                        <option value="{{ $table->id }}">🍽 {{ strtoupper($table->name) }} ({{ $table->area }})</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                    <x-heroicon-o-chevron-down class="w-4 h-4" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Cart Items --}}
                <div class="px-6 py-4 space-y-4 min-h-[150px] overflow-y-auto custom-scroll flex-1">
                    <template x-for="(item, index) in cart" :key="index">
                        <div class="flex items-start gap-4 p-4 rounded-2xl bg-white border border-slate-100 shadow-sm hover:border-primary-100 transition-colors group">
                            <div class="w-14 h-14 bg-slate-50 rounded-xl shrink-0 flex items-center justify-center overflow-hidden border border-slate-100 relative">
                                <img x-cloak x-show="item.image" :src="item.image" class="w-full h-full object-cover" x-on:error="$el.style.display='none'">
                                <div x-show="!item.image" class="font-black text-[10px] text-slate-300 text-center px-1" x-text="item.name ? item.name.substring(0,3) : ''"></div>
                                
                                <button @click="removeItem(index)" class="absolute inset-0 bg-red-600/90 text-white opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                    <x-heroicon-o-trash class="w-5 h-5" />
                                </button>
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <h4 class="text-xs font-black text-slate-900 uppercase truncate tracking-tight" x-text="item.name"></h4>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    <template x-if="item.variant_name">
                                        <span class="text-[9px] font-bold text-primary-600 bg-primary-50 px-2 py-0.5 rounded-full uppercase" x-text="item.variant_name"></span>
                                    </template>
                                    <template x-if="item.addons && item.addons.length > 0">
                                        <span class="text-[9px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full uppercase" x-text="'+' + item.addons.length + ' addons'"></span>
                                    </template>
                                    <template x-if="item.note">
                                        <span class="text-[9px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full uppercase truncate max-w-[120px]" :title="item.note" x-text="'Note: ' + item.note"></span>
                                    </template>
                                </div>
                                
                                <div class="flex flex-col mt-2">
                                    <template x-if="item.original_price && item.original_price > item.price">
                                        <span class="text-[9px] text-slate-400 line-through" x-text="'Rp' + new Intl.NumberFormat('id-ID').format((item.original_price + (item.addons ? item.addons.reduce((a, b) => a + b.price, 0) : 0)) * item.quantity)"></span>
                                    </template>
                                    <span class="text-[11px] font-black" :class="item.original_price && item.original_price > item.price ? 'text-red-600' : 'text-slate-900'" x-text="'Rp' + new Intl.NumberFormat('id-ID').format(item.total_price)"></span>
                                </div>
                            </div>

                            <div class="flex flex-col items-center gap-1.5 bg-slate-50 rounded-xl p-1.5 border border-slate-100 shadow-inner">
                                <button @click="updateQty(index, 1)" class="w-6 h-6 flex items-center justify-center bg-white rounded-lg shadow-sm text-slate-600 hover:bg-primary-500 hover:text-white transition-all">
                                    <x-heroicon-o-plus class="w-3 h-3" />
                                </button>
                                <span class="text-xs font-black text-slate-900 w-5 text-center" x-text="item.quantity"></span>
                                <button @click="updateQty(index, -1)" class="w-6 h-6 flex items-center justify-center bg-white rounded-lg shadow-sm text-slate-600 hover:bg-red-500 hover:text-white transition-all">
                                    <x-heroicon-o-minus class="w-3 h-3" />
                                </button>
                            </div>
                        </div>
                    </template>

                    <div x-show="cart.length === 0" class="h-full flex flex-col items-center justify-center text-slate-300 py-20">
                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                            <x-heroicon-o-shopping-bag class="w-8 h-8 opacity-20" />
                        </div>
                        <p class="font-black text-[10px] uppercase tracking-widest opacity-40">Keranjang Kosong</p>
                    </div>
                </div>

                </div>
                {{-- Cart Footer / Payment --}}
                <div class="p-5 bg-white border-t border-slate-200 shadow-[0_-10px_40px_-15px_rgba(0,0,0,0.05)] z-20 shrink-0">
                    <div class="space-y-1 mb-4">
                        <div class="flex justify-between items-center text-slate-400">
                            <span class="font-bold text-[9px] uppercase tracking-widest">Subtotal</span>
                            <span class="text-xs font-black">Rp{{ number_format($this->cartTotal, 0, ',', '.') }}</span>
                        </div>

                        {{-- Additional Fees --}}
                        @if($restaurant->additional_fees)
                            @foreach($restaurant->additional_fees as $fee)
                                @php
                                    $feeAmount = $fee['type'] === 'fixed' ? $fee['value'] : ($this->cartTotal * ($fee['value'] / 100));
                                @endphp
                                @if($feeAmount > 0)
                                    <div class="flex justify-between items-center text-slate-400">
                                        <span class="font-bold text-[9px] uppercase tracking-widest">{{ $fee['name'] }}</span>
                                        <span class="text-xs font-black">Rp{{ number_format($feeAmount, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                            @endforeach
                        @endif

                        {{-- Tax --}}
                        @if($restaurant->tax_enabled && $this->cartTax > 0)
                            <div class="flex justify-between items-center text-slate-400">
                                <span class="font-bold text-[9px] uppercase tracking-widest">Pajak ({{ $restaurant->tax_percentage }}%)</span>
                                <span class="text-xs font-black">Rp{{ number_format($this->cartTax, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        {{-- Voucher Discount --}}
                        @if($this->voucherDiscount > 0)
                            <div class="flex justify-between items-center text-green-500">
                                <span class="font-bold text-[9px] uppercase tracking-widest">Diskon Voucher</span>
                                <span class="text-xs font-black">- Rp{{ number_format($this->voucherDiscount, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        {{-- Point Discount --}}
                        @if($this->pointDiscount > 0)
                            <div class="flex justify-between items-center text-amber-600">
                                <span class="font-bold text-[9px] uppercase tracking-widest">Potongan Poin ({{ number_format($this->pointsToUse) }} Poin)</span>
                                <span class="text-xs font-black">- Rp{{ number_format($this->pointDiscount, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        <div class="flex justify-between items-end border-t border-slate-100 pt-1 mt-1">
                            <span class="text-slate-900 font-black text-[10px] uppercase tracking-widest">Main Total</span>
                            <span class="text-2xl font-black text-slate-900 tracking-tighter" x-text="'Rp' + totalPrice.toLocaleString('id-ID')"></span>
                        </div>
                        
                        <template x-if="remainingBalance > 0 && remainingBalance < totalPrice">
                            <div class="flex justify-between items-end border-t border-slate-100 pt-1 mt-1">
                                <span class="text-rose-500 font-black text-[10px] uppercase tracking-widest">Sisa Tagihan</span>
                                <span class="text-lg font-black text-rose-500 tracking-tighter" x-text="'Rp' + remainingBalance.toLocaleString('id-ID')"></span>
                            </div>
                        </template>
                    </div>

                    @if(auth()->user()->hasFeature('Split Bill'))
                        <div class="mb-3">
                            <button @click="isSplitBill = !isSplitBill; if(isSplitBill){ splitAmount = remainingBalance > 0 ? remainingBalance : totalPrice; } else { splitAmount = 0; }" class="w-full py-2 rounded-xl font-black text-[9px] uppercase tracking-[0.1em] transition-all flex items-center justify-center gap-2 border-2" :class="isSplitBill ? 'border-primary-500 text-primary-600 bg-primary-50' : 'border-slate-200 text-slate-500 hover:bg-slate-50'">
                                <x-heroicon-o-arrows-right-left class="w-4 h-4" /> SPLIT BILL / PARTIAL PAY
                            </button>
                            
                            <div x-show="isSplitBill" x-cloak class="mt-2 space-y-2">
                                @if(auth()->user()->hasSplitByItem())
                                <div class="flex items-center gap-1 p-1 bg-slate-100 rounded-lg">
                                    <button @click="splitType = 'amount'" class="flex-1 py-1.5 rounded-md font-black text-[8px] uppercase tracking-widest transition-all" :class="splitType === 'amount' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-400'">Amount</button>
                                    <button wire:click="switchToSplitByItem" class="flex-1 py-1.5 rounded-md font-black text-[8px] uppercase tracking-widest transition-all" :class="splitType === 'item' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-400'">Item</button>
                                </div>
                                @endif

                                <div x-show="splitType === 'amount'">
                                    <div class="flex items-center bg-white border-2 border-slate-200 rounded-xl p-0.5 focus-within:border-primary-500 transition-colors">
                                        <span class="bg-slate-100 px-3 py-2 rounded-lg text-slate-500 font-black text-[9px] uppercase tracking-widest leading-none">Nominal</span>
                                        <input type="number" x-model.number="splitAmount" class="w-full bg-transparent text-right font-black text-lg outline-none px-2" placeholder="0">
                                    </div>
                                </div>

                                <div x-show="splitType === 'item'" class="bg-white border-2 border-slate-200 rounded-xl overflow-hidden">
                                     <div class="bg-slate-50 px-3 py-1.5 border-b border-slate-200 flex justify-between items-center">
                                         <span class="text-[8px] font-black uppercase text-slate-400 tracking-widest leading-none">Pilih Item</span>
                                         <span class="text-[10px] font-black text-primary-600" x-text="'Total: Rp' + amountToPay.toLocaleString('id-ID')"></span>
                                     </div>
                                     <div class="max-h-[150px] overflow-y-auto custom-scroll p-1 space-y-1">
                                         @php $orderForSplit = $existingOrderId ? \App\Models\Order::find($existingOrderId) : null; @endphp
                                         @if($orderForSplit)
                                             @foreach($orderForSplit->items as $splitItem)
                                                @if(!$splitItem->is_paid)
                                                 <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50 cursor-pointer transition-colors group">
                                                     <input type="checkbox" wire:model.live="selectedSplitItems" value="{{ $splitItem->id }}" class="w-4 h-4 rounded border-slate-300 text-primary-500 focus:ring-primary-500">
                                                     <div class="flex-1 min-w-0">
                                                         <div class="text-[10px] font-black text-slate-700 uppercase tracking-tight truncate">{{ $splitItem->menuItem->name }}</div>
                                                         <div class="text-[8px] font-bold text-slate-400">{{ $splitItem->quantity }}x @ Rp{{ number_format($splitItem->unit_price, 0, ',', '.') }}</div>
                                                     </div>
                                                     <div class="text-[10px] font-black text-slate-900">
                                                         Rp{{ number_format($splitItem->total_price, 0, ',', '.') }}
                                                     </div>
                                                 </label>
                                                @endif
                                             @endforeach
                                         @else
                                             <div class="p-4 text-center">
                                                 <p class="text-[10px] text-slate-400 font-bold uppercase">Simpan Pesanan Dulu</p>
                                             </div>
                                         @endif
                                     </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-{{ (in_array($restaurant->payment_mode ?? 'kasir', ['kasir', 'both']) && in_array($restaurant->payment_mode ?? 'kasir', ['gateway', 'both'])) ? '2' : '1' }} gap-2 mb-3">
                        @if(in_array($restaurant->payment_mode ?? 'kasir', ['kasir', 'both']))
                        <button @click="paymentMethod = (paymentMethod === 'cash' ? null : 'cash')" :class="paymentMethod === 'cash' ? 'bg-slate-900 text-white ring-4 ring-slate-900/10' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'" class="py-3 rounded-xl font-black text-[9px] uppercase tracking-[0.1em] transition-all flex items-center justify-center gap-2">
                             <x-heroicon-o-banknotes class="w-4 h-4" /> CASH
                        </button>
                        @endif
                        @if(in_array($restaurant->payment_mode ?? 'kasir', ['gateway', 'both']))
                        <button @click="if(!isOnline) { triggerToast('QRIS tidak tersedia saat offline.', 'warning'); return; } paymentMethod = (paymentMethod === 'qris' ? null : 'qris')" 
                                :class="[!isOnline ? 'opacity-40 grayscale cursor-not-allowed' : '', paymentMethod === 'qris' ? 'bg-primary-500 text-white ring-4 ring-primary-500/10' : 'bg-slate-100 text-slate-500 hover:bg-slate-200']" 
                                class="py-3 rounded-xl font-black text-[9px] uppercase tracking-[0.1em] transition-all flex items-center justify-center gap-2">
                            <x-heroicon-o-qr-code class="w-4 h-4" /> QRIS
                        </button>
                        @endif
                        @if(auth()->user()->hasFeature('EDC Integration') && (auth()->user()->can('use_edc_payment') || auth()->user()->hasRole('restaurant_owner')) && $restaurant->edc_config && count($restaurant->edc_config) > 0)
                        <button @click="paymentMethod = (paymentMethod === 'edc' ? null : 'edc')" 
                                :class="paymentMethod === 'edc' ? 'bg-indigo-600 text-white ring-4 ring-indigo-600/10' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'" 
                                class="py-3 rounded-xl font-black text-[9px] uppercase tracking-[0.1em] transition-all flex items-center justify-center gap-2">
                             <x-heroicon-o-credit-card class="w-4 h-4" /> EDC
                        </button>
                        @endif
                    </div>

                    <div x-show="paymentMethod" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         class="mb-3" x-cloak>
                        
                        <div x-show="paymentMethod === 'cash'" class="space-y-3">
                            <div class="flex items-center bg-white border-2 border-slate-200 rounded-xl p-0.5 focus-within:border-primary-500 transition-colors">
                                <span class="bg-slate-100 px-3 py-2 rounded-lg text-slate-500 font-black text-[9px] uppercase tracking-widest leading-none">Diterima</span>
                                <input type="number" x-model.number="cashAmount" class="w-full bg-transparent text-right font-black text-lg outline-none px-2" placeholder="0">
                            </div>
                            <div class="grid grid-cols-4 gap-1.5">
                                <template x-for="val in [10000, 20000, 50000, 100000]">
                                    <button @click="addCash(val)" class="py-2 bg-white border border-slate-200 rounded-lg text-[9px] font-black text-slate-600 hover:bg-slate-50 hover:border-primary-500 transition-all shadow-sm" x-text="val >= 1000 ? val/1000 + 'k' : val"></button>
                                </template>
                                <button @click="cashAmount = amountToPay" class="col-span-2 py-2 bg-primary-50 text-primary-600 border border-primary-100 rounded-lg text-[9px] font-black uppercase tracking-widest">PAS</button>
                                <button @click="cashAmount = 0" class="col-span-2 py-2 bg-red-50 text-red-500 border border-red-100 rounded-lg text-[9px] font-black uppercase tracking-widest">RESET</button>
                            </div>
                            <div class="flex justify-between items-center py-2 px-3 bg-emerald-50 rounded-xl border border-emerald-100">
                                <span class="text-emerald-600 font-black text-[9px] uppercase tracking-[0.2em]">Kembalian</span>
                                <span class="text-emerald-700 font-black text-base tracking-tight" x-text="'Rp' + change.toLocaleString('id-ID')"></span>
                            </div>
                        </div>

                        <div x-show="paymentMethod === 'qris'" class="flex flex-col items-center justify-center h-[100px] bg-slate-50 rounded-xl border-2 border-dashed border-slate-200 group hover:bg-white hover:border-primary-500 transition-all">
                            <x-heroicon-o-cpu-chip class="w-8 h-8 text-slate-300 group-hover:text-primary-500 mb-1 transition-colors" />
                            <p class="text-[9px] font-black text-slate-400 group-hover:text-primary-500 uppercase tracking-widest">Integrasi QRIS Otomatis</p>
                        </div>
                        
                        <div x-show="paymentMethod === 'edc'" class="space-y-3 p-3 bg-indigo-50 border border-indigo-100 rounded-2xl">
                             {{-- Dynamic Amount Info for EDC --}}
                             <div class="flex flex-col items-center justify-center py-2 px-3 bg-white border border-indigo-200 rounded-xl shadow-sm">
                                 <span class="text-[9px] font-black text-indigo-400 uppercase tracking-[0.2em] mb-0.5">Tagihan EDC</span>
                                 <span class="text-xl font-black text-indigo-600 tracking-tighter" x-text="'Rp' + (isSplitBill ? splitAmount : totalPrice).toLocaleString('id-ID')"></span>
                             </div>

                             <div class="relative">
                                 <select x-model="selectedBank" class="w-full appearance-none bg-white border border-indigo-200 rounded-xl py-2.5 pl-3 pr-10 text-xs font-black text-indigo-900 uppercase tracking-wider focus:ring-2 focus:ring-indigo-500 transition-all">
                                     <option value="">-- PILIH BANK EDC --</option>
                                     @foreach($restaurant->edc_config ?? [] as $bank)
                                         <option value="{{ $bank['bank_name'] }}">{{ strtoupper($bank['bank_name']) }} (MDR: {{ $bank['mdr_percent'] }}%)</option>
                                     @endforeach
                                 </select>
                                 <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-indigo-400">
                                     <x-heroicon-o-chevron-down class="w-4 h-4" />
                                 </div>
                             </div>
                             <div class="flex items-center bg-white border-2 border-indigo-200 rounded-xl p-0.5 focus-within:border-indigo-500 transition-colors">
                                 <span class="bg-indigo-100 px-3 py-1.5 rounded-lg text-indigo-600 font-black text-[9px] uppercase tracking-widest leading-none">TRACE NO</span>
                                 <input type="text" x-model="edcReference" class="w-full bg-transparent text-right font-black text-sm outline-none px-2 text-indigo-900" placeholder="000123">
                             </div>
                        </div>
                    </div>

                    <button @click="processTransaction()" :disabled="cart.length === 0" class="w-full bg-primary-500 hover:bg-primary-600 text-white font-black py-4 rounded-xl shadow-lg shadow-primary-500/20 uppercase tracking-[0.2em] text-[10px] transition-all transform active:scale-[0.98] disabled:opacity-50 flex items-center justify-center gap-2">
                        <x-heroicon-o-check-circle class="w-4 h-4" /> PROSES TRANSAKSI
                    </button>
                </div>
            </div>
        </aside>

        {{-- Mobile Overlay --}}
        <div x-show="mobileCartOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="mobileCartOpen = false" 
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 lg:hidden"></div>

        {{-- MODAL VARIANT & ADDON --}}
        @if($showVariantModal && $selectedItemForVariant)
            <div class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-0 sm:p-4">
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md transition-opacity" wire:click="$set('showVariantModal', false)"></div>
                
                <div class="bg-white w-full max-w-lg rounded-t-[2.5rem] sm:rounded-[2.5rem] shadow-[0_-20px_100px_-20px_rgba(0,0,0,0.3)] transform transition-all overflow-hidden relative z-10 flex flex-col max-h-[92vh] sm:max-h-[85vh] animate-fade-in">
                    
                    {{-- Modal Header --}}
                    <div class="relative h-40 shrink-0 bg-slate-900">
                        @if($selectedItemForVariant->image)
                            <img src="{{ Storage::url($selectedItemForVariant->image) }}" class="w-full h-full object-cover opacity-60">
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-white via-white/40 to-transparent"></div>
                        <button wire:click="$set('showVariantModal', false)" class="absolute top-6 right-6 p-2.5 bg-black/20 hover:bg-black/40 text-white rounded-2xl transition backdrop-blur-xl border border-white/20">
                            <x-heroicon-o-x-mark class="w-6 h-6" />
                        </button>
                        <div class="absolute bottom-6 left-8 right-8">
                            <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight leading-none">{{ $selectedItemForVariant->name }}</h3>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="bg-primary-500 text-white text-[10px] font-black px-3 py-1 rounded-full uppercase shadow-lg shadow-primary-500/40">{{ str_replace('Rp ', 'Rp', $selectedItemForVariant->formatted_price) }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Modal Content --}}
                    <div class="p-8 overflow-y-auto custom-scroll flex-1 space-y-10">
                        {{-- Variants Selection --}}
                        @if($selectedItemForVariant->variants->count() > 0)
                            <div>
                                <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Pilih Varian Item</label>
                                    <span class="text-[9px] bg-red-50 text-red-500 px-2 py-0.5 rounded font-black uppercase">Wajib</span>
                                </div>
                                <div class="space-y-3">
                                    @foreach($selectedItemForVariant->variants as $variant)
                                        <label class="flex items-center justify-between p-4 bg-white border-2 rounded-2xl cursor-pointer transition-all hover:bg-slate-50 relative overflow-hidden group {{ $selectedVariantId == $variant->id ? 'border-primary-500 bg-primary-50/10' : 'border-slate-100 hover:border-slate-300' }}">
                                            <input type="radio" wire:model.live="selectedVariantId" value="{{ $variant->id }}" class="sr-only">
                                            
                                            <div class="flex items-center gap-4 relative z-10">
                                                <div class="w-6 h-6 rounded-xl border-2 flex items-center justify-center transition-all {{ $selectedVariantId == $variant->id ? 'border-primary-500 bg-primary-500' : 'border-slate-200 bg-white group-hover:border-slate-400' }}">
                                                    @if($selectedVariantId == $variant->id)
                                                        <x-heroicon-s-check class="w-4 h-4 text-white" />
                                                    @endif
                                                </div>
                                                <span class="font-black text-xs uppercase tracking-wide {{ $selectedVariantId == $variant->id ? 'text-primary-600' : 'text-slate-600' }}">
                                                    {{ $variant->name }}
                                                </span>
                                            </div>

                                            <span class="font-black text-sm relative z-10 {{ $selectedVariantId == $variant->id ? 'text-primary-600' : 'text-slate-900' }}">
                                                Rp{{ number_format($variant->price, 0, ',', '.') }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Addons Selection --}}
                        @if($selectedItemForVariant->addons->count() > 0)
                            <div>
                                <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Tambahan / Topping</label>
                                    <span class="text-[9px] bg-slate-100 text-slate-500 px-2 py-0.5 rounded font-black uppercase">Opsional</span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach($selectedItemForVariant->addons as $addon)
                                        <label class="flex items-center justify-between p-4 bg-white border-2 border-slate-100 rounded-2xl cursor-pointer hover:bg-slate-50 transition-all {{ in_array($addon->id, $selectedAddons) ? 'border-primary-500/30 bg-primary-50/20' : '' }}">
                                            <div class="flex items-center gap-3">
                                                <input type="checkbox" wire:model="selectedAddons" value="{{ $addon->id }}" class="w-5 h-5 rounded-lg border-slate-200 text-primary-500 focus:ring-primary-500">
                                                <div class="flex flex-col">
                                                    <span class="text-[11px] font-black uppercase tracking-tight {{ in_array($addon->id, $selectedAddons) ? 'text-slate-900' : 'text-slate-600' }}">{{ $addon->name }}</span>
                                                    <span class="text-[9px] font-bold text-primary-500">+Rp{{ number_format($addon->price, 0, ',', '.') }}</span>
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Notes for individual item --}}
                        <div class="mt-8">
                            <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Catatan Khusus</label>
                                <span class="text-[9px] bg-slate-100 text-slate-500 px-2 py-0.5 rounded font-black uppercase">Opsional</span>
                            </div>
                            <textarea wire:model.defer="itemNote" rows="2" 
                                class="w-full bg-slate-50 border-2 border-slate-100 focus:border-primary-500 rounded-2xl p-4 text-xs font-bold transition-all outline-none placeholder:text-slate-300"
                                placeholder="Keterangan detail pesanan (contoh: pedas, tanpa bawang...)"></textarea>
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="p-8 border-t border-slate-100 bg-white grid grid-cols-1 gap-4 shrink-0 shadow-[0_-15px_50px_-10px_rgba(0,0,0,0.05)]">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Total Harga Item</span>
                            <div class="text-right">
                                <span class="text-2xl font-black text-slate-900 tracking-tighter">
                                    @php
                                        $vPrice = $selectedVariantId ? $selectedItemForVariant->variants->find($selectedVariantId)?->price : $selectedItemForVariant->price;
                                        
                                        $discount = $selectedItemForVariant->getActiveDiscount();
                                        if ($discount) {
                                            if ($discount->type === 'percentage') {
                                                $vPrice = $vPrice - ($vPrice * ($discount->value / 100));
                                            } else {
                                                $vPrice = max(0, $vPrice - $discount->value);
                                            }
                                        }

                                        $aPrice = \App\Models\MenuItemAddon::whereIn('id', $this->selectedAddons)->sum('price');
                                        echo 'Rp' . number_format($vPrice + $aPrice, 0, ',', '.');
                                    @endphp
                                </span>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <button wire:click="$set('showVariantModal', false)" class="py-4 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] text-slate-500 hover:bg-slate-100 transition-colors border border-slate-100">
                                BATAL
                            </button>
                            <button wire:click="confirmVariantSelection" class="py-4 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] text-white bg-primary-500 shadow-xl shadow-primary-500/30 hover:bg-primary-600 transition-all transform active:scale-95">
                                TAMBAHKAN
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- MODAL OPEN/CLOSE REGISTER --}}
        @if($showRegisterModal)
            <div class="fixed inset-0 z-[110] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity"></div>
                
                <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl transform transition-all overflow-hidden relative z-10 animate-fade-in">
                    <div class="p-8">
                        @if(!$activeSession)
                            <div class="text-center mb-8">
                                <div class="w-16 h-16 bg-primary-50 rounded-2xl flex items-center justify-center mx-auto mb-4 text-primary-500">
                                    <x-heroicon-o-key class="w-8 h-8" />
                                </div>
                                <h3 class="text-xl font-black text-slate-900 uppercase">Buka Kasir</h3>
                                <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-wider">Masukkan modal awal uang di laci</p>
                            </div>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Modal Awal (Cash)</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 font-bold">Rp</span>
                                        <input type="number" wire:model="openingCash" class="w-full pl-12 pr-4 py-4 bg-slate-50 border-none rounded-2xl text-lg font-black focus:ring-2 focus:ring-primary-500 transition-all text-right">
                                    </div>
                                </div>
                                <button wire:click="openRegister" class="w-full bg-primary-500 text-white py-4 rounded-2xl font-black uppercase tracking-widest text-xs shadow-lg shadow-primary-500/30 hover:bg-primary-600 transition-all">
                                    Mulai Shift Kasir
                                </button>
                            </div>
                        @else
                            <div class="text-center mb-8">
                                <div class="w-16 h-16 bg-rose-50 rounded-2xl flex items-center justify-center mx-auto mb-4 text-rose-500 border border-rose-100">
                                    <x-heroicon-o-power class="w-8 h-8" />
                                </div>
                                <h3 class="text-xl font-black text-slate-900 uppercase">Tutup Kasir</h3>
                                <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-widest">Selesaikan shift & hitung uang laci</p>
                            </div>

                            <div class="bg-slate-50 rounded-2xl p-4 mb-6 border border-slate-100">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Saldo Seharusnya</span>
                                    <span class="text-lg font-black text-slate-900">Rp{{ number_format($activeSession->expected_cash, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center text-[9px] text-slate-400 font-bold uppercase">
                                    <span>Modal: Rp{{ number_format($activeSession->opening_cash, 0, ',', '.') }}</span>
                                    <span>Penjualan: +Rp{{ number_format($activeSession->expected_cash - $activeSession->opening_cash, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            
                            <div class="space-y-5">
                                <div>
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Uang Fisik di Laci (Dihitung)</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 font-bold">Rp</span>
                                        <input type="number" wire:model="closingCash" class="w-full pl-12 pr-4 py-4 bg-white border-2 border-slate-200 rounded-2xl text-lg font-black focus:ring-2 focus:ring-rose-500 transition-all text-right">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <button @click="$wire.set('showRegisterModal', false)" class="py-4 border-2 border-slate-100 text-slate-400 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-50">Batal</button>
                                    <button wire:click="closeRegister" class="py-4 bg-rose-500 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-rose-500/20 hover:bg-rose-600">Simpan & Tutup</button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- MODAL CASH ADJUSTMENT (Petty Cash) --}}
        @if($showAdjustmentModal)
            <div class="fixed inset-0 z-[110] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity"></div>
                
                <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl transform transition-all overflow-hidden relative z-10 animate-fade-in">
                    <div class="p-8">
                        <div class="text-center mb-8">
                            <h3 class="text-xl font-black text-slate-900 uppercase">Manajemen Laci</h3>
                            <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-widest">Pilih aktivitas kasir yang dilakukan</p>
                        </div>
                        
                        <div class="space-y-5">
                            <div class="flex gap-2 p-1 bg-slate-100 rounded-2xl">
                                <button wire:click="$set('adjustmentType', 'open')" class="flex-1 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $adjustmentType === 'open' ? 'bg-amber-500 text-white shadow-md' : 'text-slate-500 hover:text-slate-800' }}">BUKA SAJA</button>
                                <button wire:click="$set('adjustmentType', 'in')" class="flex-1 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $adjustmentType === 'in' ? 'bg-emerald-500 text-white shadow-md' : 'text-slate-500 hover:text-slate-800' }}">KAS MASUK</button>
                                <button wire:click="$set('adjustmentType', 'out')" class="flex-1 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $adjustmentType === 'out' ? 'bg-rose-500 text-white shadow-md' : 'text-slate-500 hover:text-slate-800' }}">KAS KELUAR</button>
                            </div>

                            @if($adjustmentType !== 'open')
                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Nominal Uang</label>
                                <input type="number" wire:model="adjustmentAmount" class="w-full px-4 py-4 bg-slate-50 border-none rounded-2xl text-lg font-black focus:ring-2 focus:ring-primary-500 transition-all">
                            </div>
                            @endif

                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Alasan / Keperluan</label>
                                <textarea wire:model="adjustmentReason" rows="2" class="w-full px-4 py-4 bg-slate-50 border-none rounded-2xl text-xs font-bold focus:ring-2 focus:ring-primary-500 transition-all" 
                                    placeholder="{{ $adjustmentType === 'in' ? 'Misal: Tambah modal receh, Koreksi selisih lebih' : ($adjustmentType === 'out' ? 'Misal: Beli Galon, Parkir, Bayar Supplier' : 'Misal: Tukar Uang, Cek Fisik Uang') }}"></textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <button @click="$wire.set('showAdjustmentModal', false)" class="py-4 border-2 border-slate-100 text-slate-400 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-50 transition-colors">Batal</button>
                                <button wire:click="adjustCash" class="py-4 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-black transition-all shadow-lg shadow-black/10">Proses</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- PREMIUM TOAST NOTIFICATION --}}
        <div x-show="showToast" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-4 sm:translate-y-0 sm:translate-x-4"
             x-transition:enter-end="opacity-100 transform translate-y-0 sm:translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed bottom-6 right-6 z-[200] flex items-center gap-4 px-6 py-4 rounded-2xl shadow-2xl border backdrop-blur-md transition-all active:scale-95"
             :class="{
                'bg-white/90 border-slate-100 text-slate-900': toastType === 'info',
                'bg-emerald-500/90 border-emerald-400 text-white': toastType === 'success',
                'bg-amber-500/90 border-amber-400 text-white': toastType === 'warning',
                'bg-rose-500/90 border-rose-400 text-white': toastType === 'danger'
             }"
             style="display: none;">
            
            <template x-if="toastType === 'danger'">
                <x-heroicon-s-x-circle class="w-6 h-6" />
            </template>
            <template x-if="toastType === 'warning'">
                <x-heroicon-s-exclamation-triangle class="w-6 h-6" />
            </template>
            <template x-if="toastType === 'success'">
                <x-heroicon-s-check-circle class="w-6 h-6" />
            </template>
            <template x-if="toastType === 'info'">
                <x-heroicon-s-information-circle class="w-6 h-6 text-primary-500" />
            </template>

            <div class="flex flex-col">
                <span class="text-[10px] font-black uppercase tracking-[0.2em] opacity-80" x-text="toastType === 'danger' ? 'Peringatan' : (toastType === 'warning' ? 'Perhatian' : 'Informasi')"></span>
                <p class="text-xs font-bold leading-tight" x-text="toastMessage"></p>
            </div>

            <button @click="showToast = false" class="ml-2 opacity-50 hover:opacity-100 transition-opacity">
                <x-heroicon-s-x-mark class="w-4 h-4" />
            </button>
        </div>
    </div>

    {{-- MIDTRANS SCRIPTS --}}
    @if(config('midtrans.is_production'))
        <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    @else
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    @endif

    <script>
        document.addEventListener('livewire:init', () => {
             // Receipt Printing
             Livewire.on('open-receipt', (event) => {
                const url = event.url;
                if (url) {
                    window.open(url, '_blank', 'width=400,height=600');
                }
            });

            // Midtrans Payment
            Livewire.on('start-midtrans-payment', (event) => {
                const token = Array.isArray(event.token) ? event.token[0] : event.token;
                const orderId = Array.isArray(event.order_id) ? event.order_id[0] : event.order_id;
                const amount = Array.isArray(event.amount) ? event.amount[0] : event.amount;

                if (token && token.startsWith('MOCK_TOKEN_')) {
                    // MOCK MODE FOR LOCAL TESTING
                    if (confirm(`[MOCK SIMULATOR] Anda sedang online. Klik OK untuk mensimulasikan pembayaran QRIS senilai Rp${amount.toLocaleString('id-ID')} yang BERHASIL (Online Mode).`)) {
                        @this.handlePaymentSuccess(orderId, amount);
                    }
                    return;
                }

                if (token) {
                    window.snap.pay(token, {
                        onSuccess: function(result) {
                            @this.handlePaymentSuccess(orderId, amount);
                        },
                        onPending: function(result) {
                            console.log('Payment pending', result);
                        },
                        onError: function(result) {
                            alert('Pembayaran gagal!');
                        },
                        onClose: function() {
                            console.log('Payment modal closed');
                        }
                    });
                }
            });
            // Cash Drawer Pulse (Hardware Bridge Hook)
            Livewire.on('open-cash-drawer-pulse', () => {
                console.log('Sending ESC/POS pulse to printer DK port...');
            });
        });

        @php
            $posMenuArray = $menuItems->map(function($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'price' => $item->price,
                    'image' => $item->image ? Storage::url($item->image) : null,
                    'is_out_of_stock' => $item->manage_stock && $item->stock_quantity <= 0,
                    'stock_quantity' => $item->stock_quantity,
                    'manage_stock' => $item->manage_stock,
                    'variants_count' => $item->variants->count()
                ];
            })->toArray();
        @endphp
        window.posMenuData = {!! json_encode($posMenuArray) !!};

        window.renderOfflineReceipt = function(order, restaurantName, tableText, cashAmount, changeAmount) {
            const receiptWindow = window.open('', '_blank', 'width=400,height=600');
            let itemsHtml = '';
            order.cart.forEach(item => {
                itemsHtml += '\n' +
                    '                    \x3Cdiv style="display: flex; justify-content: space-between; margin-bottom: 4px;">\n' +
                    '                        \x3Cdiv style="flex: 1;">\n' +
                    '                            \x3Cdiv>' + item.name.toUpperCase() + '\x3C/div>\n' +
                    '                            ' + (item.variant_name ? '\x3Cdiv style="font-size: 10px;">[' + item.variant_name + ']\x3C/div>' : '') + '\n' +
                    '                            \x3Cdiv style="font-size: 10px;">' + item.quantity + ' x ' + new Intl.NumberFormat('id-ID').format(item.price) + '\x3C/div>\n' +
                    '                        \x3C/div>\n' +
                    '                        \x3Cdiv style="font-weight: bold;">' + new Intl.NumberFormat('id-ID').format(item.total_price) + '\x3C/div>\n' +
                    '                    \x3C/div>\n';
            });

            const html = '\x3Chtml>\n' +
                '\x3Chead>\n' +
                '    \x3Ctitle>Struk Offline - ' + order.offline_id + '\x3C/title>\n' +
                '    \x3Cstyle>\n' +
                '        body { font-family: "Courier New", Courier, monospace; font-size: 12px; line-height: 1.2; padding: 20px; width: 300px; }\n' +
                '        .text-center { text-align: center; }\n' +
                '        .border-top { border-top: 1px dashed #000; margin-top: 8px; padding-top: 8px; }\n' +
                '        .header { margin-bottom: 15px; }\n' +
                '        .footer { margin-top: 20px; font-size: 10px; }\n' +
                '        @media print { body { padding: 0; width: 100%; } }\n' +
                '    \x3C/style>\n' +
                '\x3C/head>\n' +
                '\x3Cbody>\n' +
                '    \x3Cdiv class="header text-center">\n' +
                '        \x3Ch2 style="margin: 0;">' + restaurantName.toUpperCase() + '\x3C/h2>\n' +
                '        \x3Cp style="margin: 4px 0;">STRUK OFFLINE\x3C/p>\n' +
                '        \x3Cp style="margin: 0;">ID: ' + order.offline_id + '\x3C/p>\n' +
                '    \x3C/div>\n' +
                '    \n' +
                '    \x3Cdiv style="margin-bottom: 8px;">\n' +
                '        \x3Cdiv>Tgl: ' + new Date(order.created_at).toLocaleString('id-ID') + '\x3C/div>\n' +
                '        \x3Cdiv>Plgn: ' + order.customer_name + '\x3C/div>\n' +
                '        \x3Cdiv>Meja: ' + tableText + '\x3C/div>\n' +
                '    \x3C/div>\n' +
                '\n' +
                '    \x3Cdiv class="border-top">\n' +
                '        ' + itemsHtml + '\n' +
                '    \x3C/div>\n' +
                '\n' +
                '    \x3Cdiv class="border-top">\n' +
                '        \x3Cdiv style="display: flex; justify-content: space-between; font-weight: bold; font-size: 14px;">\n' +
                '            \x3Cspan>TOTAL\x3C/span>\n' +
                '            \x3Cspan>Rp' + new Intl.NumberFormat('id-ID').format(order.data.total_amount) + '\x3C/span>\n' +
                '        \x3C/div>\n' +
                '        \x3Cdiv style="display: flex; justify-content: space-between; margin-top: 4px;">\n' +
                '            \x3Cspan>BAYAR (CASH)\x3C/span>\n' +
                '            \x3Cspan>Rp' + new Intl.NumberFormat('id-ID').format(cashAmount || 0) + '\x3C/span>\n' +
                '        \x3C/div>\n' +
                '        \x3Cdiv style="display: flex; justify-content: space-between;">\n' +
                '            \x3Cspan>KEMBALI\x3C/span>\n' +
                '            \x3Cspan>Rp' + new Intl.NumberFormat('id-ID').format(changeAmount || 0) + '\x3C/span>\n' +
                '        \x3C/div>\n' +
                '    \x3C/div>\n' +
                '\n' +
                '    \x3Cdiv class="footer text-center">\n' +
                '        \x3Cp>PESANAN DISIMPAN LOKAL\x3C/p>\n' +
                '        \x3Cp>Akan disinkronkan saat online\x3C/p>\n' +
                '        \x3Cp>*** TERIMA KASIH ***\x3C/p>\n' +
                '    \x3C/div>\n' +
                '\n' +
                '    \x3Cscript>\n' +
                '        window.onload = function() {\n' +
                '            window.print();\n' +
                '            setTimeout(function() { window.close(); }, 500);\n' +
                '        };\n' +
                '    \x3C/script>\n' +
                '\x3C/body>\n' +
                '\x3C/html>';

            receiptWindow.document.write(html);
            receiptWindow.document.close();
        };
    </script>
</div>