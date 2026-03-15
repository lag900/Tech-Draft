<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     * Usage: ->middleware('permission:orders.view')
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Super admin bypasses all checks
        if ($user->role === 'superadmin') {
            return $next($request);
        }

        // Load permissions from role in DB (cached per request via role cache)
        $permissions = $this->getUserPermissions($user);

        if (!in_array($permission, $permissions)) {
            return response()->json([
                'message' => 'Forbidden. You do not have the required permission: ' . $permission,
                'required' => $permission,
            ], 403);
        }

        return $next($request);
    }

    protected function getUserPermissions($user): array
    {
        // Use cached permissions on user object if already loaded
        if (isset($user->_cachedPermissions)) {
            return $user->_cachedPermissions;
        }

        // Load from Role model
        $role = \App\Models\Role::where('slug', $user->role)->first();
        $permissions = $role ? ($role->permissions ?? []) : [];

        $user->_cachedPermissions = $permissions;
        return $permissions;
    }
}
