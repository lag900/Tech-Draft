<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPageSetting extends Model
{
    protected $fillable = [
        'hero_title_en', 'hero_title_ar',
        'hero_subtitle_en', 'hero_subtitle_ar',
        'features', 'steps',
        'cta_title_en', 'cta_title_ar',
    ];

    protected $casts = [
        'features' => 'array',
        'steps' => 'array',
    ];
}
