<?php

namespace App\Livewire\Restaurant;

use Livewire\Component;
use Livewire\Attributes\Layout;

class Menu extends Component
{
    public \App\Models\Restaurant $restaurant;
    public $table; // Can be a Table model or null
    public $activeCategory = null;
    public $search = '';

    public $selectedItem = null;
    public $quantity = 1;
    public $selectedVariant = null;
    public $selectedAddons = [];
    public $cart = [];
    public $hasPendingCall = false;
    public $showRespondedStatus = false;
    public $stockErrorMessage = ''; // Pesan error stok inline di popup
    public $quickConfigErrorMessage = ''; // Error inline untuk modal upsell
    public $note = ''; // Catatan item per menu

    // Smart Bundling Features
    public $bundledItems = []; // Format: [itemId => [variantId, [addonIds], quantity, price]]
    public $quickConfigItem = null;
    public $quickConfigVariant = null;
    public $quickConfigAddons = [];
    public $quickConfigQuantity = 1;
    public $quickConfigNote = '';

    public function handleWaiterResponded($data)
    {
        if (isset($data['table_id']) && $this->table && $data['table_id'] == $this->table->id) {
            $this->hasPendingCall = false;
            $this->showRespondedStatus = true;
            
            // Auto hide after 5 seconds
            $this->dispatch('hide-responded-status');
        }
    }

    public function hideRespondedStatus()
    {
        $this->showRespondedStatus = false;
    }

    public function mount(\App\Models\Restaurant $restaurant, $qr_code = null)
    {
        $this->restaurant = $restaurant;

        if ($qr_code) {
            $this->table = \App\Models\Table::where('qr_code', $qr_code)
                ->where('restaurant_id', $this->restaurant->id)
                ->firstOrFail();
            
            // Save to session so Cart can retrieve it
            session()->put('dineflo.table_id', $this->table->id);

            // Check for existing pending call
            $this->hasPendingCall = \App\Models\WaiterCall::where('table_id', $this->table->id)
                ->where('status', 'pending')
                ->exists();
        } else {
            // Remove session if accessing without table
            session()->forget('dineflo.table_id');
        }

        $this->cart = session()->get('cart.' . $this->restaurant->id, []);
    }

    public function openItemModal($itemId)
    {
        // Guard for online orders (no table context)
        if (!$this->table && !$this->restaurant->is_online_order_enabled) {
            $this->dispatch('notify', [
                'type'    => 'error',
                'message' => 'Pemesanan online saat ini sedang ditutup.',
            ]);
            return;
        }

        // Server-side stock guard
        $checkItem = \App\Models\MenuItem::find($itemId);
        if ($checkItem && $checkItem->manage_stock && $checkItem->stock_quantity <= 0) {
            $this->dispatch('notify', [
                'type'    => 'error',
                'message' => 'Maaf, stok ' . $checkItem->name . ' sudah habis.',
            ]);
            return;
        }

        $this->stockErrorMessage = '';
        // Get IDs of items already in cart to exclude them from suggestions
        $cartItemIds = collect($this->cart)->pluck('menu_item_id')->toArray();
        $cartItemIds[] = $itemId; // Also exclude the item itself if somehow related

        $this->selectedItem = \App\Models\MenuItem::with([
            'variants', 
            'addons', 
            'upsells' => function($query) use ($cartItemIds) {
                // Don't suggest items already in cart
                $query->whereNotIn('upsell_item_id', $cartItemIds);
            },
            'upsells.upsellItem' => function($query) {
                // Only suggest active/available items
                $query->where('is_available', true);
            }
        ])
            ->where('restaurant_id', $this->restaurant->id)
            ->findOrFail($itemId);
            
        $this->quantity = 1;
        $this->note = '';
        $this->selectedVariant = $this->selectedItem->variants->first()?->id;
        $this->selectedAddons = [];
    }

    public function closeItemModal()
    {
        $this->selectedItem = null;
        $this->bundledItems = [];
        $this->quickConfigItem = null;
        $this->stockErrorMessage = '';
        $this->reset(['quantity', 'selectedVariant', 'selectedAddons', 'note']);
    }

