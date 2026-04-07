<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class IngredientStockPermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'view_ingredient_history',
            'adjust_ingredient_stock',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // Assign to Restaurant Owner and Admin (Super Admin already has Gate::before)
        $roles = Role::whereIn('name', ['restaurant_owner', 'restaurant_admin'])->get();

        foreach ($roles as $role) {
            $role->givePermissionTo($permissions);
        }
    }
}
