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
        Schema::create('queues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $table->foreignId('table_id')->nullable()->constrained()->onDelete('set null');
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->integer('guest_count')->default(1);
            $table->string('prefix', 5)->default('A');
            $table->integer('queue_number');
            $table->enum('status', ['waiting', 'calling', 'seated', 'skipped', 'cancelled'])->default('waiting');
            $table->enum('source', ['kiosk', 'online'])->default('kiosk');
            $table->timestamp('called_at')->nullable();
            $table->timestamp('seated_at')->nullable();
            $table->timestamps();
            
            // Index for faster queries in Display and Staff Panel
            $table->index(['restaurant_id', 'status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queues');
    }
};
