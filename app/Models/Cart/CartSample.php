<?php

namespace App\Models\Cart;

use Illuminate\Database\Eloquent\Model;

class CartSample extends Model
{
    protected $fillable = [
        'cart_id',
        'organization',
        'test',
        'details'
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }
}
