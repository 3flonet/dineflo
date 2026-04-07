<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.subscription_expiry_warning_days', 7);
    }

    public function down(): void
    {
        $this->migrator->delete('general.subscription_expiry_warning_days');
    }
};
