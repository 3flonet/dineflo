<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawRequest extends Model
{
    protected $guarded = [];

    protected $casts = [
        'amount'         => 'decimal:2',
        'requested_at'   => 'datetime',
        'approved_at'    => 'datetime',
        'transferred_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->withdraw_code)) {
                do {
                    $code = 'WD-' . now()->format('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(5));
                } while (self::where('withdraw_code', $code)->exists());
                
                $model->withdraw_code = $code;
            }
        });
    }

    // ── Relationships ────────────────────────────────────────

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // ── Helpers ──────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isTransferred(): bool
    {
        return $this->status === 'transferred';
    }

    public static function statusOptions(): array
    {
        return [
            'pending'     => 'Menunggu',
            'approved'    => 'Disetujui',
            'transferred' => 'Selesai / Sudah Ditransfer',
        ];
    }

    public static function statusColors(): array
    {
        return [
            'pending'     => 'warning',
            'approved'    => 'info',
            'transferred' => 'success',
        ];
    }
}
