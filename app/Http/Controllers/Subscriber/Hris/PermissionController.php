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
        $roles = Role::forTenant($tenant->id)->orderBy('name')->get();

        return view('subscriber.hris.permissions.index', compact('permissions', 'roles'));
    }
}
