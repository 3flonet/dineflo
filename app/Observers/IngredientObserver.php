<?php

namespace App\Observers;

use App\Models\Ingredient;

class IngredientObserver
{
    /**
     * Handle the Ingredient "created" event.
     */
    public function created(Ingredient $ingredient): void
    {
        // Log initial stock if > 0
        if ($ingredient->stock > 0) {
            $ingredient->movements()->create([
                'restaurant_id' => $ingredient->restaurant_id,
                'user_id' => auth()->id(),
                'type' => 'in',
                'quantity' => $ingredient->stock,
                'before_quantity' => 0,
                'after_quantity' => $ingredient->stock,
                'reason' => 'initial',
                'notes' => 'Stok awal saat pendaftaran bahan baku.',
            ]);
        }
    }

    /**
     * Handle the Ingredient "updated" event.
     */
    public function updated(Ingredient $ingredient): void
    {
        if ($ingredient->isDirty('stock')) {
            $before = $ingredient->getOriginal('stock');
            $after = $ingredient->stock;
            $diff = $after - $before;

            if ($diff == 0) return;

            $meta = $ingredient->movementMetadata;
            
            $ingredient->movements()->create([
                'restaurant_id' => $ingredient->restaurant_id,
                'user_id' => $meta['user_id'] ?? auth()->id(),
                'type' => $meta['type'] ?? ($diff > 0 ? 'in' : 'out'),
                'quantity' => $meta['quantity'] ?? abs($diff),
                'unit_price' => $meta['newUnitPrice'] ?? $ingredient->cost_per_unit,
                'before_quantity' => $before,
                'after_quantity' => $after,
                'reason' => $meta['reason'] ?? 'adjustment',
                'reference_type' => $meta['reference_type'] ?? null,
                'reference_id' => $meta['reference_id'] ?? null,
                'notes' => $meta['notes'] ?? null,
            ]);
        }
    }

    /**
     * Handle the Ingredient "deleted" event.
     */
    public function deleted(Ingredient $ingredient): void
    {
        //
    }

    /**
     * Handle the Ingredient "restored" event.
     */
    public function restored(Ingredient $ingredient): void
    {
        //
    }

    /**
     * Handle the Ingredient "force deleted" event.
     */
    public function forceDeleted(Ingredient $ingredient): void
    {
        //
    }
}
