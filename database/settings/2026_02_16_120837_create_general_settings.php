<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.site_name', 'Dineflo');
        $this->migrator->add('general.site_logo', null);
        $this->migrator->add('general.support_email', 'support@dineflo.com');
        $this->migrator->add('general.site_currency', 'IDR');
        $this->migrator->add('general.site_timezone', 'Asia/Jakarta');
        $this->migrator->add('general.midtrans_is_production', false);
        $this->migrator->add('general.midtrans_merchant_id', 'G708109926');
        $this->migrator->add('general.midtrans_server_key', 'PLACEHOLDER_SERVER_KEY');
        $this->migrator->add('general.midtrans_client_key', 'PLACEHOLDER_CLIENT_KEY');
        $this->migrator->add('general.smtp_host', 'smtp.mailtrap.io');
        $this->migrator->add('general.smtp_port', 2525);
        $this->migrator->add('general.smtp_username', '');
        $this->migrator->add('general.smtp_password', '');
        $this->migrator->add('general.smtp_encryption', 'tls');
        $this->migrator->add('general.smtp_from_address', 'noreply@dineflo.com');
        $this->migrator->add('general.smtp_from_name', 'Dineflo System');
    }
};
