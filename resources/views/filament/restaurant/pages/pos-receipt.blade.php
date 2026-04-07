<!DOCTYPE html>
<html>
<head>
    <title>Struk Pesanan #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 80mm; /* Standard Thermal Printer Width */
            margin: 0;
            padding: 5mm;
            font-size: 12px;
            line-height: 1.2;
        }
        .header {
            text-align: center;
            margin-bottom: 5mm;
        }
        .restaurant-name {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 2px;
        }
        .dashed-line {
            border-top: 1px dashed #000;
            margin: 3mm 0;
        }
        .item-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1mm;
        }
        .item-details {
            font-size: 10px;
            color: #555;
            margin-left: 3mm;
            margin-bottom: 2mm;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            margin-top: 1mm;
        }
        .footer {
            text-align: center;
            margin-top: 5mm;
            font-size: 10px;
        }
        @media print {
            body { width: 80mm; }
            @page { margin: 0; }
        }
    </style>
</head>
<body onload="window.print(); window.onafterprint = function(){ window.close(); };">
    <div class="header">
        <div class="restaurant-name">{{ $restaurant->name }}</div>
        <div>{{ $restaurant->address ?? '' }}</div>
        <div>Telp: {{ $restaurant->phone ?? '-' }}</div>
        <div class="dashed-line"></div>
        <div>No: {{ $order->order_number }}</div>
        <div>Tgl: {{ $order->created_at->format('d/m/Y H:i') }}</div>
        <div>Meja: {{ $order->table ? $order->table->name : 'Takeaway' }}</div>
        <div>Kasir: {{ auth()->user()->name }}</div>
    </div>

    <div class="dashed-line"></div>

    <div class="items">
        @foreach($order->items as $item)
            <div class="item-row">
                <span>{{ $item->quantity }}x {{ $item->menuItem->name }}</span>
                <span>{{ number_format($item->total_price, 0, ',', '.') }}</span>
            </div>
            @if($item->menuItemVariant || !empty(json_decode($item->addons)))
                <div class="item-details">
                    @if($item->menuItemVariant)
                        ({{ $item->menuItemVariant->name }})<br>
                    @endif
                    @php $addons = json_decode($item->addons, true); @endphp
                    @if(!empty($addons))
                        @foreach($addons as $addon)
                            + {{ $addon['name'] }}<br>
                        @endforeach
                    @endif
                </div>
            @endif
        @endforeach
    </div>

    <div class="dashed-line"></div>

    <div class="totals">
        <div class="item-row">
            <span>Subtotal:</span>
            <span>{{ number_format($order->total_amount / 1.1, 0, ',', '.') }}</span>
        </div>
        <div class="item-row">
            <span>Pajak (10%):</span>
            <span>{{ number_format($order->total_amount - ($order->total_amount / 1.1), 0, ',', '.') }}</span>
        </div>
        <div class="totals-row">
            <span>TOTAL:</span>
            <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="dashed-line"></div>
    
    <div class="footer">
        Terima Kasih Atas Kunjungan Anda<br>
        Powered by Dineflo
    </div>
</body>
</html>
