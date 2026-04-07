<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        // Tambah credential watsap.id (berbeda dari watzap.id)
        $this->migrator->add('general.platform_watsap_api_key', null);
        $this->migrator->add('general.platform_watsap_id_device', null);
    }

    public function down(): void
    {
        $this->migrator->delete('general.platform_watsap_api_key');
        $this->migrator->delete('general.platform_watsap_id_device');
    }
};
