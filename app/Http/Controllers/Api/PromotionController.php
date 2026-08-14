<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmployeePromotion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $promotions = EmployeePromotion::with(['employee.user', 'oldDepartment', 'newDepartment', 'oldDesignation', 'newDesignation'])
            ->where('tenant_id', $request->user()->tenant_id)
            ->orderBy('id', 'desc')
            ->paginate($request->per_page ?? 15);

        $promotions->getCollection()->transform(fn($p) => [
            'id' => $p->id,
            'employee' => $p->employee?->user?->name ?? 'N/A',
            'type' => $p->promotion_type,
            'from_department' => $p->oldDepartment?->name,
            'to_department' => $p->newDepartment?->name,
            'from_designation' => $p->oldDesignation?->title,
            'to_designation' => $p->newDesignation?->title,
            'effective_date' => $p->effective_date?->format('Y-m-d'),
            'status' => $p->status,
        ]);

        return response()->json($promotions);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employee_profile_id' => 'required|exists:employee_profiles,id',
            'old_department_id' => 'nullable|exists:departments,id',
            'new_department_id' => 'required|exists:departments,id',
            'old_designation_id' => 'nullable|exists:designations,id',
            'new_designation_id' => 'required|exists:designations,id',
            'promotion_type' => 'required|string|in:' . implode(',', array_keys(EmployeePromotion::TYPES)),
            'notes' => 'nullable|string',
            'effective_date' => 'required|date',
            'reference_number' => 'nullable|string|max:100',
        ]);

        $promotion = EmployeePromotion::create(array_merge($validated, [
            'tenant_id' => $request->user()->tenant_id,
            'status' => 'pending',
        ]));

        return response()->json(['message' => 'Promotion created', 'promotion' => $promotion], 201);
    }

    public function show($id): JsonResponse
    {
        $promotion = EmployeePromotion::with(['employee.user', 'oldDepartment', 'newDepartment', 'oldDesignation', 'newDesignation'])
            ->findOrFail($id);
        return response()->json(['promotion' => $promotion]);
    }
}
