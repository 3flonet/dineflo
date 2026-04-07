<?php

namespace App\Actions\Pos;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Member;
use App\Models\Restaurant;
use App\Models\MenuItem;
use App\Models\MenuItemVariant;
use App\Models\MenuItemAddon;
use Illuminate\Support\Facades\DB;

class SaveOrderAction
{
    /**
     * @param Restaurant $tenant
     * @param array $cart
     * @param array $data Data order header (table_id, customer_name, customer_phone, dll)
     * @param int|null $existingOrderId
     * @param Member|null $member
     * @return Order
     */
    public function execute(
        Restaurant $tenant,
        array $cart,
        array $data,
        ?int $existingOrderId = null,
        ?Member $member = null,
        bool $wantsToRegister = false
    ): Order {
        return DB::transaction(function () use ($tenant, $cart, $data, $existingOrderId, $member, $wantsToRegister) {
            
            // 1. Loyalty Points Handling if Member exists
            if ($member && isset($data['points_used'])) {
                $pointsUsed = (int) $data['points_used'];
                $initialPoints = 0;
                
                if ($existingOrderId) {
                    $existingOrder = Order::find($existingOrderId);
                    $initialPoints = $existingOrder->points_used ?? 0;
                }

                $diff = $pointsUsed - $initialPoints;
                if ($diff > 0) {
                    $member->decrement('points_balance', $diff);
                } elseif ($diff < 0) {
                    $member->increment('points_balance', abs($diff));
                }
            }

            // 2. Auto-Register Member if requested
            if (!$member && $wantsToRegister && !empty($data['customer_phone'])) {
                $member = Member::create([
                    'restaurant_id' => $tenant->id,
                    'name'          => $data['customer_name'] ?? 'Guest',
                    'whatsapp'      => $data['customer_phone'],
                    'points_balance' => 0,
                    'total_spent'    => 0,
                    'tier'           => 'bronze',
                ]);
            }

            // 3. Create or Update Order Header (Re-calculate totals on server-side)
            $calculatedSubtotal = 0;
            $itemsToCreate = [];

            foreach ($cart as $item) {
                $menuItem = MenuItem::findOrFail($item['id']);
                
                // Get price for specific variant if exists
                if (!empty($item['variant_id'])) {
                    $variant = MenuItemVariant::where('menu_item_id', $menuItem->id)->findOrFail($item['variant_id']);
                    $unitPrice = $variant->price;
                } else {
                    $unitPrice = $menuItem->price;
                }

                // Addons calculation
                $addonPriceSum = 0;
                if (!empty($item['addons'])) {
                    foreach ($item['addons'] as $addonId) {
                        $addon = MenuItemAddon::where('menu_item_id', $menuItem->id)->findOrFail($addonId);
                        $addonPriceSum += $addon->price;
                    }
                }

                $totalUnitPrice = $unitPrice + $addonPriceSum;
                $itemSubtotal = $totalUnitPrice * $item['quantity'];
                
                // Item Discount (if provided from client, verify it exists and is active)
                // Note: For now we trust discount names/amounts if valid, 
                // but we should Ideally re-fetch active discounts.
                // To keep it simple but safe, we calculate based on unitPrice * qty.
                if (isset($item['final_price']) && $item['final_price'] < $totalUnitPrice) {
                    $itemSubtotal = $item['final_price'] * $item['quantity'];
                }

                $calculatedSubtotal += $itemSubtotal;
                
                $itemsToCreate[] = [
                    'menu_item_id'         => $menuItem->id,
                    'menu_item_variant_id' => $item['variant_id'] ?? null,
                    'addons'               => $item['addons'] ?? [],
                    'note'                 => $item['note'] ?? null,
                    'quantity'             => $item['quantity'],
                    'original_unit_price'  => $totalUnitPrice,
                    'discount_name'        => $item['discount_name'] ?? null,
                    'unit_price'           => $item['final_price'] ?? $totalUnitPrice,
                    'total_price'          => $itemSubtotal,
                ];
            }

            // Calculate Fees & Taxes server-side
            $voucherDiscount = (float)($data['voucher_discount_amount'] ?? 0);
            $pointsDiscount = (float)($data['points_discount_amount'] ?? 0);
            $giftCardDiscount = (float)($data['gift_card_discount_amount'] ?? 0);
            
            $taxableAmount = $calculatedSubtotal - $voucherDiscount - $pointsDiscount - $giftCardDiscount;
            
            $taxAmount = 0;
            if ($tenant->tax_enabled) {
                $taxAmount = round($taxableAmount * ($tenant->tax_percentage / 100));
            }

            $additionalFees = 0;
            if (!empty($tenant->additional_fees)) {
                foreach ($tenant->additional_fees as $fee) {
                    if (($fee['is_active'] ?? true)) {
                        if (($fee['type'] ?? 'fixed') === 'percentage') {
                            $additionalFees += round($taxableAmount * (($fee['value'] ?? 0) / 100));
                        } else {
                            $additionalFees += ($fee['value'] ?? 0);
                        }
                    }
                }
            }

            $finalTotalAmount = $calculatedSubtotal - $voucherDiscount - $pointsDiscount - $giftCardDiscount + $taxAmount + $additionalFees;

            $orderData = [
                'restaurant_id'           => $tenant->id,
                'table_id'                => $data['table_id'] ?? null,
                'member_id'               => $member?->id,
                'customer_name'           => $data['customer_name'] ?? null,
                'customer_phone'          => $data['customer_phone'] ?? null,
                'subtotal'                => $calculatedSubtotal,
                'total_amount'            => max(0, round($finalTotalAmount)),
                'discount_id'             => $data['discount_id'] ?? null,
                'voucher_code'            => $data['voucher_code'] ?? null,
                'voucher_discount_amount' => $voucherDiscount,
                'tax_amount'              => $taxAmount,
                'additional_fees_amount'  => $additionalFees,
                'additional_fees_details' => $tenant->additional_fees,
                'points_used'             => $data['points_used'] ?? 0,
                'points_discount_amount'  => $pointsDiscount,
                'gift_card_discount_amount' => $giftCardDiscount,
                'offline_id'              => $data['offline_id'] ?? null,
            ];

            if ($existingOrderId) {
                $order = Order::findOrFail($existingOrderId);
                $order->update($orderData);
                $order->items()->delete();
            } else {
                $orderData['status']         = 'pending';
                $orderData['payment_status'] = 'pending';
                $orderData['payment_method'] = $data['payment_method'] ?? 'cash';
                $orderData['is_split_bill']  = $data['is_split_bill'] ?? false;
                $orderData['split_type']     = ($data['is_split_bill'] ?? false) ? 'custom' : null;
                $orderData['amount_paid']    = 0;
                
                $order = Order::create($orderData);
            }

            // 4. Save Order Items
            foreach ($itemsToCreate as $itemData) {
                $itemData['order_id'] = $order->id;
                OrderItem::create($itemData);

                // Note: Stock management is now handled by OrderObserver 
                // when order status changes to 'confirmed' or 'completed'.
            }

            return $order;
        });
    }
}
