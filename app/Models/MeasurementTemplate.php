<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeasurementTemplate extends Model
{
    protected $fillable = ['name', 'category_id', 'item_type_id', 'labels'];

    protected $casts = [
        'labels' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function itemType()
    {
        return $this->belongsTo(ItemType::class);
    }
}
