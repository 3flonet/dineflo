<?php

namespace App\Observers;

use App\Models\MenuItem;

class MenuItemObserver
{
    /**
     * Handle the MenuItem "created" event.
     */
    public function created(MenuItem $menuItem): void
    {
        //
    }

    /**
     * Handle the MenuItem "updated" event.
     */
    public function updated(MenuItem $menuItem): void
    {
        if ($menuItem->manage_stock && $menuItem->isDirty('stock_quantity')) {
            $currentStock = $menuItem->stock_quantity;
            $previousStock = $menuItem->getOriginal('stock_quantity');
            $threshold = $menuItem->low_stock_threshold;

            // Only notify when stock drops AT or BELOW threshold, AND was previously ABOVE threshold
            // This prevents spamming notifications on every decrement below threshold
            if ($currentStock <= $threshold && $previousStock > $threshold) {
                
                // Find recipients
                $recipients = collect();

                // 1. Owner
                // Ensure relationship exists or load explicitly
                $restaurant = $menuItem->restaurant;
                if ($restaurant && $restaurant->user_id) {
                     $owner = \App\Models\User::find($restaurant->user_id);
                     if ($owner) $recipients->push($owner);
                }

                // 2. Staff
                $staffRoles = \Spatie\Permission\Models\Role::whereIn('name', ['staff', 'kitchen', 'manager'])->get();
                $staff = collect();
                
                if ($staffRoles->isNotEmpty()) {
                    $staff = \App\Models\User::role($staffRoles)
                                ->where('restaurant_id', $menuItem->restaurant_id)
                                ->get();
                }
                
                $recipients = $recipients->merge($staff)->unique('id');

                if ($recipients->isNotEmpty()) {
                    \Filament\Notifications\Notification::make()
                        ->title('Low Stock Alert')
                        ->body("Item **{$menuItem->name}** is running low ({$currentStock} left).")
                        ->warning()
                        ->actions([
                            // Link to edit page (might need specific route handling for multi-tenancy)
                            \Filament\Notifications\Actions\Action::make('view')
                                ->label('Update Stock')
                                ->url('/restaurants/' . $restaurant->slug . '/menu-items/' . $menuItem->id . '/edit') 
                                ->button(),
                        ])
                        ->sendToDatabase($recipients);
                }
            }
        }
    }

    /**
     * Handle the MenuItem "deleted" event.
     */
    public function deleted(MenuItem $menuItem): void
    {
        //
    }

    /**
     * Handle the MenuItem "restored" event.
     */
    public function restored(MenuItem $menuItem): void
    {
        //
    }

    /**
     * Handle the MenuItem "force deleted" event.
     */
    public function forceDeleted(MenuItem $menuItem): void
    {
        //
    }
}
