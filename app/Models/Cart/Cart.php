<?php

namespace App\Models\Cart;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $casts = [
        'meta' => 'array',
    ];

    public function sample()
    {
        return $this->hasOne(CartSample::class);
    }
}
