<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderPublicReceiptController extends Controller
{
    /**
     * View public receipt/nota for a customer without login.
     */
    public function show(Order $order)
    {
        // Add a simple security hash/token in the future if needed, 
        // for now we trust the ID/UUID from the URL.
        
        // Load relationships
        $order->load(['items.menuItem', 'items.variant', 'restaurant', 'table']);

        return view('print.receipt', compact('order'));
    }
}
