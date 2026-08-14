<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdvanceSource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdvanceSourceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $sources = AdvanceSource::where('tenant_id', $request->user()->tenant_id)
            ->orderBy('name')
            ->get();
        return response()->json($sources);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $source = AdvanceSource::create(array_merge($validated, [
            'tenant_id' => $request->user()->tenant_id,
        ]));

        return response()->json(['message' => 'Advance source created', 'source' => $source], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $source = AdvanceSource::where('tenant_id', $request->user()->tenant_id)->findOrFail($id);
        $source->update($request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]));
        return response()->json(['message' => 'Updated', 'source' => $source]);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        AdvanceSource::where('tenant_id', $request->user()->tenant_id)->findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
