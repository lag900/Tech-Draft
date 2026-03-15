<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarkerPlan extends Model
{
    protected $fillable = [
        'order_id',
        'marker_length',
        'marker_width',
        'fabric_width',
        'efficiency',
        'part_count',
        'dxf_file_path',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
