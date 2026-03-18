<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'fabric_code',
        'fabric_supplier',
        'fabric_width',
        'dye_method',
        'fabric_finish',
        'zipper_type',
        'button_type',
        'thread_type',
        'stitch_type',
        'seam_type',
        'label_type', 'packaging_type', 'carton_quantity', 'factory_notes', 'sewing_sequence',
        'cord_type', 'rib_type', 'top_stitch', 'reinforcement', 'label_position',
        'size_label_type', 'care_label_type', 'folding_method', 'barcode_required',
    ];

    protected $casts = [
        'fabric_width' => 'decimal:2',
        'carton_quantity' => 'integer',
        'sewing_sequence' => 'array'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
