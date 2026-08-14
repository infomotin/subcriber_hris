<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Increment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IncrementController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $increments = Increment::with(['employee.user', 'rule'])
            ->where('tenant_id', $request->user()->tenant_id)
            ->orderBy('id', 'desc')
            ->paginate($request->per_page ?? 15);

        $increments->getCollection()->transform(fn($i) => [
            'id' => $i->id,
            'employee' => $i->employee?->user?->name ?? 'N/A',
            'type' => $i->increment_type,
            'old_basic' => $i->old_basic,
            'new_basic' => $i->new_basic,
            'amount' => $i->increment_amount,
            'percentage' => $i->increment_percentage,
            'status' => $i->status,
            'effective_date' => $i->enforced_at?->format('Y-m-d'),
        ]);

        return response()->json($increments);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employee_profile_id' => 'required|exists:employee_profiles,id',
            'increment_rule_id' => 'nullable|exists:increment_rules,id',
            'increment_type' => 'required|string',
            'old_basic' => 'nullable|numeric',
            'old_gross' => 'nullable|numeric',
            'new_basic' => 'required|numeric|min:0',
            'new_gross' => 'required|numeric|min:0',
            'increment_amount' => 'required|numeric|min:0',
            'increment_percentage' => 'nullable|numeric',
            'based_on' => 'nullable|string',
            'notes' => 'nullable|string',
            'reference_number' => 'nullable|string|max:100',
        ]);

        $increment = Increment::create(array_merge($validated, [
            'tenant_id' => $request->user()->tenant_id,
            'status' => 'pending',
        ]));

        return response()->json(['message' => 'Increment created', 'increment' => $increment], 201);
    }

    public function show($id): JsonResponse
    {
        $increment = Increment::with(['employee.user', 'rule'])->findOrFail($id);
        return response()->json(['increment' => $increment]);
    }
}
