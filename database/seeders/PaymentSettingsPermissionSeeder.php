<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PaymentSettingsPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Daftar permission baru
        $permissions = [
            'manage_payment_settings',    // Akses tab Metode Pembayaran di Restaurant Settings
            'view_withdraw_balance',      // Lihat saldo & riwayat withdraw
            'create_withdraw_request',    // Ajukan permintaan penarikan dana
            'use_edc_payment',           // Gunakan metode EDC di POS
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // 2. Restaurant Owner — semua akses payment settings & withdraw
        $ownerRole = Role::where('name', 'restaurant_owner')->first();
        if ($ownerRole) {
            $ownerRole->givePermissionTo($permissions);
        }

        // 3. Staff — hanya boleh lihat saldo (tidak bisa ajukan/setting)
        $staffRole = Role::where('name', 'staff')->first();
        if ($staffRole) {
            $staffRole->givePermissionTo('view_withdraw_balance');
        }

        // 4. Super Admin — otomatis punya semua permission via hasRole check

        $this->command->info('✅ Payment Settings permissions seeded successfully!');
    }
}
