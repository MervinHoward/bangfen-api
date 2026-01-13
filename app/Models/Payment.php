<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'payment_method',
        'amount_bill',
        'amount_paid',
        'change_amount'
    ];

    public function order() {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
