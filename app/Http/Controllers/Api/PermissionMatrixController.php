<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PermissionMatrixController extends Controller
{
    public function index()
    {
        $roles = \App\Models\Role::orderBy('id')->get(['id', 'name', 'slug', 'permissions', 'color', 'level']);
        return response()->json($roles);
    }

    public function update(Request $request)
    {
        $matrix = $request->json()->all();
        foreach ($matrix as $slug => $permissions) {
            $role = \App\Models\Role::where('slug', $slug)->first();
            if ($role) {
                // Remove level-based filtering completely to allow admin full control
                $role->update(['permissions' => array_values($permissions)]);
            }
        }

        return response()->json(['message' => 'Permissions updated successfully.']);
    }
}
