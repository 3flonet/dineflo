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
        Schema::create('whatsapp_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->longText('content');
            $table->string('trigger_type'); // birthday, win_back, tier_up, welcome, points_expiring, manual
            $table->json('target_tiers')->nullable();
            $table->integer('delay_days')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('status')->default('active'); // draft, scheduled, sending, completed, cancelled
            $table->timestamp('scheduled_at')->nullable();
            $table->string('segmentation_type')->default('all'); // all, tiers
            $table->json('segmentation_filter')->nullable();
            $table->foreignId('discount_id')->nullable()->constrained()->nullOnDelete();
            
            // Stats cache
            $table->integer('total_recipients')->default(0);
            $table->integer('sent_count')->default(0);
            $table->integer('read_count')->default(0); // For WA we call it Read count, though Fonnte might just give 'sent' status
            
            $table->timestamp('last_run_at')->nullable();
            $table->timestamps();
        });

        Schema::create('whatsapp_campaign_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('whatsapp_campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->timestamp('sent_at');
            $table->string('status'); // sent, failed
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_campaign_logs');
        Schema::dropIfExists('whatsapp_campaigns');
    }
};
