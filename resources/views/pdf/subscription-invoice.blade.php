<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Subscription Invoice #{{ $invoice->midtrans_id ?? $invoice->id }}</title>
    @php
        $settings = app(\App\Settings\GeneralSettings::class);
    @endphp
    <style>
        @page { margin: 0; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 14px; color: #1f2937; margin: 0; padding: 0; line-height: 1.5; }
        .invoice-box { padding: 40px; margin: 0; position: relative; }
        
        /* Decorative Header background */
        .header-bg { background-color: #4f46e5; height: 10px; width: 100%; border-bottom: 5px solid #4338ca; }
        
        .header { margin-bottom: 40px; }
        .logo { max-height: 50px; float: left; }
        .invoice-title { float: right; text-align: right; }
        .invoice-title h1 { margin: 0; color: #4f46e5; font-size: 28px; font-weight: 800; letter-spacing: -0.025em; }
        .invoice-title p { margin: 5px 0 0; font-size: 12px; color: #6b7280; font-weight: 600; text-transform: uppercase; }
        .clear { clear: both; }
        
        .info-grid { margin-bottom: 40px; }
        .info-col { width: 48%; float: left; }
        .info-col.right { float: right; }
        .info-label { display: block; font-size: 10px; font-weight: 700; color: #9ca3af; text-transform: uppercase; margin-bottom: 8px; border-bottom: 1px solid #f3f4f6; padding-bottom: 4px; }
        .info-content { font-size: 13px; color: #374151; }
        .info-content strong { color: #111827; font-size: 15px; }
        
        table.items-table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        table.items-table th { background-color: #f9fafb; border-bottom: 2px solid #e5e7eb; padding: 12px 15px; text-align: left; font-size: 11px; font-weight: 700; color: #4b5563; text-transform: uppercase; }
        table.items-table td { padding: 20px 15px; border-bottom: 1px solid #f3f4f6; vertical-align: middle; }
        .item-name { font-weight: 700; color: #111827; font-size: 15px; margin-bottom: 4px; }
        .item-desc { font-size: 12px; color: #6b7280; }
        
        .total-container { float: right; width: 300px; }
        .total-row { display: table; width: 100%; margin-bottom: 10px; }
        .total-label { display: table-cell; text-align: left; color: #6b7280; font-size: 13px; }
        .total-value { display: table-cell; text-align: right; font-weight: 600; color: #111827; font-size: 14px; }
        .grand-total { margin-top: 15px; padding-top: 15px; border-top: 2px solid #e5e7eb; }
        .grand-total .total-label { font-weight: 800; color: #111827; font-size: 15px; text-transform: uppercase; }
        .grand-total .total-value { font-weight: 800; color: #4f46e5; font-size: 20px; }
        
        .footer { margin-top: 80px; text-align: center; border-top: 1px solid #f3f4f6; padding-top: 30px; }
        .footer p { margin: 0; font-size: 11px; color: #9ca3af; }
        .footer .brand { font-weight: 700; color: #4f46e5; margin-bottom: 15px; display: block; font-size: 14px; }
        
        .badge { display: inline-block; padding: 6px 12px; border-radius: 9999px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
        .badge-paid { background-color: #ecfdf5; color: #059669; border: 1px solid #10b981; }
        .badge-pending { background-color: #fffbeb; color: #d97706; border: 1px solid #f59e0b; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header-bg"></div>
    <div class="invoice-box">
        <!-- Header -->
        <div class="header">
            <div class="logo-container">
                @if($settings->site_logo && file_exists(public_path('storage/' . $settings->site_logo)))
                    <img src="{{ public_path('storage/' . $settings->site_logo) }}" class="logo">
                @else
                    <h1 style="float: left; margin: 0; color: #4f46e5; font-weight: 800;">{{ $settings->site_name ?? 'DINEFLO' }}</h1>
                @endif
            </div>
            
            <div class="invoice-title">
                <h1>INVOICE</h1>
                <p>#{{ $invoice->midtrans_id ?? 'INV-'.$invoice->id }}</p>
                <div style="margin-top: 10px;">
                    <span class="badge badge-{{ $invoice->status === 'paid' ? 'paid' : 'pending' }}">
                        {{ $invoice->status }}
                    </span>
                </div>
            </div>
            <div class="clear"></div>
        </div>
        
        <!-- Info Grid -->
        <div class="info-grid">
            <div class="info-col">
                <span class="info-label">Diterbitkan Oleh</span>
                <div class="info-content">
                    <strong>{{ $settings->site_name ?? 'Dineflo Business' }}</strong><br>
                    {{ $settings->site_address ?? 'Jakarta, Indonesia' }}<br>
                    Email: {{ $settings->support_email }}<br>
                    WA: {{ $settings->site_phone ?? '-' }}
                </div>
            </div>
            <div class="info-col right">
                <span class="info-label">Ditagihkan Kepada</span>
                <div class="info-content">
                    <strong>{{ $invoice->subscription->user->name }}</strong><br>
                    Email: {{ $invoice->subscription->user->email }}<br>
                    @if($invoice->subscription->user->ownedRestaurants->count() > 0)
                        Restaurant: {{ $invoice->subscription->user->ownedRestaurants->first()->name }}
                    @endif
                </div>
            </div>
            <div class="clear"></div>
        </div>

        <div class="info-grid" style="margin-bottom: 20px;">
            <div class="info-col">
                <span class="info-label">Tanggal Pembayaran</span>
                <div class="info-content">
                    {{ $invoice->paid_at ? $invoice->paid_at->format('d F Y, H:i') : $invoice->created_at->format('d F Y') }}
                </div>
            </div>
            <div class="info-col right">
                <span class="info-label">Metode Pembayaran</span>
                <div class="info-content text-right">
                    {{ $invoice->payment_method ?? 'Payment Gateway (Midtrans)' }}
                </div>
            </div>
            <div class="clear"></div>
        </div>
        
        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th width="60%">Deskripsi Layanan</th>
                    <th width="20%" class="text-right">Durasi</th>
                    <th width="20%" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="item-name">Langganan Paket {{ $invoice->subscription->plan->name }}</div>
                        <div class="item-desc">Akses fitur premium: 
                            @if(isset($invoice->subscription->plan->limits['max_restaurants']))
                                {{ $invoice->subscription->plan->limits['max_restaurants'] == -1 ? 'Unlimited' : $invoice->subscription->plan->limits['max_restaurants'] }} Cabang, 
                            @endif
                            Semua fitur yang termasuk dalam paket {{ $invoice->subscription->plan->name }}.
                        </div>
                    </td>
                    <td class="text-right">
                        {{ $invoice->subscription->plan->duration_days }} Hari
                    </td>
                    <td class="text-right">
                        Rp {{ number_format($invoice->amount, 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>
        
        <!-- Totals -->
        <div class="total-container">
            <div class="total-row">
                <span class="total-label">Subtotal</span>
                <span class="total-value">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</span>
            </div>
            <div class="total-row">
                <span class="total-label">Diskon / Promo</span>
                <span class="total-value">Rp 0</span>
            </div>
            <div class="total-row grand-total">
                <span class="total-label">Total Pembayaran</span>
                <span class="total-value">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</span>
            </div>
        </div>
        <div class="clear"></div>
        
        <!-- Footer -->
        <div class="footer">
            <span class="brand">{{ $settings->site_name ?? 'DINEFLO' }}</span>
            <p>Terima kasih telah mempercayakan operasional bisnis Anda kepada kami.</p>
            <p>Invoice ini dihasilkan secara sistem dan sah tanpa tanda tangan basah.</p>
            <p style="margin-top: 20px;">Support: {{ $settings->support_email }} | Website: {{ url('/') }}</p>
        </div>
    </div>
</body>
</html>
