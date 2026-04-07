<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'restaurant_id',
        'name',
        'unit',
        'stock',
        'cost_per_unit',
        'min_stock_alert',
        'bulk_purchase_price',
        'bulk_quantity',
        'purchase_unit',
        'bulk_unit_type',
        'conversion_factor',
        'description',
    ];

    protected $casts = [
        'stock' => 'decimal:2',
        'cost_per_unit' => 'decimal:2',
        'min_stock_alert' => 'decimal:2',
        'bulk_purchase_price' => 'decimal:2',
        'bulk_quantity' => 'decimal:2',
        'purchase_unit' => 'string',
        'bulk_unit_type' => 'string',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function movements()
    {
        return $this->hasMany(IngredientStockMovement::class);
    }

    /**
     * Metadata for the observer to log movements
     */
    public $movementMetadata = null;

    /**
     * Adjust stock and prepare metadata for the observer
     */
    public function adjustStock($quantity, $type, $reason, $reference = null, $notes = null, $userId = null, $newUnitPrice = null)
    {
        $this->movementMetadata = [
            'type' => $type,
            'reason' => $reason,
            'quantity' => $quantity,
            'reference_type' => $reference ? get_class($reference) : null,
            'reference_id' => $reference ? $reference->id : null,
            'notes' => $notes,
            'user_id' => $userId ?? auth()->id(),
            'newUnitPrice' => $newUnitPrice,
        ];

        if ($type === 'in') {
            // Weighted Average Cost (WAC) Calculation if price is provided
            if ($newUnitPrice !== null && $newUnitPrice > 0) {
                $currentStock = (float) $this->stock;
                $currentCost = (float) $this->cost_per_unit;
                $incomingQty = (float) $quantity;
                $incomingPrice = (float) $newUnitPrice;

                // Formula: ((Old Qty * Old Price) + (New Qty * New Price)) / (Old Qty + New Qty)
                $newTotalValue = ($currentStock * $currentCost) + ($incomingQty * $incomingPrice);
                $newTotalQty = $currentStock + $incomingQty;

                if ($newTotalQty > 0) {
                    $this->cost_per_unit = $newTotalValue / $newTotalQty;
                }
            }
            
            $this->stock += $quantity;
            $this->save();
        } elseif ($type === 'out') {
            $this->stock -= $quantity;
            $this->save();
        } elseif ($type === 'adjustment') {
            $this->stock = $quantity;
            $this->save();
        }
    }
}
