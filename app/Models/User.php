<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\HasTenants;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use NotificationChannels\WebPush\HasPushSubscriptions;

use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements HasTenants, FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasPushSubscriptions, \App\Traits\NormalizesPhone;

    public function canImpersonate(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function canBeImpersonated(): bool
    {
        // Don't allow impersonating super admins
        return !$this->hasRole('super_admin');
    }

    protected static function booted(): void
    {
        static::saving(function ($user) {
            if ($user->phone) {
                $user->phone = $user->normalizePhoneNumber($user->phone);
            }
        });
    }
    
    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->hasRole('super_admin');
        }

        if ($panel->getId() === 'hq') {
            return $this->hasRole('super_admin') || ($this->hasRole('restaurant_owner') && $this->hasFeature('Dashboard HQ'));
        }

        if ($panel->getId() === 'restaurant') {
            // 1. Super Admin always access
            if ($this->hasRole('super_admin')) return true;

            // 2. Owners (who have restaurants linked via user_id)
            if ($this->ownedRestaurants()->exists()) return true;

            // 3. Staff (Check if they have any valid role in ANY restaurant)
            // We must bypass the default Spatie 'current team' scope check
            return \DB::table('model_has_roles')
                ->where('model_id', $this->id)
                ->where('model_type', self::class)
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->whereIn('roles.name', ['restaurant_owner', 'restaurant_admin', 'staff', 'waiter', 'kitchen', 'delivery'])
                ->exists();
        }

        return false;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'restaurant_id',
        'notification_preferences',
        'settings',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'notification_preferences' => 'array',
            'settings' => 'array',
        ];
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function ownedRestaurants()
    {
        return $this->hasMany(Restaurant::class, 'user_id');
    }

    public function getAccessibleRestaurantIds()
    {
        if ($this->hasRole('super_admin')) {
            return null; // Means ALL
        }
        
        if ($this->hasRole('restaurant_owner')) {
            // Also include the assigned restaurant_id just in case
            // IMPORTANT: Bypass 'tenant' global scope to avoid infinite recursion
            $ids = $this->ownedRestaurants()->withoutGlobalScope('tenant')->pluck('id');
            if ($this->restaurant_id) $ids->push($this->restaurant_id);
            return $ids->unique();
        }

        // Staff
        if ($this->restaurant_id) {
            return collect([$this->restaurant_id]);
        }
        
        return collect([]);
    }

    public function currentSubscription()
    {
        return $this->hasOne(Subscription::class)->orderByDesc('id'); // Use id desc as proxy for latest
    }
    
    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)->where('status', 'active')->where('expires_at', '>', now())->orderByDesc('expires_at');
    }

    public function getLimits()
    {
        if ($this->hasRole('super_admin')) {
             return ['max_restaurants' => 9999, 'max_menus' => 9999, 'max_orders' => 9999, 'max_members' => 9999];
        }
        
        // Contextual awareness: if we are in a tenant context, check the tenant owner's sub
        $owner = $this;
        $tenant = \Filament\Facades\Filament::getTenant();
        
        // Fallback for standalone pages outside Filament panel
        if (!$tenant && $this->restaurant_id) {
            $tenant = \App\Models\Restaurant::find($this->restaurant_id);
        }

        if ($tenant && $this->restaurant_id === $tenant->id && $tenant->user_id !== $this->id) {
            $owner = $tenant->owner;
        }

        // Get active subscription of the owner
        $sub = $owner ? $owner->activeSubscription : null;
        
        if ($sub && $sub->isValid()) {
            return $sub->plan->limits ?? ['max_restaurants' => 1, 'max_menus' => 10, 'max_orders' => 50]; 
        }
        
        // Default to Free / Strict limits if no sub
        return ['max_restaurants' => 1, 'max_menus' => 10, 'max_orders' => 50];
    }

    public function hasFeature($featureName)
    {
        if ($this->hasRole('super_admin')) return true;
        
        // Contextual awareness: staff should use owner's features
        $owner = $this;
        $tenant = \Filament\Facades\Filament::getTenant();

        // Fallback for standalone pages outside Filament panel
        if (!$tenant && $this->restaurant_id) {
            $tenant = \App\Models\Restaurant::find($this->restaurant_id);
        }

        if ($tenant && $this->restaurant_id === $tenant->id && $tenant->user_id !== $this->id) {
            $owner = $tenant->owner;
        }

        $sub = $owner ? $owner->activeSubscription : null;
        if ($sub && $sub->isValid()) {
            $features = $sub->plan->features ?? [];
            return in_array($featureName, $features);
        }
        
        return false; 
    }

    public function hasExpenseManagement()
    {
        return $this->hasFeature('Expense Management');
    }

    public function hasSplitByItem()
    {
        return $this->hasFeature('Split Bill by Item');
    }

    public function canCreateRestaurant()
    {
        $limit = $this->getLimits()['max_restaurants'] ?? 0;
        if ($limit < 0 || $limit >= 9999) return true;

        $current = $this->ownedRestaurants()->count();
        return $current < $limit;
    }

    public function canAddMenu(\App\Models\Restaurant $restaurant)
    {
        $limit = $this->getLimits()['max_menus'] ?? 0;
        if ($limit < 0 || $limit >= 9999) return true;
        
        return $restaurant->menuItems()->count() < $limit;
    }

    public function canAddMember(\App\Models\Restaurant $restaurant)
    {
        $limit = $this->getLimits()['max_members'] ?? 0;
        if ($limit < 0 || $limit >= 9999) return true;
        
        return $restaurant->members()->count() < $limit;
    }

    public function getTenants(Panel $panel): Collection
    {
        $tenants = $this->ownedRestaurants;
        
        if ($this->restaurant_id && !$tenants->contains('id', $this->restaurant_id)) {
             $restaurant = Restaurant::find($this->restaurant_id);
             if ($restaurant) {
                 $tenants->push($restaurant);
             }
        }
        
        return $tenants;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->ownedRestaurants->contains($tenant) || $this->restaurant_id === $tenant->id;
    }

    public function ordersProcessed()
    {
        return $this->hasMany(Order::class, 'processed_by_id');
    }

    public function ordersServed()
    {
        return $this->hasMany(Order::class, 'served_by_id');
    }
}
