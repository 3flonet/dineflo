<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->json('edc_config')->nullable()->after('payment_mode');
        });

        Schema::table('order_payments', function (Blueprint $table) {
            $table->string('bank_name')->nullable()->after('status');
            $table->decimal('mdr_fee_amount', 12, 2)->default(0)->after('bank_name');
            $table->decimal('net_amount', 12, 2)->nullable()->after('mdr_fee_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn('edc_config');
        });

        Schema::table('order_payments', function (Blueprint $table) {
            $table->dropColumn(['bank_name', 'mdr_fee_amount', 'net_amount']);
        });
    }
};
