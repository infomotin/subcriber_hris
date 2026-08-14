<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DeviceCommand;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $users = User::with('tenant')
            ->orderBy('id', 'desc')
            ->paginate($request->per_page ?? 15);

        $users->getCollection()->transform(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'tenant_id' => $user->tenant_id,
                'tenant_name' => $user->tenant?->name,
                'roles' => $user->roles->pluck('name'),
                'created_at' => $user->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json($users);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'tenant_id' => 'required|exists:tenants,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'tenant_id' => $validated['tenant_id'],
        ]);

        if ($request->roles) {
            $user->syncRoles($request->roles);
        }

        return response()->json(['message' => 'User created', 'user' => $user], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:8',
            'tenant_id' => 'sometimes|exists:tenants,id',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        return response()->json(['message' => 'User updated', 'user' => $user]);
    }

    public function destroy($id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'User deleted']);
    }

    public function pushToDevice(Request $request, $id, $deviceId): JsonResponse
    {
        $user = User::findOrFail($id);
        $device = Device::findOrFail($deviceId);

        DeviceCommand::create([
            'tenant_id' => $device->tenant_id,
            'device_id' => $device->id,
            'command' => 'push_user',
            'type' => 'user',
            'status' => 'pending',
            'response' => json_encode([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]),
        ]);

        return response()->json([
            'message' => 'User data queued for push to device',
            'user_id' => (int) $id,
            'device_id' => (int) $deviceId,
        ]);
    }
}
