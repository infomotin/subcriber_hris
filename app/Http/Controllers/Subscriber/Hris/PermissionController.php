<?php

namespace App\Http\Controllers\Subscriber\Hris;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::orderBy('group_name')->orderBy('name')->get()->groupBy('group_name');
        $tenant = auth()->user()->tenant;

        // Only tenant-owned roles (not system roles)
        $query = Role::query();
        if (Role::hasTenantColumn()) {
            $query->where('tenant_id', $tenant->id);
        }
        $roles = $query->orderBy('name')->get();

        return view('subscriber.hris.permissions.index', compact('permissions', 'roles'));
    }
}
