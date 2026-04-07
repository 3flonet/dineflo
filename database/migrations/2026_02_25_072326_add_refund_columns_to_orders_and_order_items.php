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
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('refunded_amount', 15, 2)->default(0)->after('total_amount');
            $table->string('refund_status')->nullable()->after('payment_status'); // partial, full
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->boolean('is_refunded')->default(false)->after('quantity');
            $table->string('refund_reason')->nullable()->after('is_refunded');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['refunded_amount', 'refund_status']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['is_refunded', 'refund_reason']);
        });
    }
};
