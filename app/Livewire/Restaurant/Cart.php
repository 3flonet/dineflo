<?php

namespace App\Livewire\Restaurant;

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

class Cart extends Component
{
    use \App\Traits\NormalizesPhone;
    public \App\Models\Restaurant $restaurant;
    public $cart = [];
    public $customerName = '';
    public $customerPhone = '';
    public $customerEmail = ''; // Added
    public $notes = '';
    public $table = null;
    public $stockErrors = []; // Inline error per cart item, keyed by cartId
    public $quickConfigErrorMessage = ''; // Error inline untuk modal upsell
    public $quickConfigNote = '';

    // Quick Config Logic (Upsells)
    public $quickConfigItem = null;
    public $quickConfigVariant = null;
    public $quickConfigAddons = [];
    public $quickConfigQuantity = 1;

    // Member Logic
    public $isMember = false;
    public $member = null;
    public $wantsToRegister = false;

    // Voucher Logic
    public $voucherCode = '';
    public $appliedVoucher = null;
    public $voucherError = '';

    // Loyalty Points Logic
    public $usePoints = false;
    public $pointsToUse = 0;

    // Gift Card Logic
    public $giftCardCode = '';
    public $appliedGiftCard = null;
    public $giftCardError = '';
    public $giftCardDiscount = 0; // Nominal rupiah yang akan dipotong

