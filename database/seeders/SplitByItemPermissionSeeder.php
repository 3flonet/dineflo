<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SplitByItemPermissionSeeder extends Seeder
{
    public function run()
    {
        // 1. Create Permission
        $permission = Permission::firstOrCreate(['name' => 'split_bill_by_item']);

        // 2. Assign to Admin, Owner & Staff by default (if they have the package)
        $roles = Role::whereIn('name', ['super_admin', 'restaurant_owner', 'staff'])->get();
        foreach ($roles as $role) {
            $role->givePermissionTo($permission);
        }
    }
}
