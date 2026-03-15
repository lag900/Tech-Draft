<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PermissionMatrixController extends Controller
{
    public function index()
    {
        $roles = \App\Models\Role::orderBy('id')->get(['id', 'name', 'slug', 'permissions', 'color']);
        return response()->json($roles);
    }

    public function update(Request $request)
    {
        $matrix = $request->json()->all();
        
        foreach ($matrix as $slug => $permissions) {
            \App\Models\Role::where('slug', $slug)->update(['permissions' => $permissions]);
        }

        return response()->json(['message' => 'Permissions updated successfully.']);
    }
}
