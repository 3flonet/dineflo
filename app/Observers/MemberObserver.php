<?php

namespace App\Observers;

use App\Models\Member;
use App\Services\MemberOtpService;

class MemberObserver
{
    /**
     * Saat member baru dibuat → kirim pesan sambutan + link portal
     */
    public function created(Member $member): void
    {
        $restaurant = $member->restaurant;

        if (!$restaurant) return;

        // Kirim welcome message (async-safe: jangan blocking)
        try {
            MemberOtpService::sendWelcome($member, $restaurant);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("MemberObserver welcome failed for member #{$member->id}: " . $e->getMessage());
        }
    }
}
