<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdvanceType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdvanceTypeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $types = AdvanceType::where('tenant_id', $request->user()->tenant_id)
            ->orderBy('name')
            ->get();
        return response()->json($types);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'payment_mode' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $type = AdvanceType::create(array_merge($validated, [
            'tenant_id' => $request->user()->tenant_id,
        ]));

        return response()->json(['message' => 'Advance type created', 'type' => $type], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $type = AdvanceType::where('tenant_id', $request->user()->tenant_id)->findOrFail($id);
        $type->update($request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'payment_mode' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]));
        return response()->json(['message' => 'Updated', 'type' => $type]);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        AdvanceType::where('tenant_id', $request->user()->tenant_id)->findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
