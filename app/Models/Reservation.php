<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory, BelongsToTenant, \App\Traits\NormalizesPhone;
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($reservation) {
            $reservation->tracking_hash = strtolower(\Illuminate\Support\Str::random(12));
        });

        static::saving(function ($reservation) {
            if ($reservation->phone) {
                $reservation->phone = $reservation->normalizePhoneNumber($reservation->phone);
            }
        });
    }

    protected $guarded = [];

    protected $casts = [
        'reservation_time' => 'datetime',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }
}
