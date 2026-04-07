<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Clear cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Restaurant Owner
        $owner = Role::firstOrCreate(['name' => 'restaurant_owner', 'guard_name' => 'web']);
        
        // Define exact permissions based on Shield convention
        $ownerPermissions = [
            // Orders
            'view_any_order', 'view_order', 'create_order', 'update_order', 'delete_order',
            // Menu Items (note: Shield uses :: for namespaced resources)
            'view_any_menu::item', 'view_menu::item', 'create_menu::item', 'update_menu::item', 'delete_menu::item',
            // Menu Categories
            'view_any_menu::category', 'view_menu::category', 'create_menu::category', 'update_menu::category', 'delete_menu::category',
            // Tables
            'view_any_table', 'view_table', 'create_table', 'update_table', 'delete_table',
            // Restaurant (View & Update own)
            'view_any_restaurant', 'view_restaurant', 'update_restaurant',
        ];

        // Assign permissions (create if missing to be safe)
        foreach ($ownerPermissions as $perm) {
            // Check if permission exists before giving
            if (Permission::where('name', $perm)->exists()) {
                $owner->givePermissionTo($perm);
            }
        }

        // 2. Staff (Kitchen / Waiter)
        $staff = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
        
        $staffPermissions = [
            'view_any_order', 'view_order', 'update_order', // Process orders
            'view_any_table', 'view_table', 
            'view_any_menu::item', 'view_menu::item', // View menu only
        ];

        foreach ($staffPermissions as $perm) {
             if (Permission::where('name', $perm)->exists()) {
                $staff->givePermissionTo($perm);
            }
        }
    }
}
