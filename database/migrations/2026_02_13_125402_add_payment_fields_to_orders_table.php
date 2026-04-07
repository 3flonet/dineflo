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
            $table->string('payment_status')->default('unpaid')->after('total_amount'); // unpaid, pending, paid, failed, expired
            $table->string('payment_method')->default('cash')->after('payment_status'); // cash, midtrans, qris
            $table->string('payment_token')->nullable()->after('payment_method'); // Snap Token
            $table->string('payment_url')->nullable()->after('payment_token'); // Snap Redirect URL
            $table->string('midtrans_transaction_id')->nullable()->after('payment_url'); // Transaction ID from Midtrans
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
};
