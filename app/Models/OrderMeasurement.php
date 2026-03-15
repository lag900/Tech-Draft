<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderMeasurement extends Model
{
    protected $fillable = [
        'order_id', 'point_of_measure', 'dimension_value', 'grading', 'tolerance', 'unit'
    ];

    protected $casts = [
        'grading' => 'array'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
