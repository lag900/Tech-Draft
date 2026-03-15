<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderSize extends Model
{
    protected $fillable = [
        'order_id', 'size_name', 'quantity'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
