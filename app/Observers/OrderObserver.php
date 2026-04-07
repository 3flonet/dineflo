<?php

namespace App\Observers;

use App\Models\Order;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        // Set Table Status to Occupied
        if ($order->table_id) {
            $order->table->update(['status' => \App\Models\Table::STATUS_OCCUPIED]);
        }
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        $newStatus = $order->status;
        $oldStatus = $order->getOriginal('status');
        $newPaymentStatus = $order->payment_status;
        $oldPaymentStatus = $order->getOriginal('payment_status');

        // 1. Status Changes
        if ($order->isDirty('status')) {
            // Moving FROM Confirmed/Completed TO something else (Restore Stock)
            if (!in_array($newStatus, ['confirmed', 'completed']) && in_array($oldStatus, ['confirmed', 'completed'])) {
                if ($order->is_stock_deducted) {
                    $this->restoreStock($order);
                }
                
                // Revert Loyalty if already processed OR if points were used
                if ($order->is_loyalty_processed || $order->points_used > 0) {
                    $this->revertMemberLoyalty($order);
                }
            }

            // Moving TO Confirmed/Completed FROM something else (Deduct Stock)
            if (in_array($newStatus, ['confirmed', 'completed']) && !in_array($oldStatus, ['confirmed', 'completed'])) {
                if (!$order->is_stock_deducted) {
                    $this->deductMenuItemStock($order);
                    $this->deductIngredientStock($order);
                    
                    // Mark as deducted
                    $order->update(['is_stock_deducted' => true]);
                }
            }

            // Award points if order is completed (and paid)
            if ($newStatus === 'completed' && $oldStatus !== 'completed' && $newPaymentStatus === 'paid') {
                $this->processMemberLoyalty($order);
            }

            // Send Feedback Request if completed
            if ($newStatus === 'completed' && $oldStatus !== 'completed') {
                if ($order->restaurant->wa_is_active && ($order->customer_phone || $order->member?->whatsapp)) {
                    \App\Jobs\SendOrderFeedbackWhatsApp::dispatch($order);
                }
            }
        }

        // 2. Payment Status Changes - Trigger Loyalty immediately when PAID
        if ($order->isDirty('payment_status') && $newPaymentStatus === 'paid') {
            // Award points immediately if payment is success (and order not cancelled)
            if (!in_array($newStatus, ['cancelled', 'failed'])) {
                $this->processMemberLoyalty($order);

                if ($order->customer_email) {
                    \App\Jobs\SendWhitelabelMail::dispatch(
                        $order->restaurant, 
                        $order->customer_email, 
                        new \App\Mail\OrderPlaced($order)
                    );
                }
            }
        }

        // 3. Table Status Sync
        if ($order->table_id && ($order->isDirty('status') || $order->isDirty('payment_status'))) {
            if ($newStatus === 'completed' || $newPaymentStatus === 'paid') {
                $order->table->update(['status' => \App\Models\Table::STATUS_DIRTY]);
            } elseif (in_array($newStatus, ['cancelled', 'failed'])) {
                $order->table->update(['status' => \App\Models\Table::STATUS_AVAILABLE]);
            }
        }
    }

    protected function processMemberLoyalty(Order $order): void
    {
        // Feature Gate
        if (!$order->restaurant->owner->hasFeature('Membership & Loyalty')) {
            return;
        }

        $restaurant = $order->restaurant;
        $member = $order->member;
        if (!$member || $order->is_loyalty_processed) return;

        // Points Rate from Restaurant Settings
        $pointRate = $restaurant->loyalty_point_rate ?: 1000;
        $earnedPoints = floor($order->total_amount / $pointRate);
        
        // Update Points and Spending
        $member->increment('points_balance', $earnedPoints);
        $member->increment('total_spent', $order->total_amount);

        // Mark as processed to prevent double awarding
        $order->update(['is_loyalty_processed' => true]);

        // Tier Logic from Restaurant Thresholds
        $totalSpent = $member->total_spent;
        $newTier = 'bronze';

        if ($totalSpent >= $restaurant->loyalty_gold_threshold) {
            $newTier = 'gold';
        } elseif ($totalSpent >= $restaurant->loyalty_silver_threshold) {
            $newTier = 'silver';
        }

        if ($member->tier !== $newTier) {
            $member->update(['tier' => $newTier]);
        }
    }

    protected function revertMemberLoyalty(Order $order): void
    {
        $restaurant = $order->restaurant;
        $member = $order->member;
        if (!$member) return;

        // 1. Revert Points Earned (if processed)
        if ($order->is_loyalty_processed) {
            $pointRate = $restaurant->loyalty_point_rate ?: 1000;
            $earnedPoints = floor($order->total_amount / $pointRate);

            // Deduct points and spending
            $member->decrement('points_balance', $earnedPoints);
            $member->decrement('total_spent', $order->total_amount);

            // Reset flag
            $order->update(['is_loyalty_processed' => false]);
        }

        // 2. Refund Points Used (Refund points to member if the order is cancelled)
        if ($order->points_used > 0) {
            $member->increment('points_balance', $order->points_used);
            // Mark points as returned in the order record so we don't refund twice if status toggles
            $order->update(['points_used' => 0, 'points_discount_amount' => 0]);
        }

        // 3. Re-check Tier
        $totalSpent = $member->total_spent;
        $newTier = 'bronze';

        if ($totalSpent >= $restaurant->loyalty_gold_threshold) {
            $newTier = 'gold';
        } elseif ($totalSpent >= $restaurant->loyalty_silver_threshold) {
            $newTier = 'silver';
        }

        if ($member->tier !== $newTier) {
            $member->update(['tier' => $newTier]);
        }
    }

    protected function deductMenuItemStock(Order $order): void
    {
        $order->load(['items.menuItem']);

        foreach ($order->items as $item) {
            $menuItem = $item->menuItem;
            if ($menuItem && $menuItem->manage_stock) {
                // Ensure stock doesn't double-deduct if already handled
                $menuItem->decrement('stock_quantity', $item->quantity);
            }
        }
    }

    protected function deductIngredientStock(Order $order): void
    {
        // Auto-deduct for Inventory Level 2 (Recipe Management)
        if (!$order->restaurant->owner->hasFeature('Inventory Level 2')) {
            return;
        }

        $order->load(['items.menuItem.menuItemIngredients.ingredient', 'restaurant.users']);

        $lowStockIngredients = [];

        foreach ($order->items as $item) {
            $menuItem = $item->menuItem;
            
            if ($menuItem && $menuItem->menuItemIngredients->count() > 0) {
                foreach ($menuItem->menuItemIngredients as $recipe) {
                    $ingredient = $recipe->ingredient;
                    if ($ingredient) {
                        // Total deduction = recipe quantity * ordered item quantity
                        $deductionAmount = $recipe->quantity * $item->quantity;
                        $ingredient->adjustStock(
                            quantity: $deductionAmount, 
                            type: 'out', 
                            reason: 'order_deduction', 
                            reference: $order,
                            notes: "Pesanan #{$order->order_number} oleh {$order->customer_name}"
                        );

                        // Check for Low Stock after deduction
                        if ($ingredient->stock <= $ingredient->min_stock_alert) {
                            $lowStockIngredients[$ingredient->id] = $ingredient;
                        }
                    }
                }
            }
        }

        // Send Notifications if any ingredient is low
        if (!empty($lowStockIngredients)) {
            $this->notifyLowStock($order->restaurant, $lowStockIngredients);
        }
    }

    protected function notifyLowStock(\App\Models\Restaurant $restaurant, array $ingredients): void
    {
        $users = $restaurant->users;
        if ($users->isEmpty()) return;

        foreach ($ingredients as $ingredient) {
            \Filament\Notifications\Notification::make()
                ->title('Peringatan: Stok Menipis!')
                ->body("Bahan baku **{$ingredient->name}** tersisa {$ingredient->stock} {$ingredient->unit}. Segera isi ulang!")
                ->danger()
                ->icon('heroicon-o-exclamation-triangle')
                ->sendToDatabase($users);
        }
    }

    protected function restoreStock(Order $order): void
    {
        // Load items with menu items and ingredients to avoid N+1
        $order->load(['items.menuItem.menuItemIngredients.ingredient', 'restaurant.owner']);
        $hasInventoryFeature = $order->restaurant && $order->restaurant->owner && $order->restaurant->owner->hasFeature('Inventory Level 2');

        foreach ($order->items as $item) {
            $menuItem = $item->menuItem;

            if ($menuItem) {
                // 1. Restore Menu Item Stock
                if ($menuItem->manage_stock) {
                    $menuItem->increment('stock_quantity', $item->quantity);
                }

                // 2. Restore Ingredient Stock
                if ($hasInventoryFeature && $menuItem->menuItemIngredients->count() > 0) {
                    foreach ($menuItem->menuItemIngredients as $recipe) {
                        $ingredient = $recipe->ingredient;
                        if ($ingredient) {
                            $restoreAmount = $recipe->quantity * $item->quantity;
                            $ingredient->adjustStock(
                                quantity: $restoreAmount, 
                                type: 'in', 
                                reason: 'order_restore', 
                                reference: $order,
                                notes: "Pembatalan Pesanan #{$order->order_number}"
                            );
                        }
                    }
                }
            }
        }

        // Mark as restored
        $order->update(['is_stock_deducted' => false]);
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        // 1. Restore Stock if it was deducted
        if ($order->is_stock_deducted) {
            $this->restoreStock($order);
        }

        // 2. Revert Loyalty Points if they were awarded
        if ($order->is_loyalty_processed || $order->points_used > 0) {
            $this->revertMemberLoyalty($order);
        }

        // 3. Clear Table Status if it was using this order
        if ($order->table_id && $order->table) {
            $activeOrdersCount = \App\Models\Order::where('table_id', $order->table_id)
                ->whereNotIn('status', ['completed', 'cancelled', 'failed'])
                ->where('id', '!=', $order->id)
                ->count();
            
            if ($activeOrdersCount === 0) {
                $order->table->update(['status' => \App\Models\Table::STATUS_AVAILABLE]);
            }
        }
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
