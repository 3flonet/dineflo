<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }
        .receipt {
            width: 58mm; /* Standard thermal width */
            margin: 0 auto;
            padding: 5px;
            background-color: #fff;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .logo {
            max-width: 60px;
            margin-bottom: 5px;
        }
        .restaurant-name {
            font-weight: bold;
            font-size: 14px;
            margin: 0;
        }
        .restaurant-info {
            font-size: 10px;
            margin-bottom: 5px;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
        .order-info {
            margin-bottom: 10px;
        }
        .item-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        .item-name {
            flex: 1;
            font-weight: bold;
        }
        .item-qty {
            width: 30px;
            text-align: center;
        }
        .item-price {
            width: 60px;
            text-align: right;
        }
        .item-details {
            font-size: 10px;
            margin-left: 10px;
            color: #555;
            margin-bottom: 2px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            margin-top: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 10px;
        }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    @php $payment = $payment ?? null; @endphp
    <div class="receipt">
        <!-- Header -->
        <div class="header">
            @if($order->restaurant->logo)
                <img src="{{ asset('storage/' . $order->restaurant->logo) }}" alt="Logo" class="logo">
            @endif
            <h1 class="restaurant-name">{{ $order->restaurant->name }}</h1>
            <p class="restaurant-info">
                {{ $order->restaurant->address ?? 'Alamat Belum Diisi' }}<br>
                Tel: {{ $order->restaurant->phone ?? '-' }}
            </p>
        </div>

        <div class="divider"></div>

        <!-- Order Info -->
        <div class="order-info">
            <div>Order #: {{ $order->order_number }}</div>
            @if($payment)
            <div style="color: #ef4444; font-weight: bold;">PARTIAL RECEIPT (Split Item)</div>
            <div>Payment ID: #{{ $payment->id }}</div>
            @endif
            <div>Date: {{ $order->created_at->format('d/m/Y H:i') }}</div>
            <div>Table: {{ $order->table->name ?? 'Takeaway' }}</div>
            <div>Cust: {{ $order->customer_name }}</div>
            @if($payment)
            <div>Pay Method: {{ strtoupper($payment->payment_method) }}</div>
            @elseif($order->payment_method)
            <div>Pay: {{ ucfirst($order->payment_method) }} ({{ ucfirst($order->payment_status) }})</div>
            @endif
        </div>

        <div class="divider"></div>

        <!-- Items -->
        @php
            $itemsToPrint = $payment ? $order->items->where('order_payment_id', $payment->id) : $order->items;
        @endphp

        @foreach($itemsToPrint as $item)
            <div class="item-row">
                <span class="item-qty">{{ $item->quantity }}x</span>
                <span class="item-name">
                    {{ $item->menuItem->name }}
                    @if($item->original_unit_price && $item->original_unit_price > $item->unit_price)
                        <br>
                        <span style="text-decoration: line-through; font-size: 9px; font-weight: normal;">
                            Rp{{ number_format($item->original_unit_price, 0, ',', '.') }}
                        </span>
                        <span style="font-size: 9px; color: #000; font-weight: bold;">
                            ({{ $item->discount_name ?? 'Disc.' }})
                        </span>
                    @endif
                </span>
                <span class="item-price">{{ number_format($item->total_price, 0, ',', '.') }}</span>
            </div>
            @if($item->variant || (is_array($item->addons) && count($item->addons) > 0) || $item->note)
                <div class="item-details" style="margin-top: -2px;">
                    @if($item->variant)
                        + {{ $item->variant->name }} <br>
                    @endif
                    @if(is_array($item->addons))
                        @foreach($item->addons as $addon)
                            + {{ $addon['name'] }} <br>
                        @endforeach
                    @endif
                    @if($item->note)
                        <span style="font-weight: bold; color: #000; font-style: italic;">* Note: {{ $item->note }}</span> <br>
                    @endif
                </div>
            @endif
        @endforeach

        <div class="divider"></div>

        <!-- Totals -->
        @if($payment)
             <div class="total-row" style="font-weight: normal; margin-top: 5px;">
                <span>Subtotal (Paid Items)</span>
                <span>{{ number_format($payment->amount, 0, ',', '.') }}</span>
            </div>
        @elseif($order->tax_amount > 0 || $order->additional_fees_amount > 0)
            <div class="total-row" style="font-weight: normal; margin-top: 5px;">
                <span>Subtotal</span>
                <span>{{ number_format($order->subtotal, 0, ',', '.') }}</span>
            </div>
            
            @if($order->additional_fees_details && is_array($order->additional_fees_details))
                @foreach($order->additional_fees_details as $fee)
                    @php
                        $feeAmount = ($fee['type'] ?? '') === 'fixed' ? $fee['value'] : ($order->subtotal * ($fee['value'] / 100));
                    @endphp
                    @if($feeAmount > 0)
                        <div class="total-row" style="font-weight: normal;">
                            <span>{{ $fee['name'] }}</span>
                            <span>{{ number_format($feeAmount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                @endforeach
            @endif

            @if($order->tax_amount > 0)
                <div class="total-row" style="font-weight: normal;">
                    <span>Pajak ({{ $order->restaurant->tax_percentage ?? 10 }}%)</span>
                    <span>{{ number_format($order->tax_amount, 0, ',', '.') }}</span>
                </div>
            @endif
        @else
            <div class="total-row" style="font-weight: normal; margin-top: 5px;">
                <span>Subtotal</span>
                <span>{{ number_format($order->subtotal ?: $order->total_amount, 0, ',', '.') }}</span>
            </div>
        @endif

        @if(!$payment)
            @if($order->voucher_discount_amount > 0)
                <div class="total-row" style="font-weight: normal;">
                    <span>Discount ({{ $order->voucher_code ?: 'Voucher' }})</span>
                    <span>- {{ number_format($order->voucher_discount_amount, 0, ',', '.') }}</span>
                </div>
            @endif

            @if($order->points_discount_amount > 0)
                <div class="total-row" style="font-weight: normal;">
                    <span>Poin ({{ number_format($order->points_used) }} Poin)</span>
                    <span>- {{ number_format($order->points_discount_amount, 0, ',', '.') }}</span>
                </div>
            @endif

            @if($order->gift_card_discount_amount > 0)
                <div class="total-row" style="font-weight: normal;">
                    <span>Gift Card</span>
                    <span>- {{ number_format($order->gift_card_discount_amount, 0, ',', '.') }}</span>
                </div>
            @endif
        @endif
        
        <div class="total-row" style="font-size: 14px; margin-top: 5px; border-top: 1px dashed #000; padding-top: 5px;">
            <span>{{ $payment ? 'PAID AMOUNT' : 'TOTAL PEMBAYARAN' }}</span>
            <span>Rp {{ number_format($payment ? $payment->amount : $order->total_amount, 0, ',', '.') }}</span>
        </div>

        <div class="divider"></div>

        <!-- Footer -->
        <div class="footer">
            <p>Terima Kasih atas Kunjungan Anda!</p>
            <p>Powered by {{ config('app.name', 'Dineflo') }}</p>
            <p style="font-size: 8px; margin-top: 5px;">{{ $order->id }}</p>
        </div>
    </div>
</body>
</html>
