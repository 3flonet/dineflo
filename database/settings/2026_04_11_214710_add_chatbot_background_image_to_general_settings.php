<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.chatbot_background_image', null);
    }

    public function down(): void
    {
        $this->migrator->delete('general.chatbot_background_image');
    }
};
