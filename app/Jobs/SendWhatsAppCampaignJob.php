<?php

namespace App\Jobs;

use App\Models\WhatsAppCampaign;
use App\Models\Member;
use App\Services\MarketingWhatsAppService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWhatsAppCampaignJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $campaign;
    protected $member;

    public function __construct(WhatsAppCampaign $campaign, Member $member)
    {
        $this->campaign = $campaign;
        $this->member = $member;
    }

    public function handle(MarketingWhatsAppService $waService)
    {
        if ($this->batch() && $this->batch()->cancelled()) {
            return;
        }

        $waService->sendCampaignMessage($this->campaign, $this->member);
    }
}
