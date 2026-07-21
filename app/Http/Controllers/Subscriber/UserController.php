<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\ZktecoUser;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $tenant = auth()->user()?->tenant ?? Tenant::first();
        if ($tenant) {
            app()->instance('current_tenant_id', $tenant->id);
            session(['tenant_id' => $tenant->id]);
        }

        $users = ZktecoUser::with('device')->orderBy('id', 'desc')->paginate(15);
        return view('subscriber.users.index', compact('users'));
    }
}