    public function toggleUpsell($itemId)
    {
        // Guard for online orders (no table context)
        if (!$this->table && !$this->restaurant->is_online_order_enabled) {
            $this->dispatch('notify', [
                'type'    => 'error',
                'message' => 'Pemesanan online saat ini sedang ditutup.',
            ]);
            return;
        }

        if (isset($this->bundledItems[$itemId])) {
            unset($this->bundledItems[$itemId]);
            return;
        }

        $item = \App\Models\MenuItem::with(['variants', 'addons'])->findOrFail($itemId);

        // Server-side stock guard
        if ($item->manage_stock) {
            $cartQty = collect($this->cart)->where('menu_item_id', $itemId)->sum('quantity');
            if ($cartQty + 1 > $item->stock_quantity) {
                $this->dispatch('notify', [
                    'type'    => 'error',
                    'message' => 'Maaf, stok ' . $item->name . ' hanya tersisa ' . max(0, $item->stock_quantity - $cartQty) . '.',
                ]);
                return;
            }
        }
        
        // If it has variants or addons, open quick config
        if ($item->variants->count() > 0 || $item->addons->count() > 0) {
            $this->openQuickConfig($item);
        } else {
            // Add simple item immediately
            $itemPrice = $item->price;
            $discount = $item->getActiveDiscount();
            if ($discount) {
                if ($discount->type === 'percentage') {
                    $itemPrice = $itemPrice - ($itemPrice * ($discount->value / 100));
                } else {
                    $itemPrice = max(0, $itemPrice - $discount->value);
                }
            }

            $this->bundledItems[$itemId] = [
                'id' => $item->id,
                'name' => $item->name,
                'variant_id' => null,
                'addon_ids' => [],
                'quantity' => 1,
                'price' => $itemPrice,
                'original_price' => $item->price,
                'note' => '',
            ];
        }
    }

    public function openQuickConfig($item)
    {
        // $item can be an ID or model
        if (!($item instanceof \App\Models\MenuItem)) {
            $item = \App\Models\MenuItem::with(['variants', 'addons'])->findOrFail($item);
        }

        $this->quickConfigItem = $item;
        $this->quickConfigVariant = $item->variants->first()?->id;
        $this->quickConfigAddons = [];
        $this->quickConfigQuantity = 1;
        $this->quickConfigNote = '';
        $this->quickConfigErrorMessage = '';
    }

    public function saveQuickConfig()
    {
        if (!$this->quickConfigItem) return;

        // Stock validation for quick config
        if ($this->quickConfigItem->manage_stock) {
            $cartQty = collect($this->cart)->where('menu_item_id', $this->quickConfigItem->id)->sum('quantity');
            $bundledQty = isset($this->bundledItems[$this->quickConfigItem->id]) ? $this->bundledItems[$this->quickConfigItem->id]['quantity'] : 0;
            if ($cartQty + $bundledQty + $this->quickConfigQuantity > $this->quickConfigItem->stock_quantity) {
                $this->quickConfigErrorMessage = 'Stok tersisa hanya ' . max(0, $this->quickConfigItem->stock_quantity - $cartQty - $bundledQty) . ' item.';
                return;
            }
        }

        $variant = $this->quickConfigItem->variants->find($this->quickConfigVariant);
        $addons = $this->quickConfigItem->addons->whereIn('id', $this->quickConfigAddons);
        
        $basePrice = $this->quickConfigItem->price;
        $variantPrice = $variant ? $variant->price : 0;
        
        $itemPrice = $basePrice + $variantPrice;
        $originalPrice = $itemPrice;

        $discountName = null;
        $discount = $this->quickConfigItem->getActiveDiscount();
        if ($discount) {
            $discountName = $discount->name;
            if ($discount->type === 'percentage') {
                $itemPrice = $itemPrice - ($itemPrice * ($discount->value / 100));
            } else {
                $itemPrice = max(0, $itemPrice - $discount->value);
            }
        }

        $addonsPrice = $addons->sum('price');
        $unitPrice = $itemPrice + $addonsPrice;

        $this->bundledItems[$this->quickConfigItem->id] = [
            'id' => $this->quickConfigItem->id,
            'name' => $this->quickConfigItem->name,
            'variant_id' => $this->quickConfigVariant,
            'variant_name' => $variant?->name,
            'addon_ids' => $this->quickConfigAddons,
            'addon_names' => $addons->pluck('name')->toArray(),
            'quantity' => $this->quickConfigQuantity,
            'price' => $unitPrice,
            'original_price' => $originalPrice,
            'discount_name' => $discountName,
            'note' => $this->quickConfigNote,
        ];

        $this->quickConfigItem = null;
    }

