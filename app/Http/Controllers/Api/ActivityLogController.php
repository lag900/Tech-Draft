<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('causer');

        if ($type = $request->input('subject_type')) {
            $query->where('subject_type', $type);
        }

        if ($id = $request->input('subject_id')) {
            $query->where('subject_id', $id);
        }

        return $query->latest()->get();
    }
}
