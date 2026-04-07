<?php

namespace App\Http\Controllers\Api\Pos;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Restaurant;
use App\Models\Table;
use Illuminate\Http\Request;

class PosInitController extends Controller
{
    public function init(Request $request)
    {
        $user = $request->user();
        $restaurantId = $user->restaurant_id;

        if (!$restaurantId) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak terikat dengan restoran manapun.'
            ], 403);
        }

        $restaurant = Restaurant::with('owner')->find($restaurantId);

        // 1. Categories
        $categories = MenuCategory::where('restaurant_id', $restaurantId)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'image']);

        // 2. Menu Items (v1: Basic, later we can add variants/addons)
        $menuItems = MenuItem::where('restaurant_id', $restaurantId)
            ->where('is_active', true)
            ->with(['variants', 'addons', 'discounts' => function($q) {
                $q->active();
            }])
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'category_id' => $item->menu_category_id,
                    'name' => $item->name,
                    'description' => $item->description,
                    'price' => (float) $item->price,
                    'image' => $item->image ? asset('storage/' . $item->image) : null,
                    'manage_stock' => $item->manage_stock,
                    'stock_quantity' => $item->stock_quantity,
                    'variants' => $item->variants,
                    'addons' => $item->addons,
                    'active_discount' => $item->getActiveDiscount(),
                ];
            });

        // 3. Tables
        $tables = Table::where('restaurant_id', $restaurantId)
            ->where('is_active', true)
            ->get(['id', 'name', 'capacity', 'status']);

        // 4. Restaurant Settings
        $settings = [
            'name' => $restaurant->name,
            'address' => $restaurant->address,
            'phone' => $restaurant->phone,
            'currency' => 'IDR',
            'tax_enabled' => $restaurant->tax_enabled,
            'tax_percentage' => (float) $restaurant->tax_percentage,
            'additional_fees' => $restaurant->additional_fees ?? [],
            'wa_is_active' => $restaurant->wa_is_active,
            'features' => [
                'membership' => $restaurant->owner->hasFeature('Membership & Loyalty'),
                'cash_drawer' => $restaurant->owner->hasFeature('Cash Drawer Integration'),
                'refund' => $restaurant->owner->hasFeature('Refund Handling'),
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'restaurant' => $settings,
                'categories' => $categories,
                'menu_items' => $menuItems,
                'tables' => $tables,
            ]
        ]);
    }
}
