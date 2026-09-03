<?php

namespace App\Http\Controllers\Subscriber\Hris;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Department;
use App\Models\EmployeeProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $tenant = auth()->user()->tenant;
        if (!$tenant) {
            return back()->with('error', 'No tenant found.');
        }

        $query = User::with('roles')->where('tenant_id', $tenant->id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();
        return view('subscriber.hris.users.index', compact('users'));
    }

    public function create()
    {
        $tenant = auth()->user()->tenant;
        if (!$tenant) {
            return back()->with('error', 'No tenant found.');
        }

        $roles = Role::forTenant($tenant->id)->orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        $employees = EmployeeProfile::with(['user', 'department', 'designation'])
            ->where('tenant_id', $tenant->id)
            ->orderBy('id', 'desc')
            ->get();

        return view('subscriber.hris.users.create', compact('roles', 'departments', 'employees'));
    }

    public function getEmployeeInfo(Request $request)
    {
        $employeeId = $request->get('employee_profile_id');
        if (!$employeeId) return response()->json(null);

        $tenant = auth()->user()->tenant;
        if (!$tenant) return response()->json(null);

        $emp = EmployeeProfile::with(['user', 'department', 'designation'])
            ->where('tenant_id', $tenant->id)
            ->where('id', $employeeId)
            ->first();

        if (!$emp) return response()->json(null);

        return response()->json([
            'id' => $emp->id,
            'employee_id' => $emp->employee_id,
            'name' => $emp->user?->name ?? '',
            'email' => $emp->user?->email ?? '',
            'phone' => $emp->phone_number,
            'department' => $emp->department?->name ?? 'N/A',
            'designation' => $emp->designation?->title ?? 'N/A',
            'gender' => $emp->gender,
            'status' => $emp->status,
            'has_user' => !is_null($emp->user_id),
        ]);
    }

    public function store(Request $request)
    {
        $tenant = auth()->user()->tenant;
        if (!$tenant) {
            return back()->with('error', 'No tenant found.');
        }

        $validated = $request->validate([
            'employee_profile_id' => 'required|exists:employee_profiles,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'nullable|string|exists:roles,name',
        ]);

        $employee = EmployeeProfile::where('tenant_id', $tenant->id)
            ->where('id', $validated['employee_profile_id'])
            ->whereNull('user_id')
            ->first();

        if (!$employee) {
            return back()->withErrors(['employee_profile_id' => 'This employee already has a user account or is not found.'])->withInput();
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'tenant_id' => $tenant->id,
        ]);

        $employee->update(['user_id' => $user->id]);

        if (!empty($validated['role'])) {
            $user->assignRole($validated['role']);
        }

        return redirect()->route('subscriber.hris.users.index')
            ->with('success', 'User created successfully for ' . $validated['name'] . '.');
    }

    public function edit(User $user)
    {
        $tenant = auth()->user()->tenant;
        if (!$tenant || $user->tenant_id !== $tenant->id) {
            return redirect()->route('subscriber.hris.users.index')
                ->with('error', 'User not found in your tenant.');
        }

        $roles = Role::forTenant($tenant->id)->orderBy('name')->get();
        $userRoles = $user->getRoleNames()->toArray();
        return view('subscriber.hris.users.edit', compact('user', 'roles', 'userRoles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$user->id}",
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'nullable|string|exists:roles,name',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        $user->syncRoles([]);
        if (!empty($validated['role'])) {
            $user->assignRole($validated['role']);
        }

        return redirect()->route('subscriber.hris.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $tenant = auth()->user()->tenant;
        if (!$tenant || $user->tenant_id !== $tenant->id) {
            return redirect()->back()->with('error', 'User not found in your tenant.');
        }

        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        EmployeeProfile::where('user_id', $user->id)->update(['user_id' => null]);

        $user->removeAllRoles();
        $user->delete();

        return redirect()->route('subscriber.hris.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
