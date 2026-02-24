<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Model;

class OrderSample extends Model
{
    protected $fillable = [
        'order_id',
        'organization',
        'test',
        'details'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
