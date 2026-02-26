<?php

namespace App\Http\Controllers\Cart;

use App\Http\Controllers\Controller;
use App\Models\Cart\Cart;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    use CommonTrait;

    public function index()
    {
        try {
            $carts = Cart::with('sample')->latest()->get();
            return $this->sendResponse($carts, 'Carts retrieved successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve carts.', ['error' => $e->getMessage()]);
        }
    }

    

    public function cartByUser()
    {
        try {
            $user = Auth::user();
            $carts = Cart::where('user_id', $user->id)->with('sample')->latest()->get();
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
                'meta' => 'array',
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
            $cart->shipping_address = $request->shipping_address;
            $cart->meta = $request->meta;
            $cart->save();

            return $this->sendResponse($cart, 'Cart created successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Failed to create cart.', ['error' => $e->getMessage()]);
        }
    }

    public function updateShipping(Request $request, $id)
    {
        try {
            $request->validate([
                'shipping_method' => 'string|max:255',
                'shipping_price' => 'numeric',
                'shipping_address' => 'string',
            ]);

            $cart = Cart::find($id);

            if (!$cart) {
                return $this->sendError('Cart not found.');
            }

            $cart->shipping_method = $request->shipping_method;
            $cart->shipping_price = $request->shipping_price;
            $cart->shipping_address = $request->shipping_address;
            $cart->total_price = $cart->total_price + $cart->shipping_price;
            $cart->save();

            return $this->sendResponse($cart, 'Cart shipping updated successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Failed to update cart shipping.', ['error' => $e->getMessage()]);
        }
    }

    public function cartSample(Request $request)
    {
        // dd($request->all());
        try {
            $request->validate([
                'cart_id' => 'required|exists:carts,id',
                'organization' => 'string|max:255',
                'test' => 'string|max:255',
                'details' => 'string'
            ]);
            $cart = Cart::findOrFail($request->cart_id);
            $cart->sample()->create([
                'cart_id' => $request->cart_id,
                'organization' => $request->organization,
                'test' => $request->test,
                'details' => $request->details
            ]);
            return $this->sendResponse($cart->sample, 'Cart sample created successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Failed to create cart sample.', ['error' => $e->getMessage()]);
        }
    }
}
