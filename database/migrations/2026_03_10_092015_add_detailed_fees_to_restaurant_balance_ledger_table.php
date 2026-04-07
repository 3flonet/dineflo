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
        Schema::table('restaurant_balance_ledger', function (Blueprint $table) {
            $table->decimal('gateway_fee_amount', 15, 2)->default(0)->after('fee_amount');
            $table->decimal('platform_fee_amount', 15, 2)->default(0)->after('gateway_fee_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurant_balance_ledger', function (Blueprint $table) {
            $table->dropColumn(['gateway_fee_amount', 'platform_fee_amount']);
        });
    }
};
