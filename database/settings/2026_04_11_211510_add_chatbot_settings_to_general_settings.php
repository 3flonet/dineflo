<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.chatbot_active', true);
        $this->migrator->add('general.chatbot_name', 'Nadia');
        $this->migrator->add('general.chatbot_whatsapp_number', '628123456789');
        $this->migrator->add('general.chatbot_initial_greeting', 'Halo! Perlu bantuan atau mau lihat demo singkat dari kami? 😊');
        $this->migrator->add('general.chatbot_ask_phone_message', 'Sebelum kita lanjut, boleh minta nomor WhatsApp Anda dulu ya? Contoh: 08123456789');
        $this->migrator->add('general.chatbot_ask_email_message', 'Terima kasih! Satu lagi, boleh tahu alamat email Anda? Contoh: nama@email.com');
        $this->migrator->add('general.chatbot_final_message', 'Terima kasih! 🎉 Data Anda sudah lengkap. Sekarang kami menghubungkan Anda langsung ke tim kami di WhatsApp.');
    }

    public function down(): void
    {
        $this->migrator->delete('general.chatbot_active');
        $this->migrator->delete('general.chatbot_name');
        $this->migrator->delete('general.chatbot_whatsapp_number');
        $this->migrator->delete('general.chatbot_initial_greeting');
        $this->migrator->delete('general.chatbot_ask_phone_message');
        $this->migrator->delete('general.chatbot_ask_email_message');
        $this->migrator->delete('general.chatbot_final_message');
    }
};
