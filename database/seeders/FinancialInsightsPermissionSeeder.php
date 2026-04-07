<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class FinancialInsightsPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'page_FinancialInsights',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // 1. Give to Restaurant Owner
        $ownerRole = Role::where('name', 'restaurant_owner')->where('guard_name', 'web')->first();
        if ($ownerRole) {
            $ownerRole->givePermissionTo($permissions);
        }

        // 2. Give to Restaurant Admin (if exists)
        $adminRole = Role::where('name', 'restaurant_admin')->where('guard_name', 'web')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }

        $this->command->info('✅ Financial Insights (Menu Engineering) permissions seeded successfully!');
    }
}
