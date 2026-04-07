<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class WhatsAppPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat Permission Baru
        $permissions = [
            'manage_whatsapp_settings', // Untuk akses tab pengaturan & API Key
            'send_whatsapp_receipt',    // Untuk akses tombol kirim di Order/POS
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // 2. Berikan ke Restaurant Owner (Semua akses)
        $ownerRole = Role::where('name', 'restaurant_owner')->first();
        if ($ownerRole) {
            $ownerRole->givePermissionTo($permissions);
        }

        // 3. Berikan ke Staff (Hanya akses kirim nota)
        $staffRole = Role::where('name', 'staff')->first();
        if ($staffRole) {
            $staffRole->givePermissionTo('send_whatsapp_receipt');
        }

        $this->command->info('✅ WhatsApp permissions seeded successfully!');
    }
}
