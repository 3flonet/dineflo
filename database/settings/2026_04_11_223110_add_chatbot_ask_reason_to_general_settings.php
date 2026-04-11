<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.chatbot_ask_reason_message', 'Terakhir, boleh tahu apa yang ingin Anda tanyakan atau konsultasikan? Agar tim kami bisa membantu dengan lebih tepat 😊');
    }

    public function down(): void
    {
        $this->migrator->delete('general.chatbot_ask_reason_message');
    }
};
