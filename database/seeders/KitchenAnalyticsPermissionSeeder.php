<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class KitchenAnalyticsPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'view_kitchen_analytics', 
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // 1. Give to Restaurant Owner
        $ownerRole = Role::where('name', 'restaurant_owner')->where('guard_name', 'web')->first();
        if ($ownerRole) {
            $ownerRole->givePermissionTo($permissions);
        }

        // Kita tidak memberikannya ke 'staff' atau 'kitchen' secara default 
        // karena ini adalah data performa manajerial (KPI).

        $this->command->info('✅ Advanced Kitchen Analytics permissions seeded successfully!');
    }
}
