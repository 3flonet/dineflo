<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->migrator->add('general.landing_hero_title', 'Kelola Restoran <span class="text-gradient">Lebih Cerdas</span>,<br class="hidden sm:block"/> Raih Profit <span class="text-gradient-gold">Lebih Maksimal.</span>');
        $this->migrator->add('general.landing_hero_subtitle', 'Tinggalkan cara manual yang lambat. ' . config('app.name', 'Dineflo') . ' menyatukan Pemesanan QR, POS, Kitchen Display, Laporan Keuangan, & Loyalitas Pelanggan dalam satu platform premium.');
        $this->migrator->add('general.landing_hero_cta_primary_text', '🚀 Mulai Gratis Sekarang');
        $this->migrator->add('general.landing_hero_cta_primary_link', '#harga');
        $this->migrator->add('general.landing_hero_cta_secondary_text', 'Lihat Fitur');
        $this->migrator->add('general.landing_hero_cta_secondary_link', '#fitur');
        $this->migrator->add('general.landing_hero_mockup_image', null);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->migrator->delete('general.landing_hero_title');
        $this->migrator->delete('general.landing_hero_subtitle');
        $this->migrator->delete('general.landing_hero_cta_primary_text');
        $this->migrator->delete('general.landing_hero_cta_primary_link');
        $this->migrator->delete('general.landing_hero_cta_secondary_text');
        $this->migrator->delete('general.landing_hero_cta_secondary_link');
        $this->migrator->delete('general.landing_hero_mockup_image');
    }
};