    public function cancelQuickConfig()
    {
        $this->quickConfigItem = null;
        $this->quickConfigNote = '';
        $this->quickConfigErrorMessage = '';
    }

    public function incrementQuantity($prop = 'quantity')
    {
        if ($prop === 'quantity' && $this->selectedItem && $this->selectedItem->manage_stock) {
            $stock = $this->selectedItem->stock_quantity;
            $cartQty = collect($this->cart)->where('menu_item_id', $this->selectedItem->id)->sum('quantity');
            $maxAllowed = max(0, $stock - $cartQty);
            if ($this->$prop >= $maxAllowed) {
                $this->stockErrorMessage = 'Stok tersisa hanya ' . $stock . ' item.';
                return;
            }
        } elseif ($prop === 'quickConfigQuantity' && $this->quickConfigItem && $this->quickConfigItem->manage_stock) {
            $stock = $this->quickConfigItem->stock_quantity;
            $cartQty = collect($this->cart)->where('menu_item_id', $this->quickConfigItem->id)->sum('quantity');
            $bundledQty = isset($this->bundledItems[$this->quickConfigItem->id]) ? $this->bundledItems[$this->quickConfigItem->id]['quantity'] : 0;
            $maxAllowed = max(0, $stock - $cartQty - $bundledQty);
            if ($this->$prop >= $maxAllowed) {
                $this->quickConfigErrorMessage = 'Stok tersisa hanya ' . $stock . ' item.';
                return;
            }
        }
        
        if ($prop === 'quantity') $this->stockErrorMessage = '';
        if ($prop === 'quickConfigQuantity') $this->quickConfigErrorMessage = '';
        
        $this->$prop++;
    }

    public function decrementQuantity($prop = 'quantity')
    {
        if ($prop === 'quantity') $this->stockErrorMessage = '';
        if ($prop === 'quickConfigQuantity') $this->quickConfigErrorMessage = '';

        if ($this->$prop > 1) {
            $this->$prop--;
        }
    }

    public function clearStockError($type = 'main')
    {
        if ($type === 'quickConfig') {
            $this->quickConfigErrorMessage = '';
        } else {
            $this->stockErrorMessage = '';
        }
    }

