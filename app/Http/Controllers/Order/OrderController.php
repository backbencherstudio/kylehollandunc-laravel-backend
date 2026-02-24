<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Models\Order\Order;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

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

    public function show($id)
    {
        try {
            $order = Order::with('user', 'items', 'sample')->findOrFail($id);
            return $this->sendResponse($order, 'Order retrieved successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve order.', ['error' => $e->getMessage()]);
        }
    }

    public function orderStatusUpdate(Request $request, $id)
    {
        try {
            $request->validate([
                'order_status' => 'required|string|max:255'
            ]);

            $order = Order::findOrFail($id)->with('user')->first();
            // dd($order->user);
            $order->order_status = $request->order_status;
            $order->save();

            if ($request->order_status === 'started') {
                $report = $order->report()->create([
                    'name' => $order->user->name,
                    'order_id' => $order->id,
                    'test_date' => Date::now()->toDateString(),
                    'progress_status' => 'pending'
                ]);
                return $this->sendResponse($report, 'Order status updated and report created successfully.');
            } else {
                return $this->sendResponse($order, 'Order status updated successfully.');
            }

        } catch (\Exception $e) {
            return $this->sendError('Failed to update order status.', ['error' => $e->getMessage()]);
        }
    }

    public function orderSample(Request $request)
    {
        // dd($request->all());
        try {
            $request->validate([
                'order_id' => 'required|exists:orders,id',
                'organization' => 'string|max:255',
                'test' => 'string|max:255',
                'details' => 'string'
            ]);

            $order = Order::findOrFail($request->order_id);
            // dd($order);
            $order->sample()->create([
                'order_id' => $request->order_id,
                'organization' => $request->organization,
                'test' => $request->test,
                'details' => $request->details
            ]);

            return $this->sendResponse($order->sample, 'Order sample created successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Failed to create order sample.', ['error' => $e->getMessage()]);
        }
    }
}
