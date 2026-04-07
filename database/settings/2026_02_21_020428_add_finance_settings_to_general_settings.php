<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.midtrans_qris_fee_percentage', 0.70);
        $this->migrator->add('general.midtrans_va_fee_flat', 4000);
        $this->migrator->add('general.midtrans_cc_fee_percentage', 2.00);
        $this->migrator->add('general.midtrans_cstore_fee_flat', 5000);
        $this->migrator->add('general.dineflo_withdraw_admin_fee_percentage', 0.00);
    }
};
