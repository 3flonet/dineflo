<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.site_favicon', null);
        $this->migrator->add('general.site_description', null);
        $this->migrator->add('general.site_keywords', []);
        $this->migrator->add('general.site_address', null);
        $this->migrator->add('general.site_phone', null);
    }
};
