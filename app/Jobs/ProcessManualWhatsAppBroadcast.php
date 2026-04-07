<?php

namespace App\Jobs;

use App\Models\WhatsAppCampaign;
use App\Models\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;

class ProcessManualWhatsAppBroadcast implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $campaign;

    public function __construct(WhatsAppCampaign $campaign)
    {
        $this->campaign = $campaign;
    }

    public function handle()
    {
        // 1. Update Status to Sending
        if ($this->campaign->status === 'sending') {
            return;
        }

        $this->campaign->update([
            'status' => 'sending',
            'sent_count' => 0,
            'read_count' => 0,
            'last_run_at' => now(),
        ]);

        // 2. Query Recipients
        $query = Member::where('restaurant_id', $this->campaign->restaurant_id)
            ->whereNotNull('whatsapp');

        if ($this->campaign->segmentation_type === 'tiers' && !empty($this->campaign->target_tiers)) {
            $query->whereIn('tier', $this->campaign->target_tiers);
        }

        $members = $query->get();
        
        $this->campaign->update([
            'total_recipients' => $members->count(),
        ]);

        if ($members->isEmpty()) {
            $this->campaign->update(['status' => 'completed']);
            return;
        }

        // 3. Prepare Batch Jobs
        $jobs = [];
        foreach ($members as $member) {
            $jobs[] = new SendWhatsAppCampaignJob($this->campaign, $member);
        }

        // 4. Dispatch Batch
        $campaignId = $this->campaign->id;
        Bus::batch($jobs)
            ->name('WhatsApp Broadcast: ' . $this->campaign->name)
            ->then(function () use ($campaignId) {
                WhatsAppCampaign::find($campaignId)->update(['status' => 'completed']);
            })
            ->finally(function () use ($campaignId) {
                $campaign = WhatsAppCampaign::find($campaignId);
                if ($campaign->status === 'sending') {
                    $campaign->update(['status' => 'completed']);
                }
            })
            ->dispatch();
    }
}
