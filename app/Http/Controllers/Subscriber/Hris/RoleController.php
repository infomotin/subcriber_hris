<?php

namespace App\Http\Controllers\Subscriber\Hris;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    private array $systemRoles = ['admin', 'hr-manager', 'employee'];

    public function index()
    {
        $tenant = auth()->user()->tenant;

        $query = Role::with('permissions');
        if (Role::hasTenantColumn()) {
            $query->forTenant($tenant->id);
        }

        $roles = $query->orderBy('name')->paginate(15);
        return view('subscriber.hris.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::orderBy('group_name')->orderBy('name')->get()->groupBy('group_name');
        return view('subscriber.hris.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $tenant = auth()->user()->tenant;

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'permissions' => 'nullable|array',
        ]);

        $name = $validated['name'];

        if (in_array(strtolower($name), $this->systemRoles)) {
            return back()->withErrors(['name' => 'Cannot create a role with a system-reserved name.'])->withInput();
        }

        $roleData = ['name' => $name, 'guard_name' => 'web'];
        if (Role::hasTenantColumn()) {
            $roleData['tenant_id'] = $tenant->id;

            $exists = Role::where('tenant_id', $tenant->id)
                ->where('name', $name)
                ->exists();

            if ($exists) {
                return back()->withErrors(['name' => 'A role with this name already exists in your tenant.'])->withInput();
            }
        } else {
            $exists = Role::where('name', $name)->exists();
            if ($exists) {
                return back()->withErrors(['name' => 'A role with this name already exists.'])->withInput();
            }
        }

        $role = Role::create($roleData);

        if (!empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return redirect()->route('subscriber.hris.roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        $tenant = auth()->user()->tenant;

        if ($role->isSystemRole()) {
            return redirect()->route('subscriber.hris.roles.index')
                ->with('error', 'System roles cannot be edited.');
        }

        if (Role::hasTenantColumn() && $role->tenant_id !== $tenant->id) {
            return redirect()->route('subscriber.hris.roles.index')
                ->with('error', 'You do not have access to this role.');
        }

        $permissions = Permission::orderBy('group_name')->orderBy('name')->get()->groupBy('group_name');
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('subscriber.hris.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $tenant = auth()->user()->tenant;

        if ($role->isSystemRole()) {
            return redirect()->route('subscriber.hris.roles.index')
                ->with('error', 'System roles cannot be modified.');
        }

        if (Role::hasTenantColumn() && $role->tenant_id !== $tenant->id) {
            return redirect()->route('subscriber.hris.roles.index')
                ->with('error', 'You do not have access to this role.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'permissions' => 'nullable|array',
        ]);

        $name = $validated['name'];

        if (in_array(strtolower($name), $this->systemRoles)) {
            return back()->withErrors(['name' => 'Cannot use a system-reserved name.'])->withInput();
        }

        if (Role::hasTenantColumn()) {
            $exists = Role::where('tenant_id', $tenant->id)
                ->where('name', $name)
                ->where('id', '!=', $role->id)
                ->exists();
        } else {
            $exists = Role::where('name', $name)
                ->where('id', '!=', $role->id)
                ->exists();
        }

        if ($exists) {
            return back()->withErrors(['name' => 'A role with this name already exists.'])->withInput();
        }

        $role->update(['name' => $name]);
        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()->route('subscriber.hris.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        $tenant = auth()->user()->tenant;

        if ($role->isSystemRole()) {
            return redirect()->route('subscriber.hris.roles.index')
                ->with('error', 'System roles cannot be deleted.');
        }

        if (Role::hasTenantColumn() && $role->tenant_id !== $tenant->id) {
            return redirect()->route('subscriber.hris.roles.index')
                ->with('error', 'You do not have access to this role.');
        }

        $role->delete();

        return redirect()->route('subscriber.hris.roles.index')
            ->with('success', 'Role deleted.');
    }
}
