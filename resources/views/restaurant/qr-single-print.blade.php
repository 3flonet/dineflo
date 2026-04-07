<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print QR – {{ $record->name }} | {{ $restaurant->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', 'Inter', sans-serif;
            background: #f3f4f6;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 40px 20px;
        }

        .screen-bar {
            position: fixed;
            top: 0; left: 0; right: 0;
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 100;
            box-shadow: 0 1px 8px rgba(0,0,0,0.06);
        }

        .screen-bar h1 { font-size: 16px; font-weight: 700; color: #111; }
        .screen-bar p  { font-size: 12px; color: #6b7280; }

        .btn-print {
            background: #4f46e5; color: #fff;
            border: none; cursor: pointer;
            padding: 10px 24px; border-radius: 10px;
            font-size: 14px; font-weight: 600;
            display: flex; align-items: center; gap-8px;
            gap: 8px; transition: background 0.2s;
        }
        .btn-print:hover { background: #4338ca; }
        .btn-back {
            background: #f3f4f6; color: #374151;
            border: 1px solid #d1d5db; cursor: pointer;
            padding: 10px 20px; border-radius: 10px;
            font-size: 14px; font-weight: 600;
            transition: background 0.2s;
        }
        .btn-back:hover { background: #e5e7eb; }

        .card-wrapper {
            margin-top: 80px;
        }

        @media print {
            body          { background: #fff; padding: 0; margin: 0; }
            .screen-bar   { display: none !important; }
            .card-wrapper { margin: 0; padding: 0; }

            /* A6 exact size for print */
            .qr-card {
                width: 105mm !important;
                min-height: 148mm !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
        }
    </style>
</head>
<body>
    {{-- Toolbar (screen only) --}}
    <div class="screen-bar">
        <div>
            <h1>📋 Preview Kartu QR – {{ $record->name }}</h1>
            <p>{{ $restaurant->name }} · Template: {{ ucfirst($template) }}</p>
        </div>
        <div style="display:flex; gap:10px;">
            <button class="btn-back" onclick="window.close()">← Kembali</button>
            <button class="btn-print" onclick="window.print()">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print / Save PDF
            </button>
        </div>
    </div>

    {{-- Card Preview --}}
    <div class="card-wrapper">
        @include('restaurant.qr-card', [
            'record'   => $record,
            'template' => $template,
        ])
    </div>
</body>
</html>
