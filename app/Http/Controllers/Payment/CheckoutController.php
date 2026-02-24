<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Cart\Cart;
use App\Models\Order\Order;
use Illuminate\Http\Request;
use App\Services\PaymentService;
use App\Models\Payment\Payment as ModelsPayment;
use App\Traits\CommonTrait;
use Illuminate\Support\Facades\Validator;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\PayPalHttpClient;

class CheckoutController extends Controller
{
    use CommonTrait;

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
                'total' => $cart->total_price,
                'shipping_method' => $cart->shipping_method,
                'shipping_price' => $cart->shipping_price,
                'shipping_address' => $cart->shipping_address,
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
            return $this->sendError(['error' => 'Payment failed: ' . $e->getMessage()], 500);
        }
    }

    public function CompleteStripePayment(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'payment_intent_id' => 'required|string'
        ]);

        if ($validated->fails()) {
            return $this->sendError(['error' => 'Validation failed', 'details' => $validated->errors()], 422);
        }


        // dd('order:', $order, 'paymentIntentId:', $paymentIntentId);
        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $order = Order::find($request->order_id);
            $paymentIntentId = $request->payment_intent_id;

            $intent = PaymentIntent::retrieve($paymentIntentId);

            // dd($intent);

            if ($intent->status === 'succeeded') {
                ModelsPayment::where('order_id', $order->id)->update([
                    'status' => 'success',
                    'transaction_id' => $intent->id,
                ]);

                return $this->sendResponse(null, 'Payment completed successfully.');
            } else {
                return $this->sendError('Payment not completed.', [], 400);
            }
        } catch (\Exception $e) {
            return $this->sendError(['error' => $e->getMessage()], 500);
        }
    }

    public function CompletePaypalPayment(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'paypal_order_id' => 'required|string'
        ]);

        if ($validated->fails()) {
            return $this->sendError(['error' => 'Validation failed', 'details' => $validated->errors()], 422);
        }

        try {
            $environment = new SandboxEnvironment(
                config('services.paypal.client_id'),
                config('services.paypal.client_secret')
            );

            $client = new PayPalHttpClient($environment);

            $captureRequest = new OrdersCaptureRequest($request->paypal_order_id);
            $captureRequest->prefer('return=representation');

            $response = $client->execute($captureRequest);

            if ($response->result->status === "COMPLETED") {

                ModelsPayment::where('order_id', $request->order_id)->update([
                    'status' => 'success'
                ]);

                Order::where('id', $request->order_id)->update([
                    'payment_status' => 'paid'
                ]);
                return $this->sendResponse(null, 'Payment completed successfully.');
            }
            return $this->sendError('Payment not completed.', [], 400);
        } catch (\Exception $e) {
            return $this->sendError(['error' => $e->getMessage()], 500);
        }
    }
}
