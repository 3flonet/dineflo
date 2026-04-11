<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public ?string $site_favicon;
    public ?string $site_og_image;
    public ?string $pwa_icon_512; // PWA Icon 512x512
    public ?string $pwa_icon_192; // PWA Icon 192x192
    public ?string $site_description;
    public $site_keywords = [];
    public ?string $site_author;
    public ?string $site_twitter_handle;
    public ?string $site_address;
    public ?string $site_phone;

    public string $site_name;
    public ?string $site_logo;
    public string $support_email;
    public ?string $site_currency;
    public ?string $site_timezone;

    // ── Landing Page Settings ────────────────────────────────────────────────
    public ?string $landing_hero_title;
    public ?string $landing_hero_subtitle;
    public ?string $landing_hero_cta_primary_text;
    public ?string $landing_hero_cta_primary_link;
    public ?string $landing_hero_cta_secondary_text;
    public ?string $landing_hero_cta_secondary_link;
    public ?string $landing_hero_mockup_image;
    public array $landing_hero_mockups = [];

    // ── Social Media Links ───────────────────────────────────────────────────
    public ?string $site_facebook_url;
    public ?string $site_instagram_url;
    public ?string $site_youtube_url;
    public ?string $site_linkedin_url;
    public ?string $site_github_url;
    public ?string $site_twitter_url;

    public ?string $site_google_maps_embed;

    public bool $midtrans_is_production;
    public string $midtrans_merchant_id;
    public string $midtrans_server_key;
    public string $midtrans_client_key;

    // ── Finance & Fee Settings ───────────────────────────────────────────────
    // Midtrans fee rates (dynamic, can be updated from Admin Panel)
    public float $midtrans_qris_fee_percentage  = 0.70;  // QRIS / GoPay / ShopeePay
    public float $midtrans_va_fee_flat          = 4000;  // Virtual Account (flat Rp)
    public float $midtrans_cc_fee_percentage    = 2.00;  // Credit Card
    public float $midtrans_cstore_fee_flat      = 5000;  // Minimarket (flat Rp)

    // Dineflo platform fee on withdraw (0 = disabled / free)
    public float $dineflo_withdraw_admin_fee_percentage = 0.00;

    // Dineflo commission per transaction (0 = disabled / free)
    public float $dineflo_commission_percentage = 0.00;


    // ── Subscription Settings ────────────────────────────────────────────────
    public int $subscription_expiry_warning_days = 7; // Days before expiry to show warning / allow renewal

    // ── Platform WhatsApp (Multi-Provider) ──────────────────────────────────
    // Digunakan untuk System Broadcast WA dari platform Dineflo ke owner restoran
    public bool    $platform_wa_is_active = false;   // Global toggle
    public ?string $platform_wa_provider  = 'fonnte'; // Provider aktif: fonnte | wablas | zenziva | dsb

    // Provider-specific credentials
    public ?string $platform_fonnte_api_key    = null;  // Fonnte: API Token
    public ?string $platform_watzap_api_key    = null;  // Watzap.id: API Key
    public ?string $platform_watzap_number_key = null;  // Watzap.id: Number Key (nomor sender)
    public ?string $platform_watsap_api_key    = null;  // Watsap.id: API Key
    public ?string $platform_watsap_id_device  = null;  // Watsap.id: ID Device (nomor pengirim)

    public ?string $smtp_host;
    public ?int $smtp_port;
    public ?string $smtp_username;
    public ?string $smtp_password;
    public ?string $smtp_encryption;
    public ?string $smtp_from_address;
    public ?string $smtp_from_name;
    
    // ── Broadcasting & Real-time Settings ────────────────────────────────────
    public string $broadcast_driver = 'reverb';
    public ?string $pusher_app_id;
    public ?string $pusher_app_key;
    public ?string $pusher_app_secret;
    public ?string $pusher_app_cluster;
    
    public ?string $reverb_app_id;
    public ?string $reverb_app_key;
    public ?string $reverb_app_secret;
    public ?string $reverb_host;
    public ?int $reverb_port;
    public ?string $reverb_scheme;

    public static function group(): string
    {
        return 'general';
    }
}
