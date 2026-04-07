<?php

namespace App\Http\Controllers;

use App\Models\EmailCampaignLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class MarketingTrackingController extends Controller
{
    public function pixel($hash)
    {
        $log = EmailCampaignLog::where('tracking_hash', $hash)->first();

        if ($log && !$log->opened_at) {
            $log->update([
                'opened_at' => now(),
            ]);

            // Increment campaign open count
            $log->campaign()->increment('open_count');
        }

        // Return 1x1 transparent pixel
        $pixel = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
        
        return Response::make($pixel, 200, [
            'Content-Type' => 'image/gif',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
