<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gift_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            $table->string('code', 20)->unique(); // GC-XXXXX-XXXXX
            $table->string('recipient_name');
            $table->string('recipient_phone', 30)->nullable();
            $table->string('recipient_email')->nullable();
            $table->text('personal_message')->nullable();

            $table->decimal('original_amount', 12, 2);
            $table->decimal('remaining_balance', 12, 2);

            $table->enum('status', ['active', 'used', 'expired', 'cancelled'])->default('active');

            $table->timestamp('expires_at')->nullable();
            $table->timestamp('used_at')->nullable(); // set saat balance = 0
            $table->timestamps();

            $table->index(['restaurant_id', 'status']);
            $table->index('code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gift_cards');
    }
};
