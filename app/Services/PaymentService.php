<?php

namespace App\Services;

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
            'method' => 'stripe',
            'transaction_id' => $intent->id,
            'amount' => $order->total,
            'status' => 'pending',
            'gateway' => 'stripe',
        ]); 

        return [
            'client_secret' => $intent->client_secret,
        ];
    }

    private function processPaypalPayment(Order $order)
    {
        $payment = ModelsPayment::create([
            'order_id' => $order->id,
            'method' => 'paypal',
            'amount' => $order->total,
            'status' => 'pending',
            'gateway' => 'paypal',
        ]);

        return [
            'payment_id' => $payment->id,
            'approval_url' => 'https://www.paypal.com/checkoutnow?token=' . $payment->id,
        ];
    }
}