<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class OrderFeedbackPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'view_order::feedback',
            'view_any_order::feedback',
            'create_order::feedback',
            'update_order::feedback',
            'delete_order::feedback',
            'delete_any_order::feedback',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // 1. Give to Restaurant Owner (Full access)
        $ownerRoles = Role::where('name', 'restaurant_owner')->where('guard_name', 'web')->get();
        foreach ($ownerRoles as $role) {
            $role->givePermissionTo($permissions);
        }

        // 2. Give view access to Staff
        $staffRoles = Role::where('name', 'staff')->where('guard_name', 'web')->get();
        foreach ($staffRoles as $role) {
            $role->givePermissionTo([
                'view_order::feedback',
                'view_any_order::feedback',
                'update_order::feedback', // Staff can also reply
            ]);
        }

        // 3. Give view access to Waiter
        $waiterRoles = Role::where('name', 'waiter')->where('guard_name', 'web')->get();
        foreach ($waiterRoles as $role) {
            $role->givePermissionTo([
                'view_order::feedback',
                'view_any_order::feedback',
            ]);
        }

        $this->command->info('✅ Order Feedback permissions seeded successfully!');
    }
}
