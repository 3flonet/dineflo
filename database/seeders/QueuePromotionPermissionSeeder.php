<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class QueuePromotionPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $resourcePermissions = [
            'view_queue::promotion',
            'view_any_queue::promotion',
            'create_queue::promotion',
            'update_queue::promotion',
            'restore_queue::promotion',
            'restore_any_queue::promotion',
            'replicate_queue::promotion',
            'reorder_queue::promotion',
            'delete_queue::promotion',
            'delete_any_queue::promotion',
            'force_delete_queue::promotion',
            'force_delete_any_queue::promotion',
        ];

        foreach ($resourcePermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $roles = Role::whereIn('name', ['super_admin', 'restaurant_owner'])->get();
        foreach ($roles as $role) {
            $role->givePermissionTo($resourcePermissions);
        }
    }
}
