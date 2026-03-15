<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Design extends Model
{
    use Auditable;

    protected $fillable = [
        'name', 
        'product_code',
        'season',
        'fabric_type',
        'category_id', 
        'image_path', 
        'description', 
        'user_id',
        'client_id',
        'version'
    ];

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
        return $this->belongsTo(User::class, 'user_id');
    }

    public function logs()
    {
        return $this->morphMany(ActivityLog::class, 'subject');
    }
    public function scopeForUser($query, $user)
    {
        if (!$user || !isset($user->role)) return $query;
        
        if ($user->role === 'client') {
            $clientId = \Illuminate\Support\Facades\DB::table('clients')->where('user_id', $user->id)->value('id');
            return $query->where('client_id', $clientId);
        }
        
        return $query;
    }
}
