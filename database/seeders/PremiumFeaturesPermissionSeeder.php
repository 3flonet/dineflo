<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PremiumFeaturesPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'view_ingredient', 'view_any_ingredient', 'create_ingredient', 'update_ingredient', 'delete_ingredient', 'delete_any_ingredient',
            'view_reservation', 'view_any_reservation', 'create_reservation', 'update_reservation', 'delete_reservation', 'delete_any_reservation'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // 1. Give to Restaurant Owner
        $ownerRole = Role::where('name', 'restaurant_owner')->where('guard_name', 'web')->first();
        if ($ownerRole) {
            $ownerRole->givePermissionTo($permissions);
        }

        // 2. Give basic access to Staff / Waiter
        $staffRole = Role::where('name', 'staff')->where('guard_name', 'web')->first();
        if ($staffRole) {
            $staffRole->givePermissionTo([
                'view_ingredient', 'view_any_ingredient',
                'view_reservation', 'view_any_reservation', 'create_reservation', 'update_reservation',
            ]);
        }

        $waiterRole = Role::where('name', 'waiter')->where('guard_name', 'web')->first();
        if ($waiterRole) {
            $waiterRole->givePermissionTo([
                'view_reservation', 'view_any_reservation', 'create_reservation', 'update_reservation',
            ]);
        }

        $this->command->info('✅ Premium features (Inventory & Reservation) permissions seeded successfully!');
    }
}
