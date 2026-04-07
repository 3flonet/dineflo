<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderPrintController extends Controller
{
    public function print(Order $order)
    {
        // Auth Check
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }

        // Tenant Access Check
        // Ensure the user has access to the restaurant of this order
        // This relies on Filament Shield / Policy logic usually, but here we can check direct relation
        $user = Auth::user();
        if (!$user->canAccessTenant($order->restaurant)) {
             abort(403, 'You do not have access to this restaurant order.');
        }

        // Load necessary relationships for the receipt
        $order->load(['items.menuItem', 'items.variant', 'restaurant', 'table']);

        // Handle Split by Item Receipt
        $paymentId = request('payment_id');
        $payment = null;
        if ($paymentId) {
            $payment = $order->orderPayments()->find($paymentId);
            // If it's a split by item, we might want to filter items in the view or here
        }

        return view('print.receipt', compact('order', 'payment'));
    }

    public function downloadPdf(Order $order)
    {
        // Auth Check
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }

        // Tenant Access Check
        $user = Auth::user();
        if (!$user->canAccessTenant($order->restaurant)) {
             abort(403, 'You do not have access to this restaurant order.');
        }

        // Load necessary relationships for the invoice
        $order->load(['items.menuItem', 'restaurant', 'table', 'items.variant']);

        $paymentId = request('payment_id');
        $payment = null;
        if ($paymentId) {
            $payment = $order->orderPayments()->find($paymentId);
        }

        $pdf = Pdf::loadView('pdf.invoice', compact('order', 'payment'));
        return $pdf->download('invoice-' . $order->order_number . '.pdf');
    }
}
