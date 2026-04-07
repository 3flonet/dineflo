<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Restaurant;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\MenuItemVariant;
use App\Models\MenuItemIngredient;
use App\Models\Ingredient;
use Illuminate\Support\Str;

class ComprehensiveMenuSeeder extends Seeder
{
    public function run(): void
    {
        $restaurant = Restaurant::first();
        if (!$restaurant) return;

        // 1. Categories
        $categories = [
            ['name' => 'Rice Bowls', 'slug' => 'rice-bowls'],
            ['name' => 'Signature Coffee', 'slug' => 'signature-coffee'],
            ['name' => 'Western Favs', 'slug' => 'western-favs'],
            ['name' => 'Indonesian Special', 'slug' => 'indonesian-special'],
        ];

        $catModels = [];
        foreach ($categories as $cat) {
            $catModels[$cat['slug']] = MenuCategory::updateOrCreate(
                ['restaurant_id' => $restaurant->id, 'slug' => $cat['slug']],
                ['name' => $cat['name'], 'is_active' => true]
            );
        }

        // 2. Ingredients Mapping
        $ingMap = Ingredient::where('restaurant_id', $restaurant->id)->pluck('id', 'name');

        // 3. Menus Implementation
        
        // --- BEEF RICE BOWL (With Variants) ---
        $beefRiceBowl = MenuItem::updateOrCreate(
            ['restaurant_id' => $restaurant->id, 'name' => 'Beef Rice Bowl'],
            [
                'slug' => 'beef-rice-bowl',
                'menu_category_id' => $catModels['rice-bowls']->id,
                'description' => 'Sliced beef served over warm rice with onions.',
                'price' => 0, // Using variants
                'is_available' => true,
            ]
        );

        // Variants for Beef Rice Bowl
        $variants = [
            ['name' => 'Small 250', 'price' => 25000],
            ['name' => 'Medium 300', 'price' => 27000],
            ['name' => 'Large 350', 'price' => 30000],
        ];

        foreach ($variants as $v) {
            MenuItemVariant::updateOrCreate(
                ['menu_item_id' => $beefRiceBowl->id, 'name' => $v['name']],
                ['price' => $v['price']]
            );
        }

        // Recipe for Beef Rice Bowl
        $beefRecipe = [
            ['name' => 'Daging Sapi Slice', 'qty' => 250],
            ['name' => 'Bawang Bombay', 'qty' => 150],
            ['name' => 'Beras Pandan Wangi', 'qty' => 150],
        ];

        foreach ($beefRecipe as $r) {
            if (isset($ingMap[$r['name']])) {
                MenuItemIngredient::updateOrCreate(
                    ['menu_item_id' => $beefRiceBowl->id, 'ingredient_id' => $ingMap[$r['name']]],
                    ['quantity' => $r['qty']]
                );
            }
        }

        // --- ICED PALM SUGAR LATTE ---
        $latte = MenuItem::updateOrCreate(
            ['restaurant_id' => $restaurant->id, 'name' => 'Iced Palm Sugar Latte'],
            [
                'slug' => 'iced-palm-sugar-latte',
                'menu_category_id' => $catModels['signature-coffee']->id,
                'description' => 'Premium Arabica with fresh milk and palm sugar.',
                'price' => 22000,
                'is_available' => true,
            ]
        );

        $latteRecipe = [
            ['name' => 'Kopi Arabica Gayo', 'qty' => 18],
            ['name' => 'Susu Full Cream', 'qty' => 150],
        ];

        foreach ($latteRecipe as $r) {
            if (isset($ingMap[$r['name']])) {
                MenuItemIngredient::updateOrCreate(
                    ['menu_item_id' => $latte->id, 'ingredient_id' => $ingMap[$r['name']]],
                    ['quantity' => $r['qty']]
                );
            }
        }

        // --- NASI GORENG SPESIAL ---
        $nasgor = MenuItem::updateOrCreate(
            ['restaurant_id' => $restaurant->id, 'name' => 'Nasi Goreng Spesial'],
            [
                'slug' => 'nasi-goreng-spesial',
                'menu_category_id' => $catModels['indonesian-special']->id,
                'description' => 'Fried rice with egg and special seasoning.',
                'price' => 25000,
                'is_available' => true,
            ]
        );

        $nasgorRecipe = [
            ['name' => 'Beras Pandan Wangi', 'qty' => 200],
            ['name' => 'Telur Ayam', 'qty' => 1],
            ['name' => 'Minyak Goreng', 'qty' => 20],
        ];

        foreach ($nasgorRecipe as $r) {
            if (isset($ingMap[$r['name']])) {
                MenuItemIngredient::updateOrCreate(
                    ['menu_item_id' => $nasgor->id, 'ingredient_id' => $ingMap[$r['name']]],
                    ['quantity' => $r['qty']]
                );
            }
        }

        $this->command->info('✅ Comprehensive Menu data seeded!');
    }
}
