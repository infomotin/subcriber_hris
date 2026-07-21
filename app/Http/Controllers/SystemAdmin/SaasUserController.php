<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SaasUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['roles', 'tenant'])->orderBy('id', 'desc');

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(15);
        $roles = Role::all();
        $tenants = Tenant::all();

        return view('system_admin.users.index', compact('users', 'roles', 'tenants'));
    }

    public function create()
    {
        $roles = Role::all();
        $tenants = Tenant::all();
        return view('system_admin.users.create', compact('roles', 'tenants'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:8',
            'role' => 'required|string|exists:roles,name',
            'tenant_id' => 'nullable|exists:tenants,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'tenant_id' => $validated['tenant_id'] ?? null,
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('admin.system.users.index')
            ->with('success', "SaaS User '{$user->name}' created successfully with role '{$validated['role']}'.");
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $tenants = Tenant::all();
        return view('system_admin.users.edit', compact('user', 'roles', 'tenants'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|string|exists:roles,name',
            'tenant_id' => 'nullable|exists:tenants,id',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->tenant_id = $validated['tenant_id'] ?? null;

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();
        $user->syncRoles([$validated['role']]);

        return redirect()->route('admin.system.users.index')
            ->with('success', "SaaS User '{$user->name}' updated successfully.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own logged-in System Admin account.');
        }

        $user->delete();

        return redirect()->route('admin.system.users.index')
            ->with('success', 'SaaS User removed successfully.');
    }
}
