<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $designations = Designation::where('tenant_id', $request->user()->tenant_id)
            ->orderBy('title')
            ->get();
        return response()->json($designations);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'grade' => 'nullable|string|max:50',
        ]);

        $designation = Designation::create(array_merge($validated, [
            'tenant_id' => $request->user()->tenant_id,
        ]));

        return response()->json(['message' => 'Designation created', 'designation' => $designation], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $designation = Designation::where('tenant_id', $request->user()->tenant_id)->findOrFail($id);
        $designation->update($request->validate([
            'title' => 'required|string|max:255',
            'grade' => 'nullable|string|max:50',
        ]));
        return response()->json(['message' => 'Updated', 'designation' => $designation]);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        Designation::where('tenant_id', $request->user()->tenant_id)->findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
