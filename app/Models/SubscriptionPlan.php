<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'features'     => 'array',
        'limits'       => 'array',
        'price'        => 'decimal:2',
        'yearly_price' => 'decimal:2',
        'duration_days'=> 'integer',
        'is_active'    => 'boolean',
        'is_highlighted'=> 'boolean',
        'is_trial'     => 'boolean',
        'has_yearly'   => 'boolean',
        'billing_period'=> 'string',
    ];

    /**
     * Hitung persentase hemat jika berlangganan tahunan vs bulanan.
     * Contoh: monthly=175.000 × 12 = 2.100.000, yearly=1.750.000 → hemat 16,67%
     */
    public function yearlySavingsPercent(): ?int
    {
        if (!$this->has_yearly || !$this->yearly_price || $this->price <= 0) {
            return null;
        }
        $monthlyTotal = $this->price * 12;
        return (int) round((($monthlyTotal - $this->yearly_price) / $monthlyTotal) * 100);
    }

    /**
     * Harga per bulan jika memilih periode tahunan (yearly_price / 12).
     */
    public function monthlyEquivalentYearly(): ?float
    {
        if (!$this->has_yearly || !$this->yearly_price) {
            return null;
        }
        return round($this->yearly_price / 12);
    }
}
