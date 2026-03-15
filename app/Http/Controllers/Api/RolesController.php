<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class RolesController extends Controller
{
    public function index()
    {
        if (Role::count() === 0) {
            $defaultRoles = [
                ['name' => 'Super Admin', 'slug' => 'superadmin', 'description' => 'Full system access, manage roles and configuration.', 'color' => '#ef4444', 'permissions' => []],
                ['name' => 'Administrator', 'slug' => 'admin', 'description' => 'Manage users, orders, and system core modules.', 'color' => '#3b82f6', 'permissions' => []],
                ['name' => 'Manager', 'slug' => 'manager', 'description' => 'Quality control, approval workflow and team management.', 'color' => '#10b981', 'permissions' => []],
                ['name' => 'Designer', 'slug' => 'designer', 'description' => 'Sketching, pattern review and design management.', 'color' => '#ec4899', 'permissions' => []],
                ['name' => 'Data Entry', 'slug' => 'dataentry', 'description' => 'Creating orders, fabrics, and updating records.', 'color' => '#8b5cf6', 'permissions' => []],
                ['name' => 'Client', 'slug' => 'client', 'description' => 'External brand access for creating and tracking orders.', 'color' => '#f59e0b', 'permissions' => []],
            ];
            foreach ($defaultRoles as $role) {
                Role::create($role);
            }
        }

        $roles = Role::all();
        $userCounts = User::select('role', DB::raw('count(*) as count'))
            ->groupBy('role')
            ->pluck('count', 'role');

        return response()->json([
            'roles' => $roles,
            'userCounts' => $userCounts
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'color' => 'nullable|string'
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        
        // Ensure slug is unique
        $originalSlug = $validated['slug'];
        $count = 1;
        while (Role::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $count++;
        }

        $role = Role::create($validated);

        return response()->json($role, 201);
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'color' => 'nullable|string',
            'status' => 'nullable|string|in:active,disabled'
        ]);

        if (isset($validated['name']) && $validated['name'] !== $role->name) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $role->update($validated);

        return response()->json($role);
    }

    public function destroy(Role $role)
    {
        // Don't delete system roles if we had them here, but let's assume we can delete any created ones
        $role->delete();
        return response()->json(null, 204);
    }

    public function toggleStatus(Role $role)
    {
        $role->status = $role->status === 'active' ? 'disabled' : 'active';
        $role->save();
        return response()->json($role);
    }
}
