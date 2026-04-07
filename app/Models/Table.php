<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use \App\Traits\BelongsToTenant;
    protected $fillable = [
        'restaurant_id',
        'name',
        'capacity',
        'qr_code',
        'is_active',
        'status',
        'description',
    ];

    const STATUS_AVAILABLE = 'available';
    const STATUS_OCCUPIED = 'occupied';
    const STATUS_DIRTY = 'dirty';
    const STATUS_RESERVED = 'reserved';

    public static function getStatuses()
    {
        return [
            self::STATUS_AVAILABLE => [
                'label' => 'Available',
                'color' => 'success',
                'bg' => 'bg-green-100',
                'text' => 'text-green-700',
                'icon' => 'heroicon-o-check-circle',
            ],
            self::STATUS_OCCUPIED => [
                'label' => 'Occupied',
                'color' => 'danger',
                'bg' => 'bg-red-100',
                'text' => 'text-red-700',
                'icon' => 'heroicon-o-user-group',
            ],
            self::STATUS_DIRTY => [
                'label' => 'Dirty',
                'color' => 'warning',
                'bg' => 'bg-amber-100',
                'text' => 'text-amber-700',
                'icon' => 'heroicon-o-sparkles',
            ],
            self::STATUS_RESERVED => [
                'label' => 'Reserved',
                'color' => 'info',
                'bg' => 'bg-blue-100',
                'text' => 'text-blue-700',
                'icon' => 'heroicon-o-calendar-days',
            ],
        ];
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($table) {
            if (empty($table->qr_code)) {
                $table->qr_code = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function getUrlAttribute()
    {
        return $this->restaurant ? url('restaurant/' . $this->restaurant->slug . '/table/' . $this->qr_code) : '#';
    }
}
