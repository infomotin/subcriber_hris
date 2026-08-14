<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SaasUserController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::whereNull('tenant_id')->with('roles')->orderBy('name')->get()->map(fn($u) => [
            'id' => $u->id,
            'name' => $u->name,
            'email' => $u->email,
            'roles' => $u->getRoleNames(),
            'created_at' => $u->created_at->format('Y-m-d'),
        ]);
        return response()->json($users);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'nullable|string|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        if (!empty($validated['role'])) {
            $user->assignRole($validated['role']);
        }

        return response()->json(['message' => 'System user created', 'user' => $user], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $user = User::whereNull('tenant_id')->findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'role' => 'nullable|string|exists:roles,name',
        ]);

        $data = ['name' => $validated['name'], 'email' => $validated['email']];
        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }
        $user->update($data);

        if (isset($validated['role'])) {
            $user->syncRoles($validated['role']);
        }

        return response()->json(['message' => 'System user updated', 'user' => $user]);
    }

    public function delete($id): JsonResponse
    {
        User::whereNull('tenant_id')->findOrFail($id)->delete();
        return response()->json(['message' => 'System user deleted']);
    }
}
