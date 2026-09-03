<?php

namespace App\Http\Controllers\SystemAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use App\Models\Role;

class RolePermissionController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();

        $defaultPermissions = [
            'user_management' => ['view_users', 'create_users', 'edit_users', 'delete_users'],
            'role_permissions' => ['view_roles', 'create_roles', 'edit_roles', 'assign_permissions'],
            'website_management' => ['view_website', 'edit_website'],
            'system_monitoring' => ['view_logs', 'view_metrics', 'view_realtime_traffic'],
            'database_management' => ['view_database', 'trigger_backup'],
            'system_security' => ['view_security_audit', 'manage_ip_blocklist'],
            'gateway_configuration' => ['view_gateways', 'edit_gateways', 'test_gateways'],
            'network_management' => ['view_network_settings', 'edit_network_settings'],
        ];

        // Ensure permissions exist in DB
        foreach ($defaultPermissions as $category => $perms) {
            foreach ($perms as $permName) {
                Permission::firstOrCreate(['name' => $permName]);
            }
        }

        $allPermissions = Permission::all();

        return view('system_admin.roles.index', compact('roles', 'defaultPermissions', 'allPermissions'));
    }

    public function storeRole(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::create(['name' => $validated['name'], 'tenant_id' => 0]);

        if (! empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return redirect()->route('admin.system.roles.index')
            ->with('success', "Role '{$role->name}' created with selected permissions.");
    }

    public function updateRolePermissions(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()->route('admin.system.roles.index')
            ->with('success', "Permissions updated for Role '{$role->name}'.");
    }
}
