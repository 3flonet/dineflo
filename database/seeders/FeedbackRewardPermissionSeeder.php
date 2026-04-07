<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class FeedbackRewardPermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'manage_feedback_rewards',
            'view_feedback_rewards',
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
