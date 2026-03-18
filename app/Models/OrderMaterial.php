<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderMaterial extends Model
{
    protected $fillable = [
        'order_id',
        'item_name',
        'description',
        'color',
        'unit',
        'quantity'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
