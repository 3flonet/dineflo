<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        // Tambah global toggle & provider selector (generik)
        $this->migrator->add('general.platform_wa_is_active', false);
        $this->migrator->add('general.platform_wa_provider', 'fonnte');

        // Hapus property lama yang terlalu spesifik
        $this->migrator->delete('general.platform_fonnte_is_active');

        // Catatan: platform_fonnte_api_key tetap dipertahankan
        // karena itu credential spesifik provider Fonnte
    }

    public function down(): void
    {
        $this->migrator->delete('general.platform_wa_is_active');
        $this->migrator->delete('general.platform_wa_provider');
        $this->migrator->add('general.platform_fonnte_is_active', false);
    }
};
