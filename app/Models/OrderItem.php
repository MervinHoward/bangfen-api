<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'menu_id',
        'price',
        'quantity',
        'subtotal'
    ];

    public function order() {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function menu() {
        return $this->belongsTo(Menu::class, 'menu_id');
    }
}
