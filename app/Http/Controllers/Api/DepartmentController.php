<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $departments = Department::with('parent')
            ->where('tenant_id', $request->user()->tenant_id)
            ->orderBy('name')
            ->get()
            ->map(fn($d) => [
                'id' => $d->id,
                'name' => $d->name,
                'parent' => $d->parent?->name,
                'employee_count' => $d->employees()->count(),
            ]);

        return response()->json($departments);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:departments,id',
        ]);

        $dept = Department::create([
            'tenant_id' => $request->user()->tenant_id,
            'name' => $validated['name'],
            'parent_id' => $validated['parent_id'],
        ]);

        return response()->json(['message' => 'Department created', 'department' => $dept], 201);
    }

    public function show($id): JsonResponse
    {
        return response()->json(['department' => Department::with('children', 'parent')->findOrFail($id)]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $dept = Department::findOrFail($id);
        $dept->update($request->validate(['name' => 'required|string|max:255', 'parent_id' => 'nullable|exists:departments,id']));
        return response()->json(['message' => 'Updated', 'department' => $dept]);
    }

    public function destroy($id): JsonResponse
    {
        Department::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
