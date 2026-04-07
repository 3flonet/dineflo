<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        // Tambah credential Watzap.id (api_key + number_key)
        $this->migrator->add('general.platform_watzap_api_key', null);
        $this->migrator->add('general.platform_watzap_number_key', null);
    }

    public function down(): void
    {
        $this->migrator->delete('general.platform_watzap_api_key');
        $this->migrator->delete('general.platform_watzap_number_key');
    }
};
