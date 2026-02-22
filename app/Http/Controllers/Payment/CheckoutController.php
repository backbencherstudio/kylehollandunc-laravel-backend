<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Cart\Cart;
use App\Models\Order\Order;
use Illuminate\Http\Request;
use App\Services\PaymentService;
use App\Models\Payment\Payment as ModelsPayment;
use Illuminate\Support\Facades\Validator;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class CheckoutController extends Controller
{
    public function makePayment(Request $request, PaymentService $paymentService)
    {
        try {
            $validated = Validator::make($request->all(), [
                'cart_id' => 'required|exists:carts,id',
                'payment_method' => 'required|string|max:255'
            ]);

            if ($validated->fails()) {
                return response()->json(['error' => 'Validation failed', 'details' => $validated->errors()], 422);
            }

            $cart = Cart::find($request->cart_id);
            // dd($request->all());

            $order = Order::create([
                'order_number' => strtoupper(uniqid()),
                'user_id' => $request->user()->id,
                'subtotal' => $cart->price,
                'shipping_price' => $cart->shipping_price,
                'total' => $cart->total_price,
                'shipping_method' => $cart->shipping_method,
                'payment_method' => $request->payment_method,
                'order_status' => 'pending'
            ]);

            // foreach ($cart->meta as $item) {
            //     dd($item);
            //     $order->items()->create([
            //         'order_id' => $order->id,
            //         'type' => $item['type'],
            //         'name' => $item['name'],
            //         'quantity' => 1,
            //         'price' => $item['price'],
            //         'total_price' => $item['total_price']
            //     ]);
            // }

            $meta = $cart->meta;

            // dd($meta);
            // Insert main test
            if (!empty($meta['test'])) {
                $order->items()->create([
                    'type' => 'test',
                    'name' => $meta['test']['title'], // ← FIXED
                    'quantity' => 1,
                    'price' => $meta['test']['base_price'],
                    'total_price' => $meta['test']['base_price'],
                ]);
            }

            // Insert addons
            if (!empty($meta['addons'])) {
                foreach ($meta['addons'] as $addon) {
                    $order->items()->create([
                        'type' => 'addon',
                        'name' => $addon['name'], // addon already has name
                        'quantity' => 1,
                        'price' => $addon['price'],
                        'total_price' => $addon['price'],
                    ]);
                }
            }

            return $paymentService->pay($order, $request->payment_method);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Payment failed: ' . $e->getMessage()], 500);
        }
    }

    public function CompleteStripePayment(Order $order, $paymentIntentId)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $intent = PaymentIntent::retrieve($paymentIntentId);

            if ($intent->status === 'succeeded') {
                ModelsPayment::where('order_id', $order->id)->update([
                    'status' => 'completed',
                    'transaction_id' => $intent->id,
                ]);

                return response()->json(['message' => 'Payment completed successfully.']);
            } else {
                return response()->json(['error' => 'Payment not completed.'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to complete payment: ' . $e->getMessage()], 500);
        }
    }
}
