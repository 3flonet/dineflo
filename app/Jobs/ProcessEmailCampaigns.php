<?php

namespace App\Jobs;

use App\Models\EmailCampaign;
use App\Models\Member;
use App\Services\MarketingMailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class ProcessEmailCampaigns implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // 1. Process Automatic Trigger Campaigns
        $campaigns = EmailCampaign::where('is_active', true)
            ->where('trigger_type', '!=', 'manual')
            ->get();

        foreach ($campaigns as $campaign) {
            // Check if restaurant plan has Email Marketing
            if ($campaign->restaurant && $campaign->restaurant->owner->hasFeature('Email Marketing')) {
                $this->processCampaign($campaign, $mailService);
                $campaign->update(['last_run_at' => now()]);
            }
        }

        // 2. Process Scheduled Manual Campaigns
        $scheduled = EmailCampaign::where('trigger_type', 'manual')
            ->where('status', 'scheduled')
            ->where('scheduled_at', '<=', now())
            ->get();
        
        foreach ($scheduled as $campaign) {
            ProcessManualCampaign::dispatch($campaign);
        }
    }

    protected function processCampaign(EmailCampaign $campaign, MarketingMailService $mailService)
    {
        $members = collect();

        switch ($campaign->trigger_type) {
            case 'birthday':
                // Birthday in 3 days (default) or configurable delay_days
                $delayDays = $campaign->delay_days ?: 3;
                $targetDate = Carbon::now()->addDays($delayDays);
                
                $members = Member::where('restaurant_id', $campaign->restaurant_id)
                    ->whereMonth('birthday', $targetDate->month)
                    ->whereDay('birthday', $targetDate->day)
                    ->whereIn('tier', $campaign->target_tiers ?? [])
                    ->get();
                break;

            case 'welcome':
                // Joined recently
                $members = Member::where('restaurant_id', $campaign->restaurant_id)
                    ->where('created_at', '>=', Carbon::now()->subDays(1))
                    ->whereIn('tier', $campaign->target_tiers ?? [])
                    ->get();
                break;

            case 'win_back':
                // Inactive for X days (e.g. 30 days)
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
            // Check if already sent specifically for this campaign to avoid duplicates
            $alreadySent = $campaign->logs()
                ->where('member_id', $member->id)
                ->where('status', 'sent')
                // For birthday, we check if sent in last 330 days (once per year)
                // For welcome, we check if sent at all
                ->when($campaign->trigger_type === 'birthday', function ($q) {
                    return $q->where('sent_at', '>', Carbon::now()->subDays(330));
                })
                ->when($campaign->trigger_type === 'welcome', function ($q) {
                    return $q; // Any previous log counts
                })
                ->exists();

            if (!$alreadySent && $member->email) {
                \App\Jobs\SendCampaignEmailJob::dispatch($campaign, $member);
            }
        }
    }
}
