<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class AssignStaffPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions for Staff Management (User Resource)
        $permissions = [
            'view_any_user',
            'view_user',
            'create_user',
            'update_user',
            'delete_user',
            // Add any other resource permissions if missing
             'view_any_order',
             'view_order',
             'create_order',
             'update_order',
             'delete_order',
             
             'view_any_menu_item',
             'view_menu_item',
             'create_menu_item',
             'update_menu_item',
             'delete_menu_item',
             
             'view_any_role',
             'view_role',
             'create_role',
             'update_role',
             'delete_role',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Assign to 'restaurant_owner' role
        $role = Role::firstOrCreate(['name' => 'restaurant_owner', 'guard_name' => 'web']);
        $role->givePermissionTo($permissions);

        $this->command->info('Staff permissions assigned to restaurant_owner role.');
    }
}
