<?php

namespace App\Models\Order;

use App\Models\Report\Report;
use App\Models\User;
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function sample()
    {
        return $this->hasOne(OrderSample::class);
    }

    public function report()
    {
        return $this->hasOne(Report::class);
    }
}
