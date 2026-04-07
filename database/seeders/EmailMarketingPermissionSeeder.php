<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class EmailMarketingPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat Permission Baru (Filament Shield Style + Custom)
        $permissions = [
            'view_any_email_campaign',
            'view_email_campaign',
            'create_email_campaign',
            'update_email_campaign',
            'delete_email_campaign',
            'delete_any_email_campaign',
            'manage_email_marketing', // Untuk akses tab pengaturan di Profil Restoran
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // 2. Berikan ke Restaurant Owner (Semua akses)
        $ownerRole = Role::where('name', 'restaurant_owner')->first();
        if ($ownerRole) {
            foreach ($permissions as $permission) {
                if (!$ownerRole->hasPermissionTo($permission)) {
                    $ownerRole->givePermissionTo($permission);
                }
            }
        }

        $this->command->info('✅ Email Marketing permissions seeded successfully!');
    }
}
