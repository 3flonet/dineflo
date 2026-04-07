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
        Schema::table('email_campaigns', function (Blueprint $blueprint) {
            // trigger_type will now also support 'manual'
            $blueprint->string('status')->default('active')->after('is_active'); // draft, scheduled, sending, completed, cancelled
            $blueprint->timestamp('scheduled_at')->nullable()->after('status');
            $blueprint->string('segmentation_type')->default('all')->after('scheduled_at'); // all, tiers, activity
            $blueprint->json('segmentation_filter')->nullable()->after('segmentation_type');
            
            // Stats cache
            $blueprint->integer('total_recipients')->default(0)->after('segmentation_filter');
            $blueprint->integer('sent_count')->default(0)->after('total_recipients');
            $blueprint->integer('open_count')->default(0)->after('sent_count');
        });

        Schema::table('email_campaign_logs', function (Blueprint $blueprint) {
            $blueprint->string('tracking_hash')->nullable()->unique()->after('status');
            $blueprint->timestamp('opened_at')->nullable()->after('tracking_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_campaigns', function (Blueprint $blueprint) {
            $blueprint->dropColumn([
                'status',
                'scheduled_at',
                'segmentation_type',
                'segmentation_filter',
                'total_recipients',
                'sent_count',
                'open_count'
            ]);
        });

        Schema::table('email_campaign_logs', function (Blueprint $blueprint) {
            $blueprint->dropColumn(['tracking_hash', 'opened_at']);
        });
    }
};
