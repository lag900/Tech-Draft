<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:client,enduser',
            'brand_name' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'business_type' => 'nullable|string|max:100',
            'website' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'expected_quantity' => 'nullable|string|max:255',
            'preferred_category' => 'nullable|string|max:255',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        if ($request->role === 'client') {
            Client::create([
                'user_id' => $user->id,
                'brand_name' => $request->brand_name ?: $request->company_name,
                'company_name' => $request->company_name,
                'phone' => $request->phone,
                'country' => $request->country,
                'city' => $request->city,
                'business_type' => $request->business_type,
                'website' => $request->website,
                'instagram' => $request->instagram,
                'expected_quantity' => $request->expected_quantity,
                'preferred_category' => $request->preferred_category,
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user->load('client'),
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user->load('client'),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }

    public function me(Request $request)
    {
        $user = $request->user()->load('client');
        
        // Attach permissions from role
        $role = \App\Models\Role::where('slug', $user->role)->first();
        $userData = $user->toArray();
        $userData['permissions'] = $role ? ($role->permissions ?? []) : [];
        
        return response()->json($userData);
    }
}
