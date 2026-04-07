<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\TenancyScope;

class WhatsAppCampaign extends Model
{
    use HasFactory, TenancyScope;

    protected $table = 'whatsapp_campaigns';

    protected $guarded = [];

    protected $casts = [
        'target_tiers' => 'array',
        'is_active' => 'boolean',
        'delay_days' => 'integer',
        'last_run_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'segmentation_filter' => 'array',
        'total_recipients' => 'integer',
        'sent_count' => 'integer',
        'read_count' => 'integer',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($campaign) {
            if ($campaign->trigger_type === 'manual') {
                if ($campaign->scheduled_at && $campaign->status !== 'completed' && $campaign->status !== 'sending') {
                    $campaign->status = 'scheduled';
                } elseif (!$campaign->scheduled_at && $campaign->status === 'active') {
                    $campaign->status = 'draft';
                }
            }
        });
    }

    public function logs()
    {
        return $this->hasMany(WhatsAppCampaignLog::class);
    }
}
