<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name', 'sku', 'description', 'price', 
        'category_id', 'design_id', 'client_id', 'status', 'image_path'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function design()
    {
        return $this->belongsTo(Design::class);
    }

    public function scopeForUser($query, $user)
    {
        if (!$user || !isset($user->role)) return $query;
        
        if ($user->role === 'client') {
            return $query->whereHas('client', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }
        
        return $query;
    }
}
