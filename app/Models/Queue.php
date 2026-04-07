<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Queue extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'restaurant_id',
        'table_id',
        'customer_name',
        'customer_phone',
        'guest_count',
        'prefix',
        'queue_number',
        'status',
        'source',
        'called_at',
        'seated_at',
    ];

    protected $appends = ['full_number'];

    protected $casts = [
        'called_at' => 'datetime',
        'seated_at' => 'datetime',
    ];

    const STATUS_WAITING = 'waiting';
    const STATUS_CALLING = 'calling';
    const STATUS_SEATED = 'seated';
    const STATUS_SKIPPED = 'skipped';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Determine Prefix based on Guest Count
     */
    public static function getPrefixByGuestCount(int $count): string
    {
        if ($count <= 2) return 'A'; // Small
        if ($count <= 5) return 'B'; // Medium
        return 'C'; // Large
    }

    /**
     * Full Queue Label (e.g. A-01)
     */
    public function getFullNumberAttribute(): string
    {
        return $this->prefix . '-' . str_pad($this->queue_number, 2, '0', STR_PAD_LEFT);
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }
}
