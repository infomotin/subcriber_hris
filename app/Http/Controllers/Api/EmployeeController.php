<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmployeeProfile;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;
        $query = EmployeeProfile::with(['user', 'department', 'designation', 'shift'])
            ->where('tenant_id', $tenant->id);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('employee_id', 'like', "%{$request->search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$request->search}%"));
            });
        }
        if ($request->department_id) {
            $query->where('department_id', $request->department_id);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $employees = $query->orderBy('id', 'desc')->paginate($request->per_page ?? 15);

        $employees->getCollection()->transform(function ($emp) {
            return [
                'id' => $emp->id,
                'employee_id' => $emp->employee_id,
                'name' => $emp->user?->name ?? 'N/A',
                'email' => $emp->user?->email ?? 'N/A',
                'department' => $emp->department?->name,
                'designation' => $emp->designation?->name,
                'shift' => $emp->shift?->name,
                'joining_date' => $emp->joining_date?->format('Y-m-d'),
                'phone' => $emp->phone_number,
                'status' => $emp->status,
            ];
        });

        return response()->json($employees);
    }

    public function store(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;

        if (!$tenant->canAddEmployee()) {
            return response()->json([
                'message' => "Employee limit reached ({$tenant->employees()->count()}/{$tenant->max_employees})",
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'employee_id' => 'required|string|unique:employee_profiles,employee_id',
            'department_id' => 'nullable|exists:departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            'shift_id' => 'nullable|exists:work_shifts,id',
            'joining_date' => 'required|date',
            'gender' => 'required|string',
            'dob' => 'required|date',
            'phone_number' => 'required|string|max:20',
            'status' => 'required|string',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'tenant_id' => $tenant->id,
        ]);
        $user->assignRole('Employee');

        $profile = EmployeeProfile::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'employee_id' => $validated['employee_id'],
            'department_id' => $validated['department_id'],
            'designation_id' => $validated['designation_id'],
            'shift_id' => $validated['shift_id'],
            'joining_date' => $validated['joining_date'],
            'gender' => $validated['gender'],
            'dob' => $validated['dob'],
            'phone_number' => $validated['phone_number'],
            'status' => $validated['status'],
        ]);

        return response()->json(['message' => 'Employee created', 'employee' => $profile], 201);
    }

    public function show($id): JsonResponse
    {
        $employee = EmployeeProfile::with(['user', 'department', 'designation', 'shift',
            'bankInfo', 'addresses', 'dependents', 'nominees', 'education', 'experiences',
            'salaryStructure', 'documents'])->findOrFail($id);

        return response()->json(['employee' => $employee]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $employee = EmployeeProfile::findOrFail($id);
        $employee->update($request->all());
        return response()->json(['message' => 'Employee updated', 'employee' => $employee]);
    }

    public function destroy($id): JsonResponse
    {
        EmployeeProfile::findOrFail($id)->delete();
        return response()->json(['message' => 'Employee deleted']);
    }
}
