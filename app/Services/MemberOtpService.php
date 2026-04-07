<?php

namespace App\Services;

use App\Models\Member;
use App\Models\MemberOtpToken;
use App\Models\Restaurant;
use App\Services\WhatsApp\WhatsAppService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MemberOtpService
{
    /**
     * Generate & send OTP to member via WA and/or email.
     * Returns info about which channels were used.
     */
    public static function send(Member $member, Restaurant $restaurant): array
    {
        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Invalidate old unused OTPs
        MemberOtpToken::where('member_id', $member->id)
            ->where('is_used', false)
            ->delete();

        // Save new OTP (Stop saving plain 'token' for security)
        MemberOtpToken::create([
            'member_id'  => $member->id,
            'token'      => null, // Clear plain token
            'token_hash' => Hash::make($otp),
            'expires_at' => now()->addMinutes(5),
            'is_used'    => false,
            'attempts'   => 0,
        ]);

        $portalUrl = route('member.portal.login', ['restaurant' => $restaurant->slug]);
        $sentVia   = [];

        $message = self::buildWaMessage($restaurant, $member, $otp, $portalUrl);

        // ── Kirim via WhatsApp (jika aktif di restoran) ─────────────────
        if ($restaurant->wa_is_active && $restaurant->wa_api_key) {
            \App\Jobs\SendWhatsAppMessage::dispatch($restaurant, $member->whatsapp, $message);
            $sentVia[] = ['channel' => 'whatsapp', 'destination' => self::maskPhone($member->whatsapp)];
        }

        // ── Kirim via Email (jika member punya email) ───────────────────
        if ($member->email) {
            try {
                // Ensure config is resolved before queuing
                self::resolveMailer($restaurant);
                
                Mail::to($member->email)->queue(new \App\Mail\MemberOtpMail($member, $restaurant, $otp, $portalUrl));
                $sentVia[] = ['channel' => 'email', 'destination' => self::maskEmail($member->email)];
            } catch (\Exception $e) {
                Log::warning('MemberOTP Email enqueue failed: ' . $e->getMessage());
            }
        }

        return $sentVia;
    }

    /**
     * Verify OTP token. Returns true if valid, false if not.
     */
    public static function verify(Member $member, string $inputOtp): bool
    {
        $token = MemberOtpToken::where('member_id', $member->id)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$token) return false;

        // Rate limit: Max 3 attempts per OTP
        if ($token->attempts >= 3) {
            $token->update(['is_used' => true]);
            return false;
        }

        if (!Hash::check($inputOtp, $token->token_hash)) {
            $token->increment('attempts');
            return false;
        }

        $token->update(['is_used' => true]);
        return true;
    }

    /**
     * Rate limiting: Check if member can request a new OTP.
     * Limit: 1 minute between requests.
     */
    public static function canSend(Member $member): bool
    {
        $lastToken = MemberOtpToken::where('member_id', $member->id)
            ->latest()
            ->first();

        if (!$lastToken) return true;

        // Allow only after 60 seconds
        return $lastToken->created_at->addMinute()->isPast();
    }

    /**
     * Send welcome message when member is first created.
     */
    public static function sendWelcome(Member $member, Restaurant $restaurant): void
    {
        $portalUrl = route('member.portal.login', ['restaurant' => $restaurant->slug]);

        $waMessage = "🎉 *Selamat bergabung, {$member->name}!*\n\n"
            . "Anda telah terdaftar sebagai Member *{$restaurant->name}*.\n\n"
            . "✨ Nikmati keuntungan sebagai member:\n"
            . "• Kumpulkan poin dari setiap transaksi\n"
            . "• Pantau tier & histori belanja Anda\n"
            . "• Dapatkan reward eksklusif\n\n"
            . "🔗 *Portal Member Anda:*\n{$portalUrl}\n\n"
            . "_Login cukup dengan nomor WhatsApp, tanpa password!_";

        // Kirim WA jika aktif
        if ($restaurant->wa_is_active && $restaurant->wa_api_key) {
            \App\Jobs\SendWhatsAppMessage::dispatch($restaurant, $member->whatsapp, $waMessage);
        }

        // Kirim email sambutan jika ada email
        if ($member->email) {
            try {
                self::resolveMailer($restaurant);
                Mail::to($member->email)->queue(new \App\Mail\MemberWelcomeMail($member, $restaurant, $portalUrl));
            } catch (\Exception $e) {
                Log::warning('MemberOTP welcome email enqueue failed: ' . $e->getMessage());
            }
        }
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    private static function buildWaMessage(Restaurant $restaurant, Member $member, string $otp, string $portalUrl): string
    {
        return "🔐 *Kode OTP Login - {$restaurant->name}*\n\n"
            . "Halo *{$member->name}*,\n\n"
            . "Kode OTP Anda untuk login ke Portal Member:\n\n"
            . "  *{$otp}*\n\n"
            . "⏱️ Kode berlaku *5 menit* dan hanya bisa digunakan sekali.\n\n"
            . "🔗 Portal Member: {$portalUrl}\n\n"
            . "_Jangan bagikan kode ini kepada siapapun._";
    }

    private static function sendEmail(Restaurant $restaurant, Member $member, string $otp, string $portalUrl): void
    {
        Mail::mailer(self::resolveMailer($restaurant))
            ->to($member->email)
            ->send(new \App\Mail\MemberOtpMail($member, $restaurant, $otp, $portalUrl));
    }

    /**
     * Gunakan SMTP restoran jika dikonfigurasi, fallback ke default Dineflo.
     */
    private static function resolveMailer(Restaurant $restaurant): string
    {
        if (!empty($restaurant->email_marketing_smtp_host)) {
            config([
                'mail.mailers.restaurant_smtp' => [
                    'transport'  => 'smtp',
                    'host'       => $restaurant->email_marketing_smtp_host,
                    'port'       => $restaurant->email_marketing_smtp_port ?? 587,
                    'encryption' => $restaurant->email_marketing_smtp_encryption ?? 'tls',
                    'username'   => $restaurant->email_marketing_smtp_username,
                    'password'   => $restaurant->email_marketing_smtp_password,
                ],
                // WAJIB KARENA BEBERAPA SERVER SMTP SPT HOSTINGER MENOLAK SPOOFING
                'mail.from.address' => $restaurant->email_marketing_smtp_username,
                'mail.from.name'    => $restaurant->name,
            ]);
            return 'restaurant_smtp';
        }
        
        // Tetapkan nama sender menjadi nama restoran tapi tetap gunakan email system
        config(['mail.from.name' => $restaurant->name]);
        return config('mail.default');
    }

    public static function maskPhone(string $phone): string
    {
        $clean = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($clean) <= 6) return $clean;
        return substr($clean, 0, strlen($clean) - 3) . str_repeat('*', 3);
    }

    public static function maskEmail(string $email): string
    {
        [$user, $domain] = explode('@', $email, 2);
        $masked = strlen($user) > 3
            ? substr($user, 0, 3) . str_repeat('*', strlen($user) - 3)
            : $user;
        return $masked . '@' . $domain;
    }
}