    public function updatedPointsToUse($value)
    {
        if (!$this->isMember || !$this->member) {
            $this->pointsToUse = 0;
            return;
        }

        $maxPoints = $this->member->points_balance;
        
        $pointValue = $this->restaurant->loyalty_point_redemption_value ?: 0;
        
        if ($pointValue > 0) {
            $currentTotalBeforePoints = max(0, $this->subtotal - $this->voucherDiscount) + $this->additionalFees + $this->tax;
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
        // Only allow member detection if owner has the feature
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
                $this->customerEmail = $this->member->email;
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

    public function mount(\App\Models\Restaurant $restaurant)
    {
        $this->restaurant = $restaurant;
        $this->cart = session()->get('cart.' . $this->restaurant->id, []);

        if (session()->has('dineflo.table_id')) {
            $this->table = \App\Models\Table::find(session('dineflo.table_id'));
        }

        $paymentMode = $this->restaurant->payment_mode ?? 'kasir';
        if ($paymentMode === 'gateway') {
            $this->paymentMethod = 'midtrans';
        } else {
            $this->paymentMethod = 'cash';
        }
    }

    public function removeItem($cartId)
    {
        $this->cart = array_values(array_filter($this->cart, fn($item) => $item['id'] !== $cartId));
        unset($this->stockErrors[$cartId]); // Clear error saat item dihapus
        session()->put('cart.' . $this->restaurant->id, $this->cart);
    }

    public function clearStockError($cartId)
    {
        if ($cartId === 'quickConfig') {
            $this->quickConfigErrorMessage = '';
        } else {
            unset($this->stockErrors[$cartId]);
        }
    }

    public function updateQuantity($cartId, $change)
    {
        foreach ($this->cart as &$item) {
            if ($item['id'] === $cartId) {
                $newQty = max(1, $item['quantity'] + $change);

                // Stock check saat nambah qty
                if ($change > 0) {
                    $menuItem = \App\Models\MenuItem::find($item['menu_item_id']);
                    if ($menuItem && $menuItem->manage_stock) {
                        $totalCartQty = collect($this->cart)
                            ->where('menu_item_id', $item['menu_item_id'])
                            ->sum('quantity');
                        if ($totalCartQty + 1 > $menuItem->stock_quantity) {
                            $this->stockErrors[$cartId] = 'Stok ' . $menuItem->name . ' tersisa ' . $menuItem->stock_quantity . ' item.';
                            return;
                        }
                    }
                }

                // Clear error saat berhasil update atau decrement
                unset($this->stockErrors[$cartId]);

                $item['quantity'] = $newQty;
                $unitPrice   = $item['price'];
                $addonsPrice = collect($item['addons'] ?? [])->sum('price');
                $item['total_price'] = ($unitPrice + $addonsPrice) * $item['quantity'];
                break;
            }
        }
        session()->put('cart.' . $this->restaurant->id, $this->cart);
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
    public function voucherDiscount()
    {
        if (!$this->appliedVoucher) return 0;
        
        $discount = 0;
        $activeSubtotal = $this->subtotal;

        // If voucher applies to specific items/categories, we need to filter
        if ($this->appliedVoucher->scope !== 'all') {
            $eligibleTotal = collect($this->cart)->filter(function($item) {
                if ($this->appliedVoucher->scope === 'items') {
                    return in_array($item['menu_item_id'], $this->appliedVoucher->menuItems->pluck('id')->toArray());
                }
                if ($this->appliedVoucher->scope === 'categories') {
                    $menuItem = \App\Models\MenuItem::find($item['menu_item_id']);
                    return in_array($menuItem->menu_category_id, $this->appliedVoucher->menuCategories->pluck('id')->toArray());
                }
                return false;
            })->sum('total_price');
            
            $activeSubtotal = $eligibleTotal;
        }

        if ($this->appliedVoucher->type === 'percentage') {
            $discount = $activeSubtotal * ($this->appliedVoucher->value / 100);
        } else {
            $discount = min($activeSubtotal, $this->appliedVoucher->value);
        }

        return $discount;
    }

    #[Computed]
    public function pointDiscount()
    {
        if (!$this->usePoints || !$this->pointsToUse) return 0;
        
        $pointValue = $this->restaurant->loyalty_point_redemption_value ?: 0;
        
        return $this->pointsToUse * $pointValue;
    }

    #[Computed]
    public function total()
    {
        $baseTotal = max(0, $this->subtotal - $this->voucherDiscount) + $this->additionalFees + $this->tax;
        $finalValue = max(0, $baseTotal - $this->pointDiscount - $this->giftCardDiscount);
        return round($finalValue);
    }

    public function applyGiftCard()
    {
        $this->giftCardError = '';

        if (!$this->restaurant->owner->hasFeature('Gift Cards')) {
            $this->giftCardError = 'Fitur Gift Card tidak tersedia di paket langganan ini.';
            return;
        }

        if (empty($this->giftCardCode)) {
            $this->appliedGiftCard = null;
            $this->giftCardDiscount = 0;
            return;
        }

        $card = \App\Models\GiftCard::where('restaurant_id', $this->restaurant->id)
            ->where('code', strtoupper(trim($this->giftCardCode)))
            ->first();

        if (!$card) {
            $this->giftCardError = 'Kode Gift Card tidak ditemukan.';
            $this->appliedGiftCard = null;
            $this->giftCardDiscount = 0;
            return;
        }

        if (!$card->isUsable()) {
            $status = $card->status_label;
            $this->giftCardError = "Gift Card ini tidak dapat digunakan (status: {$status}).";
            $this->appliedGiftCard = null;
            $this->giftCardDiscount = 0;
            return;
        }

        // Apply: max discount = min(saldo GC, total saat ini)
        $currentTotal = max(0, $this->subtotal - $this->voucherDiscount) + $this->additionalFees + $this->tax - $this->pointDiscount;
        $this->giftCardDiscount = min((float) $card->remaining_balance, $currentTotal);
        $this->appliedGiftCard  = $card;

        $this->dispatch('notify', [
            'type'    => 'success',
            'message' => 'Gift Card ' . strtoupper($this->giftCardCode) . ' berhasil dipasang! Potongan: Rp ' . number_format($this->giftCardDiscount, 0, ',', '.'),
        ]);
    }

    public function removeGiftCard()
    {
        $this->appliedGiftCard  = null;
        $this->giftCardCode     = '';
        $this->giftCardError    = '';
        $this->giftCardDiscount = 0;
    }

    public function applyVoucher()
    {
        $this->voucherError = '';
        
        if (auth()->check() && !auth()->user()->can('apply_voucher')) {
            $this->voucherError = 'Anda tidak memiliki izin untuk memasang voucher.';
            return;
        }

        if (!$this->restaurant->owner->hasFeature('Voucher & Marketing')) {
            $this->voucherError = 'Fitur voucher tidak tersedia di paket langganan ini.';
            return;
        }

        if (empty($this->voucherCode)) {
            $this->appliedVoucher = null;
            return;
        }

        $voucher = \App\Models\Discount::where('restaurant_id', $this->restaurant->id)
            ->where('code', strtoupper($this->voucherCode))
            ->where('is_active', true)
            ->first();

        if (!$voucher) {
            $this->voucherError = 'Kode voucher tidak valid atau sudah kedaluwarsa.';
            $this->appliedVoucher = null;
            return;
        }

        // 1. Validity Check (Time/Date)
        if (!$voucher->isValidNow()) {
            $this->voucherError = 'Voucher ini tidak dapat digunakan saat ini.';
            $this->appliedVoucher = null;
            return;
        }

        // 2. Minimum Order Check
        if ($this->subtotal < $voucher->min_order_amount) {
            $this->voucherError = 'Minimal belanja untuk voucher ini adalah Rp ' . number_format($voucher->min_order_amount, 0, ',', '.');
            $this->appliedVoucher = null;
            return;
        }

        // 3. Usage Limit Check
        if ($voucher->usage_limit && $voucher->total_usage >= $voucher->usage_limit) {
            $this->voucherError = 'Maaf, kuota voucher ini sudah habis.';
            $this->appliedVoucher = null;
            return;
        }

        // 4. Target Audience Check
        if ($voucher->target_type !== 'all') {
            if (!$this->isMember) {
                $this->voucherError = 'Voucher ini khusus untuk member. Silakan masukkan nomor WA terdaftar.';
                $this->appliedVoucher = null;
                return;
            }

            if ($voucher->target_type === 'tiers_only') {
                $memberTier = strtolower($this->member->tier ?? 'bronze');
                $eligibleTiers = array_map('strtolower', $voucher->target_tiers ?? []);
                if (!in_array($memberTier, $eligibleTiers)) {
                    $this->voucherError = 'Voucher ini hanya untuk member tier: ' . implode(', ', $eligibleTiers);
                    $this->appliedVoucher = null;
                    return;
                }
            }
        }

        $this->appliedVoucher = $voucher;
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Voucher ' . strtoupper($this->voucherCode) . ' berhasil dipasang!']);
    }

    public function removeVoucher()
    {
        $this->appliedVoucher = null;
        $this->voucherCode = '';
        $this->voucherError = '';
    }

    #[Computed]
    public function upsellRecommendations()
    {
        if (empty($this->cart)) return collect();
        if (!$this->restaurant->owner?->hasFeature('Smart Upselling')) return collect();

        // Get IDs of items already in cart to exclude them from suggestions
        $cartItemIds = collect($this->cart)->pluck('menu_item_id')->toArray();

        // Find all upsells for items currently in cart
        $upsells = \App\Models\MenuItemUpsell::whereIn('menu_item_id', $cartItemIds)
            ->whereNotIn('upsell_item_id', $cartItemIds) // Don't suggest items already in cart
            ->with(['upsellItem' => function ($query) {
                // Ensure the suggested item is available and visible
                $query->where('is_available', true);
            }])
            ->get()
            ->pluck('upsellItem')
            ->filter() // Remove nulls in case the item wasn't loaded
            ->unique('id')
            ->take(3); // Suggest max 3 items

        return $upsells;
    }

    public function addUpsellItem($menuItemId)
    {
        $menuItem = \App\Models\MenuItem::with(['variants', 'addons'])->find($menuItemId);
        if (!$menuItem || !$menuItem->is_available) return;

        // Server-side stock guard
        if ($menuItem->manage_stock) {
            $cartQty = collect($this->cart)->where('menu_item_id', $menuItemId)->sum('quantity');
            if ($cartQty + 1 > $menuItem->stock_quantity) {
                $this->dispatch('notify', [
                    'type'    => 'error',
                    'message' => 'Maaf, stok ' . $menuItem->name . ' hanya tersisa ' . max(0, $menuItem->stock_quantity - $cartQty) . '.',
                ]);
                return;
            }
        }

        // If it has variants or addons, open quick config modal instead of direct adding
        if ($menuItem->variants->count() > 0 || $menuItem->addons->count() > 0) {
            $this->quickConfigItem = $menuItem;
            $this->quickConfigVariant = $menuItem->variants->first()?->id;
            $this->quickConfigAddons = [];
            $this->quickConfigQuantity = 1;
            $this->quickConfigNote = '';
            $this->quickConfigErrorMessage = '';
            return;
        }

        // Direct add if no variants/addons
        $itemPrice = $menuItem->price;
        $discountName = null;
        $discount = $menuItem->getActiveDiscount();
        if ($discount) {
            $discountName = $discount->name;
            if ($discount->type === 'percentage') {
                $itemPrice = $itemPrice - ($itemPrice * ($discount->value / 100));
            } else {
                $itemPrice = max(0, $itemPrice - $discount->value);
            }
        }

        $cartId = uniqid();
        $this->cart[] = [
            'id' => $cartId,
            'menu_item_id' => $menuItem->id,
            'name' => $menuItem->name,
            'quantity' => 1,
            'price' => $itemPrice,
            'total_price' => $itemPrice,
            'original_price' => $menuItem->price,
            'discount_name' => $discountName,
            'variant' => null,
            'addons' => [],
        ];

        session()->put('cart.' . $this->restaurant->id, $this->cart);
        $this->dispatch('notify', ['type' => 'success', 'message' => "{$menuItem->name} berhasil ditambahkan!"]);
    }

    public function saveQuickConfig()
    {
        if (!$this->quickConfigItem) return;

        // Stock validation for quick config
        if ($this->quickConfigItem->manage_stock) {
            $cartQty = collect($this->cart)->where('menu_item_id', $this->quickConfigItem->id)->sum('quantity');
            if ($cartQty + $this->quickConfigQuantity > $this->quickConfigItem->stock_quantity) {
                $this->quickConfigErrorMessage = 'Stok tersisa hanya ' . max(0, $this->quickConfigItem->stock_quantity - $cartQty) . ' item.';
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
        $totalPrice = $unitPrice * $this->quickConfigQuantity;

        $cartId = uniqid();
        $this->cart[] = [
            'id' => $cartId,
            'menu_item_id' => $this->quickConfigItem->id,
            'name' => $this->quickConfigItem->name,
            'quantity' => $this->quickConfigQuantity,
            'price' => $itemPrice, // Price without addons
            'original_price' => $originalPrice,
            'discount_name' => $discountName,
            'total_price' => $totalPrice, // Price including addons * qty
            'variant' => $variant ? ['id' => $variant->id, 'name' => $variant->name, 'price' => $variant->price] : null,
            'addons' => $addons->map(fn($a) => ['id' => $a->id, 'name' => $a->name, 'price' => $a->price])->toArray(),
            'image' => $this->quickConfigItem->image,
            'note' => $this->quickConfigNote,
        ];

        session()->put('cart.' . $this->restaurant->id, $this->cart);
        $this->dispatch('notify', ['type' => 'success', 'message' => "{$this->quickConfigItem->name} berhasil ditambahkan!"]);
        
        $this->quickConfigItem = null;
    }

    public function cancelQuickConfig()
    {
        $this->quickConfigItem = null;
        $this->quickConfigNote = '';
        $this->quickConfigErrorMessage = '';
    }

    public function incrementQuantity($prop = 'quickConfigQuantity')
    {
        if ($prop === 'quickConfigQuantity' && $this->quickConfigItem && $this->quickConfigItem->manage_stock) {
            $stock = $this->quickConfigItem->stock_quantity;
            $cartQty = collect($this->cart)->where('menu_item_id', $this->quickConfigItem->id)->sum('quantity');
            $maxAllowed = max(0, $stock - $cartQty);
            if ($this->$prop >= $maxAllowed) {
                $this->quickConfigErrorMessage = 'Stok tersisa hanya ' . $stock . ' item.';
                return;
            }
        }
        
        if ($prop === 'quickConfigQuantity') $this->quickConfigErrorMessage = '';
        $this->$prop++;
    }

    public function decrementQuantity($prop = 'quickConfigQuantity')
    {
        if ($prop === 'quickConfigQuantity') $this->quickConfigErrorMessage = '';

        if ($this->$prop > 1) {
            $this->$prop--;
        }
    }

    public $paymentMethod = 'cash'; // cash, midtrans

    public function checkout()
    {
        $this->validate([
            'customerName' => 'required_with:customerPhone|nullable|min:3|max:100',
            'customerPhone' => 'nullable|min:10|max:16',
            'customerEmail' => 'nullable|email|max:100', // Added validation
            'paymentMethod' => 'required|in:cash,midtrans',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Rate Limiting: Max 5 checkout attempts per minute
        $key = 'checkout:' . request()->ip();
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($key);
            $this->dispatch('notify', [
                'type'    => 'error',
                'message' => "Terlalu banyak permintaan. Silakan coba lagi dalam $seconds detik.",
            ]);
            return;
        }

        // Guard for online orders (no table context)
        if (!$this->table && !$this->restaurant->is_online_order_enabled) {
            $this->dispatch('notify', [
                'type'    => 'error',
                'message' => 'Maaf, pemesanan online saat ini sedang ditutup.',
            ]);
            return;
        }

        if (empty($this->cart)) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Your cart is empty.']);
            return;
        }

        // Sanitization
        $cleanName = strip_tags($this->customerName ?: '');
        $cleanNotes = strip_tags($this->notes ?: '');

        // Validate & Lock Stock (Hard check dengan lockForUpdate di dalam transaction)
        // Race condition guard: hanya satu request bisa lanjut untuk stok terakhir
        try {
            DB::beginTransaction();

            foreach ($this->cart as $item) {
                $menuItem = \App\Models\MenuItem::lockForUpdate()->find($item['menu_item_id']);
                if ($menuItem && $menuItem->manage_stock) {
                    if ($menuItem->stock_quantity < $item['quantity']) {
                        DB::rollBack();
                        $this->dispatch('notify', [
                            'type'    => 'error',
                            'message' => "Maaf, stok '" . $item['name'] . "' tidak mencukupi. Sisa stok: " . $menuItem->stock_quantity . " item.",
                        ]);
                        return;
                    }
                }
            }

            // Process Member Registration if opted-in
            if (!$this->isMember && $this->wantsToRegister && $this->customerPhone) {
                $this->member = \App\Models\Member::create([
                    'restaurant_id' => $this->restaurant->id,
                    'name'          => $cleanName,
                    'whatsapp'      => $this->customerPhone,
                    'email'         => $this->customerEmail,
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

            $order = \App\Models\Order::create([
                'restaurant_id' => $this->restaurant->id,
                'table_id' => $this->table?->id,
                'member_id' => $this->member?->id,
                'customer_name' => $cleanName,
                'customer_phone' => $this->customerPhone,
                'customer_email' => $this->customerEmail,
                'status' => 'pending',
                'subtotal' => $this->subtotal,
                'tax_amount' => $this->tax,
                'additional_fees_amount' => $this->additionalFees,
                'additional_fees_details' => $this->restaurant->additional_fees,
                'total_amount' => $this->total,
                'voucher_discount_amount' => $this->voucherDiscount,
                'discount_id' => $this->appliedVoucher?->id,
                'voucher_code' => $this->appliedVoucher?->code,
                'points_used' => $pointsUsedThisTime,
                'points_discount_amount' => $pointsValueUsedThisTime,
                'gift_card_discount_amount' => $this->giftCardDiscount,
                'notes' => $cleanNotes,
                'payment_method' => $this->paymentMethod,
                'payment_status' => $this->total <= 0 ? 'paid' : 'unpaid',
                'status'         => $this->total <= 0 ? 'confirmed' : 'pending',
            ]);

            \Illuminate\Support\Facades\RateLimiter::hit($key, 60);

            if ($order->total_amount <= 0) {
                $specialMethod = 'loyalty_giftcard';
                if ($order->points_used > 0) $specialMethod = 'points';
                elseif ($order->gift_card_discount_amount > 0) $specialMethod = 'gift_card';
                elseif ($order->voucher_discount_amount > 0) $specialMethod = 'voucher';

                $order->update(['payment_method' => $specialMethod]);

                try {
                    app(\App\Http\Controllers\MidtransController::class)->handleOrderPayment(
                        $order->id . '-ZERO-' . time(),
                        'settlement',
                        'accept',
                        'points_giftcard',
                        0
                    );
                } catch (\Exception $e) {
                    \Log::error('Cart Zero-Total Ledger Error: ' . $e->getMessage());
                }
            }

            foreach ($this->cart as $cartItem) {
                \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $cartItem['menu_item_id'],
                    'menu_item_variant_id' => $cartItem['variant']['id'] ?? null,
                    'addons' => $cartItem['addons'] ?? [],
                    'quantity' => $cartItem['quantity'],
                    'original_unit_price' => $cartItem['original_price'] ?? $cartItem['price'],
                    'discount_name' => $cartItem['discount_name'] ?? null,
                    'unit_price' => $cartItem['price'],
                    'total_price' => $cartItem['total_price'],
                    'note' => $cartItem['note'] ?? null,
                ]);
            }

            if ($this->appliedVoucher) {
                $this->appliedVoucher->increment('total_usage');
            }

            // --- Gift Card Deduction ---
            if ($this->appliedGiftCard && $this->giftCardDiscount > 0) {
                $freshCard = \App\Models\GiftCard::lockForUpdate()->find($this->appliedGiftCard->id);
                if ($freshCard && $freshCard->isUsable()) {
                    $freshCard->applyAmount($this->giftCardDiscount, $order->id);
                }
            }
            // --- End Gift Card ---

            DB::commit();

            // Clear session cart
            session()->forget('cart.' . $this->restaurant->id);
            session()->save();

            // --- Send Notifications (WhatsApp & Email) ---
            if ($this->customerPhone && $this->restaurant->wa_is_active) {
                \App\Jobs\SendOrderWhatsAppMessage::dispatch($order);
            }

            if ($this->customerEmail) {
                \App\Jobs\SendWhitelabelMail::dispatch($this->restaurant, $this->customerEmail, new \App\Mail\OrderPlaced($order));
            }

            $targetUrl = route('order.summary', ['order' => $order->id]);

            $this->dispatch('checkout-completed', url: $targetUrl);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Checkout failed: ' . $e->getMessage()]);
            return;
        }
    }

    private function constructWhatsAppMessage($order)
    {
        $message = "Halo *" . $this->restaurant->name . "*,\n";
        $message .= "Saya ingin memesan:\n\n";

        foreach ($this->cart as $item) {
            $variant = isset($item['variant']) ? " (" . $item['variant']['name'] . ")" : "";
            $addons = !empty($item['addons']) ? "\n  + " . implode(", ", array_column($item['addons'], 'name')) : "";
            
            $itemNote = !empty($item['note']) ? "\n  (Catatan: " . $item['note'] . ")" : "";
            
            $message .= "- " . $item['quantity'] . "x " . $item['name'] . $variant . $addons . $itemNote . "\n";
        }

        $message .= "\nTotal: *Rp " . number_format($order->total_amount, 0, ',', '.') . "*\n";
        $message .= "----------------\n";
        $message .= "Nama: " . $this->customerName . "\n";
        $message .= "Order ID: #" . $order->order_number . "\n";
        $message .= "Pembayaran: " . ($this->paymentMethod == 'midtrans' ? 'Online (Pending)' : 'Cash/Tunai / Kasir') . "\n";
        
        if ($this->table) {
            $message .= "Meja: " . $this->table->name . " (" . $this->table->area . ")\n";
        }
        
        if ($this->notes) {
            $message .= "Catatan: " . $this->notes . "\n";
        }
        
        return $message;
    }

    #[Layout('components.layouts.app', ['title' => 'Checkout'])]
    public function render()
    {
        return view('livewire.restaurant.cart', [
            'total' => $this->total, 
        ])->layoutData(['restaurant' => $this->restaurant]);
    }
}
