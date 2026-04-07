<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use \App\Traits\BelongsToTenant;

    protected $fillable = [
        'restaurant_id',
        'name',
        'code',
        'type',
        'value',
        'scope',
        'target_type',
        'target_tiers',
        'is_active',
        'is_recurring',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'days_of_week',
        'min_order_amount',
        'usage_limit',
        'total_usage',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_recurring' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'days_of_week' => 'array',
        'target_tiers' => 'array',
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function menuCategories()
    {
        return $this->belongsToMany(MenuCategory::class, 'discount_menu_category');
    }

    public function menuItems()
    {
        return $this->belongsToMany(MenuItem::class, 'discount_menu_item');
    }

    /**
     * Check if the discount is currently valid based on schedule and time
     */
    public function isValidNow(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();

        // 1. Check Date Range (if not recurring)
        if (!$this->is_recurring) {
            if ($this->start_date && $now->startOfDay()->lt($this->start_date->startOfDay())) {
                return false;
            }
            if ($this->end_date && $now->startOfDay()->gt($this->end_date->startOfDay())) {
                return false;
            }
        }

        // 2. Check Days of Week (Happy Hour Recurring)
        if ($this->is_recurring && is_array($this->days_of_week) && count($this->days_of_week) > 0) {
            $currentDay = $now->englishDayOfWeek; // e.g. "Monday"
            if (!in_array($currentDay, $this->days_of_week)) {
                return false;
            }
        }

        // 3. Check Time Range (Happy Hour)
        if ($this->start_time && $this->end_time) {
            $currentTime = $now->format('H:i:s');
            if ($currentTime < $this->start_time || $currentTime > $this->end_time) {
                return false;
            }
        }

        return true;
    }
}
