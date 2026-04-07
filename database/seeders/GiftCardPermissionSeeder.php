<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use App\Models\Role;

class GiftCardPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'view_any_gift_card',
            'view_gift_card',
            'create_gift_card',
            'update_gift_card',
            'delete_gift_card',
        ];

        // Create permissions globally
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Assign to all existing restaurant_owner and restaurant_admin roles
        $ownerRoles = Role::where('name', 'restaurant_owner')->get();
        foreach ($ownerRoles as $role) {
            $role->givePermissionTo($permissions);
        }

        // restaurant_admin can view and manage gift cards
        $adminRoles = Role::where('name', 'restaurant_admin')->get();
        foreach ($adminRoles as $role) {
            $role->givePermissionTo([
                'view_any_gift_card',
                'view_gift_card',
                'create_gift_card',
                'update_gift_card',
            ]);
        }

        // staff can only view
        $staffRoles = Role::where('name', 'staff')->get();
        foreach ($staffRoles as $role) {
            $role->givePermissionTo([
                'view_any_gift_card',
                'view_gift_card',
            ]);
        }

        $this->command->info('Gift Card permissions seeded successfully.');
    }
}
