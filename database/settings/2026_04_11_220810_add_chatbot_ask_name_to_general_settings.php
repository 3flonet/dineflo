<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.chatbot_ask_name_message', 'Halo! Boleh tahu dengan siapa saya berbicara? Silakan tuliskan nama Anda ya 😊');
    }

    public function down(): void
    {
        $this->migrator->delete('general.chatbot_ask_name_message');
    }
};