    public function addToCart()
    {
        if (!$this->selectedItem) return;

        // Stock Validation
        if ($this->selectedItem->manage_stock) {
            $currentStock = $this->selectedItem->stock_quantity;
            
            // Calculate item quantity already in cart
            $cartQuantity = collect($this->cart)->where('menu_item_id', $this->selectedItem->id)->sum('quantity');
            
            if (($cartQuantity + $this->quantity) > $currentStock) {
                $this->stockErrorMessage = 'Stok tersisa hanya ' . max(0, $currentStock - $cartQuantity) . ' item.';
                return;
            }
        }

        $variant = $this->selectedItem->variants->find($this->selectedVariant);
        $addons = $this->selectedItem->addons->whereIn('id', $this->selectedAddons);
        
        $basePrice = $this->selectedItem->price;
        $variantPrice = $variant ? $variant->price : 0;
        
        $itemPrice = $basePrice + $variantPrice;
        $originalPrice = $itemPrice;

        $discountName = null;
        $discount = $this->selectedItem->getActiveDiscount();
        if ($discount) {
            $discountName = $discount->name;
            if ($discount->type === 'percentage') {
                $itemPrice = $itemPrice - ($itemPrice * ($discount->value / 100));
            } else {
                $itemPrice = max(0, $itemPrice - $discount->value);
            }
        }

        $addonsPrice = $addons->sum('price');
        
        $totalPrice = ($itemPrice + $addonsPrice) * $this->quantity;

        $cartItem = [
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'menu_item_id' => $this->selectedItem->id,
            'name' => $this->selectedItem->name,
            'price' => $itemPrice,
            'quantity' => $this->quantity,
            'variant' => $variant ? ['id' => $variant->id, 'name' => $variant->name, 'price' => $variant->price] : null,
            'addons' => $addons->map(fn($a) => ['id' => $a->id, 'name' => $a->name, 'price' => $a->price])->toArray(),
            'total_price' => $totalPrice,
            'image' => $this->selectedItem->image, 
            'original_price' => $originalPrice,
            'discount_name' => $discountName,
            'note' => $this->note,
        ];

        $this->cart[] = $cartItem;

        // Process Bundled Items
        foreach ($this->bundledItems as $bItem) {
            $menuItem = \App\Models\MenuItem::find($bItem['id']);
            if (!$menuItem) continue;

            $this->cart[] = [
                'id' => \Illuminate\Support\Str::uuid()->toString(),
                'menu_item_id' => $menuItem->id,
                'name' => $menuItem->name,
                'price' => $bItem['price'],
                'quantity' => $bItem['quantity'],
                'variant' => isset($bItem['variant_id']) ? ['id' => $bItem['variant_id'], 'name' => $bItem['variant_name']] : null,
                'addons' => collect($bItem['addon_ids'])->map(fn($id, $index) => ['id' => $id, 'name' => $bItem['addon_names'][$index]])->toArray(),
                'total_price' => $bItem['price'] * $bItem['quantity'],
                'image' => $menuItem->image,
                'original_price' => $bItem['original_price'] ?? $bItem['price'],
                'note' => $bItem['note'] ?? '',
            ];
        }

        session()->put('cart.' . $this->restaurant->id, $this->cart);

        $this->closeItemModal();
        
        $this->dispatch('items-added'); // Changed event name to trigger animation
        
        // Use filament notification or custom dispatch
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Item added to cart!',
        ]);
    }

    public function getCartCountProperty()
    {
        return count($this->cart);
    }

    #[Layout('components.layouts.app')] 
    public function render()
    {
        $categories = \App\Models\MenuItem::getCachedCategories($this->restaurant->id);

        $menuItems = \App\Models\MenuItem::getCachedItems($this->restaurant->id);

        // Apply PHP filtering for category and search to benefit from caching
        if ($this->activeCategory) {
            $menuItems = $menuItems->where('menu_category_id', $this->activeCategory);
        }

        if ($this->search) {
            $search = strtolower($this->search);
            $menuItems = $menuItems->filter(function($item) use ($search) {
                return str_contains(strtolower($item->name), $search);
            });
        }

        $hasRemoveBranding = $this->restaurant->owner?->hasFeature('Remove Branding');

        return view('livewire.restaurant.menu', [
            'categories' => $categories,
            'menuItems' => $menuItems,
        ])->layoutData([
            'restaurant' => $hasRemoveBranding ? $this->restaurant : null,
            'title' => $this->restaurant->name . ' - Digital Menu'
        ]);
    }

    public function selectCategory($categoryId)
    {
        $this->activeCategory = $categoryId;
    }

    public function callWaiter()
    {
        // Feature Gate
        if (!$this->restaurant->owner->hasFeature('Waiter Call System')) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Service not available.']);
            return;
        }

        if (!$this->table) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Please scan QR code again.']);
            return;
        }

        // Anti-spam check (optional but good)
        $exists = \App\Models\WaiterCall::where('table_id', $this->table->id)
            ->where('status', 'pending')
            ->where('created_at', '>=', now()->subMinutes(1))
            ->exists();

        if ($exists) {
            $this->dispatch('notify', ['type' => 'info', 'message' => 'Request already sent. Please wait.']);
            return;
        }

        $waiterCall = \App\Models\WaiterCall::create([
            'restaurant_id' => $this->restaurant->id,
            'table_id' => $this->table->id,
            'status' => 'pending',
            'called_at' => now(),
        ]);

        $this->hasPendingCall = true;

        // Broadcast real-time event to restaurant staff
        broadcast(new \App\Events\WaiterCalled($waiterCall));

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Waiter has been called! They will be with you shortly.'
        ]);
        
        // Dispatch specific event for internal tracking if needed
        $this->dispatch('waiter-called', [
            'id' => $waiterCall->id,
            'table_id' => $this->table->id,
            'table_name' => $this->table->name,
        ]);
    }
}
