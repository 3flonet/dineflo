<?php

namespace App\Livewire\Restaurant;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use App\Models\Restaurant;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class Kiosk extends Component
{
    use \App\Traits\NormalizesPhone;
    public Restaurant $restaurant;
    public $categories = [];
    public $activeCategory = null;
    public $search = '';

    public $cart = [];

    // Modal state for selection
    public $selectedItem = null;
    public $selectedVariant = null;
    public $selectedAddons = [];
    public $quantity = 1;
    public $note = '';

    // View states: 'menu', 'cart', 'checkout', 'success'
    public $currentView = 'welcome';
    
    // Order State
    public $customerName = 'Guest'; 
    public $customerPhone = '';
    public $isMember = false;
    public $member = null;
    public $wantsToRegister = false;
    public $orderType = 'dine_in'; // default, chosen in modal
    public $selectedTable = null;
    public $paymentMethod = 'midtrans'; // Kiosk is usually cashless
    public $createdOrder = null;
    public $checkoutStep = 1; // 1: Info, 2: Payment

    // Gift Card Logic
    public $giftCardCode = '';
    public $appliedGiftCard = null;
    public $giftCardError = '';
    public $giftCardDiscountAmount = 0;
    public $useGiftCard = false;
    
    // Loyalty Points Logic
    public $usePoints = false;
    public $pointsToUse = 0;

    public function updatedPointsToUse($value)
    {
        if (!$this->isMember || !$this->member) {
            $this->pointsToUse = 0;
            return;
        }

        $maxPoints = $this->member->points_balance;
        
        $pointValue = $this->restaurant->loyalty_point_redemption_value ?: 0;
        
        if ($pointValue > 0) {
            $currentTotalBeforePoints = $this->subtotal + $this->tax;
            $maxPossiblePointsForTotal = ceil($currentTotalBeforePoints / $pointValue);
            $maxPoints = min($maxPoints, $maxPossiblePointsForTotal);
        }

        if ($value > $maxPoints) {
            $this->pointsToUse = $maxPoints;
        }

        if ($this->pointsToUse < 0) $this->pointsToUse = 0;
    }

    public function updatedUsePoints($value)
    {
        if (!$value) {
            $this->pointsToUse = 0;
        } else {
            $this->updatedPointsToUse($this->member?->points_balance ?? 0);
        }
    }

    public function updatedCustomerPhone($value)
    {
        if (!$this->restaurant->owner->hasFeature('Membership & Loyalty')) {
            return;
        }

        // Silent Normalization: Don't update $this->customerPhone visually
        $normalizedPhone = $this->normalizePhoneNumber($value);

        if ($normalizedPhone && strlen($normalizedPhone) >= 10) {
            $this->member = \App\Models\Member::where('restaurant_id', $this->restaurant->id)
                ->where('whatsapp', $normalizedPhone)
                ->first();
            
            if ($this->member) {
                $this->isMember = true;
                $this->customerName = $this->member->name;
                $this->wantsToRegister = false;
            } else {
                $this->isMember = false;
                $this->member = null;
            }
        } else {
            $this->isMember = false;
            $this->member = null;
            $this->wantsToRegister = false;
        }
    }

    // Smart Bundling Features
    public $bundledItems = []; // Format: [itemId => [variantId, [addonIds], quantity, price]]
    public $quickConfigItem = null;
    public $quickConfigVariant = null;
    public $quickConfigAddons = [];
    public $quickConfigQuantity = 1;
    public $stockErrorMessage = ''; // Pesan error stok inline di popup
    public $quickConfigErrorMessage = ''; // Error inline untuk modal upsell
    public $quickConfigNote = '';

    public function mount(Restaurant $restaurant)
    {
        $this->restaurant = $restaurant;
        
        // Gate Check: verify subscription has kiosk
        if (!$this->restaurant->owner?->hasFeature('Kiosk Mode')) {
            abort(403, 'Kiosk Mode is not active for this restaurant.');
        }

        $this->categories = MenuItem::getCachedCategories($this->restaurant->id);
            
        // Reset cart when Kiosk is mounted (fresh start for a new customer)
        $this->cart = [];

        $paymentMode = $this->restaurant->payment_mode ?? 'kasir';
        if ($paymentMode === 'kasir') {
            $this->paymentMethod = 'cash';
        } else {
            $this->paymentMethod = 'midtrans';
        }
    }

