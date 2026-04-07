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
        // 1. Subscription Plans (Definisi Paket)
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Basic, Pro
            $table->string('slug')->unique(); // basic, pro
            $table->decimal('price', 15, 2);
            $table->integer('duration_days')->default(30);
            $table->json('features')->nullable(); // List fitur untuk display
            $table->json('limits')->nullable(); // {max_restaurants: 1, max_menus: 10}
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. User Subscriptions (Langganan Aktif)
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_plan_id')->constrained();
            $table->string('status')->default('pending_payment'); // active, expired, cancelled, pending_payment
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('payment_token')->nullable(); // Snap Token
            $table->string('midtrans_id')->nullable();
            $table->timestamps();
        });
        
        // 3. Subscription Invoices (History)
        Schema::create('subscription_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->cascadeOnDelete();
            $table->string('midtrans_id')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('status')->default('unpaid'); // unpaid, paid, failed
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_invoices');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('subscription_plans');
    }
};
