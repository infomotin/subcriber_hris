<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::with('tenant')->where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = Str::random(60);
        $user->forceFill(['api_token' => $token])->save();

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'tenant_id' => $user->tenant_id,
                'roles' => $user->getRoleNames(),
            ],
            'tenant' => $user->tenant ? [
                'id' => $user->tenant->id,
                'name' => $user->tenant->name,
                'slug' => $user->tenant->slug,
                'max_devices' => $user->tenant->max_devices,
                'max_employees' => $user->tenant->max_employees,
                'status' => $user->tenant->status,
                'expires_at' => $user->tenant->expires_at,
            ] : null,
        ]);
    }

    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
        ]);

        $plan = SubscriptionPlan::findOrFail($validated['subscription_plan_id']);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
        $user->assignRole('Subscriber');

        $tenant = Tenant::create([
            'name' => $validated['name'],
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'max_devices' => $plan->max_devices,
            'max_employees' => $plan->max_employees,
            'status' => 'active',
            'expires_at' => now()->addMonth(),
        ]);

        $user->update(['tenant_id' => $tenant->id]);

        $token = Str::random(60);
        $user->forceFill(['api_token' => $token])->save();

        return response()->json([
            'message' => 'Registration successful',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'tenant_id' => $user->tenant_id,
                'roles' => $user->getRoleNames(),
            ],
            'tenant' => [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'slug' => $tenant->slug,
                'max_devices' => $tenant->max_devices,
                'max_employees' => $tenant->max_employees,
                'status' => $tenant->status,
            ],
        ], 201);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->forceFill(['api_token' => null])->save();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('tenant');

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'tenant_id' => $user->tenant_id,
                'roles' => $user->getRoleNames(),
            ],
            'tenant' => $user->tenant ? [
                'id' => $user->tenant->id,
                'name' => $user->tenant->name,
                'slug' => $user->tenant->slug,
                'max_devices' => $user->tenant->max_devices,
                'max_employees' => $user->tenant->max_employees,
                'status' => $user->tenant->status,
                'expires_at' => $user->tenant->expires_at,
                'devices_count' => $user->tenant->devices()->count(),
                'employees_count' => $user->tenant->employees()->count(),
            ] : null,
        ]);
    }
}
