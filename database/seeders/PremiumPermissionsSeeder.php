<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PremiumPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Define New Permissions for Premium Features
        $newPermissions = [
            // Ingredients (Inventory Level 2)
            'view_any_ingredient', 'view_ingredient', 'create_ingredient', 'update_ingredient', 'delete_ingredient',
            
            // Reservations (Table Reservation)
            'view_any_reservation', 'view_reservation', 'create_reservation', 'update_reservation', 'delete_reservation',
        ];

        foreach ($newPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // 2. Assign to Roles
        
        // Owner gets everything
        $owner = Role::where('name', 'restaurant_owner')->first();
        if ($owner) {
            $owner->givePermissionTo($newPermissions);
        }

        // Staff can view and manage some
        $staff = Role::where('name', 'staff')->first();
        if ($staff) {
            $staff->givePermissionTo([
                'view_any_ingredient', 'view_ingredient', 'update_ingredient', // Process stock
                'view_any_reservation', 'view_reservation', 'update_reservation', // Manage guests
            ]);
        }
    }
}