    public function updatedUseGiftCard($value)
    {
        if (!$value) {
            $this->removeGiftCard();
        }
    }

    public function applyGiftCard()
    {
        $this->giftCardError = '';

        if (!$this->restaurant->owner->hasFeature('Gift Cards')) {
            $this->giftCardError = 'Fitur Gift Card tidak tersedia.';
            return;
        }

        if (empty($this->giftCardCode)) {
            $this->appliedGiftCard = null;
            return;
        }

        $card = \App\Models\GiftCard::where('restaurant_id', $this->restaurant->id)
            ->where('code', strtoupper($this->giftCardCode))
            ->first();

        if (!$card) {
            $this->giftCardError = 'Kode tidak valid.';
            $this->appliedGiftCard = null;
            return;
        }

        if (!$card->isUsable()) {
            $this->giftCardError = "Kartu tidak dapat digunakan ({$card->status_label}).";
            $this->appliedGiftCard = null;
            return;
        }

        $this->appliedGiftCard = $card->toArray(); 
        $this->giftCardCode = strtoupper($this->giftCardCode);
        
        $this->dispatch('notify', ['type' => 'success', 'message' => "Gift Card berhasil diterapkan."]);
    }

    public function removeGiftCard()
    {
        $this->appliedGiftCard = null;
        $this->giftCardCode = '';
        $this->giftCardError = '';
    }

    // Queue Logic
    public $queueGuestCount = 1;
    public $queueCustomerName = '';
    public $queueCustomerPhone = '';
    public $createdQueue = null;

    public function incrementQueueGuestCount()
    {
        $this->queueGuestCount++;
    }

    public function decrementQueueGuestCount()
    {
        if ($this->queueGuestCount > 1) {
            $this->queueGuestCount--;
        }
    }

    public function takeQueue()
    {
        if (!$this->restaurant->owner->hasFeature('Queue Management System')) {
            $this->dispatch('notify', ['type' => 'danger', 'message' => 'Fitur antrean tidak tersedia.']);
            return;
        }
        $this->currentView = 'queue-form';
    }

    public function submitQueue()
    {
        $this->validate([
            'queueGuestCount' => 'required|integer|min:1',
            'queueCustomerName' => 'nullable|string|min:2',
            'queueCustomerPhone' => 'nullable|min:10',
        ]);

        $prefix = \App\Models\Queue::getPrefixByGuestCount($this->queueGuestCount);
        
        // Get next number for this restaurant and prefix today
        $lastQueue = \App\Models\Queue::where('restaurant_id', $this->restaurant->id)
            ->where('prefix', $prefix)
            ->whereDate('created_at', today())
            ->orderBy('queue_number', 'desc')
            ->first();
            
        $nextNumber = $lastQueue ? $lastQueue->queue_number + 1 : 1;

        $queue = \App\Models\Queue::create([
            'restaurant_id' => $this->restaurant->id,
            'customer_name' => $this->queueCustomerName,
            'customer_phone' => $this->queueCustomerPhone,
            'guest_count' => $this->queueGuestCount,
            'prefix' => $prefix,
            'queue_number' => $nextNumber,
            'status' => 'waiting',
            'source' => 'kiosk',
        ]);

        $this->createdQueue = $queue;
        $this->currentView = 'queue-success';

        // Dispatch status update for Displays (TV)
        event(new \App\Events\QueueUpdated($queue));
    }

    public function startOrder()
    {
        $this->cart = [];
        $this->currentView = 'order-type-selection';
        $this->activeCategory = null; // Default to 'All'
    }

    public function selectOrderType($type)
    {
        $this->orderType = $type;
        
        if ($type === 'dine_in') {
            $this->currentView = 'table-selection';
        } else {
            $this->selectedTable = null;
            $this->currentView = 'menu';
        }
    }

