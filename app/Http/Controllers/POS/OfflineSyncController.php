<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Actions\Pos\SaveOrderAction;
use App\Actions\Pos\ProcessCheckoutAction;
use App\Models\Restaurant;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OfflineSyncController extends Controller
{
    public function sync(Request $request, SaveOrderAction $saveOrderAction, ProcessCheckoutAction $checkoutAction)
    {
        $orders = $request->input('orders', []);
        $results = [];

        foreach ($orders as $offlineOrder) {
            try {
                DB::transaction(function () use ($offlineOrder, $saveOrderAction, $checkoutAction, &$results) {
                    $tenant = Restaurant::find($offlineOrder['restaurant_id']);
                    if (!$tenant) throw new \Exception('Restaurant not found');

                    // Security Check: Verify user can access this tenant
                    if (!auth()->user()->hasRole('super_admin') && !auth()->user()->getAccessibleRestaurantIds()->contains($tenant->id)) {
                        throw new \Exception('Unauthorized access to this restaurant');
                    }

                    // Idempotency: Avoid double sync
                    $existingOrder = \App\Models\Order::where('offline_id', $offlineOrder['offline_id'])->first();
                    if ($existingOrder) {
                        $results[] = [
                            'offline_id' => $offlineOrder['offline_id'],
                            'status' => 'success',
                            'order_id' => $existingOrder->id,
                            'order_number' => $existingOrder->order_number,
                            'message' => 'Already synced'
                        ];
                        return;
                    }

                    $member = null;
                    if (!empty($offlineOrder['customer_phone'])) {
                        $member = Member::where('restaurant_id', $tenant->id)
                            ->where('whatsapp', $offlineOrder['customer_phone'])
                            ->first();
                    }

                    // Map offline_id into data array for SaveOrderAction
                    $orderData = $offlineOrder['data'];
                    $orderData['offline_id'] = $offlineOrder['offline_id'];

                    // 1. Save Order
                    $order = $saveOrderAction->execute(
                        $tenant,
                        $offlineOrder['cart'],
                        $orderData,
                        null, // Always new order for offline sync
                        $member,
                        $offlineOrder['wants_to_register'] ?? false
                    );

                    // 2. Process Checkout (Assuming it's a paid transaction like Cash)
                    if ($offlineOrder['payment_method'] === 'cash') {
                        $checkoutAction->execute(
                            $order,
                            $order->total_amount,
                            'cash'
                        );
                    }

                    $results[] = [
                        'offline_id' => $offlineOrder['offline_id'],
                        'status' => 'success',
                        'order_id' => $order->id,
                        'order_number' => $order->order_number
                    ];
                });
            } catch (\Exception $e) {
                $results[] = [
                    'offline_id' => $offlineOrder['offline_id'],
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'success' => true,
            'results' => $results
        ]);
    }
}
