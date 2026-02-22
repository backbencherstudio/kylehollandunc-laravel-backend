<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'payment_method',
        'amount',
        'status',
        'transaction_id',
        'gateway'
    ];

    public function order()
    {
        return $this->belongsTo(\App\Models\Order\Order::class);
    }
}
