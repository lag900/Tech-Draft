<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'user_id', 
        'brand_name', 
        'company_name', 
        'phone', 
        'country', 
        'city', 
        'business_type', 
        'website', 
        'instagram', 
        'expected_quantity', 
        'preferred_category',
        'contact_info', 
        'manufacturing_notes'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
