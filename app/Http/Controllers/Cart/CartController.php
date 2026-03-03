<?php

namespace App\Http\Controllers\Cart;

use App\Http\Controllers\Controller;
use App\Models\Cart\Cart;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CartController extends Controller
{
    use CommonTrait;

    public function index()
    {
        try {
            $user = Auth::user();
            $carts = Cart::where('user_id', $user->id)->with('sample')->latest()->get();
            return $this->sendResponse($carts, 'Carts retrieved successfully.');
        } catch (\Exception $e) {
            return $this->sendError('Failed to retrieve carts.', ['error' => $e->getMessage()]);
        }
    }



    public function cartByUser(Request $request)
    {
        try {
            $user = Auth::guard('sanctum')->user();
            // dd($user);
            $guestToken = $request->header('X-Guest-Token');
            if ($user) {
                // dd($user);
                $carts = Cart::where('user_id', $user->id)->with('sample')->latest()->get();
            } elseif ($guestToken) {
                // dd($guestToken);
                $carts = Cart::where('guest_token', $guestToken)->with('sample')->latest()->get();
            }
            // dd($carts);
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

            $user = Auth::guard('sanctum')->user();
            $guestToken = $request->header('X-Guest-Token');

            if (!$user && !$guestToken) {
                $guestToken = (string) Str::uuid();
            }

            if ($user) {
                Cart::where('user_id', $user->id)->delete();
            } elseif ($guestToken) {
                Cart::where('guest_token', $guestToken)->delete();
            }

            $cart = new Cart();
            if ($user) {
                $cart->user_id = $user->id;
            } else {
                $cart->guest_token = $guestToken;
            }

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
                // 'shipping_address' => 'string',
            ]);

            $cart = Cart::find($id);

            if (!$cart) {
                return $this->sendError('Cart not found.');
            }

            // keep previous shipping price to adjust total
            $oldShippingPrice = $cart->shipping_price ?? 0;

            $cart->shipping_method = $request->shipping_method;
            // if no price provided assume zero (e.g. own_courier)
            $cart->shipping_price = $request->shipping_price ?? 0;
            $cart->shipping_address = $request->shipping_address;

            // subtract old price then add new price
            $cart->total_price = $cart->total_price - $oldShippingPrice + $cart->shipping_price;
            if ($cart->total_price < 0) {
                $cart->total_price = 0;
            }

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
