<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RefundPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'process_refunds', 
            'open_cash_drawer'
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // 1. Give to Restaurant Owner (Keduanya)
        $ownerRole = \Spatie\Permission\Models\Role::where('name', 'restaurant_owner')->where('guard_name', 'web')->first();
        if ($ownerRole) {
            $ownerRole->givePermissionTo($permissions);
        }

        // 2. Give to Staff (Hanya buka laci kasir, refund tidak boleh default)
        $staffRole = \Spatie\Permission\Models\Role::where('name', 'staff')->where('guard_name', 'web')->first();
        if ($staffRole) {
            $staffRole->givePermissionTo(['open_cash_drawer']);
        }

        $this->command->info('✅ Refund Handling & Cash Drawer permissions seeded successfully!');
    }
}
