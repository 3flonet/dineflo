<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk Print QR Meja | {{ $restaurant->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', 'Inter', sans-serif;
            background: #f3f4f6;
            padding: 0;
        }

        /* ── Screen toolbar ── */
        .screen-bar {
            position: sticky;
            top: 0; left: 0; right: 0;
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            padding: 14px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 100;
            box-shadow: 0 1px 8px rgba(0,0,0,0.07);
        }
        .screen-bar h1  { font-size: 17px; font-weight: 800; color: #111; }
        .screen-bar p   { font-size: 12px; color: #6b7280; margin-top: 2px; }

        .template-tabs {
            display: flex; gap: 6px; align-items: center;
        }
        .tab-btn {
            padding: 7px 16px; border-radius: 8px; font-size: 13px;
            font-weight: 600; cursor: pointer; border: 1.5px solid #d1d5db;
            background: #f9fafb; color: #374151; transition: all 0.15s;
        }
        .tab-btn.active {
            background: #4f46e5; color: #fff; border-color: #4f46e5;
        }
        .tab-btn.custom-btn {
            background: #10b981; color: #fff; border-color: #10b981;
        }
        .tab-btn.custom-btn.active {
            background: #059669; border-color: #059669;
        }
        .tab-btn:hover:not(.active) { background: #e5e7eb; }

        .btn-print {
            background: #4f46e5; color: #fff;
            border: none; cursor: pointer;
            padding: 10px 24px; border-radius: 10px;
            font-size: 14px; font-weight: 700;
            display: inline-flex; align-items: center; gap: 8px;
            transition: background 0.2s;
        }
        .btn-print:hover { background: #4338ca; }

        /* ── Card Grid ── */
        .cards-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 24px;
            justify-content: center;
            padding: 32px 24px;
        }

        .card-cell {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .card-label {
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            text-align: center;
        }

        /* Template panels – show/hide via JS */
        .tpl-panel { display: none; }
        .tpl-panel.active { display: flex; flex-wrap: wrap; gap: 24px; justify-content: center; padding: 32px 24px; }

        /* ── Print ── */
        @media print {
            .screen-bar { display: none !important; }

            body { background: #fff; }

            .tpl-panel.active {
                display: flex !important;
                flex-wrap: wrap;
                gap: 0;
                padding: 0;
                justify-content: flex-start;
            }

            .card-label { display: none; }

            .card-cell {
                page-break-inside: avoid;
                break-inside: avoid;
                margin: 4mm;
            }

            .qr-card {
                width: 105mm !important;
                min-height: 148mm !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            /* Cut guide */
            .card-cell::after {
                content: '';
                display: block;
                height: 0;
                border-bottom: 0.5px dashed #ccc;
            }
        }
    </style>
</head>
<body>

    {{-- Toolbar --}}
    <div class="screen-bar">
        <div>
            <h1>🖨️ Bulk Print QR Meja — {{ $restaurant->name }}</h1>
            <p>{{ $tables->count() }} meja · Klik template yang diinginkan, lalu Print.</p>
        </div>
        <div style="display:flex; align-items:center; gap:12px;">
            <div class="template-tabs">
                <span style="font-size:12px; font-weight:600; color:#9ca3af; margin-right:4px;">Template:</span>
                <button class="tab-btn custom-btn active" onclick="switchTemplate('custom', this)">🎨 Desain Custom</button>
                <button class="tab-btn" onclick="switchTemplate('minimal', this)">✨ Minimal</button>
                <button class="tab-btn" onclick="switchTemplate('bistro', this)">☕ Bistro</button>
                <button class="tab-btn" onclick="switchTemplate('dark', this)">🌙 Dark</button>
            </div>
            <button class="btn-print" onclick="window.print()">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print Semua ({{ $tables->count() }} Kartu)
            </button>
        </div>
    </div>

    {{-- Template: Custom Global --}}
    <div id="tpl-custom" class="tpl-panel active">
        @foreach($tables as $table)
            <div class="card-cell">
                <div class="card-label">{{ $table->name }} {{ $table->area ? '• '.$table->area : '' }}</div>
                @include('restaurant.qr-card-custom', ['record' => $table])
            </div>
        @endforeach
    </div>

    {{-- Template: Minimal --}}
    <div id="tpl-minimal" class="tpl-panel">
        @foreach($tables as $table)
            <div class="card-cell">
                <div class="card-label">{{ $table->name }} {{ $table->area ? '• '.$table->area : '' }}</div>
                @include('restaurant.qr-card', ['record' => $table, 'template' => 'minimal'])
            </div>
        @endforeach
    </div>

    {{-- Template: Bistro --}}
    <div id="tpl-bistro" class="tpl-panel">
        @foreach($tables as $table)
            <div class="card-cell">
                <div class="card-label">{{ $table->name }} {{ $table->area ? '• '.$table->area : '' }}</div>
                @include('restaurant.qr-card', ['record' => $table, 'template' => 'bistro'])
            </div>
        @endforeach
    </div>

    {{-- Template: Dark --}}
    <div id="tpl-dark" class="tpl-panel">
        @foreach($tables as $table)
            <div class="card-cell">
                <div class="card-label">{{ $table->name }} {{ $table->area ? '• '.$table->area : '' }}</div>
                @include('restaurant.qr-card', ['record' => $table, 'template' => 'dark'])
            </div>
        @endforeach
    </div>

    <script>
        function switchTemplate(name, btn) {
            document.querySelectorAll('.tpl-panel').forEach(p => p.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.getElementById('tpl-' + name).classList.add('active');
            btn.classList.add('active');
        }
    </script>
</body>
</html>
