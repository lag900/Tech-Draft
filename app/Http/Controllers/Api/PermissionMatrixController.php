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
        
        $levels = [
            'view' => 10,
            'create' => 50,
            'edit' => 50,
            'export' => 60,
            'import' => 80,
            'approve' => 80,
            'delete' => 90,
        ];

        foreach ($matrix as $slug => $permissions) {
            $role = \App\Models\Role::where('slug', $slug)->first();
            if ($role) {
                $roleLevel = $role->level ?? 0;
                $filteredPermissions = array_filter($permissions, function ($p) use ($roleLevel, $levels) {
                    $parts = explode('.', $p);
                    $actionLevel = isset($parts[1]) ? ($levels[$parts[1]] ?? 10) : 10;
                    return $roleLevel >= $actionLevel;
                });
                
                // Super Admin bypasses level check
                if ($slug === 'superadmin') {
                    $role->update(['permissions' => $permissions]);
                } else {
                    $role->update(['permissions' => array_values($filteredPermissions)]);
                }
            }
        }

        return response()->json(['message' => 'Permissions updated successfully.']);
    }
}
