<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IncrementRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IncrementRuleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $rules = IncrementRule::where('tenant_id', $request->user()->tenant_id)
            ->orderBy('name')
            ->get();
        return response()->json($rules);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'joining_date_from' => 'nullable|date',
            'joining_date_to' => 'nullable|date|after_or_equal:joining_date_from',
            'increment_based_on' => 'nullable|string|max:100',
            'year_start_date' => 'nullable|date',
            'special_max_percentage' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        $rule = IncrementRule::create(array_merge($validated, [
            'tenant_id' => $request->user()->tenant_id,
        ]));

        return response()->json(['message' => 'Increment rule created', 'rule' => $rule], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $rule = IncrementRule::where('tenant_id', $request->user()->tenant_id)->findOrFail($id);
        $rule->update($request->validate([
            'name' => 'required|string|max:255',
            'joining_date_from' => 'nullable|date',
            'joining_date_to' => 'nullable|date|after_or_equal:joining_date_from',
            'increment_based_on' => 'nullable|string|max:100',
            'year_start_date' => 'nullable|date',
            'special_max_percentage' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ]));
        return response()->json(['message' => 'Updated', 'rule' => $rule]);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        IncrementRule::where('tenant_id', $request->user()->tenant_id)->findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
