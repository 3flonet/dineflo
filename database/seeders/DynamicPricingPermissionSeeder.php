<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DynamicPricingPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $newPermissions = [
            'view_any_discount', 'view_discount', 'create_discount', 'update_discount', 'delete_discount',
        ];

        foreach ($newPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Owner role
        $owner = Role::where('name', 'restaurant_owner')->first();
        if ($owner) {
            $owner->givePermissionTo($newPermissions);
        }

        // Staff role
        $staff = Role::where('name', 'staff')->first();
        if ($staff) {
            $staff->givePermissionTo([
                'view_any_discount', 'view_discount',
            ]);
        }
    }
}
