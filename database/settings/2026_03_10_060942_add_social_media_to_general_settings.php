<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.site_facebook_url', '');
        $this->migrator->add('general.site_instagram_url', '');
        $this->migrator->add('general.site_youtube_url', '');
        $this->migrator->add('general.site_linkedin_url', '');
        $this->migrator->add('general.site_github_url', '');
    }
};
