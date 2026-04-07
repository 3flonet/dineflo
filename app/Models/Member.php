<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\TenancyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    use HasFactory, TenancyScope, \App\Traits\NormalizesPhone;

    protected static function booted()
    {
        static::saving(function ($member) {
            // Normalize WhatsApp number
            if ($member->whatsapp) {
                $member->whatsapp = $member->normalizePhoneNumber($member->whatsapp);
            }

            $restaurant = $member->restaurant;
            
            if (!$restaurant) return;

            if ($member->total_spent >= $restaurant->loyalty_gold_threshold) {
                $member->tier = 'gold';
            } elseif ($member->total_spent >= $restaurant->loyalty_silver_threshold) {
                $member->tier = 'silver';
            } else {
                $member->tier = 'bronze';
            }
        });
    }

    protected $casts = [
        'birthday' => 'date',
    ];

    protected $fillable = [
        'restaurant_id',
        'name',
        'whatsapp',
        'email',
        'birthday',
        'points_balance',
        'total_spent',
        'tier',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
