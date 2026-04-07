<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds missing performance indexes for fields frequently used in filters, 
     * where clauses, and sorting across the Dineflo system.
     */
    public function up(): void
    {
        // 1. Orders table performance boost
        Schema::table('orders', function (Blueprint $table) {
            $table->index('status');
            $table->index('created_at');
        });

        // 2. Reservations table performance boost
        Schema::table('reservations', function (Blueprint $table) {
            $table->index('status');
            $table->index('reservation_time');
        });

        // 3. Withdraw requests (for Super Admin panels)
        Schema::table('withdraw_requests', function (Blueprint $table) {
            $table->index('status');
            $table->index('requested_at');
        });

        // 4. Order Payments (for financial audits)
        Schema::table('order_payments', function (Blueprint $table) {
            $table->index('status');
            $table->index('payment_method');
        });

        // 5. Members (for tier-based marketing)
        Schema::table('members', function (Blueprint $table) {
            $table->index('tier');
        });

        // 6. Menu Items (for digital menu filtering)
        Schema::table('menu_items', function (Blueprint $table) {
            $table->index('is_available');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['reservation_time']);
        });

        Schema::table('withdraw_requests', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['requested_at']);
        });

        Schema::table('order_payments', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['payment_method']);
        });

        Schema::table('members', function (Blueprint $table) {
            $table->dropIndex(['tier']);
        });

        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropIndex(['is_available']);
        });
    }
};
