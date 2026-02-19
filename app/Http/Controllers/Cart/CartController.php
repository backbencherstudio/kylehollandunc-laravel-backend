<?php

namespace App\Http\Controllers\Cart;

use App\Http\Controllers\Controller;
use App\Models\Cart\Cart;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;

class CartController extends Controller
{
    use CommonTrait;

    public function index()
    {
        try {
            $carts = Cart::latest()->get();
            return $this->sendResponse($carts, 'Carts retrieved successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve carts.', ['error' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try {
            // dd($request->all());
            $request->validate([
                'type' => 'string|max:255',
                'name' => 'string|max:255',
                'price' => 'numeric',
                'total_price' => 'numeric',
                'shipping_method' => 'string|max:255',
                'shipping_price' => 'numeric',
                'quantity' => 'integer',
            ]);

            $user = $request->user();

            $cart = new Cart();
            $cart->user_id = $user->id;
            $cart->type = $request->type;
            $cart->name = $request->name;
            $cart->quantity = $request->quantity;
            $cart->price = $request->price;
            $cart->total_price = $request->total_price;
            $cart->shipping_method = $request->shipping_method;
            $cart->shipping_price = $request->shipping_price;
            $cart->save();

            return $this->sendResponse($cart, 'Cart created successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Failed to create cart.', ['error' => $e->getMessage()]);
        }
    }
}
