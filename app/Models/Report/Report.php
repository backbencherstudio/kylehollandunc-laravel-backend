<?php

namespace App\Models\Report;

use App\Models\Order\Order;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'name',
        'order_id',
        'test_date',
        'progress_status',
        'result_status',
        'report_url'
    ];

    public function order()
    {
        $orders = $this->belongsTo(Order::class);
        $orders->with('user', 'items', 'sample');
        return $orders;
    }
}
