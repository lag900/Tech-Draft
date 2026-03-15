<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use Illuminate\Support\Str;

class Order extends Model
{
    use Auditable;

    protected $fillable = [
        'order_code', 'title', 'client_id', 'category_id', 'product_id', 'season', 'year',
        'fabric_details', 'colors', 'measurements', 'production_details', 'notes', 'status', 'created_by',
        
        // Tech Pack Identification
        'product_code', 'pattern_code', 'order_number', 'version_number', 'last_update_date', 'brand_name',
        
        // Design Assets
        'design_front_image', 'design_back_image', 'technical_sketch', 'reference_images',
        
        // Trim & Label Details
        'zipper_type', 'button_type', 'cord_type', 'drawcord_type', 'rib_type', 'stitching_type',
        'label_details', 'main_label_type', 'care_label_type', 'size_label_type', 'hangtag_type',
        'packaging_notes', 'packaging_type', 'folding_method', 'barcode_required',
        'revision_number'
    ];

    protected $casts = [
        'fabric_details' => 'array',
        'colors' => 'array',
        'measurements' => 'array',
        'production_details' => 'array',
        'last_update_date' => 'datetime',
        'reference_images' => 'array',
        'barcode_required' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($order) {
            if (!$order->order_number) {
                $order->order_number = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            }
            if (!$order->product_code && $order->title) {
                $order->product_code = strtoupper(substr($order->title, 0, 3)) . '-' . rand(1000, 9999);
            }
            if (!$order->version_number) {
                $order->version_number = 1;
            }
            if (!$order->revision_number) {
                $order->revision_number = 0;
            }
        });

        static::updating(function ($order) {
            $order->last_update_date = now();
        });
    }

    public function orderColors()
    {
        return $this->hasMany(OrderColor::class);
    }

    public function orderSizes()
    {
        return $this->hasMany(OrderSize::class);
    }

    public function orderMeasurements()
    {
        return $this->hasMany(OrderMeasurement::class);
    }

    public function orderVariants()
    {
        return $this->hasMany(OrderVariant::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function images()
    {
        return $this->hasMany(OrderImage::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function anatomies()
    {
        return $this->hasMany(ModelAnatomy::class);
    }

    public function markerPlans()
    {
        return $this->hasMany(MarkerPlan::class);
    }

    public function logs()
    {
        return $this->morphMany(ActivityLog::class, 'subject');
    }

    public function statusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class)->with('changer')->latest();
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
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
    public function production()
    {
        return $this->hasOne(OrderProduction::class);
    }
}
