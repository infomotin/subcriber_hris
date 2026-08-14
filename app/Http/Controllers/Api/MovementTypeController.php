<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MovementType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MovementTypeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $types = MovementType::with('monthlyLimits')
            ->where('tenant_id', $request->user()->tenant_id)
            ->orderBy('name')
            ->get();
        return response()->json($types);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'duration_type' => 'nullable|string|max:50',
            'max_hours' => 'nullable|numeric|min:0',
            'requires_return' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $type = MovementType::create(array_merge($validated, [
            'tenant_id' => $request->user()->tenant_id,
        ]));

        return response()->json(['message' => 'Movement type created', 'type' => $type], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $type = MovementType::where('tenant_id', $request->user()->tenant_id)->findOrFail($id);
        $type->update($request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'duration_type' => 'nullable|string|max:50',
            'max_hours' => 'nullable|numeric|min:0',
            'requires_return' => 'boolean',
            'is_active' => 'boolean',
        ]));
        return response()->json(['message' => 'Updated', 'type' => $type]);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        MovementType::where('tenant_id', $request->user()->tenant_id)->findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
