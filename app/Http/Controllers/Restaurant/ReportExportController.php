<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Filament\Facades\Filament;

class ReportExportController extends Controller
{
    public function export(Request $request, \App\Models\Restaurant $restaurant)
    {
        // $tenant is now $restaurant via Route Model Binding
        $tenant = $restaurant;

        if (!$tenant) {
            abort(404, 'Restaurant not found context.');
        }
        
        // Validasi akses
        if (!auth()->user()->hasFeature('Sales Reports')) {
            abort(403, 'Anda memerlukan paket Pro untuk mengakses fitur ini.');
        }

        $start = $request->get('date_start', now()->startOfMonth()->format('Y-m-d'));
        $end = $request->get('date_end', now()->endOfMonth()->format('Y-m-d'));

        // Query orders
        $orders = Order::where('restaurant_id', $tenant->id)
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $end)
            ->with(['table', 'items.menuItem'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Generate CSV
        $filename = "laporan-penjualan-{$tenant->slug}-{$start}-to-{$end}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            
            // BOM untuk Excel UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header CSV
            fputcsv($file, [
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
                'Refunded Amount',
                'Net Total',
                'Items',
            ]);

            // Data rows
            foreach ($orders as $order) {
                $items = $order->items->map(fn($item) => $item->menuItem->name ?? 'N/A')->join(', ');
                
                $originalTotal = $order->items->sum(fn($i) => ($i->original_unit_price ?? $i->unit_price) * $i->quantity);
                $discountTotal = $originalTotal - $order->total_amount;
                $refunded = (float) $order->refunded_amount;

                fputcsv($file, [
                    $order->order_number,
                    $order->created_at->format('Y-m-d'),
                    $order->created_at->format('H:i:s'),
                    $order->table->name ?? '-',
                    $order->customer_name ?? '-',
                    ucfirst($order->status),
                    ucfirst($order->payment_method ?? '-'),
                    ucfirst($order->payment_status ?? '-'),
                    number_format($originalTotal, 2, '.', ''),
                    number_format($discountTotal, 2, '.', ''),
                    number_format($refunded, 2, '.', ''),
                    number_format($order->total_amount - $refunded, 2, '.', ''),
                    $items,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    public function exportPdf(Request $request, \App\Models\Restaurant $restaurant)
    {
        // $tenant is now $restaurant via Route Model Binding
        $tenant = $restaurant;

        if (!$tenant) {
            abort(404, 'Restaurant context not found.');
        }
        
        // Validasi akses
        if (!auth()->user()->hasFeature('Sales Reports')) {
            abort(403, 'Anda memerlukan paket Pro untuk mengakses fitur ini.');
        }

        $start = $request->get('date_start', now()->startOfMonth()->format('Y-m-d'));
        $end = $request->get('date_end', now()->endOfMonth()->format('Y-m-d'));

        // Query Stats
        $ordersQuery = Order::where('restaurant_id', $tenant->id)
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $end);
            
        $totalRevenue = (clone $ordersQuery)->whereIn('payment_status', ['paid', 'partial'])
            ->selectRaw('SUM(total_amount - refunded_amount) as net_rev')
            ->value('net_rev') ?? 0;
            
        $totalRefunded = (clone $ordersQuery)->sum('refunded_amount');
        
        $totalDiscount = \App\Models\OrderItem::whereHas('order', function($q) use ($tenant, $start, $end) {
            $q->where('restaurant_id', $tenant->id)
              ->whereIn('payment_status', ['paid', 'partial'])
              ->whereDate('created_at', '>=', $start)
              ->whereDate('created_at', '<=', $end);
        })->where('is_refunded', false)
          ->selectRaw('SUM((COALESCE(original_unit_price, unit_price) - unit_price) * quantity) as total')
          ->value('total') ?? 0;

        $grossRevenue = $totalRevenue + $totalDiscount + $totalRefunded;

        $totalOrders = (clone $ordersQuery)->where('status', '!=', 'cancelled')->count();
        $paidCount = (clone $ordersQuery)->whereIn('payment_status', ['paid', 'partial'])->count();
        $avgOrderValue = $paidCount > 0 ? $totalRevenue / $paidCount : 0;

        // Top Selling Items (excluding refunded items)
        $topItems = \App\Models\MenuItem::query()
            ->select('menu_items.*')
            ->join('order_items', 'menu_items.id', '=', 'order_items.menu_item_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.restaurant_id', $tenant->id)
            ->whereIn('orders.payment_status', ['paid', 'partial'])
            ->where('order_items.is_refunded', false)
            ->whereBetween('orders.created_at', [$start . ' 00:00:00', $end . ' 23:59:59'])
            ->selectRaw('SUM(order_items.quantity) as total_sold')
            ->selectRaw('SUM(order_items.total_price) as total_revenue')
            ->with(['category'])
            ->groupBy('menu_items.id')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // Recent Orders
        $orders = (clone $ordersQuery)
            ->with(['table'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.sales-report', [
            'restaurant' => $tenant,
            'start' => $start,
            'end' => $end,
            'totalRevenue' => $totalRevenue,
            'totalRefunded' => $totalRefunded,
            'totalDiscount' => $totalDiscount,
            'grossRevenue' => $grossRevenue,
            'totalOrders' => $totalOrders,
            'avgOrderValue' => $avgOrderValue,
            'topItems' => $topItems,
            'orders' => $orders,
        ]);
        
        $filename = "laporan-pdf-{$tenant->slug}-{$start}-to-{$end}.pdf";

        return $pdf->download($filename);
    }
    public function exportExcel(Request $request, \App\Models\Restaurant $restaurant)
    {
        $tenant = $restaurant;

        if (!$tenant) {
            abort(404, 'Restaurant context not found.');
        }

        // Validasi akses
        if (!auth()->user()->hasFeature('Sales Reports')) {
            abort(403, 'Anda memerlukan paket Pro untuk mengakses fitur ini.');
        }

        $start = $request->get('date_start', now()->startOfMonth()->format('Y-m-d'));
        $end = $request->get('date_end', now()->endOfMonth()->format('Y-m-d'));

        $filename = "laporan-penjualan-{$tenant->slug}-{$start}-to-{$end}.xlsx";

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\SalesExport($tenant, $start, $end), 
            $filename
        );
    }
}
