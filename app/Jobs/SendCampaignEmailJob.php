<?php

namespace App\Jobs;

use App\Models\EmailCampaign;
use App\Models\Member;
use App\Services\MarketingMailService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendCampaignEmailJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $campaign;
    protected $member;

    public function __construct(EmailCampaign $campaign, Member $member)
    {
        $this->campaign = $campaign;
        $this->member = $member;
    }

    public function handle(MarketingMailService $mailService)
    {
        if ($this->batch() && $this->batch()->cancelled()) {
            return;
        }

        $mailService->sendCampaignEmail($this->campaign, $this->member);
    }
}
