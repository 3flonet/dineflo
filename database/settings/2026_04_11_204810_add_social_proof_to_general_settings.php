<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.landing_partner_logos', []);
        $this->migrator->add('general.landing_testimonials', []);
    }

    public function down(): void
    {
        $this->migrator->delete('general.landing_partner_logos');
        $this->migrator->delete('general.landing_testimonials');
    }
};
