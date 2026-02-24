<?php

namespace App\Services;

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use App\Models\Order\Order;
use App\Models\Payment\Payment as ModelsPayment;
use Stripe\PaymentIntent;
use Stripe\Stripe;


class PaymentService
{
    public function pay(Order $order, $paymentMethod)
    {
        try {
            if ($paymentMethod === 'card') {
                return $this->processStripePayment($order);
            }

            if ($paymentMethod === 'paypal') {
                return $this->processPaypalPayment($order);
            }

            throw new \Exception('Invalid payment method: ' . $paymentMethod);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function processStripePayment(Order $order)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        // Implement Stripe payment processing logic here
        $intent = \Stripe\PaymentIntent::create([
            'amount' => $order->total * 100, // Convert to cents
            'currency' => 'usd',
            'metadata' => [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
            ],
        ]);

        ModelsPayment::updateOrCreate([
            'order_id' => $order->id,
        ], [
            // 'method' => 'stripe',
            'gateway' => 'stripe',
            'transaction_id' => $intent->id,
            'amount' => $order->total,
            'status' => 'pending',
        ]);

        return [
            'client_secret' => $intent->client_secret,
            'payment_intent_id' => $intent->id,
            'order_id' => $order->id,
        ];
    }

    private function processPaypalPayment(Order $order)
    {
        try {

            $environment = new SandboxEnvironment(
                config('services.paypal.client_id'),
                config('services.paypal.client_secret')
            );

            $client = new PayPalHttpClient($environment);

            $request = new OrdersCreateRequest();
            $request->prefer('return=representation');

            $request->body = [
                "intent" => "CAPTURE",
                "purchase_units" => [[
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => number_format($order->total, 2, '.', '')
                    ]
                ]]
            ];

            $response = $client->execute($request);

            $paypalOrderId = $response->result->id;

            $payment = ModelsPayment::create([
                'order_id' => $order->id,
                // 'method' => 'paypal',
                'gateway' => 'paypal',
                'transaction_id' => $paypalOrderId,
                'amount' => $order->total,
                'status' => 'pending',
            ]);

            return [
                'order_id' => $order->id,
                'paypal_order_id' => $paypalOrderId,
            ];
        } catch (\Exception $e) {
            throw new \Exception('PayPal payment processing failed: ' . $e->getMessage());
        }
    }
}
