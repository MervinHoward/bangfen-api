<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Table extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'is_available'
    ];

    public function orders() {
        return $this->hasMany(Order::class, 'table_id');
    }
}
