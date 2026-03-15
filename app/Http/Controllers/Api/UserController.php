<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', '!=', 'client');

        if ($request->role) {
            $query->where('role', $request->role);
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Ensure the currently logged in user is included in the results (even if they don't match the exact filters)
        $users = collect([$request->user()])->merge($query->with('client')->latest()->get())->unique('id')->values();
        
        return $users;
    }

    public function countsByRole()
    {
        return User::groupBy('role')
            ->selectRaw('role, count(*) as count')
            ->pluck('count', 'role');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string',
            'department' => 'nullable|string',
            'status' => 'required|string|in:active,disabled',
        ]);

        return User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'department' => $request->department,
            'status' => $request->status,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            // These might be optional if updating from profile
            'role' => 'sometimes|required|string',
            'status' => 'sometimes|required|string|in:active,disabled',
        ]);

        $data = $request->only(['name', 'email', 'role', 'department', 'status']);
        
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('profile-images', 'public');
            $data['avatar'] = $path;
        }

        $user->update($data);

        // Update Client info if applicable
        if (($request->company_name || $request->phone) && $user->role === 'client') {
            $user->client()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'company_name' => $request->company_name,
                    'phone' => $request->phone,
                    'brand_name' => $request->brand_name ?? $request->company_name ?? 'My Brand'
                ]
            );
        }

        return $user->load('client');
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        return $this->update($request, $user);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'The provided password does not match your current password.'], 422);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json(['message' => 'Password changed successfully']);
    }

    public function destroy(User $user)
    {
        if ($user->role === 'superadmin') {
            return response()->json(['message' => 'Cannot delete superadmin'], 403);
        }
        $user->delete();
        return response()->json(['message' => 'User deleted']);
    }

    public function toggleStatus(User $user)
    {
        if ($user->role === 'superadmin') {
             return response()->json(['message' => 'Cannot toggle superadmin status'], 403);
        }
        $user->status = $user->status === 'active' ? 'disabled' : 'active';
        $user->save();
        return $user;
    }
}
