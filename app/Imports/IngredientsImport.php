<?php

namespace App\Imports;

use App\Models\Ingredient;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Filament\Facades\Filament;

class IngredientsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Skip if name is empty
        if (empty($row['name'])) {
            return null;
        }

        $restaurantId = Filament::getTenant()->id;

        $ingredient = Ingredient::firstOrNew([
            'restaurant_id' => $restaurantId,
            'name' => $row['name'],
        ]);

        $isNew = !$ingredient->exists;
        $newStock = $row['current_stock'] ?? 0;

        if ($ingredient->stock != $newStock) {
            $ingredient->movementMetadata = [
                'type' => $isNew ? 'in' : 'adjustment',
                'reason' => $isNew ? 'initial' : 'adjustment',
                'notes' => 'Diperbarui melalui Import Excel.',
            ];
        }

        $ingredient->fill([
            'unit' => $row['unit'] ?? 'gram',
            'cost_per_unit' => $row['cost_per_unit'] ?? 0,
            'stock' => $newStock,
            'min_stock_alert' => $row['min_stock_alert'] ?? 0,
            'bulk_purchase_price' => $row['bulk_purchase_price'] ?? null,
            'bulk_quantity' => $row['bulk_quantity'] ?? null,
            'purchase_unit' => $row['purchase_unit'] ?? null,
            'bulk_unit_type' => $row['bulk_unit_type'] ?? null,
        ]);

        return $ingredient;
    }
}
