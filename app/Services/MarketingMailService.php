<?php

namespace App\Services;

use App\Models\Restaurant;
use App\Models\Member;
use App\Models\EmailCampaign;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use App\Mail\CampaignMailable;

class MarketingMailService
{
    public function sendTestEmail(EmailCampaign $campaign, string $recipientEmail)
    {
        $restaurant = $campaign->restaurant;
        
        // 1. Setup Mailer Configuration
        $this->setupMailer($restaurant);

        // 2. Mock a generic member for placeholders
        $mockMember = new Member([
            'name' => 'Demo User',
            'points_balance' => 750,
            'tier' => 'gold',
            'email' => $recipientEmail
        ]);

        // 3. Process Content
        $content = $this->parseContent($campaign->content, $campaign, $mockMember);
        $subject = '[TEST] ' . $this->parseContent($campaign->subject, $campaign, $mockMember);

        // 4. Send Email (No logging to DB for tests)
        Mail::mailer('smtp')->to($recipientEmail)->send(new CampaignMailable($subject, $content, $restaurant, $campaign));
    }

    public function sendCampaignEmail(EmailCampaign $campaign, Member $member)
    {
        $restaurant = $campaign->restaurant;
        
        // 1. Setup Mailer Configuration
        $this->setupMailer($restaurant);

        // 2. Create Log Entry First (to get tracking_hash)
        $log = $campaign->logs()->create([
            'member_id' => $member->id,
            'sent_at' => now(),
            'status' => 'pending', // Initially pending
        ]);

        // 3. Process Content (Placeholders)
        $content = $this->parseContent($campaign->content, $campaign, $member);
        $subject = $this->parseContent($campaign->subject, $campaign, $member);

        // 4. Append Tracking Pixel
        $trackingUrl = route('marketing.tracking', ['hash' => $log->tracking_hash]);
        $content .= '<img src="' . $trackingUrl . '" width="1" height="1" style="display:none !important;" />';

        // 5. Send Email
        try {
            Mail::mailer('smtp')->to($member->email)->send(new CampaignMailable($subject, $content, $restaurant, $campaign));
            
            // Log Success
            $log->update([
                'sent_at' => now(),
                'status' => 'sent',
            ]);

            // Increment campaign sent count
            $campaign->increment('sent_count');
        } catch (\Exception $e) {
            // Log Failure
            $log->update([
                'sent_at' => now(),
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }

    public function sendTransactionalEmail(Restaurant $restaurant, string $recipient, $mailable)
    {
        // 1. Setup Mailer Configuration
        $this->setupMailer($restaurant);

        // 2. Send Email
        try {
            Mail::mailer('smtp')->to($recipient)->send($mailable);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Transactional Email Error for Restaurant [{$restaurant->name}]: " . $e->getMessage());
            throw $e;
        }
    }

    public function setupMailer(Restaurant $restaurant)
    {
        // Reset to system default first (from settings)
        $settings = app(\App\Settings\GeneralSettings::class);
        $config = [
            'host' => $settings->smtp_host,
            'port' => $settings->smtp_port,
            'username' => $settings->smtp_username,
            'password' => $settings->smtp_password,
            'encryption' => $settings->smtp_encryption,
            'from_address' => $settings->smtp_from_address,
            'from_name' => $settings->smtp_from_name,
        ];

        // Check if restaurant has Remove Branding and Custom SMTP
        if ($restaurant->owner->hasFeature('Remove Branding') && 
            $restaurant->email_marketing_provider === 'custom' && 
            $restaurant->email_marketing_smtp_host) {
            
            $config = [
                'host' => $restaurant->email_marketing_smtp_host,
                'port' => $restaurant->email_marketing_smtp_port,
                'username' => $restaurant->email_marketing_smtp_username,
                'password' => $restaurant->email_marketing_smtp_password,
                'encryption' => $restaurant->email_marketing_smtp_encryption === 'null' ? null : $restaurant->email_marketing_smtp_encryption,
                'from_address' => $restaurant->email_marketing_smtp_from_address,
                'from_name' => $restaurant->email_marketing_smtp_from_name,
            ];
        }

        Config::set('mail.mailers.smtp.host', $config['host']);
        Config::set('mail.mailers.smtp.port', $config['port']);
        Config::set('mail.mailers.smtp.username', $config['username']);
        Config::set('mail.mailers.smtp.password', $config['password']);
        Config::set('mail.mailers.smtp.encryption', $config['encryption']);
        Config::set('mail.from.address', $config['from_address']);
        Config::set('mail.from.name', $config['from_name']);

        // Purge to apply changes
        Mail::purge('smtp');
    }

    protected function parseContent($text, EmailCampaign $campaign, Member $member)
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
