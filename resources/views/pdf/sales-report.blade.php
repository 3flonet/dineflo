<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { margin-bottom: 20px; border-bottom: 2px solid #ea580c; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #ea580c; }
        .header p { margin: 5px 0 0; }
        .meta { margin-bottom: 20px; }
        .summary-box { 
            background: #f3f4f6; 
            padding: 15px; 
            border-radius: 8px; 
            margin-bottom: 20px; 
            display: table; 
            width: 100%; 
        }
        .stat-item { display: table-cell; text-align: center; width: 33%; }
        .stat-label { font-size: 10px; text-transform: uppercase; color: #6b7280; font-weight: bold; }
        .stat-value { font-size: 18px; font-weight: bold; color: #111827; margin-top: 5px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { background-color: #f9fafb; font-weight: bold; font-size: 11px; text-transform: uppercase; }
        
        .footer { position: fixed; bottom: 0; left: 0; right: 0; font-size: 10px; color: #9ca3af; text-align: center; border-top: 1px solid #e5e7eb; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $restaurant->name }}</h1>
        <p>{{ $restaurant->address }} | {{ $restaurant->phone }}</p>
    </div>

    <div class="meta">
        <strong>Laporan Penjualan</strong><br>
        Periode: {{ \Carbon\Carbon::parse($start)->format('d M Y') }} - {{ \Carbon\Carbon::parse($end)->format('d M Y') }}<br>
        Dicetak pada: {{ now()->format('d M Y H:i') }}
    </div>

    <div class="summary-box">
        <div class="stat-item">
            <div class="stat-label">Gross Revenue</div>
            <div class="stat-value">Rp {{ number_format($grossRevenue, 0, ',', '.') }}</div>
        </div>
        <div class="stat-item">
            <div class="stat-label">Total Potongan</div>
            <div class="stat-value" style="color: #dc2626;">-Rp {{ number_format($totalDiscount, 0, ',', '.') }}</div>
        </div>
        <div class="stat-item">
            <div class="stat-label">Total Revenue (Net)</div>
            <div class="stat-value" style="color: #16a34a;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="summary-box" style="margin-top: -10px;">
        <div class="stat-item">
            <div class="stat-label">Total Pesanan</div>
            <div class="stat-value">{{ $totalOrders }}</div>
        </div>
        <div class="stat-item">
            <div class="stat-label">Total Refund</div>
            <div class="stat-value" style="color: #6366f1;">Rp {{ number_format($totalRefunded, 0, ',', '.') }}</div>
        </div>
        <div class="stat-item">
            <div class="stat-label">Rata-rata Transaksi</div>
            <div class="stat-value">Rp {{ number_format($avgOrderValue, 0, ',', '.') }}</div>
        </div>
    </div>

    <h3>Menu Terlaris (Top 5)</h3>
    <table>
        <thead>
            <tr>
                <th>Menu Item</th>
                <th>Kategori</th>
                <th style="text-align: center;">Terjual</th>
                <th style="text-align: right;">Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topItems as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td>{{ $item->category->name ?? '-' }}</td>
                <td style="text-align: center;">{{ $item->total_sold }}</td>
                <td style="text-align: right;">Rp {{ number_format($item->total_revenue, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Detail Transaksi Terakhir (Max 50)</h3>
    <table>
        <thead>
            <tr>
                <th>No. Order</th>
                <th>Tanggal</th>
                <th>Meja</th>
                <th>Status</th>
                <th style="text-align: right;">Total</th>
                <th style="text-align: right; color: #6366f1;">Refund</th>
                <th style="text-align: right; color: #16a34a;">Net Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            @php $refunded = (float)($order->refunded_amount ?? 0); @endphp
            <tr>
                <td>{{ $order->order_number }}</td>
                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $order->table->name ?? 'Takeaway' }}</td>
                <td>{{ ucfirst($order->status) }}</td>
                <td style="text-align: right;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                <td style="text-align: right; color: {{ $refunded > 0 ? '#6366f1' : '#9ca3af' }};">
                    {{ $refunded > 0 ? '-Rp ' . number_format($refunded, 0, ',', '.') : '-' }}
                </td>
                <td style="text-align: right; font-weight: bold; color: #16a34a;">
                    Rp {{ number_format($order->total_amount - $refunded, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generated by {{ config('app.name', 'Dineflo') }} Restaurant Management System
    </div>
</body>
</html>
