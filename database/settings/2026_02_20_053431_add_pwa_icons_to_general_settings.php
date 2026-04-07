<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->migrator->add('general.pwa_icon_512', '');
        $this->migrator->add('general.pwa_icon_192', '');
    }

    public function down(): void
    {
        $this->migrator->delete('general.pwa_icon_512');
        $this->migrator->delete('general.pwa_icon_192');
    }
};
