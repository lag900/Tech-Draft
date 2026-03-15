<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModelAnatomy extends Model
{
    protected $fillable = [
        'order_id',
        'piece_name',
        'part_name',
        'count',
        'image_path',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
