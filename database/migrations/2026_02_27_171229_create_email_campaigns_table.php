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
        Schema::create('email_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('subject');
            $table->longText('content');
            $table->string('trigger_type'); // birthday, win_back, tier_up, welcome, points_expiring
            $table->json('target_tiers')->nullable();
            $table->integer('delay_days')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('discount_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('last_run_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_campaigns');
    }
};
