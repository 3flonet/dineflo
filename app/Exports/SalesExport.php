<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    protected $restaurant;
    protected $start;
    protected $end;

    public function __construct($restaurant, $start, $end)
    {
        $this->restaurant = $restaurant;
        $this->start = $start;
        $this->end = $end;
    }

    public function query()
    {
        return Order::query()
            ->where('restaurant_id', $this->restaurant->id)
            ->whereDate('created_at', '>=', $this->start)
            ->whereDate('created_at', '<=', $this->end)
            ->with(['items.menuItem', 'table'])
            ->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'No. Order',
            'Tanggal',
            'Waktu',
            'Meja',
            'Customer',
            'Status',
            'Payment Method',
            'Payment Status',
            'Original Total',
            'Discount Total',
            'Total Refund',
            'Net Total (Inc. Refund)',
            'Items',
        ];
    }

    public function map($order): array
    {
        $items = $order->items->map(fn($item) => 
            ($item->menuItem->name ?? 'Deleted Item') . " (x{$item->quantity})"
        )->join(', ');

        $originalTotal = $order->items->sum(fn($i) => ($i->original_unit_price ?? $i->unit_price) * $i->quantity);
        $discountTotal = $originalTotal - $order->total_amount;
        $refunded = (float) ($order->refunded_amount ?? 0);

        return [
            $order->order_number,
            $order->created_at->format('Y-m-d'),
            $order->created_at->format('H:i:s'),
            $order->table->name ?? 'Takeaway/Delivery',
            $order->customer_name ?? 'Guest',
            ucfirst($order->status),
            ucfirst($order->payment_method ?? '-'),
            ucfirst($order->payment_status ?? '-'),
            $originalTotal,
            $discountTotal,
            $refunded,
            $order->total_amount - $refunded,
            $items,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
