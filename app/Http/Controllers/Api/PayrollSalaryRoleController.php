<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SalaryRoleAssignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PayrollSalaryRoleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $roles = SalaryRoleAssignment::with(['salaryRole', 'department'])
            ->where('tenant_id', $request->user()->tenant_id)
            ->orderBy('id', 'desc')
            ->get()
            ->map(fn($r) => [
                'id' => $r->id,
                'role' => $r->salaryRole?->name,
                'department' => $r->department?->name ?? 'All',
                'month' => $r->applicable_month,
                'created_at' => $r->created_at->format('Y-m-d'),
            ]);

        return response()->json($roles);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'salary_role_id' => 'required|exists:salary_roles,id',
            'department_id' => 'nullable|exists:departments,id',
            'applicable_month' => 'required|date_format:Y-m',
        ]);

        $assignment = SalaryRoleAssignment::create(array_merge($validated, [
            'tenant_id' => $request->user()->tenant_id,
        ]));

        return response()->json(['message' => 'Salary role assigned', 'assignment' => $assignment], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $assignment = SalaryRoleAssignment::where('tenant_id', $request->user()->tenant_id)->findOrFail($id);
        $assignment->update($request->validate([
            'salary_role_id' => 'required|exists:salary_roles,id',
            'department_id' => 'nullable|exists:departments,id',
            'applicable_month' => 'required|date_format:Y-m',
        ]));
        return response()->json(['message' => 'Updated', 'assignment' => $assignment]);
    }

    public function delete(Request $request, $id): JsonResponse
    {
        SalaryRoleAssignment::where('tenant_id', $request->user()->tenant_id)->findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
