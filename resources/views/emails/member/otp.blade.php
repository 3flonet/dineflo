<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Inter', Arial, sans-serif; background: #f4f4f7; margin: 0; padding: 40px 20px; }
        .container { max-width: 520px; margin: 0 auto; background: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 4px 30px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%); padding: 40px 40px 32px; text-align: center; }
        .header h1 { color: white; margin: 0; font-size: 22px; font-weight: 800; }
        .header p { color: rgba(255,255,255,0.7); margin: 6px 0 0; font-size: 14px; }
        .body { padding: 40px; }
        .otp-box { background: #f4f4f7; border-radius: 16px; padding: 32px; text-align: center; margin: 24px 0; }
        .otp-code { font-size: 48px; font-weight: 900; letter-spacing: 12px; color: #1a1a2e; }
        .otp-note { color: #6b7280; font-size: 13px; margin-top: 12px; }
        .btn { display: block; background: linear-gradient(135deg, #7c3aed, #4f46e5); color: white; text-decoration: none; text-align: center; padding: 16px; border-radius: 14px; font-weight: 800; font-size: 15px; margin-top: 24px; }
        .footer { text-align: center; padding: 24px 40px; color: #9ca3af; font-size: 12px; background: #fafafa; border-top: 1px solid #f0f0f0; }
        p { color: #374151; line-height: 1.7; font-size: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $restaurant->name }}</h1>
            <p>Portal Member — Kode OTP Login</p>
        </div>
        <div class="body">
            <p>Halo <strong>{{ $member->name }}</strong>,</p>
            <p>Gunakan kode OTP berikut untuk masuk ke Portal Member <strong>{{ $restaurant->name }}</strong>:</p>

            <div class="otp-box">
                <div class="otp-code">{{ $otp }}</div>
                <p class="otp-note">⏱️ Kode ini berlaku selama <strong>5 menit</strong> dan hanya bisa digunakan sekali.</p>
            </div>

            <a href="{{ $portalUrl }}" class="btn">Buka Portal Member →</a>

            <p style="margin-top: 24px; color: #9ca3af; font-size: 13px;">
                Jika Anda tidak meminta kode ini, abaikan email ini. Jangan bagikan kode OTP kepada siapapun.
            </p>
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} {{ $restaurant->name }} · Powered by Dineflo</p>
        </div>
    </div>
</body>
</html>
