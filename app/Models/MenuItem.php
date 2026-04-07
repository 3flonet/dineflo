<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use \App\Traits\BelongsToTenant;
    protected $fillable = [
        'restaurant_id',
        'menu_category_id',
        'name',
        'slug',
        'description',
        'price',
        'image',
        'is_available',
        'allergens',
        'manage_stock',
        'stock_quantity',
        'is_reciprocal',
        'prep_time',
        'low_stock_threshold',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'price' => 'decimal:2',
        'allergens' => 'array',
        'is_reciprocal' => 'boolean',
        'manage_stock' => 'boolean',
        'stock_quantity' => 'integer',
        'low_stock_threshold' => 'integer',
        'prep_time' => 'integer',
    ];

    public function getTotalCostAttribute()
    {
        return $this->menuItemIngredients->sum(function ($item) {
            return ($item->ingredient?->cost_per_unit ?? 0) * $item->quantity;
        });
    }

    public function getProfitMarginAttribute()
    {
        $cost = $this->total_cost;
        $effectivePrice = $this->price > 0 ? $this->price : ($this->variants()->min('price') ?? 0);
        if ($effectivePrice <= 0) return 0;
        return (($effectivePrice - $cost) / $effectivePrice) * 100;
    }

    public function getTotalContributionAttribute()
    {
        $effectivePrice = $this->price > 0 ? $this->price : ($this->variants()->min('price') ?? 0);
        $netProfitPerUnit = $effectivePrice - $this->total_cost;
        return $netProfitPerUnit * $this->sold_quantity;
    }

    public function getFormattedPriceAttribute()
    {
        $hasVariants = $this->variants()->count() > 0;
        $displayPrice = $hasVariants ? ($this->variants()->min('price') ?? $this->price) : $this->price;
        
        $discount = $this->getActiveDiscount();
        if ($discount) {
            if ($discount->type === 'percentage') {
                $displayPrice = $displayPrice - ($displayPrice * ($discount->value / 100));
            } else {
                $displayPrice = max(0, $displayPrice - $discount->value);
            }
        }

        return ($hasVariants ? 'Starts from ' : '') . 'Rp ' . number_format((float) $displayPrice, 0, ',', '.');
    }

    /**
     * Cache for active discounts during the request to prevent N+1.
     */
    protected static $requestActiveDiscounts = null;
    protected static $requestBatchInsights = null;
    protected static $requestBatchSoldQuantities = null;

    public function getActiveDiscount()
    {
        // 1. Load active discounts for the restaurant once per request if not loaded
        if (static::$requestActiveDiscounts === null || !isset(static::$requestActiveDiscounts[$this->restaurant_id])) {
            static::$requestActiveDiscounts[$this->restaurant_id] = Discount::where('restaurant_id', $this->restaurant_id)
                ->where('is_active', true)
                ->whereNull('code')
                ->where('target_type', 'all')
                ->with(['menuItems', 'menuCategories'])
                ->get()
                ->filter(fn($d) => $d->isValidNow());
        }

        $activeDiscounts = static::$requestActiveDiscounts[$this->restaurant_id];

        // 2. Check item-specific discounts
        $itemDiscount = $activeDiscounts
            ->where('scope', 'items')
            ->filter(fn($d) => $d->menuItems->contains($this->id))
            ->sortByDesc('value')
            ->first();

        if ($itemDiscount) return $itemDiscount;

        // 3. Check category-specific discounts
        $categoryDiscount = $activeDiscounts
            ->where('scope', 'categories')
            ->filter(fn($d) => $d->menuCategories->contains($this->menu_category_id))
            ->sortByDesc('value')
            ->first();

        if ($categoryDiscount) return $categoryDiscount;

        // 4. Check 'all' scope discounts
        $globalDiscount = $activeDiscounts
            ->where('scope', 'all')
            ->sortByDesc('value')
            ->first();

        return $globalDiscount;
    }

    public function getDiscountedPriceAttribute()
    {
        $hasVariants = $this->variants()->count() > 0;
        $basePrice = $hasVariants ? ($this->variants()->min('price') ?? $this->price) : $this->price;
        
        $discount = $this->getActiveDiscount();
        if (!$discount) return $basePrice;

        if ($discount->type === 'percentage') {
            return $basePrice - ($basePrice * ($discount->value / 100));
        }
        
        return max(0, $basePrice - $discount->value);
    }

    public function getOriginalPriceAttribute()
    {
        $hasVariants = $this->variants()->count() > 0;
        return $hasVariants ? ($this->variants()->min('price') ?? $this->price) : $this->price;
    }

    public function getHasActiveDiscountAttribute()
    {
        return $this->getActiveDiscount() !== null;
    }

    /**
     * Menu Engineering Insights
     * Categorizes item into Star, Plowhorse, Puzzle, or Dog
     */
    public function getMenuInsightAttribute()
    {
        // Use batch if available
        if (static::$requestBatchInsights !== null && isset(static::$requestBatchInsights[$this->id])) {
            return static::$requestBatchInsights[$this->id];
        }

        // Fallback for single record or dev (slow)
        $batch = static::getBatchMenuInsights($this->restaurant_id);
        static::$requestBatchInsights = (static::$requestBatchInsights ?? []) + $batch;
        
        return static::$requestBatchInsights[$this->id] ?? 'dog';
    }

    public function getMenuInsightLabelAttribute()
    {
        return match($this->menu_insight) {
            'star' => ['label' => 'Star (Bintang)', 'color' => 'success', 'icon' => '⭐', 'desc' => 'Populer & Untung Tinggi'],
            'plowhorse' => ['label' => 'Plowhorse (Kuda Beban)', 'color' => 'info', 'icon' => '🐎', 'desc' => 'Populer tapi Untung Rendah'],
            'puzzle' => ['label' => 'Puzzle (Teka-teki)', 'color' => 'warning', 'icon' => '🧩', 'desc' => 'Untung Tinggi tapi Kurang Populer'],
            'dog' => ['label' => 'Dog', 'color' => 'danger', 'icon' => '🐕', 'desc' => 'Kurang Populer & Untung Rendah'],
        };
    }

    public function getSoldQuantityAttribute()
    {
        if (static::$requestBatchSoldQuantities !== null && isset(static::$requestBatchSoldQuantities[$this->id])) {
            return static::$requestBatchSoldQuantities[$this->id];
        }

        // Fallback for single record
        $batch = static::getBatchSoldQuantities($this->restaurant_id)->toArray();
        static::$requestBatchSoldQuantities = (static::$requestBatchSoldQuantities ?? []) + $batch;

        return static::$requestBatchSoldQuantities[$this->id] ?? 0;
    }


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            if (!$item->slug) {
                $item->slug = \Illuminate\Support\Str::slug($item->name);
            }
        });

        static::saved(function ($item) {
            $item->clearMenuCache();
        });

        static::deleted(function ($item) {
            $item->clearMenuCache();
        });
    }

    public function clearMenuCache()
    {
        \Illuminate\Support\Facades\Cache::forget("restaurant_{$this->restaurant_id}_menu_categories");
        \Illuminate\Support\Facades\Cache::forget("restaurant_{$this->restaurant_id}_menu_items");
        // Clear batch analytics cache
        \Illuminate\Support\Facades\Cache::forget("batch_sold_res_{$this->restaurant_id}_days_30");
        \Illuminate\Support\Facades\Cache::forget("avg_sold_res_{$this->restaurant_id}");
        \Illuminate\Support\Facades\Cache::forget("batch_insights_res_{$this->restaurant_id}");
    }

    // ─── Batch Methods (Anti N+1) ─────────────────────────────────────────────

    /**
     * Ambil total sold quantity untuk SEMUA item di satu restoran dalam 1 query.
     * Gunakan ini di halaman/controller yang menampilkan banyak item sekaligus.
     *
     * @param int $restaurantId
     * @param int $days Periode hari ke belakang (default: 30)
     * @return \Illuminate\Support\Collection keyed by menu_item_id
     */
    public static function getBatchSoldQuantities(int $restaurantId, int $days = 30): \Illuminate\Support\Collection
    {
        $cacheKey = "batch_sold_res_{$restaurantId}_days_{$days}";

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addMinutes(15), function () use ($restaurantId, $days) {
            return \Illuminate\Support\Facades\DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('orders.restaurant_id', $restaurantId)
                ->whereIn('orders.payment_status', ['paid', 'partial'])
                ->where('orders.created_at', '>=', now()->subDays($days))
                ->where('order_items.is_refunded', false)
                ->groupBy('order_items.menu_item_id')
                ->select(
                    'order_items.menu_item_id',
                    \Illuminate\Support\Facades\DB::raw('SUM(order_items.quantity) as total_sold')
                )
                ->pluck('total_sold', 'menu_item_id');
        });
    }

    /**
     * Ambil insight (star/plowhorse/puzzle/dog) untuk SEMUA item sekaligus.
     * Hanya 2-3 query untuk seluruh restoran, bukan 1 query per item.
     *
     * @param int $restaurantId
     * @return array<int, string> keyed by menu_item_id => 'star'|'plowhorse'|'puzzle'|'dog'
     */
    public static function getBatchMenuInsights(int $restaurantId): array
    {
        $cacheKey = "batch_insights_res_{$restaurantId}";

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addMinutes(15), function () use ($restaurantId) {
            $soldData       = static::getBatchSoldQuantities($restaurantId);
            $avgSold        = $soldData->count() > 0 ? $soldData->avg() : 1;
            $marginThreshold = 40;

            // Ambil semua items dengan relasi cost sekaligus (1 query)
            $items = static::where('restaurant_id', $restaurantId)
                ->with(['menuItemIngredients.ingredient', 'variants'])
                ->get();

            $insights = [];
            foreach ($items as $item) {
                $sold       = (float) ($soldData->get($item->id, 0));
                $isPopular  = $sold >= $avgSold;

                // Hitung cost dari relasi yang sudah di-load (tidak ada query tambahan)
                $cost = $item->menuItemIngredients->sum(
                    fn ($r) => ($r->ingredient?->cost_per_unit ?? 0) * $r->quantity
                );

                $effectivePrice = $item->price > 0
                    ? (float) $item->price
                    : (float) ($item->variants->min('price') ?? 0);

                $margin        = $effectivePrice > 0
                    ? (($effectivePrice - $cost) / $effectivePrice) * 100
                    : 0;
                $isProfitable  = $margin >= $marginThreshold;

                $insights[$item->id] = match (true) {
                    $isPopular && $isProfitable  => 'star',
                    $isPopular && !$isProfitable => 'plowhorse',
                    !$isPopular && $isProfitable => 'puzzle',
                    default                      => 'dog',
                };
            }

            return $insights;
        });
    }

    public static function getCachedCategories($restaurantId)
    {
        return \Illuminate\Support\Facades\Cache::remember(
            "restaurant_{$restaurantId}_menu_categories",
            now()->addHours(24),
            fn() => MenuCategory::where('restaurant_id', $restaurantId)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
        );
    }

    public static function getCachedItems($restaurantId)
    {
        return \Illuminate\Support\Facades\Cache::remember(
            "restaurant_{$restaurantId}_menu_items",
            now()->addHours(24),
            fn() => MenuItem::where('restaurant_id', $restaurantId)
                ->where('is_available', true)
                ->with(['variants', 'addons'])
                ->get()
        );
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function category()
    {
        return $this->belongsTo(MenuCategory::class, 'menu_category_id');
    }

    public function variants()
    {
        return $this->hasMany(MenuItemVariant::class);
    }

    public function addons()
    {
        return $this->hasMany(MenuItemAddon::class);
    }

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'menu_item_ingredients')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    public function menuItemIngredients()
    {
        return $this->hasMany(MenuItemIngredient::class);
    }

    public function upsells()
    {
        return $this->hasMany(MenuItemUpsell::class, 'menu_item_id');
    }
}
