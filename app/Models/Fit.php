<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fit extends Model
{
    protected $fillable = ['name', 'item_type_id', 'status'];

    public function itemType(): BelongsTo
    {
        return $this->belongsTo(ItemType::class);
    }
}
