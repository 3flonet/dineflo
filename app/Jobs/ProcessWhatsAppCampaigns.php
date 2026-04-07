<?php

namespace App\Jobs;

use App\Models\WhatsAppCampaign;
use App\Models\Member;
use App\Services\MarketingWhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class ProcessWhatsAppCampaigns implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // 1. Process Automatic Trigger Campaigns
        $campaigns = WhatsAppCampaign::where('is_active', true)
            ->where('trigger_type', '!=', 'manual')
            ->get();

        foreach ($campaigns as $campaign) {
            // Check if restaurant plan has Email Marketing (We use same feature flag or dedicated one?)
            // User requested WhatsApp Marketing, but let's check if there's a specific feature.
            // In public features page we saw "WhatsApp Marketing & Alerts" but the DB actually uses "WhatsApp Marketing"
            if ($campaign->restaurant && $campaign->restaurant->owner->hasFeature('WhatsApp Marketing')) {
                $this->processCampaign($campaign, $waService);
                $campaign->update(['last_run_at' => now()]);
            }
        }

        // 2. Process Scheduled Manual Broadcasts
        $scheduled = WhatsAppCampaign::where('trigger_type', 'manual')
            ->where('status', 'scheduled')
            ->where('scheduled_at', '<=', now())
            ->get();
        
        foreach ($scheduled as $campaign) {
            ProcessManualWhatsAppBroadcast::dispatch($campaign);
        }
    }

    protected function processCampaign(WhatsAppCampaign $campaign, MarketingWhatsAppService $waService)
    {
        $members = collect();

        switch ($campaign->trigger_type) {
            case 'birthday':
                $delayDays = $campaign->delay_days ?: 0; // WA typical on the day
                $targetDate = Carbon::now()->addDays($delayDays);
                
                $members = Member::where('restaurant_id', $campaign->restaurant_id)
                    ->whereMonth('birthday', $targetDate->month)
                    ->whereDay('birthday', $targetDate->day)
                    ->whereIn('tier', $campaign->target_tiers ?? [])
                    ->get();
                break;

            case 'welcome':
                $members = Member::where('restaurant_id', $campaign->restaurant_id)
                    ->where('created_at', '>=', Carbon::now()->subDays(1))
                    ->whereIn('tier', $campaign->target_tiers ?? [])
                    ->get();
                break;

            case 'win_back':
                $inactiveDays = $campaign->delay_days ?: 30;
                $members = Member::where('restaurant_id', $campaign->restaurant_id)
                    ->whereIn('tier', $campaign->target_tiers ?? [])
                    ->whereDoesntHave('orders', function ($query) use ($inactiveDays) {
                        $query->where('created_at', '>', Carbon::now()->subDays($inactiveDays));
                    })
                    ->get();
                break;
        }

        foreach ($members as $member) {
            $alreadySent = $campaign->logs()
                ->where('member_id', $member->id)
                ->where('status', 'sent')
                ->when($campaign->trigger_type === 'birthday', function ($q) {
                    return $q->where('sent_at', '>', Carbon::now()->subDays(330));
                })
                ->when($campaign->trigger_type === 'welcome', function ($q) {
                    return $q;
                })
                ->exists();

            if (!$alreadySent && $member->whatsapp) {
                \App\Jobs\SendWhatsAppCampaignJob::dispatch($campaign, $member);
            }
        }
    }
}
