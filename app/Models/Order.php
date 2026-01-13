<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'date',
        'order_type',
        'total_price',
        'status'
    ];

    public function items() {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function payment() {
        return $this->hasOne(Payment::class, 'order_id');
    }
}
