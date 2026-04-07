<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.site_twitter_url', '');
        $this->migrator->add('general.site_google_maps_embed', '');
    }
};
