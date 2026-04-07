<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ingredient;
use App\Models\IngredientStockMovement;
use App\Models\Restaurant;
use App\Models\User;

class InventoryL2DemoSeeder extends Seeder
{
    public function run(): void
    {
        $restaurant = Restaurant::first();
        if (!$restaurant) return;

        $staff = User::where('restaurant_id', $restaurant->id)->first() ?? User::first();

        // 1. Create/Update Ingredients with varied stock levels
        $ingredients = [
            [
                'name' => 'Beras Pandan Wangi', 
                'unit' => 'gram', 
                'stock' => 50000, 
                'min' => 10000, 
                'cost' => 14, // 700.000 / 50.000 gram
                'bulk_purchase_price' => 700000,
                'bulk_quantity' => 50,
                'purchase_unit' => 'Karung',
                'bulk_unit_type' => 'kg'
            ],
            [
                'name' => 'Daging Sapi Slice', 
                'unit' => 'gram', 
                'stock' => 5000, 
                'min' => 2000, 
                'cost' => 110, // 550.000 / 5.000 gram
                'bulk_purchase_price' => 550000,
                'bulk_quantity' => 5,
                'purchase_unit' => 'Pack',
                'bulk_unit_type' => 'kg'
            ],
            [
                'name' => 'Minyak Goreng', 
                'unit' => 'ml', 
                'stock' => 5000, 
                'min' => 2000, 
                'cost' => 15, // 75.000 / 5.000 ml
                'bulk_purchase_price' => 75000,
                'bulk_quantity' => 5,
                'purchase_unit' => 'Jerigen',
                'bulk_unit_type' => 'liter'
            ],
            [
                'name' => 'Bawang Bombay', 
                'unit' => 'gram', 
                'stock' => 5000, 
                'min' => 1000, 
                'cost' => 26, // 130.000 / 5.000 gram
                'bulk_purchase_price' => 130000,
                'bulk_quantity' => 5,
                'purchase_unit' => 'Waring',
                'bulk_unit_type' => 'kg'
            ],
            [
                'name' => 'Kopi Arabica Gayo', 
                'unit' => 'gram', 
                'stock' => 1000, 
                'min' => 500, 
                'cost' => 250, // 250.000 / 1.000 gram
                'bulk_purchase_price' => 250000,
                'bulk_quantity' => 1,
                'purchase_unit' => 'Pack',
                'bulk_unit_type' => 'kg'
            ],
            [
                'name' => 'Susu Full Cream', 
                'unit' => 'ml', 
                'stock' => 12000, 
                'min' => 3000, 
                'cost' => 19, // 190.000 / 10.000 ml (10 Liter)
                'bulk_purchase_price' => 190000,
                'bulk_quantity' => 10,
                'purchase_unit' => 'Karton',
                'bulk_unit_type' => 'liter'
            ],
            [
                'name' => 'Telur Ayam', 
                'unit' => 'pcs', 
                'stock' => 300, 
                'min' => 50, 
                'cost' => 1800, // 54.000 / 30 pcs (1 Tray)
                'bulk_purchase_price' => 54000,
                'bulk_quantity' => 30,
                'purchase_unit' => 'Tray',
                'bulk_unit_type' => 'pcs'
            ],
        ];

        foreach ($ingredients as $data) {
            $ing = Ingredient::updateOrCreate(
                ['restaurant_id' => $restaurant->id, 'name' => $data['name']],
                [
                    'unit' => $data['unit'],
                    'stock' => $data['stock'],
                    'min_stock_alert' => $data['min'],
                    'cost_per_unit' => $data['cost'],
                    'bulk_purchase_price' => $data['bulk_purchase_price'],
                    'bulk_quantity' => $data['bulk_quantity'],
                    'purchase_unit' => $data['purchase_unit'],
                    'bulk_unit_type' => $data['bulk_unit_type'],
                ]
            );

            // Create some random movements for the heatmap/consumption
            for ($i = 0; $i < 5; $i++) {
                IngredientStockMovement::create([
                    'restaurant_id' => $restaurant->id,
                    'ingredient_id' => $ing->id,
                    'user_id' => $staff->id,
                    'type' => 'out',
                    'quantity' => rand(1, 5),
                    'before_quantity' => $ing->stock + 5,
                    'after_quantity' => $ing->stock,
                    'reason' => 'order_deduction',
                    'created_at' => now()->subDays(rand(1, 25)),
                ]);
            }

            // Create some wastage
            if (rand(0, 1)) {
                IngredientStockMovement::create([
                    'restaurant_id' => $restaurant->id,
                    'ingredient_id' => $ing->id,
                    'user_id' => $staff->id,
                    'type' => 'out',
                    'quantity' => rand(0, 1),
                    'before_quantity' => $ing->stock + 1,
                    'after_quantity' => $ing->stock,
                    'reason' => 'waste',
                    'notes' => 'Bahan busuk / kedaluwarsa',
                    'created_at' => now()->subDays(2),
                ]);
            }
        }

        $this->command->info('✅ Inventory L2 demo data seeded!');
    }
}
