<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice #{{ $order->order_number }}</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; color: #333; }
        .container { width: 100%; margin: 0 auto; }
        .header { margin-bottom: 30px; border-bottom: 2px solid #eee; padding-bottom: 20px; }
        .logo { max-height: 60px; float: left; }
        .invoice-details { float: right; text-align: right; }
        .clear { clear: both; }
        
        .info-section { margin-bottom: 30px; }
        .company-info { float: left; width: 45%; }
        .customer-info { float: right; width: 45%; text-align: right; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #f5f5f5; border-bottom: 1px solid #ddd; padding: 10px; text-align: left; font-weight: bold; }
        td { padding: 10px; border-bottom: 1px solid #eee; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .total-section { float: right; width: 40%; }
        .total-row { border-top: 2px solid #333; font-weight: bold; font-size: 16px; }
        
        .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #777; border-top: 1px solid #eee; padding-top: 20px; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; text-transform: uppercase; color: white; }
        .badge-pending { background-color: #f59e0b; }
        .badge-paid { background-color: #10b981; }
        .badge-cancelled { background-color: #ef4444; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            @if($order->restaurant->logo && file_exists(public_path('storage/' . $order->restaurant->logo)))
                <img src="{{ public_path('storage/' . $order->restaurant->logo) }}" class="logo">
            @else
                <h1 style="float: left; margin: 0;">{{ $order->restaurant->name }}</h1>
            @endif
            
            <div class="invoice-details">
                <h2 style="margin: 0; color: #333;">INVOICE</h2>
                <p style="margin: 5px 0;">#{{ $order->order_number }}</p>
                <p style="margin: 0; font-size: 12px; color: #777;">Date: {{ $order->created_at->format('d M Y, H:i') }}</p>
                <p style="margin-top: 5px;">
                    Status: 
                    <span class="badge badge-{{ $order->payment_status === 'paid' ? 'paid' : ($order->status === 'cancelled' ? 'cancelled' : 'pending') }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </p>
            </div>
            <div class="clear"></div>
        </div>
        
        <!-- Info -->
        <div class="info-section">
            <div class="company-info">
                <strong>From:</strong><br>
                {{ $order->restaurant->name }}<br>
                {{ $order->restaurant->address ?? 'No Address Provided' }}<br>
                Phone: {{ $order->restaurant->phone ?? '-' }}
            </div>
            <div class="customer-info">
                <strong>To:</strong><br>
                {{ $order->customer_name }}<br>
                {{ $order->customer_phone ?? '' }}<br>
                {{ $order->customer_email ?? '' }}<br>
                @if($order->table)
                    Table: {{ $order->table->name }} ({{ $order->table->area }})
                @endif
            </div>
            <div class="clear"></div>
        </div>
        
        <!-- Items -->
        <table>
            <thead>
                <tr>
                    <th width="50%">Item</th>
                    <th width="15%" class="text-right">Price</th>
                    <th width="15%" class="text-center">Qty</th>
                    <th width="20%" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>
                        {{ $item->menuItem->name ?? 'Unknown Item' }}
                        @if($item->menu_item_variant_id && $item->variant)
                             <br><small class="text-gray-500">Var: {{ $item->variant->name }}</small>
                        @endif
                        @if(!empty($item->addons))
                            <br><small class="text-gray-500">
                            @foreach($item->addons as $addon)
                                + {{ $addon['name'] }} @if(!$loop->last), @endif
                            @endforeach
                            </small>
                        @endif
                        @if(!empty($item->note))
                            <br><small style="color: #b45309; font-weight: bold;">Note: {{ $item->note }}</small>
                        @endif
                    </td>
                    <td class="text-right">{{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->total_price, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Totals -->
        <div class="total-section">
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="border: none; padding: 5px;" class="text-right">Subtotal:</td>
                    <td style="border: none; padding: 5px;" class="text-right">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
                </tr>

                @if($order->additional_fees_details && is_array($order->additional_fees_details))
                    @foreach($order->additional_fees_details as $fee)
                        @php
                            $feeAmount = ($fee['type'] ?? '') === 'fixed' ? $fee['value'] : ($order->subtotal * ($fee['value'] / 100));
                        @endphp
                        @if($feeAmount > 0)
                            <tr>
                                <td style="border: none; padding: 5px;" class="text-right">{{ $fee['name'] }}:</td>
                                <td style="border: none; padding: 5px;" class="text-right">Rp {{ number_format($feeAmount, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                    @endforeach
                @endif

                @if($order->tax_amount > 0)
                    <tr>
                        <td style="border: none; padding: 5px;" class="text-right">Pajak:</td>
                        <td style="border: none; padding: 5px;" class="text-right">Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</td>
                    </tr>
                @endif

                @if($order->voucher_discount_amount > 0)
                    <tr>
                        <td style="border: none; padding: 5px; color: #059669;" class="text-right">Voucher:</td>
                        <td style="border: none; padding: 5px; color: #059669;" class="text-right">- Rp {{ number_format($order->voucher_discount_amount, 0, ',', '.') }}</td>
                    </tr>
                @endif

                @if($order->points_discount_amount > 0)
                    <tr>
                        <td style="border: none; padding: 5px; color: #059669;" class="text-right">Poin Loyalitas:</td>
                        <td style="border: none; padding: 5px; color: #059669;" class="text-right">- Rp {{ number_format($order->points_discount_amount, 0, ',', '.') }}</td>
                    </tr>
                @endif

                @if($order->gift_card_discount_amount > 0)
                    <tr>
                        <td style="border: none; padding: 5px; color: #059669;" class="text-right">Gift Card:</td>
                        <td style="border: none; padding: 5px; color: #059669;" class="text-right">- Rp {{ number_format($order->gift_card_discount_amount, 0, ',', '.') }}</td>
                    </tr>
                @endif

                <tr class="total-row">
                    <td style="border-top: 2px solid #333; padding: 10px 5px;" class="text-right">TOTAL:</td>
                    <td style="border-top: 2px solid #333; padding: 10px 5px;" class="text-right">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
        <div class="clear"></div>
        
        <!-- Footer -->
        <div class="footer">
            <p>Thank you for your business!</p>
            <p>Payment Method: {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
        </div>
    </div>
</body>
</html>