    public function selectTable($tableId = null)
    {
        $this->selectedTable = $tableId;
        $this->currentView = 'menu';
    }

    #[Computed]
    public function tables()
    {
        return \App\Models\Table::where('restaurant_id', $this->restaurant->id)->get();
    }

    #[Computed]
    public function hasAvailableTables()
    {
        return \App\Models\Table::where('restaurant_id', $this->restaurant->id)
            ->where('status', 'available')
            ->exists();
    }

    public function selectCategory($id)
    {
        $this->activeCategory = $id;
        $this->search = '';
    }

    #[Computed]
    public function menuItems()
    {
        $items = MenuItem::getCachedItems($this->restaurant->id);

        if ($this->activeCategory !== null) {
            $items = $items->where('menu_category_id', $this->activeCategory);
        }

        if ($this->search) {
            $search = strtolower($this->search);
            $items = $items->filter(function($item) use ($search) {
                return str_contains(strtolower($item->name), $search);
            });
        }

        return $items;
    }

    // Modal Logic
    public function openItemModal($itemId)
    {
        // Guard: cegah modal terbuka jika stok habis
        $checkItem = MenuItem::find($itemId);
        if ($checkItem && $checkItem->manage_stock && $checkItem->stock_quantity <= 0) {
            $this->dispatch('notify', [
                'type'    => 'error',
                'message' => 'Maaf, stok ' . $checkItem->name . ' sudah habis.',
            ]);
            return;
        }

        $this->stockErrorMessage = '';
        $this->selectedItem = MenuItem::with(['variants', 'addons', 'upsells.upsellItem'])->find($itemId);
        $this->selectedVariant = $this->selectedItem->variants->count() > 0 ? $this->selectedItem->variants->first()->id : null;
        $this->selectedAddons = [];
        $this->quantity = 1;
        $this->note = '';
        $this->bundledItems = [];
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
        if (isset($this->bundledItems[$itemId])) {
            unset($this->bundledItems[$itemId]);
            return;
        }

        $item = MenuItem::with(['variants', 'addons'])->findOrFail($itemId);
        
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
            $discountName = null;
            $discount = $item->getActiveDiscount();
            if ($discount) {
                $discountName = $discount->name;
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
                'discount_name' => $discountName,
                'note' => '',
            ];
        }
    }

    public function openQuickConfig($item)
    {
        if (!($item instanceof MenuItem)) {
            $item = MenuItem::with(['variants', 'addons'])->findOrFail($item);
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
            $stock    = $this->selectedItem->stock_quantity;
            $cartQty  = collect($this->cart)->where('menu_item_id', $this->selectedItem->id)->sum('quantity');
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
        $variant = null;
        $price = $this->selectedItem->price;

        if ($this->selectedVariant) {
            $variant = $this->selectedItem->variants->firstWhere('id', $this->selectedVariant);
            $price = $variant->price;
        }

        $addons = $this->selectedItem->addons->whereIn('id', $this->selectedAddons)->map(function($addon) {
            return ['id' => $addon->id, 'name' => $addon->name, 'price' => $addon->price];
        })->toArray();

        $originalPrice = $price;
        $discountName = null;
        $discount = $this->selectedItem->getActiveDiscount();
        if ($discount) {
            $discountName = $discount->name;
            if ($discount->type === 'percentage') {
                $price = $price - ($price * ($discount->value / 100));
            } else {
                $price = max(0, $price - $discount->value);
            }
        }

        $addonsPrice = collect($addons)->sum('price');
        $unitPrice = $price;
        $totalPrice = ($price + $addonsPrice) * $this->quantity;

        // Check stock
        if ($this->selectedItem->manage_stock) {
            $cartQty = collect($this->cart)->where('menu_item_id', $this->selectedItem->id)->sum('quantity');
            if ($cartQty + $this->quantity > $this->selectedItem->stock_quantity) {
                $this->stockErrorMessage = 'Stok tersisa hanya ' . max(0, $this->selectedItem->stock_quantity - $cartQty) . ' item.';
                return;
            }
        }

        $this->cart[] = [
            'id' => uniqid(),
            'menu_item_id' => $this->selectedItem->id,
            'name' => $this->selectedItem->name,
            'quantity' => $this->quantity,
            'price' => $unitPrice,
            'total_price' => $totalPrice,
            'variant' => $variant ? ['id' => $variant->id, 'name' => $variant->name] : null,
            'addons' => $addons,
            'image' => $this->selectedItem->image,
            'original_price' => $originalPrice,
            'discount_name' => $discountName,
            'note' => $this->note,
        ];

        // Process Bundled Items
        foreach ($this->bundledItems as $bItem) {
            $menuItem = MenuItem::find($bItem['id']);
            if (!$menuItem) continue;

            $this->cart[] = [
                'id' => uniqid(),
                'menu_item_id' => $menuItem->id,
                'name' => $menuItem->name,
                'quantity' => $bItem['quantity'],
                'price' => $bItem['price'],
                'total_price' => $bItem['price'] * $bItem['quantity'],
                'variant' => isset($bItem['variant_id']) ? ['id' => $bItem['variant_id'], 'name' => $bItem['variant_name']] : null,
                'addons' => collect($bItem['addon_ids'])->map(fn($id, $index) => ['id' => $id, 'name' => $bItem['addon_names'][$index], 'price' => 0])->toArray(),
                'image' => $menuItem->image,
                'original_price' => $bItem['original_price'] ?? $bItem['price'],
                'discount_name' => $bItem['discount_name'] ?? null,
                'note' => $bItem['note'] ?? '',
            ];
        }

        $this->closeItemModal();
        $this->dispatch('notify', ['type' => 'success', 'message' => "Ditambahkan ke pesanan."]);
    }

    public function updateQuantity($cartId, $change)
    {
        foreach ($this->cart as &$item) {
            if ($item['id'] === $cartId) {
                $item['quantity'] = max(1, $item['quantity'] + $change);
                
                $unitPrice = $item['price'];
                $addonsPrice = collect($item['addons'] ?? [])->sum('price');
                $item['total_price'] = ($unitPrice + $addonsPrice) * $item['quantity'];
                break;
            }
        }
    }

    public function removeItem($cartId)
    {
        $this->cart = array_values(array_filter($this->cart, fn($item) => $item['id'] !== $cartId));
        if (empty($this->cart) && $this->currentView == 'cart') {
            $this->currentView = 'menu';
        }
    }

    #[Computed]
    public function subtotal()
    {
        return collect($this->cart)->sum('total_price');
    }

    #[Computed]
    public function tax()
    {
        if (!$this->restaurant->tax_enabled) {
            return 0;
        }
        return $this->subtotal * ($this->restaurant->tax_percentage / 100);
    }

    #[Computed]
    public function additionalFees()
    {
        $fees = $this->restaurant->additional_fees ?? [];
        $totalFees = 0;

        foreach ($fees as $fee) {
            if (($fee['type'] ?? '') === 'fixed') {
                $totalFees += $fee['value'];
            } elseif (($fee['type'] ?? '') === 'percentage') {
                $totalFees += ($this->subtotal * ($fee['value'] / 100));
            }
        }
        return $totalFees;
    }

    #[Computed]
    public function pointDiscount()
    {
        if (!$this->usePoints || !$this->pointsToUse) return 0;
        
        $pointValue = $this->restaurant->loyalty_point_redemption_value ?: 0;
        
        return $this->pointsToUse * $pointValue;
    }

    #[Computed]
    public function giftCardDiscount()
    {
        if (!$this->appliedGiftCard) return 0;
        
        $card = \App\Models\GiftCard::find($this->appliedGiftCard['id']);
        if (!$card || !$card->isUsable()) return 0;

        $currentTotal = $this->subtotal + $this->additionalFees + $this->tax - $this->pointDiscount;
        return min((float) $card->remaining_balance, $currentTotal);
    }

    #[Computed]
    public function total()
    {
        $baseTotal = $this->subtotal + $this->additionalFees + $this->tax;
        $final = max(0, $baseTotal - $this->pointDiscount - $this->giftCardDiscount);
        return round($final);
    }

    #[Computed]
    public function cartCount()
    {
        return collect($this->cart)->sum('quantity');
    }

    public function viewCart()
    {
        if (empty($this->cart)) return;
        $this->currentView = 'cart';
    }

    public function viewMenu()
    {
        $this->currentView = 'menu';
        $this->checkoutStep = 1;
    }

    public function goToPayment()
    {
        $this->validate([
            'customerName' => 'required_with:customerPhone|nullable|min:3',
            'customerPhone' => 'nullable|min:10',
        ]);

        $this->checkoutStep = 2;
    }

    public function backToInfo()
    {
        $this->checkoutStep = 1;
    }

    public function processCheckout()
    {
        if (empty($this->cart)) return;

        // Hard lock stock check dengan lockForUpdate — cegah race condition
        try {
            DB::beginTransaction();

            foreach ($this->cart as $item) {
                $menuItem = MenuItem::lockForUpdate()->find($item['menu_item_id']);
                if ($menuItem && $menuItem->manage_stock) {
                    if ($menuItem->stock_quantity < $item['quantity']) {
                        DB::rollBack();
                        $this->dispatch('notify', [
                            'type'    => 'error',
                            'message' => "Maaf, stok '{$item['name']}' tidak mencukupi. Sisa: {$menuItem->stock_quantity} item.",
                        ]);
                        return;
                    }
                }
            }

            // Process Member Registration if opted-in
            if (!$this->isMember && $this->wantsToRegister && $this->customerPhone) {
                $this->member = \App\Models\Member::create([
                    'restaurant_id' => $this->restaurant->id,
                    'name'          => $this->customerName,
                    'whatsapp'      => $this->customerPhone,
                    'points_balance' => 0,
                    'total_spent'   => 0,
                    'tier'          => 'bronze',
                ]);
                $this->isMember = true;
            }

            // --- Loyalty Points Deduction ---
            $pointsUsedThisTime = 0;
            $pointsValueUsedThisTime = 0;
            if ($this->usePoints && $this->pointsToUse > 0 && $this->member) {
                $pointsUsedThisTime = $this->pointsToUse;
                $pointsValueUsedThisTime = $this->pointDiscount;

                // Deduct from member balance
                $this->member->decrement('points_balance', $pointsUsedThisTime);
            }
            // --- End Loyalty Points ---

            $order = Order::create([
                'restaurant_id' => $this->restaurant->id,
                'table_id' => $this->selectedTable,
                'member_id' => $this->member?->id,
                'customer_name' => $this->customerName,
                'customer_phone' => $this->customerPhone,
                'customer_email' => $this->member?->email,
                'status' => 'pending',
                'order_type' => $this->orderType,
                'subtotal' => $this->subtotal,
                'tax_amount' => $this->tax,
                'additional_fees_amount' => $this->additionalFees,
                'additional_fees_details' => $this->restaurant->additional_fees,
                'total_amount' => $this->total,
                'points_used' => $pointsUsedThisTime,
                'points_discount_amount' => $pointsValueUsedThisTime,
                'gift_card_discount_amount' => $this->giftCardDiscount,
                'payment_method' => $this->paymentMethod,
                'payment_status' => 'unpaid',
            ]);

            foreach ($this->cart as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $cartItem['menu_item_id'],
                    'menu_item_variant_id' => $cartItem['variant']['id'] ?? null,
                    'addons' => $cartItem['addons'] ?? [],
                    'quantity' => $cartItem['quantity'],
                    'unit_price' => $cartItem['price'],
                    'total_price' => $cartItem['total_price'],
                    'note' => $cartItem['note'] ?? null,
                ]);
            }

            DB::commit();

            // --- Send Notifications (WhatsApp & Email) ---
            if ($this->customerPhone && $this->restaurant->wa_is_active) {
                \App\Jobs\SendOrderWhatsAppMessage::dispatch($order);
            }

            if ($order->customer_email) {
                \App\Jobs\SendWhitelabelMail::dispatch($this->restaurant, $order->customer_email, new \App\Mail\OrderPlaced($order));
            }

            $this->createdOrder = $order;
            
            // --- Zero Total Handling (Points/Gift Card) ---
            if ($order->total_amount <= 0) {
                $specialMethod = 'loyalty_giftcard';
                if ($order->points_used > 0) $specialMethod = 'points';
                elseif ($order->gift_card_discount_amount > 0) $specialMethod = 'gift_card';

                $order->update([
                    'payment_method' => $specialMethod,
                    'payment_status' => 'paid',
                    'status'         => 'confirmed'
                ]);

                // Also trigger ledger update via controller logic if needed
                try {
                    app(\App\Http\Controllers\MidtransController::class)->handleOrderPayment(
                        $order->id . '-ZERO-' . time(),
                        'settlement',
                        'accept',
                        'points_giftcard',
                        0
                    );
                } catch (\Exception $e) {
                    \Log::error('Kiosk Zero-Total Ledger Error: ' . $e->getMessage());
                }

                $this->currentView = 'success';
                return;
            }

            // Generate Snap Token immediately
            if ($this->paymentMethod === 'midtrans') {
                $midtrans = new \App\Services\MidtransService($this->restaurant);
                $snapToken = $midtrans->createSnapToken($order);
                
                if ($snapToken) {
                    $order->update(['payment_token' => $snapToken]);
                    // Refresh the model so we have the token available to the frontend
                    $this->createdOrder = $order->fresh();
                }
            }

            // --- Gift Card Deduction ---
            if ($this->appliedGiftCard && $this->giftCardDiscount > 0) {
                $freshCard = \App\Models\GiftCard::find($this->appliedGiftCard['id']);
                if ($freshCard && $freshCard->isUsable()) {
                    $freshCard->applyAmount($this->giftCardDiscount, $order->id);
                }
            }
            $this->useGiftCard = false;
            $this->appliedGiftCard = null;
            $this->giftCardCode = '';
            $this->giftCardError = '';
            $this->giftCardDiscountAmount = 0;
            // --- End Gift Card ---

            $this->currentView = 'checkout';
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Kiosk Checkout Error: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Checkout failed.']);
        }
    }

    public function resetKiosk()
    {
        $this->cart = [];
        $this->createdOrder = null;
        $this->createdQueue = null;
        $this->queueCustomerName = '';
        $this->queueCustomerPhone = '';
        $this->queueGuestCount = 1;
        $this->orderType = 'dine_in';
        $this->selectedTable = null;
        $this->currentView = 'welcome';
        $this->checkoutStep = 1;
        $this->useGiftCard = false;
    }

    public function markAsDone()
    {
        // For Cash orders, we just proceed to the success screen without marking as paid.
        // The cashier will handle the actual payment confirmation.
        $this->currentView = 'success';
        $this->dispatch('kiosk-completed');
    }

    public function markAsPaidAndDone()
    {
        // This is primarily for Bypass Demo testing if Midtrans is skipped.
        if ($this->createdOrder && $this->createdOrder->payment_status !== 'paid') {
            try {
                $midtransOrderId   = $this->createdOrder->id . '-KIOSK-' . time();
                $transactionStatus = 'settlement';
                $fraudStatus       = 'accept';
                $paymentType       = 'qris';
                $grossAmount       = (float) $this->createdOrder->total_amount;

                app(\App\Http\Controllers\MidtransController::class)->handleOrderPayment(
                    $midtransOrderId,
                    $transactionStatus,
                    $fraudStatus,
                    $paymentType,
                    $grossAmount
                );
            } catch (\Exception $e) {
                \Log::error('Local Kiosk Success Callback Error: ' . $e->getMessage());
            }
        }
        
        $this->currentView = 'success';
        $this->dispatch('kiosk-completed');
    }

    #[Layout('components.layouts.kiosk')]
    public function render()
    {
        return view('livewire.restaurant.kiosk')->layoutData([
            'title' => 'Self Ordering Kiosk - ' . $this->restaurant->name,
            'restaurant' => $this->restaurant
        ]);
    }
}
