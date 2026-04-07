<?php

namespace App\Filament\Restaurant\Pages;

use Filament\Pages\Page;
use App\Models\Ingredient;
use App\Models\IngredientStockMovement;
use App\Models\MenuItem;
use Illuminate\Support\Facades\DB;

class InventoryAnalytics extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-bar';

    protected static string $view = 'filament.restaurant.pages.inventory-analytics';

    protected static ?string $title = 'Analisis Stok & Inventori';

    protected static ?string $navigationLabel = 'Analisis Stok';

    protected static ?string $navigationGroup = 'STOK & INVENTORI';

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return auth()->user()->can('page_InventoryAnalytics') && auth()->user()->hasFeature('Inventory Level 2');
    }

    protected function getViewData(): array
    {
        $tenantId = \Filament\Facades\Filament::getTenant()->id;

        // 1. Ingredients to Restock (Low Stock Alert)
        $lowStockItems = Ingredient::where('restaurant_id', $tenantId)
            ->whereColumn('stock', '<=', 'min_stock_alert')
            ->orderBy('stock')
            ->get();

        // 2. Top Consumed Ingredients (Last 30 Days)
        $topConsumption = IngredientStockMovement::where('restaurant_id', $tenantId)
            ->where('type', 'out')
            ->where('reason', 'order_deduction')
            ->where('created_at', '>=', now()->subDays(30))
            ->select('ingredient_id', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('ingredient_id')
            ->orderByDesc('total_qty')
            ->with(['ingredient' => function ($query) {
                // Eager load untuk item_ingredients to avoid N+1 in sum
                $query->select('id', 'name', 'unit', 'cost_per_unit');
            }])
            ->limit(10)
            ->get();

        // 3. Wastage Analysis (Total Value of Waste/Breakage)
        $wastageData = IngredientStockMovement::where('restaurant_id', $tenantId)
            ->where('type', 'out')
            ->whereIn('reason', ['waste', 'breakage', 'expired']) // Added expired as wastage
            ->where('created_at', '>=', now()->subDays(30))
            ->with('ingredient')
            ->get()
            ->sum(function ($movement) {
                return $movement->quantity * ($movement->ingredient?->cost_per_unit ?? 0);
            });

        // 4. Batch Analytics for Menu Items (OPTIMIZED)
        // Menggunakan static methods yang sudah kita buat sebelumnya di MenuItem model
        $batchInsights = MenuItem::getBatchMenuInsights($tenantId);
        $soldData      = MenuItem::getBatchSoldQuantities($tenantId);
        
        // Ambil semua menu items dengan relasi yang diperlukan dalam satu query
        $menuItems = MenuItem::where('restaurant_id', $tenantId)
            ->with(['menuItemIngredients.ingredient', 'variants'])
            ->get();

        $highCostMenus = $menuItems->map(function ($menu) use ($batchInsights, $soldData) {
            // Hitung margin secara manual di sini untuk konsistensi atau gunakan batchInsights
            $cost = $menu->menuItemIngredients->sum(fn($r) => ($r->ingredient?->cost_per_unit ?? 0) * $r->quantity);
            $price = $menu->price > 0 ? $menu->price : ($menu->variants->min('price') ?? 0);
            $margin = $price > 0 ? (($price - $cost) / $price) * 100 : 0;
            
            return [
                'name'    => $menu->name,
                'cost'    => $cost,
                'price'   => $price,
                'margin'  => round($margin, 1),
                'sold'    => $soldData->get($menu->id, 0),
                'insight' => $batchInsights[$menu->id] ?? 'dog',
            ];
        })
        ->sortByDesc('cost')
        ->take(10);

        return [
            'lowStockItems'  => $lowStockItems,
            'totalIngredientsCount' => Ingredient::where('restaurant_id', $tenantId)->count(),
            'topConsumption' => $topConsumption,
            'wastageValue'   => $wastageData,
            'highCostMenus'  => $highCostMenus,
            // Opsional: kirim insights lengkap ke view jika diperlukan
            'menuInsights'   => $batchInsights,
        ];
    }
}
