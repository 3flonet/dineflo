<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // ── General ──────────────────────────────────────────────────────
            ['group' => 'general', 'name' => 'site_name',        'payload' => json_encode('Dineflo')],
            ['group' => 'general', 'name' => 'site_logo',        'payload' => json_encode(null)],
            ['group' => 'general', 'name' => 'support_email',    'payload' => json_encode('support@dineflo.com')],
            ['group' => 'general', 'name' => 'site_currency',    'payload' => json_encode('IDR')],
            ['group' => 'general', 'name' => 'site_timezone',    'payload' => json_encode('Asia/Jakarta')],
            ['group' => 'general', 'name' => 'site_favicon',       'payload' => json_encode(null)],
            ['group' => 'general', 'name' => 'site_og_image',      'payload' => json_encode(null)],
            ['group' => 'general', 'name' => 'pwa_icon_512',       'payload' => json_encode(null)],
            ['group' => 'general', 'name' => 'pwa_icon_192',       'payload' => json_encode(null)],
            ['group' => 'general', 'name' => 'site_description',   'payload' => json_encode('Platform manajemen restoran berbasis QR Code')],
            ['group' => 'general', 'name' => 'site_keywords',      'payload' => json_encode([])],
            ['group' => 'general', 'name' => 'site_author',        'payload' => json_encode('Dineflo')],
            ['group' => 'general', 'name' => 'site_twitter_handle','payload' => json_encode('@dineflo')],
            ['group' => 'general', 'name' => 'site_address',       'payload' => json_encode('Jakarta, Indonesia')],
            ['group' => 'general', 'name' => 'site_phone',         'payload' => json_encode(null)],

            // ── Social Media Links ───────────────────────────────────────────
            ['group' => 'general', 'name' => 'site_facebook_url',  'payload' => json_encode(null)],
            ['group' => 'general', 'name' => 'site_instagram_url', 'payload' => json_encode(null)],
            ['group' => 'general', 'name' => 'site_youtube_url',   'payload' => json_encode(null)],
            ['group' => 'general', 'name' => 'site_linkedin_url',  'payload' => json_encode(null)],
            ['group' => 'general', 'name' => 'site_github_url',    'payload' => json_encode(null)],
            ['group' => 'general', 'name' => 'site_twitter_url',   'payload' => json_encode(null)],
            ['group' => 'general', 'name' => 'site_google_maps_embed', 'payload' => json_encode(null)],

            // ── Midtrans ─────────────────────────────────────────────────────
            ['group' => 'general', 'name' => 'midtrans_is_production', 'payload' => json_encode(false)],
            ['group' => 'general', 'name' => 'midtrans_merchant_id',   'payload' => json_encode('')],
            ['group' => 'general', 'name' => 'midtrans_server_key',    'payload' => json_encode('SB-Mid-server-xxx')],
            ['group' => 'general', 'name' => 'midtrans_client_key',    'payload' => json_encode('SB-Mid-client-xxx')],

            // ── Finance & Fee Settings ────────────────────────────────────────
            ['group' => 'general', 'name' => 'midtrans_qris_fee_percentage',          'payload' => json_encode(0.70)],
            ['group' => 'general', 'name' => 'midtrans_va_fee_flat',                  'payload' => json_encode(4000)],
            ['group' => 'general', 'name' => 'midtrans_cc_fee_percentage',             'payload' => json_encode(2.00)],
            ['group' => 'general', 'name' => 'midtrans_cstore_fee_flat',               'payload' => json_encode(5000)],
            ['group' => 'general', 'name' => 'dineflo_withdraw_admin_fee_percentage',  'payload' => json_encode(0.00)],
            ['group' => 'general', 'name' => 'dineflo_commission_percentage',          'payload' => json_encode(0.00)],
            ['group' => 'general', 'name' => 'subscription_expiry_warning_days',       'payload' => json_encode(7)],

            // ── WhatsApp Provider ────────────────────────────────────────────
            ['group' => 'general', 'name' => 'platform_wa_is_active',      'payload' => json_encode(false)],
            ['group' => 'general', 'name' => 'platform_wa_provider',       'payload' => json_encode('fonnte')],
            ['group' => 'general', 'name' => 'platform_fonnte_api_key',    'payload' => json_encode(null)],
            ['group' => 'general', 'name' => 'platform_watzap_api_key',    'payload' => json_encode(null)],
            ['group' => 'general', 'name' => 'platform_watzap_number_key', 'payload' => json_encode(null)],
            ['group' => 'general', 'name' => 'platform_watsap_api_key',    'payload' => json_encode(null)],
            ['group' => 'general', 'name' => 'platform_watsap_id_device',  'payload' => json_encode(null)],

            // ── SMTP ─────────────────────────────────────────────────────────
            ['group' => 'general', 'name' => 'smtp_host',         'payload' => json_encode('smtp.mailtrap.io')],
            ['group' => 'general', 'name' => 'smtp_port',         'payload' => json_encode(2525)],
            ['group' => 'general', 'name' => 'smtp_username',     'payload' => json_encode('username')],
            ['group' => 'general', 'name' => 'smtp_password',     'payload' => json_encode('password')],
            ['group' => 'general', 'name' => 'smtp_encryption',   'payload' => json_encode('tls')],
            ['group' => 'general', 'name' => 'smtp_from_address', 'payload' => json_encode('noreply@dineflo.com')],
            ['group' => 'general', 'name' => 'smtp_from_name',    'payload' => json_encode('Dineflo System')],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->upsert(
                array_merge($setting, ['locked' => false]),
                ['group', 'name'],  // unique keys
                ['payload']         // field yang di-update jika sudah ada
            );
        }

        $this->command->info('✅ Settings seeded: General, Midtrans, Finance & Fee, SMTP');
    }
}
