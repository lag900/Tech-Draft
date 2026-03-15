<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['parent_id', 'name', 'type', 'description', 'status'];

    public function itemTypes()
    {
        return $this->hasMany(ItemType::class);
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function measurementTemplate()
    {
        return $this->hasOne(MeasurementTemplate::class);
    }
}
