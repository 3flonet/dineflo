<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use Spatie\Permission\Models\Permission;

class EssentialRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Global Roles
        $roles = [
            'super_admin',
            'restaurant_owner',
            'restaurant_admin',
            'staff',
            'waiter',
            'kitchen',
            'delivery',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // 2. Define Basic Permissions (Handled by DemoDataSeeder previously)
        $basicPerms = [
            'view_any_restaurant', 'create_restaurant', 'update_restaurant', 'delete_restaurant',
            'view_restaurant',
            'manage_payment_settings', 'manage_whatsapp_settings', 'manage_email_marketing', 'manage_feedback_rewards',
            'page_Pos', 'page_Reports', 'page_MySubscription', 'page_KitchenDisplay',
            'view_any_user', 'create_user', 'update_user', 'delete_user', 'view_user',
            'view_any_role', 'view_role'
        ];

        foreach ($basicPerms as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        // 3. Assign Permissions to Owner (Templates)
        $ownerRole = Role::where('name', 'restaurant_owner')->first();
        if ($ownerRole) {
            $ownerRole->syncPermissions($basicPerms);
        }
    }
}
