<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurant_balance_ledger', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('withdraw_request_id')->nullable()->constrained('withdraw_requests')->nullOnDelete();
            // credit = uang masuk (dari pembayaran order)
            // debit  = uang keluar (dari withdraw)
            $table->enum('type', ['credit', 'debit']);
            $table->string('payment_type')->nullable();     // qris, bank_transfer, credit_card, dll.
            $table->decimal('gross_amount', 15, 2)->default(0);
            $table->decimal('fee_percentage', 5, 2)->default(0);
            $table->decimal('fee_amount', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2);           // yang benar-benar masuk/keluar saldo
            $table->string('description');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_balance_ledger');
    }
};
