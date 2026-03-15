<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderColor extends Model
{
    protected $fillable = [
        'order_id', 'color_name', 'color_hex', 'color_code'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
