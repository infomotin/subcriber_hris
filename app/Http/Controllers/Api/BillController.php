<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BillController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $bills = Bill::with(['employee.user', 'billType', 'billPurpose'])
            ->where('tenant_id', $request->user()->tenant_id)
            ->orderBy('id', 'desc')
            ->paginate($request->per_page ?? 15);

        $bills->getCollection()->transform(fn($b) => [
            'id' => $b->id,
            'employee' => $b->employee?->user?->name ?? 'N/A',
            'type' => $b->billType?->name ?? 'N/A',
            'purpose' => $b->billPurpose?->name ?? 'N/A',
            'amount' => $b->amount,
            'status' => $b->status,
            'date' => $b->bill_date?->format('Y-m-d'),
        ]);

        return response()->json($bills);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bill_type_id' => 'required|exists:bill_types,id',
            'bill_purpose_id' => 'required|exists:bill_purposes,id',
            'amount' => 'required|numeric|min:0',
            'bill_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $employee = \App\Models\EmployeeProfile::where('user_id', $request->user()->id)->first();
        $bill = Bill::create(array_merge($validated, [
            'tenant_id' => $request->user()->tenant_id,
            'employee_profile_id' => $employee?->id,
            'status' => 'pending',
        ]));

        return response()->json(['message' => 'Bill created', 'bill' => $bill], 201);
    }

    public function show($id): JsonResponse
    {
        return response()->json(['bill' => Bill::with(['employee.user', 'billType', 'billPurpose'])->findOrFail($id)]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $bill = Bill::findOrFail($id);
        $bill->update($request->all());
        return response()->json(['message' => 'Updated', 'bill' => $bill]);
    }

    public function destroy($id): JsonResponse
    {
        Bill::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
