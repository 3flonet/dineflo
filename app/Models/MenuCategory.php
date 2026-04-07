<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuCategory extends Model
{
    use \App\Traits\BelongsToTenant;
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (!$category->slug) {
                $category->slug = \Illuminate\Support\Str::slug($category->name);
            }
        });

        static::saved(function ($category) {
            $category->clearMenuCache();
        });

        static::deleted(function ($category) {
            $category->clearMenuCache();
        });
    }

    public function clearMenuCache()
    {
        \Illuminate\Support\Facades\Cache::forget("restaurant_{$this->restaurant_id}_menu_categories");
        \Illuminate\Support\Facades\Cache::forget("restaurant_{$this->restaurant_id}_menu_items");
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
