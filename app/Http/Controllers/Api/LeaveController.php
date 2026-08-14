<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeaveApplication;
use App\Models\LeaveType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function types(Request $request): JsonResponse
    {
        $types = LeaveType::where('tenant_id', $request->user()->tenant_id)
            ->orderBy('name')
            ->get(['id', 'name']);
        return response()->json($types);
    }
    public function index(Request $request): JsonResponse
    {
        $leaves = LeaveApplication::with(['employee.user', 'leaveType'])
            ->where('tenant_id', $request->user()->tenant_id)
            ->orderBy('id', 'desc')
            ->paginate($request->per_page ?? 15);

        $leaves->getCollection()->transform(fn($l) => [
            'id' => $l->id,
            'employee' => $l->employee?->user?->name ?? 'N/A',
            'type' => $l->leaveType?->name ?? 'N/A',
            'from' => $l->start_date?->format('Y-m-d'),
            'to' => $l->end_date?->format('Y-m-d'),
            'days' => $l->total_days,
            'reason' => $l->reason,
            'status' => $l->status,
            'created_at' => $l->created_at->format('Y-m-d'),
        ]);

        return response()->json($leaves);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
        ]);

        $employee = \App\Models\EmployeeProfile::where('user_id', $request->user()->id)->first();
        if (!$employee) {
            return response()->json(['message' => 'Employee profile not found'], 404);
        }

        $leave = LeaveApplication::create([
            'tenant_id' => $request->user()->tenant_id,
            'employee_profile_id' => $employee->id,
            'leave_type_id' => $validated['leave_type_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'reason' => $validated['reason'],
            'status' => 'pending',
            'total_days' => now()->parse($validated['start_date'])->diffInDays(now()->parse($validated['end_date'])) + 1,
        ]);

        return response()->json(['message' => 'Leave applied', 'leave' => $leave], 201);
    }

    public function show($id): JsonResponse
    {
        return response()->json(['leave' => LeaveApplication::with(['employee.user', 'leaveType'])->findOrFail($id)]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $leave = LeaveApplication::findOrFail($id);
        if ($request->has('status')) {
            $leave->update(['status' => $request->status, 'actioned_by' => $request->user()->id]);
        } else {
            $leave->update($request->all());
        }
        return response()->json(['message' => 'Leave updated', 'leave' => $leave]);
    }

    public function destroy($id): JsonResponse
    {
        LeaveApplication::findOrFail($id)->delete();
        return response()->json(['message' => 'Leave deleted']);
    }
}
