<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Inter', Arial, sans-serif; background: #f4f4f7; margin: 0; padding: 40px 20px; }
        .container { max-width: 520px; margin: 0 auto; background: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 4px 30px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); padding: 40px 40px 32px; text-align: center; }
        .header h1 { color: white; margin: 0; font-size: 22px; font-weight: 800; }
        .header p { color: rgba(255,255,255,0.8); margin: 6px 0 0; font-size: 14px; }
        .body { padding: 40px; }
        .benefits { background: #fffbeb; border: 1px solid #fde68a; border-radius: 16px; padding: 24px; margin: 24px 0; }
        .benefit-item { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; color: #92400e; font-size: 14px; font-weight: 600; }
        .btn { display: block; background: linear-gradient(135deg, #f59e0b, #d97706); color: white; text-decoration: none; text-align: center; padding: 16px; border-radius: 14px; font-weight: 800; font-size: 15px; margin-top: 24px; }
        .footer { text-align: center; padding: 24px 40px; color: #9ca3af; font-size: 12px; background: #fafafa; border-top: 1px solid #f0f0f0; }
        p { color: #374151; line-height: 1.7; font-size: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Selamat Datang!</h1>
            <p>{{ $restaurant->name }} — Member Baru</p>
        </div>
        <div class="body">
            <p>Halo <strong>{{ $member->name }}</strong>,</p>
            <p>Selamat! Anda telah resmi terdaftar sebagai <strong>Member {{ $restaurant->name }}</strong>.</p>

            <div class="benefits">
                <div class="benefit-item">✨ Kumpulkan poin dari setiap transaksi</div>
                <div class="benefit-item">📊 Pantau tier & histori belanja Anda</div>
                <div class="benefit-item">🏅 Naiki tier Bronze → Silver → Gold</div>
                <div class="benefit-item">🎁 Dapatkan reward eksklusif member</div>
            </div>

            <a href="{{ $portalUrl }}" class="btn">Buka Portal Member Saya →</a>

            <p style="margin-top: 20px; color: #9ca3af; font-size: 13px; text-align: center;">
                Login hanya dengan nomor WhatsApp — tanpa password
            </p>
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} {{ $restaurant->name }} · Powered by {{ config('app.name', 'Dineflo') }}</p>
        </div>
    </div>
</body>
</html>
