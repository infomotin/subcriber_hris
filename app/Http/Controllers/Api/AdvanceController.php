<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Advance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdvanceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $advances = Advance::with(['employee.user', 'advanceType', 'advanceSource'])
            ->where('tenant_id', $request->user()->tenant_id)
            ->orderBy('id', 'desc')
            ->paginate($request->per_page ?? 15);

        $advances->getCollection()->transform(fn($a) => [
            'id' => $a->id,
            'employee' => $a->employee?->user?->name ?? 'N/A',
            'type' => $a->advanceType?->name ?? 'N/A',
            'amount' => $a->amount,
            'installments' => $a->total_installments,
            'monthly_deduction' => $a->monthly_deduction,
            'status' => $a->status,
            'date' => $a->created_at->format('Y-m-d'),
        ]);

        return response()->json($advances);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'advance_type_id' => 'required|exists:advance_types,id',
            'advance_source_id' => 'required|exists:advance_sources,id',
            'amount' => 'required|numeric|min:0',
            'total_installments' => 'required|integer|min:1',
            'reason' => 'nullable|string',
        ]);

        $employee = \App\Models\EmployeeProfile::where('user_id', $request->user()->id)->first();
        $advance = Advance::create(array_merge($validated, [
            'tenant_id' => $request->user()->tenant_id,
            'employee_profile_id' => $employee?->id,
            'monthly_deduction' => $validated['amount'] / $validated['total_installments'],
            'status' => 'pending',
        ]));

        return response()->json(['message' => 'Advance requested', 'advance' => $advance], 201);
    }

    public function show($id): JsonResponse
    {
        return response()->json(['advance' => Advance::with(['employee.user', 'advanceType', 'advanceSource'])->findOrFail($id)]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $advance = Advance::findOrFail($id);
        $advance->update($request->all());
        return response()->json(['message' => 'Updated', 'advance' => $advance]);
    }

    public function destroy($id): JsonResponse
    {
        Advance::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
