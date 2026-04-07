<?php

namespace App\Services;

use App\Models\Restaurant;
use App\Models\Member;
use App\Models\WhatsAppCampaign;
use App\Services\WhatsApp\WhatsAppService;
use Illuminate\Support\Facades\Log;

class MarketingWhatsAppService
{
    public function sendTestMessage(WhatsAppCampaign $campaign, string $recipientPhone)
    {
        $restaurant = $campaign->restaurant;
        
        // 1. Mock a generic member for placeholders
        $mockMember = new Member([
            'name' => 'Demo User',
            'points_balance' => 750,
            'tier' => 'gold',
            'whatsapp' => $recipientPhone
        ]);

        // 2. Process Content
        $content = $this->parseContent($campaign->content, $campaign, $mockMember);
        $content = "[UJI COBA]\n" . $content;

        // 3. Normalize Phone
        $cleanPhone = $this->normalizePhone($recipientPhone);

        // 4. Send Message (No logging to DB for tests)
        $success = WhatsAppService::sendMessage($restaurant, $cleanPhone, $content);
        
        if (!$success) {
            throw new \Exception("Gagal mengirim pesan WhatsApp via provider.");
        }
    }

    public function sendCampaignMessage(WhatsAppCampaign $campaign, Member $member)
    {
        $restaurant = $campaign->restaurant;
        
        // 1. Create Log Entry
        $log = $campaign->logs()->create([
            'member_id' => $member->id,
            'sent_at' => now(),
            'status' => 'pending',
        ]);

        // 2. Normalize Phone
        $cleanPhone = $this->normalizePhone($member->whatsapp);
        
        if (!$cleanPhone) {
            $log->update([
                'status' => 'failed',
                'error_message' => 'Nomor WhatsApp tidak valid atau kosong.',
            ]);
            return;
        }

        // 3. Process Content (Placeholders)
        $content = $this->parseContent($campaign->content, $campaign, $member);

        // 4. Send Message
        try {
            $success = WhatsAppService::sendMessage($restaurant, $cleanPhone, $content);
            
            if ($success) {
                // Log Success
                $log->update([
                    'status' => 'sent',
                ]);

                // Increment campaign sent count
                $campaign->increment('sent_count');
            } else {
                $log->update([
                    'status' => 'failed',
                    'error_message' => 'Provider gagal mengirim pesan (Cek Saldo/API Key).',
                ]);
            }
        } catch (\Exception $e) {
            // Log Failure
            $log->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }

    protected function normalizePhone($phone)
    {
        if (empty($phone)) return null;

        // Clean phone number (remove +, spaces, etc)
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        
        // Ensure starting with 62 for Indonesia if needed
        if (str_starts_with($cleanPhone, '08')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        }

        return $cleanPhone;
    }

    protected function parseContent($text, WhatsAppCampaign $campaign, Member $member)
    {
        $placeholders = [
            '{{member_name}}' => $member->name,
            '{{points_balance}}' => $member->points_balance,
            '{{tier}}' => ucfirst($member->tier),
            '{{restaurant_name}}' => $campaign->restaurant->name,
            '[[voucher_code]]' => $campaign->discount ? $campaign->discount->code : 'PROMO-RESTO',
        ];

        return str_replace(array_keys($placeholders), array_values($placeholders), $text);
    }
}
