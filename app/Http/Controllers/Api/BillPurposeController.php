<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BillPurpose;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BillPurposeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $purposes = BillPurpose::where('tenant_id', $request->user()->tenant_id)
            ->orderBy('name')
            ->get();
        return response()->json($purposes);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $purpose = BillPurpose::create(array_merge($validated, [
            'tenant_id' => $request->user()->tenant_id,
        ]));

        return response()->json(['message' => 'Bill purpose created', 'purpose' => $purpose], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $purpose = BillPurpose::where('tenant_id', $request->user()->tenant_id)->findOrFail($id);
        $purpose->update($request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]));
        return response()->json(['message' => 'Updated', 'purpose' => $purpose]);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        BillPurpose::where('tenant_id', $request->user()->tenant_id)->findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
