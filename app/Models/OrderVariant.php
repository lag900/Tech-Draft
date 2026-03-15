<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderVariant extends Model
{
    protected $fillable = [
        'order_id', 'order_color_id', 'fabric_id'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function color()
    {
        return $this->belongsTo(OrderColor::class, 'order_color_id');
    }

    public function fabric()
    {
        return $this->belongsTo(Fabric::class);
    }
}
