<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            // Metode pembayaran yang diaktifkan: array ['kasir', 'gateway', 'both']
            $table->string('payment_mode')->default('kasir')->after('wa_api_key');
            // Sub-opsi kasir: true = langsung ke KDS, false = bayar di kasir dulu
            $table->boolean('kasir_direct_to_kds')->default(true)->after('payment_mode');
            // Sub-opsi gateway: 'own' = akun sendiri, 'dineflo' = default akun Dineflo
            $table->string('gateway_mode')->nullable()->after('kasir_direct_to_kds');
            // Kredensial Midtrans milik restoran sendiri (jika gateway_mode = 'own')
            $table->string('midtrans_client_key')->nullable()->after('gateway_mode');
            $table->text('midtrans_server_key')->nullable()->after('midtrans_client_key');
            // Saldo restoran (jika gateway_mode = 'dineflo')
            $table->decimal('balance', 15, 2)->default(0)->after('midtrans_server_key');
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn([
                'payment_mode',
                'kasir_direct_to_kds',
                'gateway_mode',
                'midtrans_client_key',
                'midtrans_server_key',
                'balance',
            ]);
        });
    }
};
