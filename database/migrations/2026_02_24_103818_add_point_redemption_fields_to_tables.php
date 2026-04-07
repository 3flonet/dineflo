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
            $table->decimal('loyalty_point_redemption_value', 10, 2)->default(0)->after('loyalty_point_rate');
            $table->boolean('loyalty_redemption_enabled')->default(false)->after('loyalty_point_redemption_value');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->integer('points_used')->default(0)->after('voucher_discount_amount');
            $table->decimal('points_discount_amount', 15, 2)->default(0)->after('points_used');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['loyalty_point_redemption_value', 'loyalty_redemption_enabled']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['points_used', 'points_discount_amount']);
        });
    }
};
