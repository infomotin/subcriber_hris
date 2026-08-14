<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MovementPass;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MovementPassController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $passes = MovementPass::with(['employee.user', 'movementType', 'actionedBy'])
            ->where('tenant_id', $request->user()->tenant_id)
            ->orderBy('id', 'desc')
            ->paginate($request->per_page ?? 15);

        $passes->getCollection()->transform(fn($p) => [
            'id' => $p->id,
            'employee' => $p->employee?->user?->name ?? 'N/A',
            'movement_type' => $p->movementType?->name ?? 'N/A',
            'date' => $p->date?->format('Y-m-d'),
            'out_time' => $p->out_time,
            'return_time' => $p->return_time,
            'duration_hours' => $p->duration_hours,
            'status' => $p->status,
            'actioned_by' => $p->actionedBy?->name,
        ]);

        return response()->json($passes);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'employee_profile_id' => 'required|exists:employee_profiles,id',
            'movement_type_id' => 'required|exists:movement_types,id',
            'date' => 'required|date',
            'out_time' => 'required',
            'return_time' => 'nullable',
            'duration_hours' => 'nullable|numeric|min:0',
            'reason' => 'nullable|string',
        ]);

        $pass = MovementPass::create(array_merge($validated, [
            'tenant_id' => $request->user()->tenant_id,
            'status' => 'pending',
        ]));

        return response()->json(['message' => 'Movement pass created', 'pass' => $pass], 201);
    }

    public function show($id): JsonResponse
    {
        $pass = MovementPass::with(['employee.user', 'movementType'])->findOrFail($id);
        return response()->json(['pass' => $pass]);
    }

    public function approve(Request $request, $id): JsonResponse
    {
        $pass = MovementPass::where('tenant_id', $request->user()->tenant_id)->findOrFail($id);
        $pass->update([
            'status' => $request->status ?? 'approved',
            'actioned_by' => $request->user()->id,
            'action_remarks' => $request->remarks,
        ]);
        return response()->json(['message' => 'Movement pass ' . $pass->status, 'pass' => $pass]);
    }
}
