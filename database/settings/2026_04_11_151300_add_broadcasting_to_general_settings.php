<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.broadcast_driver', 'reverb');
        $this->migrator->add('general.pusher_app_id', '');
        $this->migrator->add('general.pusher_app_key', '');
        $this->migrator->add('general.pusher_app_secret', '');
        $this->migrator->add('general.pusher_app_cluster', 'mt1');
        
        $this->migrator->add('general.reverb_app_id', '189231');
        $this->migrator->add('general.reverb_app_key', 'e6ozfek9rsavsbbnkfnr');
        $this->migrator->add('general.reverb_app_secret', 'jsenhrgzofjxeppubpie');
        $this->migrator->add('general.reverb_host', 'localhost');
        $this->migrator->add('general.reverb_port', 8081);
        $this->migrator->add('general.reverb_scheme', 'http');
    }
};
