<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>License Information | Dineflo POS</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --danger: #ef4444;
            --warning: #f59e0b;
            --success: #10b981;
            --bg: #0f172a;
            --card-bg: #1e293b;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Outfit', sans-serif;
        }

        body {
            background-color: var(--bg);
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 600px;
            background: var(--card-bg);
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .icon {
            font-size: 64px;
            margin-bottom: 24px;
            display: inline-block;
        }

        h1 {
            font-size: 28px;
            margin-bottom: 12px;
            font-weight: 700;
        }

        p {
            color: #94a3b8;
            line-height: 1.6;
            margin-bottom: 32px;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 0.5px;
            margin-bottom: 32px;
        }

        .status-active     { background: rgba(16, 185, 129, 0.1); color: var(--success); }
        .status-deactivated{ background: rgba(239, 68, 68, 0.1); color: var(--danger); }
        .status-invalid    { background: rgba(245, 158, 11, 0.1); color: var(--warning); }
        .status-grace      { background: rgba(99, 102, 241, 0.1); color: var(--primary); }

        .pulse {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 8px;
            display: inline-block;
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
            animation: pulse-red 2s infinite;
        }

        @keyframes pulse-red {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }

        .details {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 16px;
            padding: 24px;
            text-align: left;
            margin-bottom: 32px;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            color: #cbd5e1;
            font-size: 14px;
        }

        .detail-item:last-child { margin-bottom: 0; }

        .label { font-weight: 600; color: #64748b; }

        .actions {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .btn {
            padding: 14px 24px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 16px;
            display: inline-block;
            border: none;
        }

        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4); }

        .btn-outline { background: transparent; border: 1px solid #334155; color: #cbd5e1; }
        .btn-outline:hover { background: rgba(255, 255, 255, 0.05); color: white; }

        .back-link {
            display: block;
            margin-top: 24px;
            color: #64748b;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link:hover { color: var(--primary); }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">
            @if(in_array($status, ['active', 'grace_period']))
                🛡️
            @else
                🔒
            @endif
        </div>

        <h1>@if(in_array($status, ['active', 'grace_period'])) Status Lisensi Aman @else Sistem Belum Aktif @endif</h1>
        
        <p>@if(in_array($status, ['active', 'grace_period'])) 
            Terima kasih telah menggunakan Dineflo POS. Lisensi Anda saat ini dalam kondisi baik dan valid. 
           @else 
            Dashboard Dineflo Anda terkunci sementara karena masalah lisensi. Harap aktifkan lisensi Anda di bawah ini atau hubungi pusat bantuan.
           @endif
        </p>

        <div class="status-badge status-{{ $status }}">
            @if(!in_array($status, ['active', 'grace_period'])) <span class="pulse"></span> @endif
            {{ strtoupper($status) }}
        </div>

        <div class="details">
            <div class="detail-item">
                <span class="label">License Key</span>
                <span>{{ $licenseKey }}</span>
            </div>
            <div class="detail-item">
                <span class="label">Current Domain</span>
                <span>{{ $domain }}</span>
            </div>
            <div class="detail-item">
                <span class="label">Last Validated</span>
                <span>{{ now()->format('d M Y H:i:s') }}</span>
            </div>
        </div>

        <div class="actions">
            <!-- Sync Action -->
            <form action="/admin/license/sync" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary" style="width: 100%;">Cek Status Lisensi Sekarang</button>
            </form>

            <div style="margin: 20px 0; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px;">
                <p style="font-size: 14px; margin-bottom: 12px; font-weight: 600; color: #cbd5e1;">Punya Kunci Lisensi Baru?</p>
                <form action="/admin/license/activate" method="POST" style="display: flex; gap: 8px;">
                    @csrf
                    <input type="text" name="new_license_key" placeholder="XXXX-XXXX-XXXX-XXXX" required 
                           style="flex: 1; padding: 12px; border-radius: 8px; border: 1px solid #334155; background: #0f172a; color: white; border-radius: 12px; font-weight: 500;">
                    <button type="submit" class="btn btn-primary" style="padding: 12px 16px; font-size: 14px;">Aktifkan</button>
                </form>
            </div>

            <a href="{{ $hubUrl }}/portal/dashboard" target="_blank" class="btn btn-outline" style="margin-top: 8px;">
                Beli Lisensi Baru di Online Hub
            </a>
        </div>

        <a href="/admin" class="back-link">← Kembali ke Dashboard Dineflo</a>
    </div>
</body>
</html>
