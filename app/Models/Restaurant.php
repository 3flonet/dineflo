<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use \App\Traits\BelongsToTenant, \App\Traits\NormalizesPhone;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($restaurant) {
            if (!$restaurant->slug) {
                $restaurant->slug = \Illuminate\Support\Str::slug($restaurant->name);
            }
        });

        static::saving(function ($restaurant) {
            if ($restaurant->phone) {
                $restaurant->phone = $restaurant->normalizePhoneNumber($restaurant->phone);
            }
            // Clear cache on any update
            \Illuminate\Support\Facades\Cache::forget("restaurant_slug_{$restaurant->slug}");
        });

        static::deleted(function ($restaurant) {
            \Illuminate\Support\Facades\Cache::forget("restaurant_slug_{$restaurant->slug}");
        });
    }

    /**
     * Cache route model binding to reduce DB load
     */
    public function resolveRouteBinding($value, $field = null)
    {
        if ($field === 'slug' || $field === null) {
            return \Illuminate\Support\Facades\Cache::remember(
                "restaurant_slug_{$value}", 
                now()->addHours(24), 
                fn() => $this->where('slug', $value)->firstOrFail()
            );
        }

        return parent::resolveRouteBinding($value, $field);
    }

    public static $tenantColumn = 'id';
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'address',
        'phone',
        'email',
        'logo',
        'logo_square',
        'cover_image',
        'is_active',
        'opening_hours',
        'wa_is_active',
        'wa_provider',
        'wa_api_key',
        'additional_fees',
        'tax_enabled',
        'tax_percentage',
        'loyalty_point_rate',
        'loyalty_silver_threshold',
        'loyalty_gold_threshold',
        'loyalty_point_redemption_value',
        'loyalty_redemption_enabled',
        'is_online_order_enabled',
        'kasir_direct_to_kds',
        'auto_close_cashier',
        'feedback_reward_enabled',
        'feedback_reward_points',
        'feedback_reward_discount_id',
        'social_links',
        'google_map_embed',
        'payment_mode',
        'gateway_mode',
        'midtrans_server_key',
        'midtrans_client_key',
        'email_marketing_provider',
        'email_marketing_smtp_host',
        'email_marketing_smtp_port',
        'email_marketing_smtp_username',
        'email_marketing_smtp_password',
        'email_marketing_smtp_encryption',
        'email_marketing_smtp_from_address',
        'queue_display_running_text',
        'wifi_name',
        'wifi_password',
        'qr_card_design',
        'edc_config',
        // Catatan: 'balance' TIDAK di-fillable — hanya diubah via increment/decrement
    ];
    
    protected $hidden = [
        'wa_api_key',
        'midtrans_server_key',
        'midtrans_client_key',
        'email_marketing_smtp_password',
        'wifi_password',
    ];

    protected $casts = [
        'opening_hours'         => 'array',
        'is_active'             => 'boolean',
        'loyalty_silver_threshold' => 'decimal:2',
        'loyalty_gold_threshold'   => 'decimal:2',
        'loyalty_point_rate'    => 'integer',
        'wa_is_active'          => 'boolean',
        'kasir_direct_to_kds'   => 'boolean',
        'balance'               => 'decimal:2',
        'tax_enabled'           => 'boolean',
        'tax_percentage'        => 'decimal:2',
        'additional_fees'       => 'array',
        'is_online_order_enabled' => 'boolean',
        'social_links'          => 'array',
        'qr_card_design'        => 'array',
        'feedback_reward_enabled' => 'boolean',
        'feedback_reward_points' => 'integer',
        'feedback_reward_discount_id' => 'integer',
        'loyalty_point_redemption_value' => 'decimal:2',
        'loyalty_redemption_enabled' => 'boolean',
        'auto_close_cashier'    => 'boolean',
        'email_marketing_smtp_port' => 'integer',
        'email_marketing_smtp_password' => 'encrypted',
        'wa_api_key'            => 'encrypted',
        'midtrans_server_key'   => 'encrypted',
        'midtrans_client_key'   => 'encrypted',
        'edc_config'            => 'array',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tables()
    {
        return $this->hasMany(Table::class);
    }

    public function menuCategories()
    {
        return $this->hasMany(MenuCategory::class);
    }

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function members()
    {
        return $this->hasMany(Member::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function expenseCategories()
    {
        return $this->hasMany(ExpenseCategory::class);
    }

    public function ingredients()
    {
        return $this->hasMany(Ingredient::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function waiterCalls()
    {
        return $this->hasMany(WaiterCall::class);
    }

    public function withdrawRequests()
    {
        return $this->hasMany(WithdrawRequest::class);
    }

    public function balanceLedger()
    {
        return $this->hasMany(RestaurantBalanceLedger::class);
    }

    public function discounts()
    {
        return $this->hasMany(Discount::class);
    }

    public function orderFeedbacks()
    {
        return $this->hasMany(OrderFeedback::class);
    }

    public function feedbackRewardDiscount()
    {
        return $this->belongsTo(Discount::class, 'feedback_reward_discount_id');
    }

    public function giftCards()
    {
        return $this->hasMany(GiftCard::class);
    }

    public function queuePromotions()
    {
        return $this->hasMany(QueuePromotion::class);
    }
}
