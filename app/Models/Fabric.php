<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fabric extends Model
{
    protected $fillable = [
        'name', 'type', 'status', 
        'fabric_code', 'fabric_structure', 'fabric_composition', 
        'yarn_type', 'fabric_finish', 'dye_type'
    ];
}
