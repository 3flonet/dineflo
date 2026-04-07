<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class HqDashboardPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat Permission Baru (Format Filament Shield untuk Custom Page)
        $permission = 'page_HqDashboard';

        Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);

        // 2. Berikan ke Restaurant Owner
        $ownerRole = Role::where('name', 'restaurant_owner')->first();
        if ($ownerRole) {
            $ownerRole->givePermissionTo($permission);
        }

        // 3. Super Admin biasanya punya akses ke semua via Gate, 
        // tapi kita masukkan saja ke role-nya untuk formalitas.
        $adminRole = Role::where('name', 'super_admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permission);
        }

        $this->command->info('✅ HQ Dashboard permissions seeded successfully!');
    }
}
