<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'subtotal',
        'shipping_price',
        'total',
        'shipping_method',
        'payment_method',
        'order_status'
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
