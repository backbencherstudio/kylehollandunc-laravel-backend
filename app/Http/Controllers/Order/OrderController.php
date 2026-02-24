<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Models\Order\Order;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use CommonTrait;

    public function index()
    {
        try {
            $orders = Order::with('user', 'items')->latest()->get();
            return $this->sendResponse($orders, 'Orders retrieved successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve orders.', ['error' => $e->getMessage()]);
        }
    }

    public function orderStatusUpdate(Request $request, $id)
    {
        try {
            $request->validate([
                'order_status' => 'required|string|max:255'
            ]);

            $order = Order::findOrFail($id);
            $order->order_status = $request->order_status;
            $order->save();

            return $this->sendResponse($order, 'Order status updated successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Failed to update order status.', ['error' => $e->getMessage()]);
        }
    }
}
