<?php

namespace App\Observers;

use App\Models\Restaurant;

class RestaurantObserver
{
    /**
     * Handle the Restaurant "created" event.
     */
    /**
     * Handle the Restaurant "created" event.
     */
    public function created(Restaurant $restaurant): void
    {
        // ═══════════════════════════════════════════════════════════════
        // Gunakan master permission list dari SyncRestaurantPermissions.
        // Jangan hardcode permission di sini — tambahkan di Command saja,
        // lalu jalankan: php artisan dineflo:sync-permissions
        // ═══════════════════════════════════════════════════════════════
        $rolePermissions = \App\Console\Commands\SyncRestaurantPermissions::getRolePermissions();

        foreach ($rolePermissions as $roleName => $perms) {
            // Buat Role yang ter-scope ke restoran ini
            $role = \App\Models\Role::firstOrCreate([
                'name'          => $roleName,
                'guard_name'    => 'web',
                'restaurant_id' => $restaurant->id,
            ]);

            foreach ($perms as $perm) {
                \Spatie\Permission\Models\Permission::firstOrCreate([
                    'name'       => $perm,
                    'guard_name' => 'web',
                ]);
                $role->givePermissionTo($perm);
            }
        }

        // Assign role 'restaurant_admin' ke pemilik restoran
        if ($restaurant->owner) {
            $adminRole = \App\Models\Role::where('name', 'restaurant_admin')
                ->where('restaurant_id', $restaurant->id)
                ->first();

            if ($adminRole) {
                $restaurant->owner->assignRole($adminRole);
            }

            // --- OTOMATIS TRIAL LOGIC ---
            // Cek apakah owner sudah punya subscription aktif
            $owner = $restaurant->owner;
            if (!$owner->activeSubscription()->exists()) {
                // Cari paket yang ditandai sebagai trial
                $trialPlan = \App\Models\SubscriptionPlan::where('is_trial', true)->where('is_active', true)->first();

                if ($trialPlan) {
                    \App\Models\Subscription::create([
                        'user_id' => $owner->id,
                        'subscription_plan_id' => $trialPlan->id,
                        'status' => 'active',
                        'starts_at' => now(),
                        'expires_at' => now()->addDays($trialPlan->duration_days),
                    ]);
                }
            }
        }
    }

    /**
     * Handle the Restaurant "updated" event.
     */
    public function updated(Restaurant $restaurant): void
    {
        //
    }

    /**
     * Handle the Restaurant "deleted" event.
     */
    public function deleted(Restaurant $restaurant): void
    {
        //
    }

    /**
     * Handle the Restaurant "restored" event.
     */
    public function restored(Restaurant $restaurant): void
    {
        //
    }

    /**
     * Handle the Restaurant "force deleted" event.
     */
    public function forceDeleted(Restaurant $restaurant): void
    {
        //
    }
}
