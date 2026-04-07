<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class SubscriptionInvoiceController extends Controller
{
    public function download(SubscriptionInvoice $invoice)
    {
        // Auth Check
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }

        // Owner Check: Ensure the user owns the subscription this invoice belongs to
        if ($invoice->subscription->user_id !== Auth::id()) {
            abort(403, 'You do not have access to this invoice.');
        }

        $invoice->load(['subscription.plan', 'subscription.user']);

        $pdf = Pdf::loadView('pdf.subscription-invoice', compact('invoice'));
        
        $filename = 'invoice-subscription-' . ($invoice->midtrans_id ?? $invoice->id) . '.pdf';
        
        return $pdf->download($filename);
    }
}
