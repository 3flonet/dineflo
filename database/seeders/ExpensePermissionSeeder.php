<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ExpensePermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'view_any_expense',
            'view_expense',
            'create_expense',
            'update_expense',
            'delete_expense',
            'view_any_expense_category',
            'view_expense_category',
            'create_expense_category',
            'update_expense_category',
            'delete_expense_category',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // Assign to Restaurant Owner and Admin
        $roles = Role::whereIn('name', ['restaurant_owner', 'restaurant_admin'])->get();

        foreach ($roles as $role) {
            $role->givePermissionTo($permissions);
        }
    }
}
