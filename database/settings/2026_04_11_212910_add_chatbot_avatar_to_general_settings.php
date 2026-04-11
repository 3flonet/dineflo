<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.chatbot_avatar', null);
    }

    public function down(): void
    {
        $this->migrator->delete('general.chatbot_avatar');
    }
};
