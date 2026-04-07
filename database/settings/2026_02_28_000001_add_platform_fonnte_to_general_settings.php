<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.platform_fonnte_api_key', null);
        $this->migrator->add('general.platform_fonnte_is_active', false);
    }

    public function down(): void
    {
        $this->migrator->delete('general.platform_fonnte_api_key');
        $this->migrator->delete('general.platform_fonnte_is_active');
    }
};
