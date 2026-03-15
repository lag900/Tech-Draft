<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    public static function bootAuditable()
    {
        static::updated(function ($model) {
            $changes = [];
            foreach ($model->getDirty() as $key => $value) {
                if ($key === 'updated_at') continue;
                $changes[] = [
                    'field' => $key,
                    'old' => $model->getOriginal($key),
                    'new' => $value,
                ];
            }

            if (!empty($changes)) {
                ActivityLog::create([
                    'description' => "Updated " . class_basename($model),
                    'subject_id' => $model->id,
                    'subject_type' => get_class($model),
                    'causer_id' => Auth::id(),
                    'causer_type' => Auth::check() ? get_class(Auth::user()) : null,
                    'properties' => $changes,
                ]);
            }
        });

        static::created(function ($model) {
            ActivityLog::create([
                'description' => "Created " . class_basename($model),
                'subject_id' => $model->id,
                'subject_type' => get_class($model),
                'causer_id' => Auth::id(),
                'causer_type' => Auth::check() ? get_class(Auth::user()) : null,
                'properties' => $model->getAttributes(),
            ]);
        });
    }
}
